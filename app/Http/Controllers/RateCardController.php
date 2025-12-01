<?php

namespace App\Http\Controllers;

use App\Models\RateCard;
use Illuminate\Http\Request;
use Log;
use App\Helpers\FilterHelper;
use Barryvdh\DomPDF\Facade\Pdf;

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
    /**
     * Generate Labor Resource Rate Report.
     */
    /**
     * Generate Labor Resource Rate Report.
     */
    public function laborRateReport(Request $request)
    {
        $sor = (object)['id' => 'general']; // Dummy SOR for helper
        $filters = FilterHelper::getRateFilters($request, $sor);
        
        $rateCardId = $filters['rate_card_id'];
        $effectiveDate = $filters['effective_date'];

        $rateCard = RateCard::findOrFail($rateCardId);

        $reportData = $this->generateResourceReport($request, $rateCard, 1, $effectiveDate); // 1 = Labour Group
        $rateCards = RateCard::all();

        return view('pages.rate-cards.labor-report', compact('rateCard', 'reportData', 'rateCards', 'rateCardId', 'effectiveDate'));
    }

    /**
     * Generate Machine Resource Rate Report.
     */
    public function machineRateReport(Request $request)
    {
        $sor = (object)['id' => 'general']; // Dummy SOR for helper
        $filters = FilterHelper::getRateFilters($request, $sor);
        
        $rateCardId = $filters['rate_card_id'];
        $effectiveDate = $filters['effective_date'];

        $rateCard = RateCard::findOrFail($rateCardId);

        $reportData = $this->generateResourceReport($request, $rateCard, 2, $effectiveDate); // 2 = Machine Group
        $rateCards = RateCard::all();

        return view('pages.rate-cards.machine-report', compact('rateCard', 'reportData', 'rateCards', 'rateCardId', 'effectiveDate'));
    }

    public function exportLaborPdf(Request $request)
    {
        $sor = (object)['id' => 'general'];
        $filters = FilterHelper::getRateFilters($request, $sor);
        $rateCardId = $filters['rate_card_id'];
        $effectiveDate = $filters['effective_date'];
        $rateCard = RateCard::findOrFail($rateCardId);

        $reportData = $this->generateResourceReport($request, $rateCard, 1, $effectiveDate);

        $pdf = Pdf::loadView('pages.rate-cards.pdf-report', [
            'title' => 'Labor Resource Rates',
            'rateCard' => $rateCard,
            'reportData' => $reportData,
            'effectiveDate' => $effectiveDate
        ]);

        return $pdf->download('labor-rates-' . $rateCard->name . '-' . $effectiveDate . '.pdf');
    }

    public function exportMachinePdf(Request $request)
    {
        $sor = (object)['id' => 'general'];
        $filters = FilterHelper::getRateFilters($request, $sor);
        $rateCardId = $filters['rate_card_id'];
        $effectiveDate = $filters['effective_date'];
        $rateCard = RateCard::findOrFail($rateCardId);

        $reportData = $this->generateResourceReport($request, $rateCard, 2, $effectiveDate);

        $pdf = Pdf::loadView('pages.rate-cards.pdf-report', [
            'title' => 'Machine Resource Rates',
            'rateCard' => $rateCard,
            'reportData' => $reportData,
            'effectiveDate' => $effectiveDate
        ]);

        return $pdf->download('machine-rates-' . $rateCard->name . '-' . $effectiveDate . '.pdf');
    }

    /**
     * Helper to generate resource report data.
     */
    private function generateResourceReport(Request $request, RateCard $rateCard, int $groupId, string $date)
    {
        $resources = \App\Models\Resource::where('resource_group_id', $groupId)
            ->with('unit')
            ->orderBy('secondary_code')
            ->get();

        $reportData = [];

        foreach ($resources as $resource) {
            $details = $this->rateAnalysisService->getResourceRateDetails($resource, $rateCard, $date);

            $components = $details['components'] ?? [];
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

        return $reportData;
    }
}
