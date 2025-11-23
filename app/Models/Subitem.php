<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Subitem extends Model
{
    use HasFactory;

    protected $table = 'subitems';

    protected $fillable = [
        'item_code',
        'sub_item_code',
        'quantity',
        'percentage',
        'based_on_id',
        'sort_order',
        'unit_id',
        'remarks',
        'valid_from',
        'valid_to',
        'factor',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_code', 'item_code');
    }

    public function subItem()
    {
        return $this->belongsTo(Item::class, 'sub_item_code', 'item_code');
    }

    public function basedOn()
    {
        return $this->belongsTo(Subitem::class, 'based_on_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate the subitem dependency tree for a given RA item.
     * This method replicates the functionality of the old system's subitemlvlFormation.
     *
     * @param int $raitemid The item_code of the Rate Analysis item.
     * @return void
     */
    public static function generateSubitemDependency(int $raitemid)
    {
        Log::debug("Starting subitem dependency generation for raitemid (item_code): {$raitemid}");

        try {
            DB::transaction(function () use ($raitemid) {
                // Step 1: Delete existing dependency records for the given main item code.
                SubitemDependency::where('item_id', $raitemid)->delete();
                Log::debug("Deleted existing dependencies for main item code: {$raitemid}");

                // Initialize position counter. In the old system, it started from 1000 and decremented.
                $pos = 1000;

                // Step 2: Recursively build the dependency tree.
                self::buildDependencyTree($raitemid, $raitemid, $pos, 1);

                // After building the tree, update the subitem count on the main item.
                $subitemCount = SubitemDependency::where('item_id', $raitemid)->where('quantity', '<>', 0)->count();
                Item::where('item_code', $raitemid)->update(['sub_item_count' => $subitemCount]);
                Log::debug("Updated sub_item_count for main item code {$raitemid} to {$subitemCount}");

            });
            Log::info("Successfully generated subitem dependency for raitemid (item_code): {$raitemid}");
        } catch (\Exception $e) {
            Log::error("Error generating subitem dependency for raitemid (item_code): {$raitemid}. Error: " . $e->getMessage());
            // Optionally re-throw the exception if you want the caller to handle it.
            // throw $e;
        }
    }

    /**
     * Recursive helper function to build the dependency tree.
     *
     * @param int $root_item_code The item_code of the root Item.
     * @param int $parent_item_code The item_code of the current parent Subitem.
     * @param int $pos The position counter (passed by reference).
     * @param int $level The current depth level of the recursion.
     */
    private static function buildDependencyTree(int $root_item_code, int $parent_item_code, int & $pos, int $level)
    {
        // Fetch all direct subitems for the current parent item_code.
        $subitems = self::where('item_id', $parent_item_code)->orderBy('sort_order')->get();

        // Get the parent Item model to fetch its turnout_quantity
        $parentItem = Item::where('item_code', $parent_item_code)->first();
        $parentTurnoutQuantity = $parentItem->turnout_quantity ?? 1;

        foreach ($subitems as $subitem) {
            $currentPos = $pos--;
            Log::debug("Processing subitem: {$subitem->sub_item_id} for parent item_code: {$parent_item_code} at level: {$level}, pos: {$currentPos}");

            // Create the dependency record.
            SubitemDependency::create([
                'item_id' => $root_item_code,
                'sub_item_id' => $subitem->sub_item_id,
                'level' => $level,
                'position' => $currentPos,
                'quantity' => $subitem->quantity,
                'unit_id' => $subitem->unit_id,
                'parent_turnout_quantity' => $parentTurnoutQuantity,
                'parent_carries_overhead' => $subitem->based_on_id, // Mapping from old system
                'parent_overhead_applicability' => $subitem->percentage, // Mapping from old system
                'valid_from' => $subitem->valid_from,
                'valid_to' => $subitem->valid_to,
            ]);

            // Update the subitem level on the item itself.
            Item::where('item_code', $subitem->sub_item_id)->update(['sub_item_level' => $level]);
            Log::debug("Updated sub_item_level for item code {$subitem->sub_item_id} to {$level}");

            // Recurse for the children of the current subitem.
            self::buildDependencyTree($root_item_code, $subitem->sub_item_id, $pos, $level + 1);
        }
    }

    /**
     * Update the sub_item_level for a single item based on its max level in the dependency tree.
     *
     * @param int $item_code
     * @return void
     */
    public static function updateSubitemLevelInfo(int $item_code)
    {
        if ($item_code) {
            $max_level = SubitemDependency::where('sub_item_id', $item_code)->max('level');
            
            if (is_null($max_level)) {
                $max_level = 0;
            }

            Item::where('item_code', $item_code)->update(['sub_item_level' => $max_level]);
            Log::debug("Updated sub_item_level for item_code {$item_code} to {$max_level}");
        }
    }

    /**
     * Update sub_item_level for a group of items, either by SOR or a single RA item.
     *
     * @param int|null $sor_id
     * @param int|null $item_code
     * @return void
     */
    public static function updateSubitemLevelInfoForGroup(int $sor_id = null, int $item_code = null)
    {
        $subitems_query = self::query();

        if ($item_code) {
            $subitems_query->where('item_id', $item_code);
        } elseif ($sor_id) {
            // This is a potentially destructive operation, as it resets levels for the whole SOR.
            // Replicating old system's logic.
            Log::warning("Resetting sub_item_level to 0 for all items in sor_id: {$sor_id}");
            Item::where('sor_id', $sor_id)->update(['sub_item_level' => 0]);

            $subitems_query->whereHas('item', function ($q) use ($sor_id) {
                $q->where('sor_id', $sor_id);
            });
        } else {
            // No parameters provided, do nothing.
            return;
        }

        $distinct_subitems = $subitems_query->distinct()->pluck('sub_item_id');

        foreach ($distinct_subitems as $sub_item_id) {
            self::updateSubitemLevelInfo($sub_item_id);
        }
    }
}
