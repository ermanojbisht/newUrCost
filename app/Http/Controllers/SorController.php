<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Ratecard;
use App\Models\Sor;
use Illuminate\Http\Request;

class SorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Sor::class, 'sor');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sors = Sor::paginate(10);
        return view('pages.sors.index', compact('sors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.sors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sors',
            'is_locked' => 'boolean',
            'display_details' => 'nullable|string',
            'filename' => 'nullable|string',
            'short_name' => 'nullable|string|max:255',
        ]);

        $sor = Sor::create($request->all());

        return redirect()->route('sors.index')->with('success', 'SOR created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sor $sor)
    {
        $items = $sor->items()->paginate(20);
        $ratecards = Ratecard::all();

        return view('pages.sors.show', compact('sor', 'items', 'ratecards'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sor $sor)
    {
        return view('pages.sors.edit', compact('sor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sor $sor)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sors,name,'.$sor->id,
            'is_locked' => 'boolean',
            'display_details' => 'nullable|string',
            'filename' => 'nullable|string',
            'short_name' => 'nullable|string|max:255',
        ]);

        $sor->update($request->all());

        return redirect()->route('sors.index')->with('success', 'SOR updated successfully!');
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
