<?php

namespace App\Http\Controllers;

use App\Models\LaborIndex;
use App\Models\MachineIndex;
use App\Models\RateCard;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Log;

class LaborIndexController extends Controller
{
    public function index(Request $request, Resource $resource)
    {
        if ($resource->resource_group_id != 1) {
            abort(403, 'Labor Index is only applicable for Labor resources.');
        }

        if ($request->ajax()) {
            $query = LaborIndex::with(['rateCard', 'createdBy'])
                ->where('resource_id', $resource->id)
                ->where('is_canceled', 0);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('valid_from', function($row) {
                    return $row->valid_from ? $row->valid_from->format('d-M-Y') : '-';
                })
                ->editColumn('valid_to', function($row) {
                    return $row->valid_to ? $row->valid_to->format('d-M-Y') : 'Until Changed';
                })
                ->editColumn('is_locked', function($row) {
                    // 0: Experimental, 1: Current, 2: Old
                    return $row->is_locked;
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    if ($row->is_locked == 0) {
                        $btn .= '<button type="button" onclick="openLockModal('.$row->id.', \''.($row->valid_from ? $row->valid_from->format('Y-m-d') : '').'\')" class="text-indigo-600 hover:text-indigo-900 mr-2" title="Lock & Promote">'.config('icons.lock').'</button>';
                        $btn .= '<button type="button" data-id="'.$row->id.'" class="edit-btn text-blue-600 hover:text-blue-900 mr-2" title="Edit">'.config('icons.edit').'</button>';
                        $btn .= '<button type="button" onclick="deleteIndex('.$row->id.')" class="text-red-600 hover:text-red-900" title="Delete">'.config('icons.delete').'</button>';
                    } else {
                        $btn .= '<span class="text-gray-400 cursor-not-allowed mr-2" title="Locked">'.config('icons.lock').'</span>';
                    }
                    return '<div class="flex items-center">'.$btn.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $rateCards = RateCard::all();
        
        $fallbackIndices = [];
        $totalFallback = 0;
        if ($resource->id != 1) {
             $query = \App\Models\LaborIndex::with(['rateCard', 'createdBy'])
                ->where('resource_id', 1)
                ->where('is_canceled', 0);
             
             $totalFallback = $query->count();
             
             $fallbackIndices = $query->orderBy('valid_from', 'desc')
                ->take(5)
                ->get();
        }

        return view('resources.labor_indices.index', compact('resource', 'rateCards', 'fallbackIndices', 'totalFallback'));
    }

    public function store(Request $request, Resource $resource)
    {
        if ($resource->resource_group_id != 1) {
            abort(403, 'Labor Index is only applicable for Labor resources.');
        }

        $validated = $request->validate([
            'rate_card_id' => 'required|exists:rate_cards,id',
            'index_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_locked' => 'boolean'
        ]);

        $index = new LaborIndex();
        $index->resource_id = $resource->id;
        $index->rate_card_id = $validated['rate_card_id'];
        $index->index_value = $validated['index_value'];
        $index->valid_from = $validated['valid_from'];
        $index->valid_to = $validated['valid_to'];
        $index->is_locked = $request->has('is_locked');
        $index->created_by = Auth::id();
        $index->save();

        return response()->json(['success' => true, 'message' => 'Index added successfully.']);
    }

    public function globalIndex(Request $request)
    {
        if ($request->ajax()) {
            $query = LaborIndex::with(['rateCard', 'createdBy'])
                ->where('resource_id', 1)
                ->where('is_canceled', 0);
            Log::info("globalIndex = ".print_r($query->get()->toArray(),true));

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('valid_from', function($row) {
                    return $row->valid_from ? $row->valid_from->format('d-M-Y') : '-';
                })
                ->editColumn('valid_to', function($row) {
                    return $row->valid_to ? $row->valid_to->format('d-M-Y') : 'Until Changed';
                })
                ->editColumn('is_locked', function($row) {
                    // 0: Experimental, 1: Current, 2: Old
                    return $row->is_locked;
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    if ($row->is_locked == 0) {
                        $btn .= '<button type="button" onclick="openLockModal('.$row->id.', \''.($row->valid_from ? $row->valid_from->format('Y-m-d') : '').'\')" class="text-indigo-600 hover:text-indigo-900 mr-2" title="Lock & Promote">'.config('icons.lock').'</button>';
                        $btn .= '<button type="button" data-id="'.$row->id.'" class="edit-btn text-blue-600 hover:text-blue-900 mr-2" title="Edit">'.config('icons.edit').'</button>';
                        $btn .= '<button type="button" onclick="deleteIndex('.$row->id.')" class="text-red-600 hover:text-red-900" title="Delete">'.config('icons.delete').'</button>';
                    } else {
                        $btn .= '<span class="text-gray-400 cursor-not-allowed mr-2" title="Locked">'.config('icons.lock').'</span>';
                    }
                    return '<div class="flex items-center">'.$btn.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $rateCards = RateCard::all();
        // Create a dummy resource object for the view
        $resource = new Resource();
        $resource->forceFill([
            'id' => 1,
            'name' => 'Global Labor Index',
            'secondary_code' => 'GLOBAL',
            'resource_group_id' => 1
        ]);
        $resource->setRelation('group', new \App\Models\ResourceGroup(['name' => 'Global']));
        
        // Pass empty fallback variables to avoid undefined variable errors in view
        $fallbackIndices = [];
        $totalFallback = 0;

        return view('resources.labor_indices.index', compact('resource', 'rateCards', 'fallbackIndices', 'totalFallback'));
    }

    public function globalStore(Request $request)
    {
        $validated = $request->validate([
            'rate_card_id' => 'required|exists:rate_cards,id',
            'index_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $index = new LaborIndex();
        $index->resource_id = 1; // Global ID
        $index->rate_card_id = $validated['rate_card_id'];
        $index->index_value = $validated['index_value'];
        $index->valid_from = $validated['valid_from'];
        $index->valid_to = $validated['valid_to'];
        $index->is_locked = 0; // Always Experimental
        $index->created_by = Auth::id();
        $index->save();

        return response()->json(['success' => true, 'message' => 'Global Index added successfully.']);
    }

    public function update(Request $request, $id)
    {
        $index = LaborIndex::findOrFail($id);

        if ($index->is_locked > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot update locked index.'], 403);
        }

        $validated = $request->validate([
            'rate_card_id' => 'required|exists:rate_cards,id',
            'index_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $index->rate_card_id = $validated['rate_card_id'];
        $index->index_value = $validated['index_value'];
        $index->valid_from = $validated['valid_from'];
        $index->valid_to = $validated['valid_to'];
        $index->save();

        return response()->json(['success' => true, 'message' => 'Index updated successfully.']);
    }

    public function destroy($id)
    {
        $index = LaborIndex::findOrFail($id);

        if ($index->is_locked > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete locked index.'], 403);
        }

        $index->is_canceled = 1;
        $index->save();

        return response()->json(['success' => true, 'message' => 'Index deleted successfully.']);
    }
    
    public function show($id)
    {
        $index = LaborIndex::findOrFail($id);
        return response()->json($index);
    }

    public function lock(Request $request, $id)
    {
        $index = LaborIndex::findOrFail($id);

        if ($index->is_locked != 0) {
            return response()->json(['success' => false, 'message' => 'Only experimental indices can be locked.'], 403);
        }

        $validated = $request->validate([
            'valid_from' => 'required|date',
        ]);

        // 1. Find existing Current rate (is_locked = 1) for same resource and rate card
        $currentRate = LaborIndex::where('resource_id', $index->resource_id)
            ->where('rate_card_id', $index->rate_card_id)
            ->where('is_locked', 1)
            ->where('is_canceled', 0)
            ->first();

        // 2. Update Previous Rate (Rate B) -> Old
        if ($currentRate) {
            $currentRate->is_locked = 2; // Old
            // Set valid_to to new rate's valid_from - 1 day
            $newValidFrom = \Carbon\Carbon::parse($validated['valid_from']);
            $currentRate->valid_to = $newValidFrom->subDay();
            $currentRate->save();
        }

        // 3. Update New Rate (Rate A) -> Current
        $index->is_locked = 1; // Current
        $index->valid_from = $validated['valid_from'];
        $index->valid_to = null; // Open-ended for current
        $index->save();

        return response()->json(['success' => true, 'message' => 'Index locked and promoted to Current successfully.']);
    }
}
