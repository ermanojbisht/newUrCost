<?php

namespace App\Http\Controllers;

use App\Models\TruckSpeed;
use Illuminate\Http\Request;

class TruckSpeedController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TruckSpeed::class, 'truck_speed');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $truckSpeeds = TruckSpeed::paginate(10);
        return view('pages.truck-speeds.index', compact('truckSpeeds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.truck-speeds.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lead_distance' => 'required|numeric|unique:truck_speeds',
            'average_speed' => 'required|numeric',
        ]);

        TruckSpeed::create($request->all());

        return redirect()->route('truck-speeds.index')
            ->with('success', 'Truck speed created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TruckSpeed $truckSpeed)
    {
        return view('pages.truck-speeds.show', compact('truckSpeed'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TruckSpeed $truckSpeed)
    {
        return view('pages.truck-speeds.edit', compact('truckSpeed'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TruckSpeed $truckSpeed)
    {
        $request->validate([
            'lead_distance' => 'required|numeric|unique:truck_speeds,lead_distance,'.$truckSpeed->id,
            'average_speed' => 'required|numeric',
        ]);

        $truckSpeed->update($request->all());

        return redirect()->route('truck-speeds.index')
            ->with('success', 'Truck speed updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TruckSpeed $truckSpeed)
    {
        $truckSpeed->delete();

        return redirect()->route('truck-speeds.index')
            ->with('success', 'Truck speed deleted successfully');
    }
}