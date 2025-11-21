<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Ohead;
use App\Models\Skeleton;
use App\Models\Sor;
use App\Models\Subitem;
use App\Services\ItemSkeletonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemSkeletonController extends Controller
{
    protected $skeletonService;

    public function __construct(ItemSkeletonService $skeletonService)
    {
        $this->skeletonService = $skeletonService;
    }

    public function show(Request $request, Sor $sor, Item $item)
    {
        $rateCardId = $request->input('rate_card_id');
        $date = $request->input('date');

        $data = $this->skeletonService->calculateRate($item, $rateCardId, $date);

        return response()->json($data);
    }

    public function showPage(Sor $sor, Item $item)
    {
        return view('sors.skeleton', compact('sor', 'item'));
    }

    // --- Resources ---

    public function addResource(Request $request, Sor $sor, Item $item)
    {
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $maxOrder = $item->skeletons()->max('sort_order') ?? 0;

        $skeleton = new Skeleton([
            'sor_id' => $sor->id,
            'item_code' => $item->item_code,
            'resource_id' => $request->input('resource_id'),
            'quantity' => $request->input('quantity'),
            'unit_id' => $request->input('unit_id'),
            'sort_order' => $maxOrder + 1,
            'valid_from' => now(), // Default
        ]);

        $skeleton->save();

        return response()->json(['message' => 'Resource added successfully', 'id' => $skeleton->id]);
    }

    public function removeResource(Sor $sor, Item $item, Skeleton $skeleton)
    {
        if ($skeleton->item_code !== $item->item_code) {
            return response()->json(['message' => 'Resource does not belong to this item'], 403);
        }
        $skeleton->delete();
        return response()->json(['message' => 'Resource removed successfully']);
    }

    // --- Subitems ---

    public function addSubitem(Request $request, Sor $sor, Item $item)
    {
        $request->validate([
            'sub_item_id' => 'required|exists:items,item_code', // Assuming sub_item_id refers to item_code
            'quantity' => 'required|numeric|min:0',
        ]);

        $maxOrder = $item->subitems()->max('sort_order') ?? 0;

        $subitem = new Subitem([
            'item_id' => $item->item_code, // Subitem links via item_code usually
            'sub_item_id' => $request->input('sub_item_id'),
            'quantity' => $request->input('quantity'),
            'sort_order' => $maxOrder + 1,
            'valid_from' => now(),
        ]);

        $subitem->save();

        return response()->json(['message' => 'Sub-item added successfully', 'id' => $subitem->id]);
    }

    public function removeSubitem(Sor $sor, Item $item, Subitem $subitem)
    {
        // Check if subitem belongs to item (via item_code)
        if ($subitem->item_id != $item->item_code) {
            return response()->json(['message' => 'Sub-item does not belong to this item'], 403);
        }
        $subitem->delete();
        return response()->json(['message' => 'Sub-item removed successfully']);
    }

    // --- Overheads ---

    public function addOverhead(Request $request, Sor $sor, Item $item)
    {
        $request->validate([
            'overhead_id' => 'required|exists:overhead_masters,id',
            'parameter' => 'required|numeric', // Percentage or amount
        ]);

        $maxOrder = $item->oheads()->max('sort_order') ?? 0;

        $ohead = new Ohead([
            'item_id' => $item->id,
            'overhead_id' => $request->input('overhead_id'),
            'parameter' => $request->input('parameter'),
            'sort_order' => $maxOrder + 1,
            'valid_from' => now(),
        ]);

        $ohead->save();

        return response()->json(['message' => 'Overhead added successfully', 'id' => $ohead->id]);
    }

    public function removeOverhead(Sor $sor, Item $item, Ohead $ohead)
    {
        if ($ohead->item_id !== $item->id) {
            return response()->json(['message' => 'Overhead does not belong to this item'], 403);
        }
        $ohead->delete();
        return response()->json(['message' => 'Overhead removed successfully']);
    }
}
