<?php

namespace App\Http\Controllers;

use App\Helpers\FilterHelper;
use App\Models\Rate;
use App\Models\RateCard;
use App\Models\Resource;
use App\Models\ResourceGroup;
use App\Models\Sor;
use App\Models\Unit;
use App\Services\RateAnalysisService;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    protected $rateAnalysisService;

    public function __construct(RateAnalysisService $rateAnalysisService)
    {
        $this->rateAnalysisService = $rateAnalysisService;
    }

    public function show(Request $request, Resource $resource)
    {
        // Handle filters from request and store in session
        // Retrieve from session or defaults
        $filters = FilterHelper::getRateFilters($request, Sor::find(1));

        $rateCardId = $filters['rate_card_id'];
        $effectiveDate = $filters['effective_date'];

        $rateCards = RateCard::select('id','name')->get();

        $rateCard = RateCard::find($rateCardId);
        if (!$rateCard) {
             $rateCard = RateCard::find(1);
        }

        $rateDetails = $this->rateAnalysisService->getResourceRateDetails($resource, $rateCard, $effectiveDate);
        
        $rate = $rateDetails['total_rate'];
        $unit = Unit::find($rateDetails['unit_id']);
        $rateComponents = $rateDetails['components'];

        // Get validity from base rate entry for display
        // We can re-query just for validity dates or modify service to return them.
        // For simplicity, let's re-query the base rate entry here just for dates, 
        // or we could add dates to the service response.
        // Let's keep it simple and re-query for now as it's just metadata.
        $rateEntry = Rate::where('resource_id', $resource->id)
            ->where('rate_card_id', $rateCard->id)
            ->where('valid_from', '<=', $effectiveDate)
            ->where(function ($query) use ($effectiveDate) {
                $query->where('valid_to', '>=', $effectiveDate)
                      ->orWhereNull('valid_to');
            })
            ->first();

        // Fallback check for dates if using card 1
        if (!$rateEntry && $rateCard->id != 1) {
             $rateEntry = Rate::where('resource_id', $resource->id)
                ->where('rate_card_id', 1)
                ->where('valid_from', '<=', $effectiveDate)
                ->where(function ($query) use ($effectiveDate) {
                    $query->where('valid_to', '>=', $effectiveDate)
                          ->orWhereNull('valid_to');
                })
                ->first();
        }

        $validFrom = $rateEntry ? $rateEntry->valid_from : null;
        $validTo = $rateEntry ? $rateEntry->valid_to : null;

        $resourceGroups = ResourceGroup::all();

        return view('resources.show', compact(
            'resource', 
            'rateCards', 
            'rateCardId', 
            'effectiveDate', 
            'rate', 
            'rateComponents',
            'validFrom',
            'validTo',
            'resourceGroups',
            'unit'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $groupId = $request->input('group_id');

        $resources = Resource::query()
            ->when($groupId, function ($q) use ($groupId) {
                $q->where('resource_group_id', $groupId);
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('secondary_code', 'like', "%{$query}%")
                  ->orWhere('id', $query);
            })
            ->with('unit')
            ->limit(20)
            ->get();

        return response()->json($resources);
    }
}
