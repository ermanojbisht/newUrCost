<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\RateCard;
use App\Models\Sor;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Log;

class SorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Sor::class, 'sor');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sors = Sor::paginate(10);
        return view('pages.sors.index', compact('sors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.sors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sors',
            'is_locked' => 'boolean',
            'display_details' => 'nullable|string',
            'filename' => 'nullable|string',
            'short_name' => 'nullable|string|max:255',
        ]);

        $sor = Sor::create($request->all());

        return redirect()->route('sors.index')->with('success', 'SOR created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Sor $sor, Item $item = null)
    {
        if ($item && $item->sor_id !== $sor->id) {
            abort(404);
        }

        $rateCardId = $request->session()->get('rate_card_id', config('urcost.default_rate_cards.' . $sor->id, 1));
        $effectiveDate = $request->session()->get('effective_date', now()->toDateString());

        $rateCard = RateCard::find($rateCardId);

        if ($item) {
            $items = $item->children()->orderBy('order_in_parent')->get();
        } else {
            $rootItem = $sor->items()->where('id', $sor->id)->first();
            $items = $rootItem ? $rootItem->children()->orderBy('order_in_parent')->get() : collect();
        }

        foreach ($items as $i) {
            if ($i->item_type != 1 && $i->item_type != 2) {
                $i->rate = $i->getRateFor($rateCard, $effectiveDate)->rate ?? null;
            }
        }

        $rateCards = RateCard::all();

        $breadcrumbs = [
            ['label' => 'Home', 'route' => route('dashboard')],
            ['label' => $sor->name, 'route' => route('sors.show', $sor)],
        ];

        if ($item) {
            foreach ($item->ancestors->where('sor_id', $sor->id) as $ancestor) {
                $breadcrumbs[] = ['label' => $ancestor->item_number, 'route' => route('sors.show', [$sor, $ancestor])];
            }
            $breadcrumbs[] = ['label' => $item->item_number, 'route' => route('sors.show', [$sor, $item])];
        }

        $currentItem = $item;

        return view('sors.show', compact('sor', 'items', 'rateCards', 'breadcrumbs', 'rateCardId', 'effectiveDate', 'currentItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sor $sor)
    {
        return view('pages.sors.edit', compact('sor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sor $sor)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sors,name,' . $sor->id,
            'is_locked' => 'boolean',
            'display_details' => 'nullable|string',
            'filename' => 'nullable|string',
            'short_name' => 'nullable|string|max:255',
        ]);

        $sor->update($request->all());

        return redirect()->route('sors.index')->with('success', 'SOR updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sor $sor)
    {
        $sor->delete();

        return redirect()->route('sors.index')->with('success', 'SOR deleted successfully!');
    }

    public function sorCards()
    {
        $sors = Sor::all();
        return view('sors.index', compact('sors'));
    }

    public function admin(Sor $sor)
    {
        return view('sors.admin', compact('sor'));
    }

    public function getTreeData(Sor $sor)
    {
        $items = $sor->items()->defaultOrder()->get()->toTree();

        $data = [];
        foreach ($items as $item) {
            $data[] = $this->formatNode($item);
        }

        return response()->json($data);
    }

    public function createNode(Request $request, Sor $sor)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:items,id',
            'description' => 'required|string|max:255',
            'item_number' => 'nullable|string|max:255',
            'item_type' => 'required|integer|in:1,2,3', // 1: Chapter, 2: Sub-chapter, 3: Item
        ]);

        $parent = null;
        if ($request->input('parent_id')) {
            $parent = Item::find($request->input('parent_id'));
            if (!$parent) {
                return response()->json(['status' => 'error', 'message' => 'Parent node not found.'], 404);
            }
        } else {
            // If no parent_id is provided, the parent is the SOR itself (root of the SOR's items)
            $parent = Item::where('sor_id', $sor->id)->whereNull('parent_id')->first();
            if (!$parent) {
                // This should ideally not happen if SORs are properly initialized with a root item
                return response()->json(['status' => 'error', 'message' => 'Root SOR item not found.'], 500);
            }
        }

        // Validate hierarchy constraints
        if ($parent->item_type == 3) { // An item cannot have children
            return response()->json(['status' => 'error', 'message' => 'An item cannot be a parent to another node.'], 422);
        }

        $itemType = $request->input('item_type');
        $itemCode = ($itemType == 3) ? $this->generateNextItemCode($sor->id) : '0';

        // Determine order_in_parent
        $maxOrder = $parent->children()->max('order_in_parent');
        $orderInParent = $maxOrder !== null ? $maxOrder + 1 : 1;

        $item = DB::transaction(function () use ($request, $sor, $parent, $itemType, $itemCode, $orderInParent) {
            $newItem = new Item([
                'sor_id' => $sor->id,
                'description' => $request->input('description'),
                'item_number' => $request->input('item_number'),
                'item_type' => $itemType,
                'item_code' => $itemCode,
                'order_in_parent' => $orderInParent,
            ]);

            $newItem->appendToNode($parent)->save();
            return $newItem;
        });

        // Re-fetch the item to ensure observer-generated 'name' is included
        $item = Item::find($item->id);

        return response()->json([
            'id' => $item->id,
            'text' => $item->item_number . ' ' . $item->description, // jsTree expects 'text'
            'type' => $item->item_type,
            'parent' => $item->parent_id,
            'item_code' => $item->item_code,
            'item_number' => $item->item_number,
            'description' => $item->description,
            'name' => $item->name,
            'order_in_parent' => $item->order_in_parent,
        ]);
    }

    public function updateNode(Request $request, Sor $sor, Item $item)
    {
        Log::info('Update node request', $request->all());
        $request->validate([
            'description' => 'required|string|max:255',
            'item_number' => 'nullable|string|max:255',
        ]);

        $item->update([
            'description' => $request->input('description'),
            'item_number' => $request->input('item_number'),
        ]);
        Log::info('item updated', $item->toArray());

        return response()->json(['status' => 'success']);
    }

    public function deleteNode(Sor $sor, Item $item)
    {
        // Ensure the item belongs to the current SOR
        if ($item->sor_id !== $sor->id) {
            return response()->json(['status' => 'error', 'message' => 'Item does not belong to this SOR.'], 403);
        }

        // Apply Deletion Rules (from items_table_structure.md):
        // 1. Chapter/Sub-chapter Deletion: Allowed only if it has no child nodes.
        //    If it has sub-chapters, deletion should prompt a confirmation warning (handled by frontend).
        //    If it contains items, deletion is not allowed unless items are first removed.
        if (($item->item_type === 1 || $item->item_type === 2) && $item->children()->count() > 0) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete a chapter/sub-chapter that contains child nodes. Please remove children first.'], 409);
        }

        // 2. Item Deletion: Allowed only if not used in other dependent tables.
        //    For now, assuming no direct dependencies are set up in the Item model for sub_item or dependency tables.
        //    If such relationships exist, checks would need to be added here.
        //    Example: if ($item->subItems()->count() > 0) { ... }

        DB::transaction(function () use ($item) {
            $item->delete();
        });

        return response()->json(['status' => 'success', 'message' => 'Node deleted successfully.']);
    }

    public function moveNode(Request $request, Sor $sor)
    {
        $request->validate([
            'id' => 'required|exists:items,id',
            'parent' => 'nullable|string',
            'position' => 'required|integer',
        ]);

        $item = Item::find($request->input('id'));
        if (!$item || $item->sor_id !== $sor->id) {
            return response()->json(['status' => 'error', 'message' => 'Item not found or does not belong to this SOR.'], 404);
        }

        $newParentId = $request->input('parent');
        $position = $request->input('position');

        // Validation: Check new parent before transaction
        if ($newParentId !== '#') {
            $newParent = Item::find($newParentId);
            if (!$newParent || $newParent->sor_id !== $sor->id) {
                return response()->json(['status' => 'error', 'message' => 'New parent node not found or does not belong to this SOR.'], 404);
            }

            // Apply Hierarchy Rules: An item cannot have children
            if ($newParent->item_type == 3) {
                return response()->json(['status' => 'error', 'message' => 'An item cannot be a parent to another node.'], 422);
            }
        }

        DB::transaction(function () use ($item, $newParentId, $position, $sor) {
            $newParent = null;
            if ($newParentId !== '#') {
                $newParent = Item::find($newParentId);
            }

            if ($newParent) {
                // Get siblings of the new parent
                $siblings = $newParent->children()->orderBy('order_in_parent')->get();

                if ($position === 0) {
                    // Move to be the first child
                    $item->prependToNode($newParent)->save();
                } elseif ($position >= $siblings->count()) {
                    // Move to be the last child
                    $item->appendToNode($newParent)->save();
                } else {
                    // Move before a specific sibling
                    $targetSibling = $siblings->get($position);
                    if ($targetSibling) {
                        $item->beforeNode($targetSibling)->save();
                    } else {
                        // Fallback to append if target sibling not found (shouldn't happen with jstree)
                        $item->appendToNode($newParent)->save();
                    }
                }
            } else {
                // Moving to root (under the SOR's own root item)
                $sorRootItem = Item::where('sor_id', $sor->id)->whereNull('parent_id')->first();
                if (!$sorRootItem) {
                    return response()->json(['status' => 'error', 'message' => 'Root SOR item not found.'], 500);
                }
                $item->appendToNode($sorRootItem)->save();
            }

            // Re-calculate order_in_parent for affected siblings and the moved item
            // This is important because nestedset doesn't automatically manage a sequential order_in_parent
            if ($newParent) {
                $parentChildren = $newParent->children()->defaultOrder()->get();
            } else {
                $parentChildren = $sorRootItem->children()->defaultOrder()->get();
            }

            $parentChildren->each(function ($child, $index) {
                $child->update(['order_in_parent' => $index + 1]);
            });
        });

        return response()->json(['status' => 'success', 'message' => 'Node moved successfully.']);
    }

    protected function formatNode(Item $item)
    {
        $typeMap = [
            1 => 'chapter',
            2 => 'sub-chapter',
            3 => 'item',
        ];

        $data = [
            'id' => $item->id,
            'text' => $item->item_number . ' ' . $item->description, // jsTree expects 'text'
            'children' => [],
            'type' => $typeMap[$item->item_type] ?? 'file', // 'chapter', 'sub-chapter', or 'item'
        ];

        foreach ($item->children as $child) {
            $data['children'][] = $this->formatNode($child);
        }

        return $data;
    }

    public function getNode(Sor $sor, Item $item)
    {
        $rateCardId = session('rate_card_id', config('urcost.default_rate_cards.' . $sor->id, 1));
        $effectiveDate = session('effective_date', now()->toDateString());
        $rateCard = RateCard::find($rateCardId);

        return response()->json([
            'id' => $item->id,
            'item_number' => $item->item_number,
            'item_code' => $item->item_code,
            'name' => $item->name,
            'unit' => $item->unit ? $item->unit->name : 'N/A',
            'rate' => $item->getRateFor($rateCard, $effectiveDate)->rate ?? null,
        ]);
    }

    public function getDataTableData(Sor $sor, Request $request)
    {
        if ($request->ajax()) {
            $rateCardId = $request->session()->get('rate_card_id', config('urcost.default_rate_cards.' . $sor->id, 1));
            $effectiveDate = $request->session()->get('effective_date', now()->toDateString());
            $rateCard = RateCard::find($rateCardId);

            $data = $sor->items()->select('id', 'item_code', 'item_number', 'name', 'unit_id', 'item_type', 'lft')->with('unit');

            return Datatables::of($data)
                ->addIndexColumn()
                ->orderColumn('lft', '-lft $1') // Initial sort by lft
                ->setRowClass(function ($item) {
                    if ($item->item_type == 1) {
                        return 'text-blue-600 dark:text-blue-400';
                    } elseif ($item->item_type == 2) {
                        return 'text-green-600 dark:text-green-400';
                    }
                    return '';
                })
                ->addColumn('unit_name', function (Item $item) {
                    return $item->unit ? $item->unit->name : 'N/A';
                })
                ->addColumn('price', function (Item $item) use ($rateCard, $effectiveDate) {
                    if ($item->item_type != 1 && $item->item_type != 2) {
                        $rate = $item->getRateFor($rateCard, $effectiveDate)->rate ?? null;
                        return $rate !== null ? number_format($rate, 2) : '';
                    }
                    return '';
                })
                ->rawColumns(['unit_name', 'price'])
                ->make(true);
        }
    }

    public function dataTable(Sor $sor)
    {
        return view('sors.datatable', compact('sor'));
    }

    /**
     * Generate the next available item code for a given SOR.
     * Item codes are numeric and unique within an SOR for item_type = 3.
     * Chapters (item_type 1 and 2) always have item_code = 0.
     */
    private function generateNextItemCode(int $sorId): string
    {
        $maxItemCode = Item::where('sor_id', $sorId)
            ->where('item_type', 3) // Only consider actual items for code generation
            ->max('item_code');

        if ($maxItemCode) {
            // Increment the numeric part of the item code
            return (string) ((int) $maxItemCode + 1);
        }

        // Default starting item code if no items exist for this SOR
        return ($sorId * 1000000) + 1; // Example starting code, adjust as needed
    }

    public function getNodeDetails(Sor $sor, Item $item)
    {
        $units = Unit::all(['id', 'name', 'code']);
        return response()->json([
            'item' => $item,
            'units' => $units,
        ]);
    }

    public function updateNodeDetails(Request $request, Sor $sor, Item $item)
    {
        $request->validate([
            'item_number' => 'nullable|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'unit_id' => 'nullable|exists:units,id',
            'specification_code' => 'nullable|string|max:255',
            'specification_page_number' => 'nullable|string|max:255',
            'turnout_quantity' => 'nullable|numeric|min:0',
            'assumptions' => 'nullable|string',
            'footnotes' => 'nullable|string',
            'is_canceled' => 'boolean',
        ]);

        $item->update($request->all());

        return response()->json(['status' => 'success', 'message' => 'Item updated successfully.', 'item' => $item]);
    }
}
