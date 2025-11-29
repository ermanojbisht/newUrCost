<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\Station::query();
            return \Yajra\DataTables\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $showUrl = route('stations.show', $row->id);
                    $btn = '<a href="'.$showUrl.'" class="text-green-600 hover:text-green-900 mr-2" title="Show">'.config('icons.show').'</a>';
                    $btn .= '<button type="button" data-id="'.$row->id.'" class="edit-btn text-blue-600 hover:text-blue-900 mr-2" title="Edit">'.config('icons.edit').'</button>';
                    $btn .= '<button type="button" onclick="deleteStation('.$row->id.')" class="text-red-600 hover:text-red-900" title="Delete">'.config('icons.delete').'</button>';
                    return '<div class="flex items-center">'.$btn.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('stations.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        \App\Models\Station::create($validated);

        return response()->json(['success' => true, 'message' => 'Station created successfully.']);
    }

    public function show(Request $request, string $id)
    {
        $station = \App\Models\Station::findOrFail($id);
        
        if ($request->ajax()) {
            return response()->json($station);
        }

        $resources = [];
        if ($station->resources) {
            $resources = \App\Models\Resource::whereIn('id', $station->resources)->get();
        }

        $rateCards = [];
        if ($station->rate_card_ids) {
            $rateCards = \App\Models\RateCard::whereIn('id', $station->rate_card_ids)->get();
        }

        return view('stations.show', compact('station', 'resources', 'rateCards'));
    }

    public function sync($id)
    {
        $station = \App\Models\Station::findOrFail($id);
        $result = $station->updateAssociations();
        return response()->json(['success' => true, 'message' => 'Associations synced successfully.', 'data' => $result]);
    }

    public function update(Request $request, string $id)
    {
        $station = \App\Models\Station::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $station->update($validated);

        return response()->json(['success' => true, 'message' => 'Station updated successfully.']);
    }

    public function destroy(string $id)
    {
        $station = \App\Models\Station::findOrFail($id);
        $station->delete();
        return response()->json(['success' => true, 'message' => 'Station deleted successfully.']);
    }
}
