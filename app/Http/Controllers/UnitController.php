<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Unit::class, 'unit');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::with('unitGroup')->paginate(10);
        return view('pages.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unitGroups = \App\Models\UnitGroup::all();
        return view('pages.units.create', compact('unitGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:units',
            'alias' => 'nullable|string|max:255',
            'unit_group_id' => 'nullable|exists:unit_groups,id',
            'conversion_factor' => 'nullable|numeric',
        ]);

        Unit::create($request->all());

        return redirect()->route('units.index')
            ->with('success', 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return view('pages.units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        $unitGroups = \App\Models\UnitGroup::all();
        return view('pages.units.edit', compact('unit', 'unitGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:units,code,'.$unit->id,
            'alias' => 'nullable|string|max:255',
            'unit_group_id' => 'nullable|exists:unit_groups,id',
            'conversion_factor' => 'nullable|numeric',
        ]);

        $unit->update($request->all());

        return redirect()->route('units.index')
            ->with('success', 'Unit updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit deleted successfully');
    }
}
