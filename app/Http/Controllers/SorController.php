<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\RateCard;
use App\Models\Sor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

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
    public function show(Sor $sor)
    {
        $items = $sor->items()->paginate(20);
        $ratecards = Ratecard::all();

        return view('pages.sors.show', compact('sor', 'items', 'ratecards'));
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
            'item_type' => 'required|string|in:chapter,item',
        ]);

        $parent = $request->input('parent_id') ? Item::find($request->input('parent_id')) : null;

        $item = DB::transaction(function () use ($request, $sor, $parent) {
            $item = new Item([
                'sor_id' => $sor->id,
                'name' => $request->input('text'),
                'item_type' => $request->input('item_type'),
                'item_code' => 'TEMP-' . uniqid(), // Temporary item code
            ]);

            if ($parent) {
                $item->appendToNode($parent)->save();
            } else {
                $item->save();
            }
            return $item;
        });

        return response()->json(['id' => $item->id, 'text' => $item->name, 'type' => $item->item_type]);
    }

    public function updateNode(Request $request, Sor $sor, Item $item)
    {
        $this->validate($request, [
            'text' => 'required|string|max:255',
        ]);

        $item->update(['name' => $request->input('text')]);

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

    public function getDataTableData(Sor $sor, Request $request)
    {
        if ($request->ajax()) {
            $data = $sor->items()->select('id', 'item_code', 'name', 'item_type', 'unit_id')->with('unit');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('unit_name', function(Item $item) {
                    return $item->unit ? $item->unit->name : 'N/A';
                })
                ->rawColumns(['unit_name'])
                ->make(true);
        }
    }

    public function dataTable(Sor $sor)
    {
        return view('sors.datatable', compact('sor'));
    }
}
