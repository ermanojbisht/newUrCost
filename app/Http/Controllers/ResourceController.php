<?php

namespace App\Http\Controllers;

use App\Helpers\FilterHelper;
use App\Models\Rate;
use App\Models\RateCard;
use App\Models\Resource;
use App\Models\ResourceGroup;
use App\Models\Sor;
use App\Models\Unit;
use App\Models\UnitGroup;
use App\Services\RateAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ResourceController extends Controller
{
    protected $rateAnalysisService;

    public function __construct(RateAnalysisService $rateAnalysisService)
    {
        $this->rateAnalysisService = $rateAnalysisService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Resource::with(['group', 'unit', 'resourceCapacityRule']);

            if ($request->filled('resource_group_id')) {
                $query->where('resource_group_id', $request->resource_group_id);
            }
            if ($request->filled('unit_group_id')) {
                $query->where('unit_group_id', $request->unit_group_id);
            }
            if ($request->filled('unit_id')) {
                $query->where('unit_id', $request->unit_id);
            }
            if ($request->filled('volume_or_weight')) {
                $query->where('volume_or_weight', $request->volume_or_weight);
            }
            if ($request->filled('resource_capacity_rule_id')) {
                $query->where('resource_capacity_rule_id', $request->resource_capacity_rule_id);
            }
            if ($request->filled('resource_capacity_group_id')) {
                $query->where('resource_capacity_group_id', $request->resource_capacity_group_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $rateUrl = route('resources.rates.index', $row->id);
                    $editUrl = route('resources.edit', $row->id);
                    $rateIcon = config('icons.calculator');
                    $editIcon = config('icons.edit');
                    $deleteIcon = config('icons.delete');
                    $chartIcon = config('icons.chart');
                    
                    $btn = '<a href="'.$rateUrl.'" class="text-green-600 hover:text-green-900 mr-2" title="Manage Rates">'.$rateIcon.'</a>';
                    
                    // Add Manage Index button for Labor (1) and Machine (2) groups
                    if ($row->resource_group_id == 1) {
                        $indexUrl = route('resources.labor-indices.index', $row->id);
                        $btn .= '<a href="'.$indexUrl.'" class="text-purple-600 hover:text-purple-900 mr-2" title="Manage Labor Indices">'.$chartIcon.'</a>';
                    } elseif ($row->resource_group_id == 2) {
                        $indexUrl = route('resources.machine-indices.index', $row->id);
                        $btn .= '<a href="'.$indexUrl.'" class="text-orange-600 hover:text-orange-900 mr-2" title="Manage Machine Indices">'.$chartIcon.'</a>';
                    }

                    $btn .= '<a href="'.$editUrl.'" class="text-blue-600 hover:text-blue-900 mr-2" title="Edit">'.$editIcon.'</a>';
                    $btn .= '<button type="button" onclick="deleteResource('.$row->id.')" class="text-red-600 hover:text-red-900" title="Delete">'.$deleteIcon.'</button>';
                    return '<div class="flex items-center">'.$btn.'</div>';
                })
                ->editColumn('volume_or_weight', function($row) {
                    if ($row->volume_or_weight == 1) return 'Volume';
                    if ($row->volume_or_weight == 2) return 'Weight';
                    return 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $resourceGroups = ResourceGroup::all();
        $unitGroups = UnitGroup::all();
        $units = Unit::all();
        $capacityRules = \App\Models\ResourceCapacityRule::all();
        $capacityGroups = \App\Models\ResourceCapacityGroup::all();

        return view('resources.index', compact('resourceGroups', 'unitGroups', 'units', 'capacityRules', 'capacityGroups'));
    }

    public function create()
    {
        $resourceGroups = ResourceGroup::all();
        $unitGroups = UnitGroup::all();
        $units = Unit::all();
        return view('resources.create', compact('resourceGroups', 'unitGroups', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'secondary_code' => 'nullable|string|max:255',
            'resource_group_id' => 'nullable|exists:resource_groups,id',
            'unit_group_id' => 'nullable|exists:unit_groups,id',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'volume_or_weight' => 'nullable|in:0,1,2',
        ]);

        $validated['created_by'] = Auth::id();

        Resource::create($validated);

        return redirect()->route('resources.index')->with('success', 'Resource created successfully.');
    }

    public function edit(Resource $resource)
    {
        $resourceGroups = ResourceGroup::all();
        $unitGroups = UnitGroup::all();
        $units = Unit::all();
        return view('resources.edit', compact('resource', 'resourceGroups', 'unitGroups', 'units'));
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'secondary_code' => 'nullable|string|max:255',
            'resource_group_id' => 'nullable|exists:resource_groups,id',
            'unit_group_id' => 'nullable|exists:unit_groups,id',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'volume_or_weight' => 'nullable|in:0,1,2',
        ]);

        $validated['updated_by'] = Auth::id();

        $resource->update($validated);

        return redirect()->route('resources.index')->with('success', 'Resource updated successfully.');
    }

    public function destroy(Resource $resource)
    {
        // Check for dependencies (e.g., used in ItemRates, SubitemRates)
        // For now, we'll just check items_using_count
        if ($resource->items_using_count > 0) {
             return response()->json(['success' => false, 'message' => 'Cannot delete resource. It is used by ' . $resource->items_using_count . ' items.']);
        }

        $resource->delete();

        return response()->json(['success' => true, 'message' => 'Resource deleted successfully.']);
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
