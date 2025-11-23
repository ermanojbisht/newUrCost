<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class OverheadService
{

    // Define constants for calculation types to improve readability
    const CALCULATION_LUMPSUM = 0;
    const CALCULATION_ON_LABOR = 11;
    const CALCULATION_ON_MACHINE = 12;
    const CALCULATION_ON_MATERIAL = 13;
    const CALCULATION_ON_SUB_ITEMS = 19;
    const CALCULATION_ON_CARRIAGE = 20;
    const CALCULATION_ON_ALL_RESOURCES = 4;
    const CALCULATION_ON_SPECIFIC_RESOURCES = 16;
    const CALCULATION_ON_PREVIOUS_OVERHEADS = 7;
    const CALCULATION_ON_CUMULATIVE_TOTAL = 18;



    /**
     * Calculate the overhead amount based on the rule's calculation type
     */
    public function calculateOverheadAmount($rule, $costs)
    {
        //Log::info("Processing overhead rule: " . print_r($rule, true));
        $baseAmount = 0;

        switch ($rule->calculation_type) {
            case self::CALCULATION_LUMPSUM:
                $baseAmount = $rule->parameter;
                break;

            case self::CALCULATION_ON_LABOR:
                $baseAmount = $costs['totalLabor'];
                break;

            case self::CALCULATION_ON_MACHINE:
                $baseAmount = $costs['totalMachine'];
                break;

            case self::CALCULATION_ON_MATERIAL:
                $baseAmount = $costs['totalMaterial'];
                break;

            case self::CALCULATION_ON_SUB_ITEMS:
                $baseAmount = $costs['subItemsWithOh'];
                break;

            case self::CALCULATION_ON_CARRIAGE:
                $baseAmount = $costs['totalCartage'];
                break;

            case self::CALCULATION_ON_ALL_RESOURCES:
                $baseAmount = $costs['totalLabor'] + $costs['totalMachine'] +
                             $costs['totalMaterial'] + $costs['totalCartage'];
                break;

            case self::CALCULATION_ON_SPECIFIC_RESOURCES:
                $baseAmount = $this->calculateSpecificResourcesCost($rule, $costs['resourceCosts']);
                break;

            case self::CALCULATION_ON_PREVIOUS_OVERHEADS:
                $baseAmount = $costs['totalOverhead'];
                break;

            case self::CALCULATION_ON_CUMULATIVE_TOTAL:
                $baseAmount = $costs['runningTotal'] + $costs['totalOverhead'];
                break;

            default:
                Log::warning("Unknown calculation type: {$rule->calculation_type}");
                return ['amount' => 0, 'base' => 0];
        }

        $amount = $rule->parameter * $baseAmount;

        return ['amount' => $amount, 'base' => $baseAmount];
    }

    /**
     * Calculate the cost of specific resources for a rule
     */
    function calculateSpecificResourcesCost($rule, $resourceCosts) {
        $itemIds = explode(',', $rule->applicable_items);
        // Sum amounts for all applicable resource_ids
        return collect($itemIds)->sum(fn($id) => $resourceCosts[$id] ?? 0);
    }

    /**
     * Format the overhead description
     */
    function formatOverheadDescription($rule, $baseAmount) {
        $percentage = round($rule->parameter * 100, 2);
        $calculationType = $rule->calculation_type;

        return "{$rule->description} ({$calculationType})\n{$percentage}% over amount {$baseAmount}";
    }

}


/*foreach ($overHeadRules as $rule) {
            $ohAmount = 0;
            switch ($rule->calculation_type) {
                case 0:  // Lumpsum
                    $ohAmount = $rule->parameter;
                    $on=$ohAmount;
                    break;
                case 11: // On Labor
                    $ohAmount = $rule->parameter * $totalLabor;
                    $on=$totalLabor;
                    break;
                case 12: // On Machine
                    $ohAmount = $rule->parameter * $totalMachine;
                    $on=$totalMachine;
                    break;
                case 13: // On Material
                    $ohAmount = $rule->parameter * $totalMaterial;
                    $on=$totalMaterial;
                    break;
                case 19: // On Sub-items (that allow further OH)
                    $ohAmount = $rule->parameter * $costs['sub_items_with_oh'];
                    $on=$costs['sub_items_with_oh'];
                    break;
                case 20: // On Carriage
                    $ohAmount = $rule->parameter * $totalCartage;
                    $on=$totalCartage;
                    break;
                case 4:  // On all resources + applicable sub-items
                    $base = $totalLabor + $totalMachine + $totalMaterial + $totalCartage;// + $costs['sub_items_with_oh'];
                    $ohAmount = $rule->parameter * $base;
                    $on=$base;
                    break;
                case 16: // On specific resources/items
                    $specificBase = 0;
                    $itemIds = explode(',', $rule->applicable_items);
                    foreach ($itemIds as $id) {
                        $specificBase += $costs['specific_resource_costs'][$id] ?? 0;
                    }
                    $ohAmount = $rule->parameter * $specificBase;
                    $on=$specificBase;
                    break;
                case 7:  // On sum of previous overheads
                    $ohAmount = $rule->parameter * $totalOverhead;
                    $on=$totalOverhead;
                    break;
                case 18: // On cumulative total (all resources + previous overheads)
                    $base = $runningTotal + $totalOverhead;
                    $ohAmount = $rule->parameter * $base;
                    $on=$base;
                    break;
            }

            // Only add to the running total for subsequent 'on previous overhead' calculations
            // if the 'furtherOhead' flag is set.
            if ($rule->allow_further_overhead) {
                 $totalOverhead += $ohAmount;
            }

            $overheadData[] = [
                'id' => $rule->id,
                'overhead_id' => $rule->overhead_id,
                'description' => $rule->description .'('. $rule->calculation_type .' )'.'\n'. round($rule->parameter*100,2) . " % over amount $on"   ,
                'parameter' => round($rule->parameter*100,2),
                'amount' => $ohAmount,
            ];
        }
*/
