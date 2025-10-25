<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Ratecard;
use App\Services\RateAnalysisService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected $rateAnalysisService;

    public function __construct(RateAnalysisService $rateAnalysisService)
    {
        $this->rateAnalysisService = $rateAnalysisService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Item $item)
    {
        $ratecardId = $request->input('ratecard', 1); // Default to ratecard 1 if not provided
        $ratecard = Ratecard::find($ratecardId);

        if (!$ratecard) {
            // Handle case where ratecard is not found
            abort(404, 'Ratecard not found.');
        }

        $analysis = $this->rateAnalysisService->calculateRate($item, $ratecard);

        return view('items.show', compact('item', 'analysis', 'ratecard'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
    }
}
