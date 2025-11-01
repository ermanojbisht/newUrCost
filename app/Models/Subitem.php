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
        'item_id',
        'sub_item_id',
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
        return $this->belongsTo(Item::class, 'item_id', 'item_code');
    }

    public function subItem()
    {
        return $this->belongsTo(Item::class, 'sub_item_id', 'item_code');
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
     * @param int $raitemid The ID of the Rate Analysis item.
     * @return void
     */
    public static function generateSubitemDependency(int $raitemid)
    {
        Log::debug("Starting subitem dependency generation for raitemid (item_code): {$raitemid}");

        try {
            DB::transaction(function () use ($raitemid) {
                // Get the actual Item model for the given raitemid (which is an item_code)
                $mainItem = Item::where('item_code', $raitemid)->firstOrFail();
                $mainItemId = $mainItem->id;

                // Step 1: Delete existing dependency records for the given main item ID.
                SubitemDependency::where('item_id', $raitemid)->delete();
                Log::debug("Deleted existing dependencies for main item code: {$raitemid}");

                // Initialize position counter. In the old system, it started from 1000 and decremented.
                $pos = 1000;

                // Step 2: Recursively build the dependency tree.
                // Pass the main item's ID and the current parent's item_code
                self::buildDependencyTree($mainItemId, $raitemid, $pos);

                // After building the tree, update the subitem count on the main item.
                $subitemCount = SubitemDependency::where('item_id', $raitemid)->where('quantity', '<>', 0)->count();
                $mainItem->update(['sub_item_count' => $subitemCount]);
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
     * @param int $mainItemId The ID of the root Item (from items.id).
     * @param int $currentParentItemCode The item_code of the current parent Subitem (from subitems.item_id).
     * @param int $pos The position counter (passed by reference).
     * @param int $level The current depth level of the recursion.
     */
    private static function buildDependencyTree(int $mainItemId, int $currentParentItemCode, int &$pos, int $level = 1)
    {
        // Fetch all direct subitems for the current parent item_code.
        $subitems = self::where('item_id', $currentParentItemCode)->orderBy('sort_order')->get();

        // Get the parent Item model to fetch its turnout_quantity
        $parentItem = Item::where('item_code', $currentParentItemCode)->first();
        $parentTurnoutQuantity = $parentItem->turnout_quantity ?? 1;

        foreach ($subitems as $subitem) {
            $currentPos = $pos--;
            Log::debug("Processing subitem: {$subitem->sub_item_id} for parent item_code: {$currentParentItemCode} at level: {$level}, pos: {$currentPos}");

            // Get the actual Item ID for the sub_item_id (which is an item_code)
            $childItem = Item::where('item_code', $subitem->sub_item_id)->first();
            $childItemId = $childItem->id ?? null; // Handle case where sub_item_id might not exist in items table

            if (is_null($childItemId)) {
                Log::warning("Subitem with item_code {$subitem->sub_item_id} not found in items table. Skipping dependency creation.");
                continue;
            }

            // Create the dependency record.
            SubitemDependency::create([
                'item_id' => $currentParentItemCode,
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
            // Use the childItem's ID for updating
            Item::where('id', $childItemId)->update(['sub_item_level' => $level]);
            Log::debug("Updated sub_item_level for item ID {$childItemId} to {$level}");

            // Recurse for the children of the current subitem.
            // Pass the main item's ID and the current subitem's item_code
            self::buildDependencyTree($mainItemId, $subitem->sub_item_id, $pos, $level + 1);
        }
    }
}
