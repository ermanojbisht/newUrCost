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

The existing Laravel project already has a comprehensive set of Eloquent models that correspond to the old database structure. We will use these as the foundation. Key models include:

-   `app/Models/Item.php`
-   `app/Models/Skeleton.php`
-   `app/Models/Resource.php`
-   `app/Models/Rate.php`
-   `app/Models/RateCard.php`
-   `app/Models/LeadDistance.php`
-   `app/Models/LaborIndex.php`
-   `app/Models/MachineIndex.php`
-   `app/Models/Ohead.php`
-   `app/Models/SubItem.php`

We will ensure that the relationships between these models (`hasMany`, `belongsTo`, etc.) are correctly defined to simplify data retrieval. For example:

```php
// In app/Models/Item.php
public function skeletons()
{
    return $this->hasMany(Skeleton::class, 'raitemid', 'itemcode');
}

public function subItems()
{
    return $this->hasMany(SubItem::class, 'raitemid', 'itemcode');
}

public function overheads()
{
    // Ensure the rules are always processed in the correct sequence.
    return $this->hasMany(Ohead::class, 'raitemid', 'itemcode')->orderBy('sorder', 'asc');
}
```

### 2. RateCalculationService (The Business Logic Layer)

All the calculation logic currently found in `Ranamodel.php` and `Resratemodel.php` will be migrated into a new service class.

**File Location:** `app/Services/RateCalculationService.php`

This service will contain the public and private methods necessary to perform the analysis.

```php
// app/Services/RateCalculationService.php

namespace App\Services;

use App\Models\Item;
use App\Models\Resource;
use App\Models\RateCard;
// ... other necessary model imports

class RateCalculationService
{
    /**
     * Main orchestration method to calculate the full rate analysis for an item.
     * Replaces logic from CodeIgniter's Ranamodel->getanalysiswithoh()
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
        foreach ($item->skeletons as $skeleton) {
            $resource = $skeleton->resource;
            $resourceRate = $this->calculateResourceRate($resource, $rateCard, $date);
            $cost = $resourceRate * $skeleton->quantity;
            
            $costs['specific_resource_costs'][$resource->code] = $cost;

            switch ($resource->resgr) {
                case 1: $costs['labor'] += $cost; break;
                case 2: $costs['machine'] += $cost; break;
                case 3: $costs['material'] += $cost; break;
                case 4: $costs['carriage'] += $cost; break;
            }
            $runningTotal += $cost;
        }

        // 2. Recursively calculate cost of sub-items
        foreach ($item->subItems as $subItem) {
            // Assuming childItem relationship exists on SubItem model
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
     * Replaces logic from CodeIgniter's Resratemodel->getrate()
     */
    private function calculateResourceRate(Resource $resource, RateCard $rateCard, \DateTime $date): float
    {
        // Get the base rate using Eloquent relationships
        $baseRate = $resource->rates()
            ->where('ratecard', $rateCard->ratecardid)
            ->where('predate', '<=', $date)
            ->where('postdate', '>=', $date)
            ->first()->rate ?? 0;

        // Apply adjustments based on resource group
        switch ($resource->resgr) {
            case 1: // Labor
                $index = $this->getLaborIndex($resource, $rateCard, $date);
                return $baseRate + ($baseRate * $index);

            case 2: // Machine
                $index = $this->getMachineIndex($resource, $rateCard, $date);
                return $baseRate + ($baseRate * $index);

            case 3: // Material
                $leadCost = $this->getLeadCost($resource, $rateCard, $date);
                return $baseRate + $leadCost;

            default:
                return $baseRate;
        }
    }

    /**
     * Calculates total overhead cost for an item.
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
            
            // This logic seems to be what was intended by the original code,
            // where $ocst is incremented and then used by rule '7'.
            $totalOverhead += $amount;
        }

        return round($totalOverhead, 2);
    }

    /**
     * Calculates total lead cost for a material resource.
     * Replaces logic from Resratemodel->getlead()
     */
    private function getLeadCost(Resource $resource, RateCard $rateCard, \DateTime $date): float
    {
        // ... Re-implement the complex logic from getlead() using Laravel models ...
        return 0.0; // placeholder
    }
    
    private function getLaborIndex(Resource $resource, RateCard $rateCard, \DateTime $date): float
    {
        // ... Re-implement logic from Resratemodel->getLaborIndex() ...
        return 0.0; // placeholder
    }
    
    private function getMachineIndex(Resource $resource, RateCard $rateCard, \DateTime $date): float
    {
        // ... Re-implement logic from Resratemodel->getLaborIndex() for machines ...
        return 0.0; // placeholder
    }
}
```

### 3. Controller (The HTTP Layer)

A controller will be responsible for handling the incoming HTTP request, calling the service, and returning a response.

**File Location:** `app/Http/Controllers/ItemRateController.php`

```php
// app/Http/Controllers/ItemRateController.php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\RateCard;
use App\Services\RateCalculationService;
use Illuminate\Http\Request;

class ItemRateController extends Controller
{
    protected $rateCalculator;

    public function __construct(RateCalculationService $rateCalculator)
    {
        $this->rateCalculator = $rateCalculator;
    }

    public function show(Request $request, Item $item)
    {
        $rateCard = RateCard::find($request->input('rate_card_id', 1));
        $date = new \DateTime($request->input('date', 'today'));

        $analysis = $this->rateCalculator->calculateItemRateAnalysis($item, $rateCard, $date);

        return response()->json([
            'item_code' => $item->itemcode,
            'item_name' => $item->ItemShortDesc,
            'analysis' => $analysis,
            'rate_card' => $rateCard->ratecardname,
            'date' => $date->format('Y-m-d'),
        ]);
    }
}
```

By following this strategy, we will create a clean, decoupled, and modern implementation of the rate calculation logic that is easy to understand, extend, and test.