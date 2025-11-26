<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\RateCard;
use App\Models\Sor;
use App\Services\RateAnalysisService;
use Illuminate\Http\Request;
use Log;

class ItemRateController extends Controller
{
    protected $rateAnalysisService;

    public function __construct(RateAnalysisService $rateAnalysisService)
    {
        $this->rateAnalysisService = $rateAnalysisService;
    }

    /**
     * Display the resource consumption report for an item.
     *
     * @param Request $request
     * @param Sor $sor
     * @param Item $item
     * @return \Illuminate\View\View
     */
    public function consumptionWithoutOh(Request $request, Sor $sor, Item $item)
    {
        $rateCardId = $request->input('rate_card_id', 1); // Default to Basic Rate Card
        $rateCard = RateCard::find($rateCardId);
        
        $date = $request->input('date', now()->toDateString());

        $consumptionList = $this->rateAnalysisService->getFlatResourceListWithoutOh($item, $rateCard, $date);
        //Log::info("consumptionList = ".print_r($consumptionList,true));

        // Fetch all rate cards for the dropdown
        $rateCards = RateCard::all();

        return view('sors.items.consumptionWithoutOh', compact('sor', 'item', 'rateCard', 'date', 'consumptionList', 'rateCards'));
    }


    public function consumption(Request $request, Sor $sor, Item $item)
    {
        $rateCardId = $request->input('rate_card_id', 1); // Default to Basic Rate Card
        $rateCard = RateCard::find($rateCardId);

        $date = $request->input('date', now()->toDateString());

        $data = $this->rateAnalysisService->getFlatResourceList($item, $rateCard, $date);
        $consumptionList = $data['resources'];
        $overheadList = $data['overheads'];

        // Fetch all rate cards for the dropdown
        $rateCards = RateCard::all();

        return view('sors.items.consumption', compact('sor', 'item', 'rateCard', 'date', 'consumptionList', 'overheadList', 'rateCards'));
    }
}
