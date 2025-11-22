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

    public function showPage(Request $request, Sor $sor, Item $item)
    {
        // Handle filters from request and store in session
        if ($request->has('rate_card_id')) {
            $request->session()->put('rate_card_id', $request->rate_card_id);
        }
        if ($request->has('effective_date')) {
            $request->session()->put('effective_date', $request->effective_date);
        }

        // Retrieve from session or defaults
        $rateCardId = $request->session()->get('rate_card_id', config('urcost.default_rate_cards.' . $sor->id, 1));
        $effectiveDate = $request->session()->get('effective_date', now()->toDateString());

        $rateCards = \App\Models\RateCard::all();
        $units = \App\Models\Unit::all();
        $resourceGroups = \App\Models\ResourceGroup::all();

        return view('sors.skeleton', compact('sor', 'item', 'rateCards', 'units', 'rateCardId', 'effectiveDate', 'resourceGroups'));
    }

    public function copySkeleton(Request $request, Sor $sor, Item $item)
    {
        $request->validate([
            'source_item_id' => 'required|exists:items,id',
        ]);

        $sourceItem = Item::findOrFail($request->source_item_id);

        DB::transaction(function () use ($item, $sourceItem, $sor) {
            // Delete Resources (linked via item_code)
            Skeleton::where('item_code', $item->item_code)->delete();

            // Delete Subitems (linked via item_id which is item_code)
            Subitem::where('item_id', $item->item_code)->delete();

            // Delete Overheads (linked via item_id which is id)
            Ohead::where('item_id', $item->id)->delete();

            // Copy Resources
            $sourceResources = Skeleton::where('item_code', $sourceItem->item_code)->get();
            foreach ($sourceResources as $res) {
                $newRes = $res->replicate();
                $newRes->item_code = $item->item_code;
                $newRes->sor_id = $sor->id;
                $newRes->created_by = auth()->id();
                $newRes->updated_by = auth()->id();
                $newRes->save();
            }

            // Copy Subitems
            $sourceSubitems = Subitem::where('item_id', $sourceItem->item_code)->get();
            foreach ($sourceSubitems as $sub) {
                $newSub = $sub->replicate();
                $newSub->item_id = $item->item_code;
                $newSub->created_by = auth()->id();
                $newSub->updated_by = auth()->id();
                $newSub->save();
            }

            // Copy Overheads
            $sourceOverheads = Ohead::where('item_id', $sourceItem->id)->get();
            foreach ($sourceOverheads as $oh) {
                $newOh = $oh->replicate();
                $newOh->item_id = $item->id;
                $newOh->created_by = auth()->id();
                $newOh->updated_by = auth()->id();
                $newOh->save();
            }
        });

        return response()->json(['message' => 'Skeleton copied successfully']);
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
            'sort_order' => $maxOrder + 1,
            'resource_description' => $request->input('resource_description'),
            'valid_from' => now(),
            'valid_to' => '2038-01-19',
            'factor' => 1,
            'is_locked' => 0,
            'is_canceled' => 0,
        ]);

        $skeleton->save();

        return response()->json(['message' => 'Resource added successfully', 'id' => $skeleton->id]);
    }

    public function updateResource(Request $request, Sor $sor, Item $item, Skeleton $skeleton)
    {
        if ($skeleton->item_code !== $item->item_code) {
            return response()->json(['message' => 'Resource does not belong to this item'], 403);
        }

        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $skeleton->update([
            'resource_id' => $request->input('resource_id'),
            'quantity' => $request->input('quantity'),
            'unit_id' => $request->input('unit_id'),
            'resource_description' => $request->input('resource_description'),
            'factor' => $request->input('factor'),
            'valid_from' => $request->input('valid_from'),
            'valid_to' => $request->input('valid_to'),
            'is_locked' => $request->input('is_locked', 0),
            'is_canceled' => $request->input('is_canceled', 0),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Resource updated successfully']);
    }

    public function reorderResources(Request $request, Sor $sor, Item $item)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:skeletons,id',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $index => $id) {
            Skeleton::where('id', $id)
                ->where('item_code', $item->item_code)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Resources reordered successfully']);
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
