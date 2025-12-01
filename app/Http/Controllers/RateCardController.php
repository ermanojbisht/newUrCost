<?php

namespace App\Http\Controllers;

use App\Models\RateCard;
use Illuminate\Http\Request;
use Log;

class RateCardController extends Controller
{
    protected $rateAnalysisService;

    public function __construct(\App\Services\RateAnalysisService $rateAnalysisService)
    {
        $this->authorizeResource(RateCard::class, 'ratecard');
        $this->rateAnalysisService = $rateAnalysisService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rateCards = RateCard::paginate(10);
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

        RateCard::create($request->all());

        return redirect()->route('rate-cards.index')
            ->with('success', 'Rate card created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RateCard $rateCard)
    {
        return view('pages.rate-cards.show', compact('rateCard'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RateCard $rateCard)
    {
        return view('pages.rate-cards.edit', compact('rateCard'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RateCard $rateCard)
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
    public function destroy(RateCard $rateCard)
    {
        $rateCard->delete();

        return redirect()->route('rate-cards.index')
            ->with('success', 'Rate card deleted successfully');
    }

    /**
     * Generate Labor Resource Rate Report.
     */
    public function laborRateReport(RateCard $rateCard)
    {
        // ID 1 is "Labour Group"
        $labourGroupId = 1;
        $date = now()->format('Y-m-d');


        $resources = \App\Models\Resource::where('resource_group_id', $labourGroupId)
            ->with('unit')
            ->orderBy('secondary_code')
            ->get();

        $reportData = [];

        //Log::info("this = ".print_r($resources->toArray(),true));

        foreach ($resources as $resource) {
            $details = $this->rateAnalysisService->getResourceRateDetails($resource, $rateCard, $date);

            $components = $details['components'] ?? [];
            // remove null/empty
            // join with space or '' , as PHP_EOL required
            $remark = collect($components)->pluck('description')->filter()->implode(' ,');

            $reportData[] = [
                'resource' => $resource,
                'unit' => $resource->unit->name ?? '-',
                'base_rate' => $details['base_rate'],
                'index_cost' => $details['index_cost'],
                'total_rate' => $details['total_rate'],
                'remarks' =>  $remark,
            ];
        }

        return view('pages.rate-cards.labor-report', compact('rateCard', 'reportData'));
    }
}
