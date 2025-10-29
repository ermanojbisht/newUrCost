<?php

namespace App\Http\Controllers;

use App\Models\UnitGroup;
use Illuminate\Http\Request;

class UnitGroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(UnitGroup::class, 'unit_group');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unitGroups = UnitGroup::with('baseUnit')->paginate(10);
        return view('pages.unit-groups.index', compact('unitGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = \App\Models\Unit::all();
        return view('pages.unit-groups.create', compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:unit_groups',
            'base_unit_id' => 'nullable|exists:units,id',
        ]);

        UnitGroup::create($request->all());

        return redirect()->route('unit-groups.index')
            ->with('success', 'Unit group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UnitGroup $unitGroup)
    {
        return view('pages.unit-groups.show', compact('unitGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnitGroup $unitGroup)
    {
        $units = \App\Models\Unit::all();
        return view('pages.unit-groups.edit', compact('unitGroup', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UnitGroup $unitGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:unit_groups,name,'.$unitGroup->id,
            'base_unit_id' => 'nullable|exists:units,id',
        ]);

        $unitGroup->update($request->all());

        return redirect()->route('unit-groups.index')
            ->with('success', 'Unit group updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnitGroup $unitGroup)
    {
        $unitGroup->delete();

        return redirect()->route('unit-groups.index')
            ->with('success', 'Unit group deleted successfully');
    }
}
