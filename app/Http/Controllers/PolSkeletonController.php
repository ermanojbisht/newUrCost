<?php

namespace App\Http\Controllers;

use App\Models\PolSkeleton;
use Illuminate\Http\Request;

class PolSkeletonController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PolSkeleton::class, 'pol_skeleton');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $polSkeletons = PolSkeleton::paginate(10);
        return view('pages.pol-skeletons.index', compact('polSkeletons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.pol-skeletons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'diesel_mileage' => 'required|numeric',
            'mobile_oil_mileage' => 'required|numeric',
            'number_of_laborers' => 'required|integer',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            'is_locked' => 'boolean',
        ]);

        PolSkeleton::create($request->all());

        return redirect()->route('pol-skeletons.index')
            ->with('success', 'POL skeleton created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PolSkeleton $polSkeleton)
    {
        return view('pages.pol-skeletons.show', compact('polSkeleton'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PolSkeleton $polSkeleton)
    {
        return view('pages.pol-skeletons.edit', compact('polSkeleton'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PolSkeleton $polSkeleton)
    {
        $request->validate([
            'date' => 'required|date',
            'diesel_mileage' => 'required|numeric',
            'mobile_oil_mileage' => 'required|numeric',
            'number_of_laborers' => 'required|integer',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            'is_locked' => 'boolean',
        ]);

        $polSkeleton->update($request->all());

        return redirect()->route('pol-skeletons.index')
            ->with('success', 'POL skeleton updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PolSkeleton $polSkeleton)
    {
        $polSkeleton->delete();

        return redirect()->route('pol-skeletons.index')
            ->with('success', 'POL skeleton deleted successfully');
    }
}