<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\RateCard;
use App\Models\Sor;
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

        $currentItem=$item;

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
            'name' => 'required|string|max:255|unique:sors,name,'.$sor->id,
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
        $this->validate($request, [
            'parent_id' => 'nullable|exists:items,id',
            'text' => 'required|string|max:255',
            'item_type' => 'required|string|in:chapter,subchapter,item',
        ]);

        $parent = $request->input('parent_id') ? Item::find($request->input('parent_id')) : null;

        if ($parent && $parent->item_type == 3) {
            return response()->json(['status' => 'error', 'message' => 'An item cannot be a child of another item.'], 422);
        }

        if ($parent && $parent->item_type == 3 && $request->input('item_type') == 'chapter') {
            return response()->json(['status' => 'error', 'message' => 'A chapter cannot be a child of an item.'], 422);
        }

        $item = DB::transaction(function () use ($request, $sor, $parent) {
            $item_type_map = [
                'chapter' => 1,
                'subchapter' => 2,
                'item' => 3,
            ];

            $item = new Item([
                'sor_id' => $sor->id,
                'name' => $request->input('text'),
                'item_type' => $item_type_map[$request->input('item_type')],
                'item_code' => 'TEMP-' . uniqid(), // Temporary item code
            ]);

            if ($parent) {
                $item->appendToNode($parent)->save();
            } else {
                $item->save();
            }
            return $item;
        });

        return response()->json(['id' => $item->id, 'text' => $item->name, 'type' => $request->input('item_type')]);
    }

    public function updateNode(Request $request, Sor $sor, Item $item)
    {
        Log::info('Update node request', $request->all());
        $request->validate([
            'text' => 'required|string|max:255',
            'item_number' => 'required|string|max:255|unique:items,item_number,'.$item->id,
        ]);

        $item->update([
            'description' => $request->input('text'),
            'item_number' => $request->input('item_number'),
        ]);
        Log::info('item updated', $item->toArray());

        return response()->json(['status' => 'success']);
    }

    public function deleteNode(Sor $sor, Item $item)
    {
        $item->delete();

        return response()->json(['status' => 'success']);
    }

    public function moveNode(Request $request, Sor $sor)
    {
        $this->validate($request, [
            'id' => 'required|exists:items,id',
            'parent' => 'nullable|exists:items,id',
            'position' => 'required|integer',
        ]);

        $item = Item::find($request->input('id'));
        $parent = $request->input('parent') !== '#' ? Item::find($request->input('parent')) : null;
        $position = $request->input('position');

        DB::transaction(function () use ($item, $parent, $position) {
            if ($parent) {
                $item->appendToNode($parent)->save();
            } else {
                $item->makeRoot()->save();
            }

            $item->update(['nested_list_order' => $position]);
        });

        return response()->json(['status' => 'success']);
    }

    protected function formatNode(Item $item)
    {
        $data = [
            'id' => $item->id,
            'text' => $item->item_code . ' - ' . $item->name,
            'children' => [],
            'type' => $item->item_type, // 'chapter' or 'item'
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
                ->addColumn('unit_name', function(Item $item) {
                    return $item->unit ? $item->unit->name : 'N/A';
                })
                ->addColumn('price', function(Item $item) use ($rateCard, $effectiveDate) {
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
}
