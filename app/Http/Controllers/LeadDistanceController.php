<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeadDistanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, \App\Models\Resource $resource)
    {
        if ($request->ajax()) {
            $query = \App\Models\LeadDistance::with(['rateCard', 'station'])
                ->where('resource_id', $resource->id)
                ->where('is_canceled', 0);

            return \Yajra\DataTables\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('station_name', function($row) {
                    return $row->station ? $row->station->name : '-';
                })
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

        $rateCards = \App\Models\RateCard::all();
        $stations = \App\Models\Station::all();

        // Fetch Global/Fallback Indices (Resource ID 1)
        $fallbackIndices = \App\Models\LeadDistance::with(['rateCard', 'station'])
            ->where('resource_id', 1)
            ->where('is_canceled', 0)
            ->get();

        return view('resources.lead_distances.index', compact('resource', 'rateCards', 'stations', 'fallbackIndices'));
    }

    public function store(Request $request, \App\Models\Resource $resource)
    {
        $validated = $request->validate([
            'rate_card_id' => 'required|exists:rate_cards,id',
            'station_id' => 'nullable|exists:stations,id',
            'distance' => 'required|numeric|min:0',
            'type' => 'required|integer', // 1: Mechanical, 2: Manual, 3: Mule
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $lead = new \App\Models\LeadDistance();
        $lead->resource_id = $resource->id;
        $lead->rate_card_id = $validated['rate_card_id'];
        $lead->station_id = $validated['station_id'];
        $lead->distance = $validated['distance'];
        $lead->type = $validated['type'];
        $lead->valid_from = $validated['valid_from'];
        $lead->valid_to = $validated['valid_to'];
        $lead->is_locked = 0; // Experimental
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Lead Distance added successfully.']);
    }

    public function update(Request $request, $id)
    {
        $lead = \App\Models\LeadDistance::findOrFail($id);

        if ($lead->is_locked > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot update locked record.'], 403);
        }

        $validated = $request->validate([
            'rate_card_id' => 'required|exists:rate_cards,id',
            'station_id' => 'nullable|exists:stations,id',
            'distance' => 'required|numeric|min:0',
            'type' => 'required|integer',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $lead->rate_card_id = $validated['rate_card_id'];
        $lead->station_id = $validated['station_id'];
        $lead->distance = $validated['distance'];
        $lead->type = $validated['type'];
        $lead->valid_from = $validated['valid_from'];
        $lead->valid_to = $validated['valid_to'];
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Lead Distance updated successfully.']);
    }

    public function destroy($id)
    {
        $lead = \App\Models\LeadDistance::findOrFail($id);

        if ($lead->is_locked > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete locked record.'], 403);
        }

        $lead->delete();

        return response()->json(['success' => true, 'message' => 'Lead Distance deleted successfully.']);
    }

    public function show($id)
    {
        $lead = \App\Models\LeadDistance::findOrFail($id);
        return response()->json($lead);
    }

    public function lock(Request $request, $id)
    {
        $lead = \App\Models\LeadDistance::findOrFail($id);

        if ($lead->is_locked != 0) {
            return response()->json(['success' => false, 'message' => 'Only experimental records can be locked.'], 403);
        }

        $validated = $request->validate([
            'valid_from' => 'required|date',
        ]);

        // 1. Find existing Current rate (is_locked = 1) for same resource and rate card
        $currentRate = \App\Models\LeadDistance::where('resource_id', $lead->resource_id)
            ->where('rate_card_id', $lead->rate_card_id)
            ->where('is_locked', 1)
            ->where('is_canceled', 0)
            ->first();

        // 2. Update Previous Rate -> Old
        if ($currentRate) {
            $currentRate->is_locked = 2; // Old
            $newValidFrom = \Carbon\Carbon::parse($validated['valid_from']);
            $currentRate->valid_to = $newValidFrom->subDay();
            $currentRate->save();
        }

        // 3. Update New Rate -> Current
        $lead->is_locked = 1; // Current
        $lead->valid_from = $validated['valid_from'];
        $lead->valid_to = null;
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Record locked and promoted to Current successfully.']);
    }

    public function allIndices(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\LeadDistance::with(['resource', 'rateCard', 'station'])
                ->where('is_canceled', 0);

            if ($request->has('resource_id') && $request->resource_id) {
                $query->where('resource_id', $request->resource_id);
            }
            if ($request->has('rate_card_id') && $request->rate_card_id) {
                $query->where('rate_card_id', $request->rate_card_id);
            }

            return \Yajra\DataTables\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('resource_name', function($row) {
                    return $row->resource ? $row->resource->name : '-';
                })
                ->addColumn('station_name', function($row) {
                    return $row->station ? $row->station->name : '-';
                })
                ->editColumn('valid_from', function($row) {
                    return $row->valid_from ? $row->valid_from->format('d-M-Y') : '-';
                })
                ->editColumn('valid_to', function($row) {
                    return $row->valid_to ? $row->valid_to->format('d-M-Y') : 'Until Changed';
                })
                ->editColumn('is_locked', function($row) {
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

        $rateCards = \App\Models\RateCard::all();
        $resources = \App\Models\Resource::all();

        return view('resources.lead_distances.all', compact('rateCards', 'resources'));
    }
}
