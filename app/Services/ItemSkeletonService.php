<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemRate;
use App\Models\RateCard;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Support\Facades\Log;

class ItemSkeletonService
{
    protected $rateAnalysisService;
    protected $overheadService;
    protected $unitService;

    public function __construct(RateAnalysisService $rateAnalysisService, OverheadService $overheadService, UnitService $unitService)
    {
        $this->rateAnalysisService = $rateAnalysisService;
        $this->overheadService = $overheadService;
        $this->unitService = $unitService;
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
        $totalCartage = 0;
        $totalMiscellaneous = 0;

        foreach ($resources as $res) {
            // Fetch Rate using RateAnalysisService
            $rateDetails = $this->rateAnalysisService->getResourceRateDetails($res->resource, $rateCard, $date);

            $rate = $rateDetails['total_rate'];
            $unit_id = $rateDetails['unit_id'];

            // Unit Conversion Logic
            $rateUnit = Unit::find($unit_id);//$res->resource->unit;
            $qtyUnit = $res->unit;

            $conversionFactor = $this->unitService->getConversionFactor( $qtyUnit, $rateUnit );//now qty can be coverted to rate unit

            // Amount = Quantity * Conversion * Rate
            $amount = $res->quantity * $conversionFactor * $rate;

            // Categorize cost based on Resource Group Name
            $resource_group_id=$res->resource->resource_group_id;
            $groupName = $res->resource->group->name ?? '';

            switch ($resource_group_id) {
                case 1: //Labour Group
                    $totalLabor += $amount;
                    break;
                case 2: //Machine Group
                    $totalMachine += $amount;
                    break;
                case 3: //Material Group
                    $totalMaterial += $amount;
                    break;
                case 4: //Carriage Group
                    $totalCartage += $amount;
                    break;

                default://Miscellaneous Group
                    $totalMiscellaneous += $amount;
                    break;
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
                'rate_unit' => $rateUnit ? $rateUnit->name : '', // Pass rate unit
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
            //Log::info("sub = ".print_r($sub->toArray(),true));
            // Fetch Pre-calculated Rate for Sub-item
            $subRateEntry = ItemRate::where('item_code', $sub->sub_item_code)
                ->where('rate_card_id', $rateCardId)
                ->where('valid_from', '<=', $date)
                ->orderBy('valid_from', 'desc')
                ->first();

            $rate = $subRateEntry ? $subRateEntry->rate : 0;
            $amount = $sub->quantity * $rate;
            $totalSubitems += $amount;

            $subitemData[] = [
                'id' => $sub->id,
                'sub_item_code' => $sub->sub_item_code,
                'sub_item_id' => $sub->subItem->id ?? null, // Add item ID for navigation
                'name' => $sub->subItem->description ?? 'Unknown Item', // Fallback
                'item_number' => $sub->subItem->item_number ?? '',
                'quantity' => $sub->quantity,
                'unit' => $sub->unit ? $sub->unit->name : '',
                'rate' => $rate,
                'amount' => $amount,
                'is_oh_applicable' => $sub->is_oh_applicable,
                'is_overhead' => $sub->is_overhead,
                'factor' => $sub->factor,
                'unit_id' => $sub->unit_id,
                'valid_to' => $sub->valid_to ? $sub->valid_to->toDateString() : null,
                'remarks' => $sub->remarks,
            ];
        }

        // 3. Fetch Overheads
        $overHeadRules = $item->overheads()->with(['overhead'])->get(); //already sorted in relation
        $overheadData = [];
        $totalOverheads = 0;

        $runningTotal = array_sum(array_column($resourceData, 'amount')) + $totalSubitems;
        $costMapOfResources = collect($resourceData)->pluck('amount', 'resource_id');


        foreach ($overHeadRules as $rule) {

            $calculationResult = $this->overheadService->calculateOverheadAmount($rule, [
                'totalLabor' => $totalLabor,
                'totalMachine' => $totalMachine,
                'totalMaterial' => $totalMaterial,
                'totalCartage' => $totalCartage,
                'subItemsWithOh' => $totalSubitems,
                'resourceCosts' => $costMapOfResources ?? [],
                'runningTotal' => $runningTotal,
                'totalOverhead' => $totalOverheads
            ]);

            $overheadAmount = $calculationResult['amount'];
            $baseAmount = $calculationResult['base'];

            // Only add to the running total for subsequent calculations if the flag is set
            if ($rule->allow_further_overhead) {
                $totalOverheads += $overheadAmount;
            }

            $overheadData[] = [
                'id' => $rule->id,
                'overhead_id' => $rule->overhead_id,
                'overhead_name' => $rule->overhead->description ?? 'Unknown', // Pass master description
                'description' => $this->overheadService->formatOverheadDescription($rule, $baseAmount),
                'raw_description' => $rule->description, // Pass raw description for editing
                'parameter' => round($rule->parameter * 100, 2),
                'raw_parameter' => $rule->parameter, // Pass raw parameter for editing
                'calculation_type' => $rule->calculation_type, // Pass calculation type for editing
                'applicable_items' => $rule->applicable_items, // Pass applicable items for editing
                'allow_further_overhead' => $rule->allow_further_overhead, // Pass flag for editing
                'amount' => $overheadAmount,
            ];
        }



        $totalOverheads= array_sum(array_column($overheadData, 'amount'));

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
                'total_cartage' => $totalCartage,
                'total_miscellaneous' => $totalMiscellaneous,
                'subitem_cost' => $totalSubitems,
                'overhead_cost' => $totalOverheads,
                'grand_total' => $grandTotal,
                'turnout' => $turnout,
                'final_rate' => $finalRate,
            ]
        ];
    }

}
