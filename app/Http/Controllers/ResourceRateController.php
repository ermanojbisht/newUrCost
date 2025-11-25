<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceRateController extends Controller
{
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
        $validated['applicable_date'] = now(); // Default to now, or use valid_from

        Rate::create($validated);

        return response()->json(['success' => true, 'message' => 'Rate added successfully.']);
    }

    public function update(Request $request, Resource $resource, Rate $rate)
    {
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
        $rate->delete();

        return response()->json(['success' => true, 'message' => 'Rate deleted successfully.']);
    }
}
