<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\RateCard;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function show(Request $request, Resource $resource)
    {
        // Handle filters from request and store in session
        if ($request->has('rate_card_id')) {
            $request->session()->put('rate_card_id', $request->rate_card_id);
        }
        if ($request->has('effective_date')) {
            $request->session()->put('effective_date', $request->effective_date);
        }

        // Retrieve from session or defaults
        $rateCardId = $request->session()->get('rate_card_id', 1); // Default to Basic Rate Card (ID 1)
        $effectiveDate = $request->session()->get('effective_date', now()->toDateString());

        $rateCards = RateCard::all();

        // Rate Determination Logic
        $rateEntry = Rate::where('resource_id', $resource->id)
            ->where('rate_card_id', $rateCardId)
            ->where('valid_from', '<=', $effectiveDate)
            ->orderBy('valid_from', 'desc')
            ->first();

        $rateSource = 'Selected Rate Card';
        $isFallback = false;

        // Fallback to Basic Rate Card (ID = 1) if not found and selected is not 1
        if (!$rateEntry && $rateCardId != 1) {
            $rateEntry = Rate::where('resource_id', $resource->id)
                ->where('rate_card_id', 1)
                ->where('valid_from', '<=', $effectiveDate)
                ->orderBy('valid_from', 'desc')
                ->first();
            
            if ($rateEntry) {
                $rateSource = 'Fallback to Basic Rate Card (ID: 1)';
                $isFallback = true;
            } else {
                $rateSource = 'Not Found (Checked Selected and Basic)';
            }
        } elseif (!$rateEntry) {
             $rateSource = 'Not Found in Basic Rate Card';
        }

        $rate = $rateEntry ? $rateEntry->rate : 0;
        $validFrom = $rateEntry ? $rateEntry->valid_from : null;
        $validTo = $rateEntry ? $rateEntry->valid_to : null;

        $resourceGroups = \App\Models\ResourceGroup::all();

        return view('resources.show', compact(
            'resource', 
            'rateCards', 
            'rateCardId', 
            'effectiveDate', 
            'rate', 
            'rateSource', 
            'isFallback',
            'validFrom',
            'validTo',
            'resourceGroups'
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
