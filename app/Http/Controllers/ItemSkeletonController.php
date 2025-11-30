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
use App\Helpers\FilterHelper;
use Log;

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
        // Retrieve from session or defaults
        $filters = FilterHelper::getRateFilters($request, $sor);

        $rateCardId = $filters['rate_card_id'];
        $effectiveDate = $filters['effective_date'];

        $rateCards = \App\Models\RateCard::all();
        $units = \App\Models\Unit::all();
        $resourceGroups = \App\Models\ResourceGroup::all();
        $overheadMasters = \App\Models\OverheadMaster::all();

        return view('sors.skeleton', compact('sor', 'item', 'rateCards', 'units', 'rateCardId', 'effectiveDate', 'resourceGroups', 'overheadMasters'));
    }

    public function showRaPage(Request $request, Sor $sor, Item $item)
    {
        // Handle filters from request and store in session
        // Retrieve from session or defaults
        $filters = FilterHelper::getRateFilters($request, $sor);

        $rateCardId = $filters['rate_card_id'];
        $effectiveDate = $filters['effective_date'];

        $rateCards = \App\Models\RateCard::all();
        
        // We don't need units, resourceGroups, overheadMasters for read-only view if they are only used for dropdowns in modals
        // But if they are used for display names, we might need them. 
        // Checking partials:
        // resources.blade.php: uses $item->skeletons (loaded via JS)
        // subitems.blade.php: uses $item->subitems (loaded via JS)
        // overheads.blade.php: uses $item->overheads (loaded via JS)
        // So mostly data is loaded via JS.
        // However, let's pass them just in case to avoid undefined variable errors if partials expect them.
        
        $units = \App\Models\Unit::all();
        $resourceGroups = \App\Models\ResourceGroup::all();
        $overheadMasters = \App\Models\OverheadMaster::all();

        return view('sors.ra', compact('sor', 'item', 'rateCards', 'units', 'rateCardId', 'effectiveDate', 'resourceGroups', 'overheadMasters'));
    }

    public function redirectToRa($item_code)
    {
        $item = Item::where('item_code', $item_code)->firstOrFail();
        return redirect()->route('sors.items.ra', ['sor' => $item->sor_id, 'item' => $item->id]);
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
            'sub_item_code' => 'required|exists:items,item_code',
            'quantity' => 'required|numeric|min:0',
            'factor' => 'nullable|numeric',
            'unit_id' => 'nullable|exists:units,id',
            'valid_to' => 'nullable|date',
            'is_oh_applicable' => 'boolean',
            'is_overhead' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        if($request->sub_item_code == $item->item_code){
             return response()->json(['message' => 'Sub-item can not be same item'], 403);
        }

        // Default unit from sub-item if not provided
        $unitId = $request->input('unit_id');
        if (!$unitId) {
            $subItem = Item::where('item_code', $request->input('sub_item_code'))->first();
            $unitId = $subItem ? $subItem->unit_id : null;
        }

        $subitem = Subitem::create([
            'item_code' => $item->item_code,
            'sub_item_code' => $request->input('sub_item_code'),
            'quantity' => $request->input('quantity'),
            'factor' => $request->input('factor', 1),
            'unit_id' => $unitId,
            'sort_order' => $item->subitems()->max('sort_order') ?? 0 + 1, // Recalculate max order here
            'valid_from' => now(),
            'valid_to' => $request->input('valid_to', '2038-01-19'),
            'is_oh_applicable' => $request->input('is_oh_applicable', 0),
            'is_overhead' => $request->input('is_overhead', 1),
            'remarks' => $request->input('remarks'),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Sub-item added successfully', 'id' => $subitem->id]);
    }

    public function updateSubitem(Request $request, Sor $sor, Item $item, Subitem $subitem)
    {

        if ($subitem->item_code != $item->item_code) {
            return response()->json(['message' => 'Sub-item does not belong to this item'], 403);
        }

        $request->validate([
            'sub_item_code' => 'required|exists:items,item_code',
            'quantity' => 'required|numeric|min:0',
            'factor' => 'nullable|numeric',
            'unit_id' => 'nullable|exists:units,id',
            'valid_to' => 'nullable|date',
            'is_oh_applicable' => 'boolean',
            'is_overhead' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        if($request->sub_item_code == $item->item_code){
            return response()->json(['message' => 'Sub-item can not be same item'], 403);
        }

        //mkb pending: check sor_id should be same for subitem and item

        $subitem->update([
            'sub_item_code' => $request->input('sub_item_code'),
            'quantity' => $request->input('quantity'),
            'factor' => $request->input('factor', 1),
            'unit_id' => $request->input('unit_id'),
            'valid_to' => $request->input('valid_to'),
            'is_oh_applicable' => $request->input('is_oh_applicable', 0),
            'is_overhead' => $request->input('is_overhead', 1),
            'remarks' => $request->input('remarks'),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Sub-item updated successfully']);
    }

    public function reorderSubitems(Request $request, Sor $sor, Item $item)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:subitems,id',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $index => $id) {
            Subitem::where('id', $id)
                ->where('item_code', $item->item_code)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Sub-items reordered successfully']);
    }

    public function removeSubitem(Sor $sor, Item $item, Subitem $subitem)
    {
        // Check if subitem belongs to item (via item_code)
        if ($subitem->item_code != $item->item_code) {
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
            'calculation_type' => 'required|integer',
            'parameter' => 'required|numeric',
            'description' => 'nullable|string',
            'applicable_items' => 'nullable|string',
            'allow_further_overhead' => 'boolean',
        ]);

        $maxOrder = $item->overheads()->max('sort_order') ?? 0;

        $parameter = $request->input('parameter');
        if ($request->input('calculation_type') != 0) { // 0 is Lumpsum
             // If not lumpsum, assume percentage and convert to decimal if > 1 (assuming user enters 10 for 10%)
             // Or strictly follow user instruction: "if i m putting value 10 for percent then it's saving 10 instead of 0.1 should be saved"
             // So we divide by 100.
             $parameter = $parameter / 100;
        }

        $ohead = new Ohead([
            'item_id' => $item->item_code,
            'overhead_id' => $request->input('overhead_id'),
            'calculation_type' => $request->input('calculation_type'),
            'parameter' => $parameter,
            'description' => $request->input('description'),
            'applicable_items' => $request->input('applicable_items'),
            'allow_further_overhead' => $request->input('allow_further_overhead', 0),
            'sort_order' => $maxOrder + 1,
            'valid_from' => now(),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $ohead->save();

        return response()->json(['message' => 'Overhead added successfully', 'id' => $ohead->id]);
    }

    public function updateOverhead(Request $request, Sor $sor, Item $item, Ohead $ohead)
    {
        // Fix: Check against item_code, not id
        if ($ohead->item_id !== $item->item_code) {
            return response()->json(['message' => 'Overhead does not belong to this item'], 403);
        }

        $request->validate([
            'overhead_id' => 'required|exists:overhead_masters,id',
            'calculation_type' => 'required|integer',
            'parameter' => 'required|numeric',
            'description' => 'nullable|string',
            'applicable_items' => 'nullable|string',
            'allow_further_overhead' => 'boolean',
        ]);

        $parameter = $request->input('parameter');
        if ($request->input('calculation_type') != 0) {
             $parameter = $parameter / 100;
        }

        $ohead->update([
            'overhead_id' => $request->input('overhead_id'),
            'calculation_type' => $request->input('calculation_type'),
            'parameter' => $parameter,
            'description' => $request->input('description'),
            'applicable_items' => $request->input('applicable_items'),
            'allow_further_overhead' => $request->input('allow_further_overhead', 0),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Overhead updated successfully']);
    }

    public function reorderOverheads(Request $request, Sor $sor, Item $item)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:oheads,id',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $index => $id) {
            Ohead::where('id', $id)
                ->where('item_id', $item->item_code) // Fix: Use item_code
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Overheads reordered successfully']);
    }

    public function removeOverhead(Sor $sor, Item $item, Ohead $ohead)
    {
        // Fix: Check against item_code, not id
        if ($ohead->item_id !== $item->item_code) {
            return response()->json(['message' => 'Overhead does not belong to this item'], 403);
        }
        $ohead->delete();
        return response()->json(['message' => 'Overhead removed successfully']);
    }
}
