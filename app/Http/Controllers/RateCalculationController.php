<?php

namespace App\Http\Controllers;

use App\Models\RateCard;
use App\Services\RateCalculationService;
use Illuminate\Http\Request;

use App\Models\Sor;

class RateCalculationController extends Controller
{
    protected $rateCalculationService;

    public function __construct(RateCalculationService $rateCalculationService)
    {
        $this->rateCalculationService = $rateCalculationService;
    }

    public function index()
    {
        $rateCards = RateCard::all();
        $sors = Sor::all();
        return view('admin.rate-calculation.index', compact('rateCards', 'sors'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'rate_card_id' => 'required|exists:rate_cards,id',
            'sor_id' => 'nullable|exists:sors,id',
            'subitems_only' => 'nullable|boolean',
            'valid_from' => 'nullable|date',
        ]);

        $result = $this->rateCalculationService->calculateAll(
            $request->rate_card_id,
            $request->sor_id,
            $request->boolean('subitems_only'),
            $request->input('valid_from')
        );

        return response()->json($result);
    }
}
