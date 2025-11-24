# Laravel Migration Strategy for Sub-item Management

This document outlines the strategy for migrating the logic for handling sub-items in a rate analysis to the Laravel application. This builds upon the architecture established in the primary rate calculation and overhead documentation.

## Core Principle

The handling of sub-items is fundamentally recursive. A sub-item is just another item, and its cost is calculated using the exact same `RateCalculationService`. The Laravel implementation will embrace this recursion to create a clean, elegant, and powerful solution.

## Proposed Implementation

### 1. Eloquent Models & Relationships

We will leverage Eloquent relationships to represent the nested structure of items.

**`app/Models/Item.php`**

```php
// In app/Models/Item.php

class Item extends Model
{
    // ...

    /**
     * Defines the relationship to the sub-item entries where this item is the PARENT.
     */
    public function subitems()
    {
        return $this->hasMany(Subitem::class, 'item_code', 'item_code');
    }
}
```

**`app/Models/SubItem.php`**

The `SubItem` model acts as the pivot table between a parent `Item` and a child `Item`.

```php
// In app/Models/SubItem.php

class SubItem extends Model
{
    protected $table = 'subitem';

    /**
     * Defines the relationship back to the PARENT item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_code', 'item_code');
    }

    /**
     * Defines the relationship to the CHILD item.
     * This allows us to get the full item details for the sub-item.
     */
    public function subItem()
    {
        return $this->belongsTo(Item::class, 'sub_item_code', 'item_code');
    }
}
```

### 2. Updating the `RateCalculationService`

The `calculateItemRateAnalysis` method will be updated to handle the recursion explicitly. It will call itself to resolve the cost of each sub-item.

```php
// In app/Services/RateCalculationService.php

class RateCalculationService
{
    /**
     * A cache to store the results of item analyses to prevent re-calculation
     * of the same sub-item multiple times within a single top-level analysis.
     * @var array
     */
    private $analysisCache = [];

    public function calculateItemRateAnalysis(Item $item, RateCard $rateCard, \DateTime $date)
    {
        // Check cache first to avoid redundant calculations
        $cacheKey = "{$item->item_code}-{$rateCard->rate_card_id}-{$date->format('Y-m-d')}";
        if (isset($this->analysisCache[$cacheKey])) {
            return $this->analysisCache[$cacheKey];
        }
        
        // ... (resource calculation logic remains the same) ...
        $costs = [ ... ];
        $runningTotal = 0;

        // --- SUB-ITEM LOGIC ---
        // 1. Get the relevant sub-item relationships for the given date
        $subItemRelations = $item->subItems()
            ->where('predate', '<=', $date)
            ->where('postdate', '>=', $date)
            ->get();

        // 2. Loop through and recursively call the analysis method
        foreach ($subItemRelations as $subItemRelation) {
            $childItem = $subItemRelation->childItem;
            
            // Find the correct rate for the sub-item based on the date.
            $subItemRate = $this->getSubItemRate($childItem, $rateCard, $date);

            // Calculate the cost based on the quantity required by the parent.
            // Note the use of dResQty from the relationship model.
            $cost = $subItemRate * $subItemRelation->dResQty;

            // This logic from the old system for OH applicability is maintained.
            if ($subItemRelation->ohapplicability == 1) {
                $costs['sub_items_with_oh'] += $cost;
            } else {
                $costs['sub_items_without_oh'] += $cost;
            }
            $runningTotal += $cost;
        }

        // ... (overhead calculation logic remains the same) ...

        // Adjust for the item's turnout quantity
        $finalRate = $item->TurnOutQuantity > 0 ? $runningTotal / $item->TurnOutQuantity : $runningTotal;

        $result = [
            'final_rate' => $finalRate,
            // ... other results
        ];
        
        // Store result in cache before returning
        $this->analysisCache[$cacheKey] = $result;

        return $result;
    }

    /**
     * Determines whether to use a "live" rate or a historical rate for a sub-item.
     * Replaces the date-checking logic from CodeIgniter's getSubItems().
     */
    private function getSubItemRate(Item $item, RateCard $rateCard, \DateTime $date): float
    {
        $today = new \DateTime('today');

        if ($date < $today) {
            // Date is in the past, look for a historical rate.
            $historicalRate = \App\Models\SubItemRate::where('racode', $item->itemcode)
                ->where('ratecard', $rateCard->ratecardid)
                ->where('predate', '<=', $date)
                ->where('postdate', '>=', $date)
                ->first();
            
            if ($historicalRate) {
                return $historicalRate->rate;
            }
        }

        // Default to the live item rate table if no historical rate is found or date is today/future.
        $liveRate = \App\Models\ItemRate::where('racode', $item->itemcode)
            ->where('ratecard', $rateCard->ratecardid)
            ->first();

        return $liveRate->rate ?? 0;
    }

    // Other methods...
}
```

### 3. Recursive Resource Consumption

The functionality of `resConsumption` (getting a flattened list of all base resources) can be achieved with a new public method in the service.

```php
// In app/Services/RateCalculationService.php

public function getFlatResourceList(Item $item, RateCard $rateCard, \DateTime $date): array
{
    $resourceList = [];
    $this->buildFlatResourceList($item, 1.0, $rateCard, $date, $resourceList);
    
    // The $resourceList is passed by reference and is now populated.
    // We can now aggregate the quantities.
    $aggregatedList = [];
    foreach ($resourceList as $resource) {
        $id = $resource['resource_id'];
        if (!isset($aggregatedList[$id])) {
            $aggregatedList[$id] = $resource;
        } else {
            $aggregatedList[$id]['quantity'] += $resource['quantity'];
        }
    }
    return array_values($aggregatedList);
}

private function buildFlatResourceList(Item $item, float $factor, RateCard $rateCard, \DateTime $date, &array $resourceList)
{
    // Add direct resources of the current item
    foreach ($item->skeletons as $skeleton) {
        $resourceList[] = [
            'resource_id' => $skeleton->resourceid,
            'name' => $skeleton->resource->name,
            'quantity' => $skeleton->quantity * $factor,
        ];
    }

    // Recursively process sub-items
    foreach ($item->subItems as $subItemRelation) {
        $childItem = $subItemRelation->childItem;
        
        // Calculate the new factor for the next level of recursion
        $turnout = $childItem->TurnOutQuantity > 0 ? $childItem->TurnOutQuantity : 1;
        $newFactor = $factor * ($subItemRelation->dResQty / $turnout);

        $this->buildFlatResourceList($childItem, $newFactor, $rateCard, $date, $resourceList);
    }
}
```

This recursive approach is clean, efficient, and leverages the power of Eloquent's object-relational mapping, resulting in a far more maintainable system than the original implementation.
