<?php

namespace App\Http\Controllers;

use App\Models\Ratecard;
use Illuminate\Http\Request;

class RateCardController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Ratecard::class, 'ratecard');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rateCards = Ratecard::paginate(10);
        return view('pages.rate-cards.index', compact('rateCards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.rate-cards.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rate_cards',
            'description' => 'nullable|string',
        ]);

        Ratecard::create($request->all());

        return redirect()->route('rate-cards.index')
            ->with('success', 'Rate card created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ratecard $rateCard)
    {
        return view('pages.rate-cards.show', compact('rateCard'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ratecard $rateCard)
    {
        return view('pages.rate-cards.edit', compact('rateCard'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ratecard $rateCard)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rate_cards,name,'.$rateCard->id,
            'description' => 'nullable|string',
        ]);

        $rateCard->update($request->all());

        return redirect()->route('rate-cards.index')
            ->with('success', 'Rate card updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ratecard $rateCard)
    {
        $rateCard->delete();

        return redirect()->route('rate-cards.index')
            ->with('success', 'Rate card deleted successfully');
    }
}
