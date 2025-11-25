# Laravel Migration Strategy for Item Consumption Page

This document outlines the strategy for creating the "Resource Consumption Report" page in the new Laravel application. This page will display the flattened, aggregated list of all base resources required for an item.

The functionality directly maps to the `getFlatResourceList()` method previously designed for the `RateCalculationService`.

## High-Level Flow

1.  A `GET` request will be made to a new route, e.g., `/items/{item}/consumption`.
2.  The `consumption()` method in the `ItemRateController` will handle the request.
3.  The controller will call the `getFlatResourceList()` method from the `RateCalculationService`.
4.  The service will recursively calculate the total quantity of each base resource, as detailed in the "Sub-item Management" documentation.
5.  The controller will pass the resulting aggregated resource list to a new Blade view.
6.  The Blade view will render the data in an HTML table.

## Proposed Implementation

### 1. Route (`routes/web.php`)

A new route will be added to handle the consumption page.

```php
// In routes/web.php

use App\Http\Controllers\ItemRateController;

// ... other routes

Route::get('/items/{item}/consumption', [ItemRateController::class, 'consumption'])->name('items.consumption');
```

### 2. Service: `RateCalculationService`

We will use the `getFlatResourceList()` method that was already designed as part of the sub-item management strategy. This method effectively replaces the functionality of `Ranamodel->resConsumption()` and `Ranamodel->sumRes()` by recursively building and then aggregating the resource list.

```php
// In app/Services/RateCalculationService.php

class RateCalculationService
{
    // ... other methods

    /**
     * Public method to get a flattened and aggregated list of all resources.
     */
    public function getFlatResourceList(Item $item, RateCard $rateCard, \DateTime $date): array
    {
        $resourceList = [];
        // The helper method populates $resourceList by reference.
        $this->buildFlatResourceList($item, 1.0, $rateCard, $date, $resourceList);
        
        // Aggregate the results
        $aggregatedList = [];
        foreach ($resourceList as $resource) {
            $id = $resource['resource_id'];
            if (!isset($aggregatedList[$id])) {
                $aggregatedList[$id] = $resource;
                // Also calculate the total amount for the aggregated entry
                $rate = $this->calculateResourceRate($resource['resource_object'], $rateCard, $date);
                $aggregatedList[$id]['amount'] = $rate * $aggregatedList[$id]['quantity'];

            } else {
                $aggregatedList[$id]['quantity'] += $resource['quantity'];
                // Recalculate the amount with the new total quantity
                $rate = $this->calculateResourceRate($resource['resource_object'], $rateCard, $date);
                $aggregatedList[$id]['amount'] = $rate * $aggregatedList[$id]['quantity'];
            }
        }
        return array_values($aggregatedList);
    }

    /**
     * Recursive helper method to build the resource list.
     */
    private function buildFlatResourceList(Item $item, float $factor, RateCard $rateCard, \DateTime $date, &array &$resourceList)
    {
        // Add direct resources of the current item
        foreach ($item->skeletons as $skeleton) {
            $resourceList[] = [
                'resource_id' => $skeleton->resourceid,
                'resource_object' => $skeleton->resource, // Pass the object for rate calculation
                'group' => $skeleton->resource->resgr,
                'name' => $skeleton->resource->name,
                'unit' => $skeleton->resource->unit->vUnitName, // Assuming unit relationship
                'quantity' => $skeleton->quantity * $factor,
            ];
        }

        // Recursively process sub-items
        foreach ($item->subItems as $subItemRelation) {
            $childItem = $subItemRelation->childItem;
            $turnout = $childItem->TurnOutQuantity > 0 ? $childItem->TurnOutQuantity : 1;
            $newFactor = $factor * ($subItemRelation->dResQty / $turnout);
            $this->buildFlatResourceList($childItem, $newFactor, $rateCard, $date, $resourceList);
        }
    }
}
```

### 3. Controller: `ItemRateController.php`

A new method, `consumption`, will be added to orchestrate the request.

```php
// In app/Http/Controllers/ItemRateController.php

class ItemRateController extends Controller
{
    // ... constructor and other methods ...

    public function consumption(Request $request, Item $item)
    {
        $rateCard = RateCard::find($request->input('rate_card_id', 1));
        $date = new \DateTime($request->input('date', 'today'));

        // Call the service to get the aggregated resource list
        $consumptionList = $this->rateCalculator->getFlatResourceList($item, $rateCard, $date);

        return view('items.consumption', [
            'item' => $item,
            'rateCard' => $rateCard,
            'date' => $date,
            'consumptionList' => $consumptionList,
        ]);
    }
}
```

### 4. View: `items/consumption.blade.php`

A new Blade view will render the data.

**File Location:** `resources/views/items/consumption.blade.php`

```blade
{{-- In resources/views/items/consumption.blade.php --}}

@extends('layouts.app')

@section('content')
    <h1>Resource Consumption Report for: {{ $item->ItemShortDesc }}</h1>
    
    {{-- Display item details, breadcrumbs, etc. --}}

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Group</th>
                <th>Resource Name</th>
                <th>Total Quantity</th>
                <th>Unit</th>
                <th>Derived Rate</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consumptionList as $resource)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $resource['group'] }}</td>
                    <td>
                        <a href="{{-- route('resources.show', $resource['resource_id']) --}}">
                            {{ $resource['name'] }}
                        </a>
                    </td>
                    <td>{{ number_format($resource['quantity'], 4) }}</td>
                    <td>{{ $resource['unit'] }}</td>
                    <td>
                        @if($resource['quantity'] > 0)
                            {{ number_format($resource['amount'] / $resource['quantity'], 2) }}
                        @else
                            0.00
                        @endif
                    </td>
                    <td>{{ number_format($resource['amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>Grand Total:</strong></td>
                <td>
                    <strong>{{ number_format(array_sum(array_column($consumptionList, 'amount')), 2) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
@endsection
```

This approach creates a clear and logical flow of data, from the route to the controller, through the service, and finally to the view, resulting in a robust and easily maintainable feature.
