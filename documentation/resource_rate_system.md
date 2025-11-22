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
    return $this->hasMany(Ohead::class, 'raitemid', 'itemcode');
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
        $totalCost = 0;

        // 1. Calculate cost of direct resources
        foreach ($item->skeletons as $skeleton) {
            $resource = $skeleton->resource;
            $resourceRate = $this->calculateResourceRate($resource, $rateCard, $date);
            $totalCost += $resourceRate * $skeleton->quantity;
        }

        // 2. Recursively calculate cost of sub-items
        foreach ($item->subItems as $subItem) {
            $subItemRate = $this->calculateItemRateAnalysis($subItem->childItem, $rateCard, $date);
            $totalCost += $subItemRate * $subItem->dResQty;
        }

        // 3. Calculate and add overheads
        $overheadCost = $this->calculateOverheads($item, $totalCost);
        $totalCost += $overheadCost;

        // 4. Adjust for the item's turnout quantity
        return $totalCost / $item->TurnOutQuantity;
    }

    /**
     * Calculates the final rate for a single resource.
     * Replaces logic from CodeIgniter's Resratemodel->getrate()
     */
    private function calculateResourceRate(Resource $resource, RateCard $rateCard, \DateTime $date): float
    {
        // Get the base rate using Eloquent relationships
        $baseRate = $resource->rates()
            ->where('rate_card_id', $rateCard->ratecardid)
            ->where('valid_from', '<=', $date)
            ->where('valid_to', '>=', $date)
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
     * Calculates total lead cost for a material resource.
     * Replaces logic from Resratemodel->getlead()
     */
    private function getLeadCost(Resource $resource, RateCard $rateCard, \DateTime $date): float
    {
        $totalLead = 0;
        $leadDistances = $resource->leadDistances()
            ->where('RateCardID', $rateCard->ratecardid)
            // ... date conditions
            ->get();

        foreach($leadDistances as $lead) {
            // ... Re-implement the complex logic from getlead() using Laravel models ...
            // e.g., $totalLead += $this->calculateMechanicalLead(...);
        }
        return $totalLead;
    }

    // ... other private helper methods for labor/machine indexes, overheads, etc.
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

        $finalRate = $this->rateCalculator->calculateItemRateAnalysis($item, $rateCard, $date);

        return response()->json([
            'item_code' => $item->itemcode,
            'item_name' => $item->ItemShortDesc,
            'final_rate' => $finalRate,
            'rate_card' => $rateCard->ratecardname,
            'date' => $date->format('Y-m-d'),
        ]);
    }
}
```

By following this strategy, we will create a clean, decoupled, and modern implementation of the rate calculation logic that is easy to understand, extend, and test.
