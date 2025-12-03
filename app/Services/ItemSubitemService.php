<?php

namespace App\Services;

use App\Models\Item;
use App\Models\SubitemDependency;
use Illuminate\Support\Facades\DB;
use Log;

class ItemSubitemService
{
    /**
     * Get all sub-items for the given parent item_codes.
     *
     * @param string|array $itemCodes
     * @param bool $includeParents If true, include parent items with level = 0
     * @return \Illuminate\Support\Collection
     */
    public function getSubitems($itemCodes, $includeParents = true)
    {
        $itemCodes = (array) $itemCodes;

        // Fetch all sub-items based on dependency table
        $subitems = Item::join('subitem_dependencies as deps', 'items.item_code', '=', 'deps.sub_item_code')
            ->select( 'items.*', 'deps.level as level' )
            ->whereIn('deps.item_code', $itemCodes)
            ->orderBy('level', 'desc')
            ->get();

        // Optionally include the parent items themselves
        if ($includeParents) {
            $parents = Item::whereIn('item_code', $itemCodes)
                ->select('items.*', DB::raw('0 as level'))
                ->get();

            return $parents->merge($subitems)->sortByDesc('level')->values()->toBase()->all();
        }

        return $subitems->toBase()->all();
    }

    /**
     * Get all sub-items for the given parent item_codes.
     *
     * @param string|array $itemCodes
     * @param bool $includeParents If true, include parent items with level = 0
     * @return \Illuminate\Support\Collection
     */
    public function getSubitemsWithParentCode($itemCodes, $includeParents = false)
    {
        $itemCodes = (array) $itemCodes;

        // Fetch all sub-items based on dependency table
        $subitems = Item::join('subitem_dependencies as deps', 'items.item_code', '=', 'deps.sub_item_code')
            ->select(
                'items.*',
                'deps.level as level',
                'deps.item_code as parent_item_code'
            )
            ->whereIn('deps.item_code', $itemCodes)
            ->orderBy('level', 'desc')
            ->get();

        // Optionally include the parent items themselves
        if ($includeParents) {
            $parents = Item::whereIn('item_code', $itemCodes)
                ->select('items.*', DB::raw('0 as level'), DB::raw('NULL as parent_item_code'))
                ->get();

            return $parents->merge($subitems)->sortByDesc('level')->values();
        }
        return $subitems;
    }
}
