<?php

namespace App\Http\Controllers;

use App\Models\ResourceCapacityRule;
use Illuminate\Http\Request;

class ResourceCapacityRuleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ResourceCapacityRule::class, 'resource_capacity_rule');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resourceCapacityRules = ResourceCapacityRule::paginate(10);
        return view('pages.resource-capacity-rules.index', compact('resourceCapacityRules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.resource-capacity-rules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mechanical_capacity' => 'nullable|numeric',
            'net_mechanical_capacity' => 'nullable|numeric',
            'manual_capacity' => 'nullable|numeric',
            'net_manual_capacity' => 'nullable|numeric',
            'mule_factor' => 'nullable|numeric',
            'sample_resource' => 'nullable|string',
        ]);

        ResourceCapacityRule::create($request->all());

        return redirect()->route('resource-capacity-rules.index')
            ->with('success', 'Resource capacity rule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ResourceCapacityRule $resourceCapacityRule)
    {
        return view('pages.resource-capacity-rules.show', compact('resourceCapacityRule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResourceCapacityRule $resourceCapacityRule)
    {
        return view('pages.resource-capacity-rules.edit', compact('resourceCapacityRule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResourceCapacityRule $resourceCapacityRule)
    {
        $request->validate([
            'mechanical_capacity' => 'nullable|numeric',
            'net_mechanical_capacity' => 'nullable|numeric',
            'manual_capacity' => 'nullable|numeric',
            'net_manual_capacity' => 'nullable|numeric',
            'mule_factor' => 'nullable|numeric',
            'sample_resource' => 'nullable|string',
        ]);

        $resourceCapacityRule->update($request->all());

        return redirect()->route('resource-capacity-rules.index')
            ->with('success', 'Resource capacity rule updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceCapacityRule $resourceCapacityRule)
    {
        $resourceCapacityRule->delete();

        return redirect()->route('resource-capacity-rules.index')
            ->with('success', 'Resource capacity rule deleted successfully');
    }
}