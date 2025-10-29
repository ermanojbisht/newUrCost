<?php

namespace App\Http\Controllers;

use App\Models\ResourceGroup;
use Illuminate\Http\Request;

class ResourceGroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ResourceGroup::class, 'resource_group');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resourceGroups = ResourceGroup::paginate(10);
        return view('pages.resource-groups.index', compact('resourceGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.resource-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:resource_groups',
        ]);

        ResourceGroup::create($request->all());

        return redirect()->route('resource-groups.index')
            ->with('success', 'Resource group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ResourceGroup $resourceGroup)
    {
        return view('pages.resource-groups.show', compact('resourceGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResourceGroup $resourceGroup)
    {
        return view('pages.resource-groups.edit', compact('resourceGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResourceGroup $resourceGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:resource_groups,name,'.$resourceGroup->id,
        ]);

        $resourceGroup->update($request->all());

        return redirect()->route('resource-groups.index')
            ->with('success', 'Resource group updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceGroup $resourceGroup)
    {
        $resourceGroup->delete();

        return redirect()->route('resource-groups.index')
            ->with('success', 'Resource group deleted successfully');
    }
}