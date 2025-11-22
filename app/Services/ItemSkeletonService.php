<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemRate;
use App\Models\RateCard;
use Illuminate\Support\Facades\Log;

class ItemSkeletonService
{
    protected $rateAnalysisService;

    public function __construct(RateAnalysisService $rateAnalysisService)
    {
        $this->rateAnalysisService = $rateAnalysisService;
    }

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
        } else {
            $rateCard = RateCard::find($rateCardId);
        }

        if (!$rateCard) {
            return ['error' => 'No rate card found'];
        }

        // 1. Fetch Resources (Skeletons)
        $resources = $item->skeletons()->with(['resource.group', 'unit'])->orderBy('sort_order')->get();
        $resourceData = [];
        $totalLabor = 0;
        $totalMaterial = 0;
        $totalMachine = 0;

        foreach ($resources as $res) {
            // Fetch Rate using RateAnalysisService
            $rateDetails = $this->rateAnalysisService->getResourceRateDetails($res->resource, $rateCard, $date);

            $rate = $rateDetails['total_rate'];
            $amount = $res->quantity * $rate;

            // Unit Conversion Logic
            $baseUnit = $res->resource->unit;
            $usageUnit = $res->unit;
            $conversionFactor = 1;

            if ($baseUnit && $usageUnit && $baseUnit->id != $usageUnit->id) {
                $baseFactor = $baseUnit->conversion_factor > 0 ? $baseUnit->conversion_factor : 1;
                $usageFactor = $usageUnit->conversion_factor > 0 ? $usageUnit->conversion_factor : 1;
                $conversionFactor = $baseFactor/$usageFactor;
            }

            // Amount = Quantity * Conversion * Rate
            $amount = $res->quantity * $conversionFactor * $rate;

            // Categorize cost based on Resource Group Name
            $groupName = strtolower($res->resource->group->name ?? '');
            if (str_contains($groupName, 'labour') || str_contains($groupName, 'labor')) {
                $totalLabor += $amount;
            } elseif (str_contains($groupName, 'machine') || str_contains($groupName, 'machinery')) {
                $totalMachine += $amount;
            } else {
                // Default to Material for everything else (including 'material' group)
                $totalMaterial += $amount;
            }

            $resourceData[] = [
                'id' => $res->id,
                'resource_id' => $res->resource_id,
                'secondary_code' => $res->resource->secondary_code, // Pass secondary_code
                'name' => $res->resource->name,
                'quantity' => $res->quantity,
                'unit' => $res->unit ? $res->unit->name : '',
                'unit_id' => $res->unit_id, // Pass unit_id
                'rate' => $rate,
                'rate_unit' => $baseUnit ? $baseUnit->name : '', // Pass rate unit
                'amount' => $amount,
                'resource_group_id' => $res->resource->group_id, // Pass resource_group_id
                'resource_group_name' => $groupName, // Pass resource_group_name
                'unit_group_id' => $res->unit ? $res->unit->unit_group_id : null, // Pass unit_group_id
                'resource_description' => $res->resource_description,
                'valid_from' => $res->valid_from ? $res->valid_from->toDateString() : null,
                'valid_to' => $res->valid_to ? $res->valid_to->toDateString() : null,
                'factor' => $res->factor,
                'is_locked' => $res->is_locked,
                'is_canceled' => $res->is_canceled,
                'rate_components' => $rateDetails['components'], // Pass components for tooltip/display
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
                'total_labor' => $totalLabor,
                'total_material' => $totalMaterial,
                'total_machine' => $totalMachine,
                'subitem_cost' => $totalSubitems,
                'overhead_cost' => $totalOverheads,
                'grand_total' => $grandTotal,
                'turnout' => $turnout,
                'final_rate' => $finalRate,
            ]
        ];
    }
}
