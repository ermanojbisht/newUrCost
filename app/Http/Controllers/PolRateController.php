<?php

namespace App\Http\Controllers;

use App\Models\PolRate;
use Illuminate\Http\Request;

class PolRateController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PolRate::class, 'pol_rate');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $polRates = PolRate::paginate(10);
        return view('pages.pol-rates.index', compact('polRates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.pol-rates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rate_date' => 'required|date',
            'diesel_rate' => 'required|numeric',
            'mobile_oil_rate' => 'required|numeric',
            'laborer_charges' => 'required|numeric',
            'hiring_charges' => 'required|numeric',
            'overhead_charges' => 'required|numeric',
            'mule_rate' => 'required|numeric',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            'is_locked' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        PolRate::create($request->all());

        return redirect()->route('pol-rates.index')
            ->with('success', 'POL rate created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PolRate $polRate)
    {
        return view('pages.pol-rates.show', compact('polRate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PolRate $polRate)
    {
        return view('pages.pol-rates.edit', compact('polRate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PolRate $polRate)
    {
        $request->validate([
            'rate_date' => 'required|date',
            'diesel_rate' => 'required|numeric',
            'mobile_oil_rate' => 'required|numeric',
            'laborer_charges' => 'required|numeric',
            'hiring_charges' => 'required|numeric',
            'overhead_charges' => 'required|numeric',
            'mule_rate' => 'required|numeric',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            'is_locked' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $polRate->update($request->all());

        return redirect()->route('pol-rates.index')
            ->with('success', 'POL rate updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PolRate $polRate)
    {
        $polRate->delete();

        return redirect()->route('pol-rates.index')
            ->with('success', 'POL rate deleted successfully');
    }
}