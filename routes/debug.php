<?php

use Illuminate\Support\Facades\Route;
use App\Models\Sor;
use App\Models\Item;
use App\Models\RateCard;
use App\Services\ItemSkeletonService;
use Illuminate\Http\Request;

Route::get('/debug-ra/{sor}/{item}', function (Sor $sor, Item $item, Request $request, ItemSkeletonService $service) {
    $rateCardId = $request->input('rate_card_id');
    $date = $request->input('date', now()->toDateString());

    if (!$rateCardId) {
        $rateCard = RateCard::first();
        $rateCardId = $rateCard ? $rateCard->id : null;
    }

    $skeletons = $item->skeletons()->get();
    $subitems = $item->subitems()->get();
    $overheads = $item->overheads()->get();
    
    $serviceData = $service->calculateRate($item, $rateCardId, $date);

    return [
        'environment' => app()->environment(),
        'item' => $item->only(['id', 'item_code', 'description']),
        'rate_card_id' => $rateCardId,
        'date' => $date,
        'counts' => [
            'skeletons' => $skeletons->count(),
            'subitems' => $subitems->count(),
            'overheads' => $overheads->count(),
        ],
        'raw_skeletons' => $skeletons,
        'service_output' => $serviceData,
    ];
});
