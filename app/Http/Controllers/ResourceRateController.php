<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\RateCard;
use App\Models\Resource;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceRateController extends Controller
{
    public function index(Resource $resource)
    {
         $rates = Rate::where('resource_id', $resource->id)
                    ->join('rate_cards', 'rates.rate_card_id', '=', 'rate_cards.id')
                    ->with(['rateCard', 'unit'])
                    ->orderBy('rate_cards.name', 'asc')
                    ->orderBy('rates.valid_to', 'desc')
                    ->select('rates.*')
                    ->get();

        $rateCards = RateCard::all();
        $units = Unit::all();

        return view('resources.rates', compact('resource', 'rates', 'rateCards', 'units'));
    }
    public function store(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'rate_card_id' => 'required|exists:rate_cards,id',
            'rate' => 'required|numeric|min:0',
            'unit_id' => 'required|exists:units,id',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'remarks' => 'nullable|string',
        ]);

        $validated['resource_id'] = $resource->id;
        $validated['created_by'] = Auth::id();
        $validated['applicable_date'] = now();
        $validated['is_locked'] = 0; // Always start as Experimental

        Rate::create($validated);

        return response()->json(['success' => true, 'message' => 'Rate added successfully.']);
    }

    public function update(Request $request, Resource $resource, Rate $rate)
    {
        if ($rate->is_locked > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot edit a locked rate.'], 403);
        }

        $validated = $request->validate([
            'rate_card_id' => 'required|exists:rate_cards,id',
            'rate' => 'required|numeric|min:0',
            'unit_id' => 'required|exists:units,id',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'remarks' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();

        $rate->update($validated);

        return response()->json(['success' => true, 'message' => 'Rate updated successfully.']);
    }

    public function destroy(Resource $resource, Rate $rate)
    {
        if ($rate->is_locked > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete a locked rate.'], 403);
        }

        $rate->delete();

        return response()->json(['success' => true, 'message' => 'Rate deleted successfully.']);
    }

    public function lock(Request $request, Resource $resource, Rate $rate)
    {
        if ($rate->is_locked > 0) {
            return response()->json(['success' => false, 'message' => 'Rate is already locked.'], 400);
        }

        $request->validate([
            'valid_from' => 'required|date',
        ]);

        $validFrom = $request->input('valid_from');

        \Illuminate\Support\Facades\DB::transaction(function () use ($resource, $rate, $validFrom) {
            // 1. Find existing current rate (is_locked = 1) for same resource & rate card
            $currentRate = Rate::where('resource_id', $resource->id)
                ->where('rate_card_id', $rate->rate_card_id)
                ->where('is_locked', 1)
                ->first();

            // 2. Update existing current rate to Old
            if ($currentRate) {
                $currentRate->update([
                    'is_locked' => 2,
                    'valid_to' => \Carbon\Carbon::parse($validFrom)->subDay(),
                    'updated_by' => Auth::id(),
                ]);
            }

            // 3. Update new rate to Current
            $rate->update([
                'is_locked' => 1,
                'valid_from' => $validFrom,
                'valid_to' => null, // Open-ended for current
                'updated_by' => Auth::id(),
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Rate locked and set as current successfully.']);
    }
}
