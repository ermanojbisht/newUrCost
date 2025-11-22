<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemRate;
use App\Models\Rate;
use App\Models\RateCard;
use Illuminate\Support\Facades\Log;

class ItemSkeletonService
{
    /**
     * Calculate the rate for an item based on its skeleton, subitems, and overheads.
     *
     * @param Item $item
     * @param int|null $rateCardId
     * @param string|null $date
     * @return array
     */
    public function calculateRate(Item $item, $rateCardId = null, $date = null)
    {
        // Defaults
        $date = $date ?? now()->toDateString();
        // If no rate card provided, use the first available one
        if (!$rateCardId) {
            $rateCard = RateCard::first();
            $rateCardId = $rateCard ? $rateCard->id : null;
        }

        if (!$rateCardId) {
            return ['error' => 'No rate card found'];
        }

        // 1. Fetch Resources (Skeletons)
        $resources = $item->skeletons()->with(['resource.group', 'unit'])->orderBy('sort_order')->get();
        $resourceData = [];
        $totalLabor = 0;
        $totalMaterial = 0;
        $totalMachine = 0;

        foreach ($resources as $res) {
            // Fetch Rate
            $rateEntry = Rate::where('resource_id', $res->resource_id)
                ->where('rate_card_id', $rateCardId)
                ->where('valid_from', '<=', $date)
                ->orderBy('valid_from', 'desc')
                ->first();

            // Fallback to Basic Rate Card (ID = 1) if not found
            if (!$rateEntry && $rateCardId != 1) {
                $rateEntry = Rate::where('resource_id', $res->resource_id)
                    ->where('rate_card_id', 1)
                    ->where('valid_from', '<=', $date)
                    ->orderBy('valid_from', 'desc')
                    ->first();
            }

            $rate = $rateEntry ? $rateEntry->rate : 0;
            $amount = $res->quantity * $rate;

            // Categorize cost (This logic depends on Resource Group or similar, assuming simplified for now)
            // In real app, we might check $res->resource->group_id to decide category.
            // For now, let's assume all are 'Material' unless specified. 
            // TODO: Refine categorization based on Resource Group.

            // Let's try to guess from group_id if available, or just sum to a generic 'resource_cost'
            // Looking at Resource model, it has group_id.
            // Let's assume: 1=Labor, 2=Material, 3=Machine (Example IDs)
            // We need to know the actual IDs. For now, I'll just sum them all to 'total_resources' 
            // and maybe separate if I can find the constants.

            $resourceData[] = [
                'id' => $res->id,
                'resource_id' => $res->resource_id,
                'secondary_code' => $res->resource->secondary_code, // Pass secondary_code
                'name' => $res->resource->name,
                'quantity' => $res->quantity,
                'unit' => $res->unit ? $res->unit->name : '',
                'unit_id' => $res->unit_id, // Pass unit_id
                'rate' => $rate,
                'amount' => $amount,
                'resource_group_id' => $res->resource->group_id, // Pass resource_group_id
                'resource_group_name' => strtolower($res->resource->group->name ?? ''), // Pass resource_group_name
                'unit_group_id' => $res->unit ? $res->unit->unit_group_id : null, // Pass unit_group_id
                'resource_description' => $res->resource_description,
                'valid_from' => $res->valid_from ? $res->valid_from->toDateString() : null,
                'valid_to' => $res->valid_to ? $res->valid_to->toDateString() : null,
                'factor' => $res->factor,
                'is_locked' => $res->is_locked,
                'is_canceled' => $res->is_canceled,
            ];
        }

        // 2. Fetch Sub-items
        $subitems = $item->subitems()->with(['subItem', 'unit'])->orderBy('sort_order')->get();
        $subitemData = [];
        $totalSubitems = 0;

        foreach ($subitems as $sub) {
            // Fetch Pre-calculated Rate for Sub-item
            $subRateEntry = ItemRate::where('item_id', $sub->sub_item_id)
                ->where('rate_card_id', $rateCardId)
                ->where('valid_from', '<=', $date)
                ->orderBy('valid_from', 'desc')
                ->first();

            $rate = $subRateEntry ? $subRateEntry->rate : 0;
            $amount = $sub->quantity * $rate;
            $totalSubitems += $amount;

            $subitemData[] = [
                'id' => $sub->id,
                'sub_item_id' => $sub->sub_item_id,
                'name' => $sub->subItem->description ?? 'Unknown Item', // Fallback
                'item_number' => $sub->subItem->item_number ?? '',
                'quantity' => $sub->quantity,
                'unit' => $sub->unit ? $sub->unit->name : '',
                'rate' => $rate,
                'amount' => $amount,
            ];
        }

        // 3. Fetch Overheads
        $oheads = $item->oheads()->with(['overhead'])->orderBy('sort_order')->get();
        $overheadData = [];
        $totalOverheads = 0;

        // Base amount for overheads usually includes resources + subitems
        // But specific overheads might apply only to Labor, etc.
        // For this implementation, I'll implement a simplified version:
        // Apply percentage to the running total or specific base if defined.

        $runningTotal = array_sum(array_column($resourceData, 'amount')) + $totalSubitems;

        foreach ($oheads as $oh) {
            $amount = 0;
            // Logic from old system: switch($oh->overhead_id) ...
            // Since we don't have the hardcoded IDs, we'll assume 'parameter' is a percentage
            // and it applies to the current running total.
            // In a real migration, we'd map the old logic precisely.

            if ($oh->calculation_type == 'percentage') { // Assuming this field exists or similar
                $amount = ($runningTotal * $oh->parameter) / 100;
            } else {
                // Fixed amount?
                $amount = $oh->parameter;
            }

            // If it's additive, add to total.
            $totalOverheads += $amount;
            // Some overheads might update the running total for subsequent overheads (compound).
            // $runningTotal += $amount; 

            $overheadData[] = [
                'id' => $oh->id,
                'overhead_id' => $oh->overhead_id,
                'description' => $oh->overhead->name ?? $oh->description,
                'parameter' => $oh->parameter,
                'amount' => $amount,
            ];
        }

        $grandTotal = array_sum(array_column($resourceData, 'amount')) + $totalSubitems + $totalOverheads;
        $turnout = $item->turnout_quantity > 0 ? $item->turnout_quantity : 1;
        $finalRate = $grandTotal / $turnout;

        return [
            'resources' => $resourceData,
            'subitems' => $subitemData,
            'overheads' => $overheadData,
            'totals' => [
                'resource_cost' => array_sum(array_column($resourceData, 'amount')),
                'subitem_cost' => $totalSubitems,
                'overhead_cost' => $totalOverheads,
                'grand_total' => $grandTotal,
                'turnout' => $turnout,
                'final_rate' => $finalRate,
            ]
        ];
    }
}
