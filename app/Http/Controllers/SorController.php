<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Ratecard;
use App\Models\Sor;
use Illuminate\Http\Request;

class SorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sors = Sor::all();
        return view('sors.index', compact('sors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'sorname' => 'required|max:255',
            'locked' => 'boolean',
            'display_details' => 'nullable|string',
            'filename' => 'nullable|string',
        ]);

        $sor = Sor::create($validatedData);

        return redirect()->route('sors.index')->with('success', 'SOR created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sor $sor)
    {
        $items = $sor->items()->paginate(20);
        $ratecards = Ratecard::all();

        return view('sors.show', compact('sor', 'items', 'ratecards'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sor $sor)
    {
        return view('sors.edit', compact('sor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sor $sor)
    {
        $validatedData = $request->validate([
            'sorname' => 'required|max:255',
            'locked' => 'boolean',
            'display_details' => 'nullable|string',
            'filename' => 'nullable|string',
        ]);

        $sor->update($validatedData);

        return redirect()->route('sors.show', $sor)->with('success', 'SOR updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sor $sor)
    {
        $sor->delete();

        return redirect()->route('sors.index')->with('success', 'SOR deleted successfully!');
    }
}