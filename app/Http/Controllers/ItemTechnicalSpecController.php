<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use App\Services\ItemTechnicalSpecService;

class ItemTechnicalSpecController extends Controller
{
    protected $service;

    public function __construct(ItemTechnicalSpecService $service)
    {
        $this->service = $service;
    }

    public function generate(Item $item)
    {
        try {
            $spec = $this->service->generateSpecs($item);
            return response()->json(['success' => true, 'message' => 'Technical specifications generated successfully.', 'data' => $spec]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error generating specifications: ' . $e->getMessage()], 500);
        }
    }

    public function edit(Item $item)
    {
        return view('items.technical-specs.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'introduction' => 'nullable|string',
            'specifications' => 'nullable|array',
            'tests_frequency' => 'nullable|array',
            'dos_donts' => 'nullable|array',
            'execution_sequence' => 'nullable|array',
            'precautionary_measures' => 'nullable|array',
            'reference_links' => 'nullable|array',
        ]);

        try {
            $spec = \App\Models\ItemTechnicalSpec::updateOrCreate(
                ['item_id' => $item->id],
                $validated
            );
            return response()->json(['success' => true, 'message' => 'Technical specifications updated successfully.', 'data' => $spec]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating specifications: ' . $e->getMessage()], 500);
        }
    }
}
