# Laravel Migration Strategy for Rate Calculation

This document outlines the strategy for migrating the item rate calculation logic from the legacy CodeIgniter application to the modern Laravel framework. The goal is to create a more robust, maintainable, and testable system by leveraging Laravel's features like Eloquent ORM and Service Classes.

The new implementation will live in the `/var/www/newUrCost` project.

## Core Principles

1.  **Model-View-Controller (MVC):** We will adhere to a strict MVC pattern.
2.  **Fat Models, Thin Controllers:** Controllers will only handle HTTP requests and responses. All business logic will be delegated to services or models.
3.  **Service Layer:** Complex business logic, like the multi-step rate calculation, will be encapsulated in a dedicated service class. This improves reusability and keeps the models focused on database interactions and relationships.
4.  **Eloquent ORM:** We will use Laravel's Eloquent ORM to interact with the database. The existing models in `/app/Models` provide a strong foundation.

## Proposed Structure

### 1. Eloquent Models (The Data Layer)

Eloquent relationships are key to this design.

**`app/Models/Item.php`**
```php
public function skeletons() {
    return $this->hasMany(Skeleton::class, 'raitemid', 'itemcode');
}
public function subItems() {
    return $this->hasMany(SubItem::class, 'raitemid', 'itemcode');
}
public function overheads() {
    return $this->hasMany(Ohead::class, 'raitemid', 'itemcode')->orderBy('sorder', 'asc');
}
```

**`app/Models/SubItem.php`**
```php
public function parentItem() {
    return $this->belongsTo(Item::class, 'raitemid', 'itemcode');
}
public function childItem() {
    return $this->belongsTo(Item::class, 'subraitem', 'itemcode');
}
```

### 2. RateCalculationService (The Business Logic Layer)

All calculation logic will be consolidated into this service.

**File Location:** `app/Services/RateCalculationService.php`

```php
// app/Services/RateCalculationService.php

namespace App\Services;

use App\Models\Item;
use App\Models\Resource;
use App\Models\RateCard;
use App\Models\SubItemRate;
use App\Models\ItemRate;

class RateCalculationService
{
    /** @var array Cache to prevent re-calculating the same item. */
    private $analysisCache = [];

    /**
     * Main orchestration method.
     */
    public function calculateItemRateAnalysis(Item $item, RateCard $rateCard, \DateTime $date)
    {
        $cacheKey = "{$item->itemcode}-{$rateCard->ratecardid}-{$date->format('Y-m-d')}";
        if (isset($this->analysisCache[$cacheKey])) {
            return $this->analysisCache[$cacheKey];
        }

        $costs = [
            'labor' => 0, 'machine' => 0, 'material' => 0, 'carriage' => 0,
            'sub_items_with_oh' => 0, 'sub_items_without_oh' => 0,
            'specific_resource_costs' => [],
        ];
        $runningTotal = 0;

        // 1. Calculate cost of direct resources
        foreach ($item->skeletons as $skeleton) {
            $resource = $skeleton->resource;
            $resourceRate = $this->calculateResourceRate($resource, $rateCard, $date);
            $cost = $resourceRate * $skeleton->quantity;
            
            $costs['specific_resource_costs'][$resource->code] = $cost;
            switch ($resource->resgr) {
                case 1: $costs['labor'] += $cost; break;
                // ... other resource groups
            }
            $runningTotal += $cost;
        }

        // 2. Recursively calculate cost of sub-items
        $subItemRelations = $item->subItems()->where('predate', '<=', $date)->where('postdate', '>=', $date)->get();
        foreach ($subItemRelations as $subItemRelation) {
            $childItem = $subItemRelation->childItem;
            $subItemRate = $this->getSubItemRate($childItem, $rateCard, $date);
            $cost = $subItemRate * $subItemRelation->dResQty;

            $costs['specific_resource_costs'][$subItemRelation->subraitem] = $cost;
            if ($subItemRelation->ohapplicability == 1) {
                $costs['sub_items_with_oh'] += $cost;
            } else {
                $costs['sub_items_without_oh'] += $cost;
            }
            $runningTotal += $cost;
        }

        // 3. Calculate and add overheads
        $overheadCost = $this->calculateOverheads($item, $costs, $runningTotal);
        $runningTotal += $overheadCost;

        // 4. Adjust for turnout quantity
        $finalRate = $item->TurnOutQuantity > 0 ? $runningTotal / $item->TurnOutQuantity : $runningTotal;

        $result = [
            'final_rate' => $finalRate,
            'costs' => $costs,
            'overhead_cost' => $overheadCost,
            'total_cost' => $runningTotal,
        ];
        
        $this->analysisCache[$cacheKey] = $result;
        return $result;
    }

    /**
     * Determines the correct rate for a sub-item based on the analysis date.
     */
    private function getSubItemRate(Item $item, RateCard $rateCard, \DateTime $date): float
    {
        if ($date < new \DateTime('today')) {
            $historicalRate = SubItemRate::where('racode', $item->itemcode)
                ->where('ratecard', $rateCard->ratecardid)
                ->where('predate', '<=', $date)->where('postdate', '>=', $date)
                ->first();
            if ($historicalRate) return $historicalRate->rate;
        }
        $liveRate = ItemRate::where('racode', $item->itemcode)->where('ratecard', $rateCard->ratecardid)->first();
        return $liveRate->rate ?? 0;
    }
    
    /**
     * Calculates the final rate for a single resource.
     */
    private function calculateResourceRate(Resource $resource, RateCard $rateCard, \DateTime $date): float
    {
        // ... (Implementation from previous documentation)
        return 0.0;
    }

    /**
     * Calculates total overhead cost for an item.
     */
    private function calculateOverheads(Item $item, array $costs, float $runningTotal): float
    {
        // ... (Implementation from previous documentation)
        return 0.0;
    }

    // ... other private helpers for lead, indexes etc.
}
```

### 3. Controller (The HTTP Layer)

The controller injects the service and uses it to respond to HTTP requests.

**File Location:** `app/Http/Controllers/ItemRateController.php`
```php
// app/Http/Controllers/ItemRateController.php
use App\Services\RateCalculationService;
// ...
class ItemRateController extends Controller
{
    // ... (Implementation from previous documentation)
}
```

This consolidated service provides a single point of entry for all rate analysis, making the system clean, testable, and easy to maintain.
