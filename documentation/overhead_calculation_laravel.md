# Laravel Migration Strategy for Overhead Calculation

This document proposes a strategy for migrating the overhead calculation logic to the Laravel application, building upon the structure defined in the **Resource Rate Calculation Strategy**.

## Core Principle

The overhead calculation is a core part of the business logic for determining an item's final rate. Therefore, it belongs within the `RateAnalysisService`. This keeps all related logic in one place, making the system easier to understand and maintain.

## Proposed Implementation

We will add a new private method to the `RateAnalysisService` called `calculateOverheads`. This method will be responsible for computing the total overhead cost for a given item based on the sub-totals of its resources and sub-items.

### 1. Eloquent Model (`Ohead.php`)

The existing `app/Models/Ohead.php` model will be used. We will ensure it has the correct relationships defined.

```php
// In app/Models/Item.php
public function overheads()
{
    // Ensure the rules are always processed in the correct sequence.
     return $this->hasMany(Ohead::class, 'item_id', 'item_code')->orderBy('sort_order', 'asc');
}
```

### 2. Updating `RateAnalysisService`

The `RateAnalysisService` will be expanded to handle the overhead calculations.

**File Location:** `app/Services/RateAnalysisService.php`

```php
// app/Services/RateAnalysisService.php

namespace App\Services;

use App\Models\Item;
use App\Models\RateCard;
// ... other necessary model imports

class RateAnalysisService
{
    /**
     * Main orchestration method to calculate the full rate analysis for an item.
     */
    public function calculateItemRateAnalysis(Item $item, RateCard $rateCard, \DateTime $date)
    {
        $costs = [
            'labor' => 0,
            'machine' => 0,
            'material' => 0,
            'carriage' => 0,
            'sub_items_with_oh' => 0,
            'sub_items_without_oh' => 0,
            'specific_resource_costs' => [],
        ];
        $runningTotal = 0;

        // 1. Calculate cost of direct resources
        foreach ($item->skeletons()->whereDate(...)->get() as $skeleton) {
            $resource = $skeleton->resource;
            $resourceRate = $this->calculateResourceRate($resource, $rateCard, $date);
            $cost = $resourceRate * $skeleton->quantity;

            $costs['specific_resource_costs'][$resource->code] = $cost;

            switch ($resource->resgr) {
                case 1: $costs['labor'] += $cost; break;
                case 2: $costs['machine'] += $cost; break;
                case 3: $costs['material']_cost += $cost; break;
                case 4: $costs['carriage']_cost += $cost; break;
            }
            $runningTotal += $cost;
        }

        // 2. Recursively calculate cost of sub-items
        foreach ($item->subItems()->whereDate(...)->get() as $subItem) {
            $subItemAnalysis = $this->calculateItemRateAnalysis($subItem->childItem, $rateCard, $date);
            $cost = $subItemAnalysis['final_rate'] * $subItem->dResQty;
            
            $costs['specific_resource_costs'][$subItem->subraitem] = $cost;

            if ($subItem->ohapplicability == 1) {
                $costs['sub_items_with_oh'] += $cost;
            } else {
                $costs['sub_items_without_oh'] += $cost;
            }
            $runningTotal += $cost;
        }

        // 3. Calculate and add overheads
        $overheadCost = $this->calculateOverheads($item, $costs, $runningTotal);
        $runningTotal += $overheadCost;

        // 4. Adjust for the item's turnout quantity
        $finalRate = $item->TurnOutQuantity > 0 ? $runningTotal / $item->TurnOutQuantity : $runningTotal;
        
        return [
            'final_rate' => $finalRate,
            'costs' => $costs,
            'overhead_cost' => $overheadCost,
            'total_cost' => $runningTotal,
        ];
    }

    /**
     * Calculates the final rate for a single resource.
     * (As defined in the previous documentation)
     */
    private function calculateResourceRate(Resource $resource, RateCard $rateCard, \DateTime $date): float
    {
        // ...
    }

    /**
     * NEW METHOD: Calculates total overhead cost for an item.
     * Replaces the overhead loop from CodeIgniter's Ranamodel.
     */
    private function calculateOverheads(Item $item, array $costs, float $runningTotal): float
    {
        $totalOverhead = 0;
        $rules = $item->overheads; // Fetches rules ordered by 'sorder'

        foreach ($rules as $rule) {
            $amount = 0;
            switch ($rule->oon) {
                case 0:  // Lumpsum
                    $amount = $rule->paramtr;
                    break;
                case 11: // On Labor
                    $amount = $rule->paramtr * $costs['labor'];
                    break;
                case 12: // On Machine
                    $amount = $rule->paramtr * $costs['machine'];
                    break;
                case 13: // On Material
                    $amount = $rule->paramtr * $costs['material'];
                    break;
                case 19: // On Sub-items (that allow further OH)
                    $amount = $rule->paramtr * $costs['sub_items_with_oh'];
                    break;
                case 20: // On Carriage
                    $amount = $rule->paramtr * $costs['carriage'];
                    break;
                case 4:  // On all resources + applicable sub-items
                    $base = $costs['labor'] + $costs['machine'] + $costs['material'] + $costs['carriage'] + $costs['sub_items_with_oh'];
                    $amount = $rule->paramtr * $base;
                    break;
                case 16: // On specific resources/items
                    $specificBase = 0;
                    $itemIds = explode(',', $rule->onitm);
                    foreach ($itemIds as $id) {
                        $specificBase += $costs['specific_resource_costs'][$id] ?? 0;
                    }
                    $amount = $rule->paramtr * $specificBase;
                    break;
                case 7:  // On sum of previous overheads
                    $amount = $rule->paramtr * $totalOverhead;
                    break;
                case 18: // On cumulative total (all resources + previous overheads)
                    $base = $runningTotal + $totalOverhead;
                    $amount = $rule->paramtr * $base;
                    break;
            }

            // Only add to the running total for subsequent 'on previous overhead' calculations
            // if the 'furtherOhead' flag is set.
            if ($rule->furtherOhead) {
                 $totalOverhead += $amount;
            }
        }

        return round($totalOverhead, 2);
    }
    
    // ... other private helper methods
}
```

### 3. Controller (`ItemRateController.php`)

The controller remains largely the same. It injects the `RateAnalysisService` and calls the public-facing `calculateItemRateAnalysis` method, then returns the result as a JSON response.

By integrating the overhead calculation into the `RateAnalysisService`, we keep all the complex rate analysis logic cleanly encapsulated in one place, following best practices for building robust and maintainable Laravel applications.
