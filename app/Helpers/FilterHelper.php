<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class FilterHelper
{
    public static function getRateFilters(Request $request, $sor)
    {
        // Store (if provided)
        if ($request->filled('rate_card_id')) {
            $request->session()->put('rate_card_id', $request->rate_card_id);
        }

        if ($request->filled('effective_date')) {
            $request->session()->put('effective_date', $request->effective_date);
        }

        // Retrieve (with defaults)
        return [
            'rate_card_id' => $request->session()->get(
                'rate_card_id',
                config('urcost.default_rate_cards.' . $sor->id, 1)
            ),

            'effective_date' => $request->session()->get(
                'effective_date',
                now()->toDateString()
            )
        ];
    }
}
