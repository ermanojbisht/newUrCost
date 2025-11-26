# Laravel: Multi-Sheet RA Excel Export Strategy

This document outlines a modern Laravel approach to generating the multi-sheet, formula-driven Rate Analysis (RA) Excel export, as per the detailed user requirements. This strategy improves upon the original by ensuring a logical bottom-up processing order and leveraging Laravel's best practices for clean, maintainable code.

The `maatwebsite/excel` package is central to this design.

## Core Architecture

The export will be managed by a main "Export" class that implements `WithMultipleSheets`. This class will be responsible for gathering all the necessary data and delegating the rendering of each worksheet to its own dedicated "Sheet" class.

1.  **Main Export Class (`RateAnalysisExport.php`):** Orchestrates the entire process. It fetches the data, gets the correctly ordered item list, and defines which "Sheet" classes to use.
2.  **Sheet Class 1 (`RateAnalysisSheet.php`):** Renders the main RA worksheet, containing the analyses of all items in a bottom-up order. It is responsible for creating the `ra_{item_code}` named ranges.
3.  **Sheet Class 2 (`ResourcesSheet.php`):** Renders the "Resources" worksheet, containing the list of all unique resources and their base rates. It is responsible for creating the `res_{secondary_code}` named ranges.
4.  **Blade Templates:** Each sheet class will use its own Blade template for layout and formula definition.

## Proposed Implementation

### 1. Data Preparation (`RateCalculationService`)

First, we need a method in our service layer to provide the correctly ordered list of items for the export. This involves building a dependency tree and then flattening it in a bottom-up (post-order traversal) manner.

**File Location:** `app/Services/RateCalculationService.php`
```php
// In app/Services/RateCalculationService.php

class RateCalculationService
{
    // ... other methods

    /**
     * Returns a flattened array of all Items required for a top-level analysis,
     * ordered from the deepest child to the top-level parent.
     */
    public function getAnalysisOrder(Item $item): array
    {
        $orderedList = [];
        $this->buildAnalysisOrder($item, $orderedList);
        return $orderedList;
    }

    /**
     * Recursive helper to perform a post-order traversal of the item dependency tree.
     */
    private function buildAnalysisOrder(Item $item, array &$list)
    {
        // First, recurse through all children
        foreach ($item->subItems as $subItemRelation) {
            $this->buildAnalysisOrder($subItemRelation->childItem, $list);
        }

        // Then, add the parent item to the list if it's not already there.
        // This ensures children are always in the list before their parents.
        if (!in_array($item, $list, true)) {
            $list[] = $item;
        }
    }
    
    /**
     * Gathers all unique resources from a list of items.
     */
    public function getUniqueResourcesForItems(array $items): array
    {
        // ... logic to loop through items and their skeletons to get a unique resource list
    }
}
```

### 2. Main Export Class

This class defines the sheets that will be in the workbook.

**File Location:** `app/Exports/RateAnalysisExport.php`
```php
// In app/Exports/RateAnalysisExport.php

namespace App\Exports;

use App\Exports\Sheets\RateAnalysisSheet;
use App\Exports\Sheets\ResourcesSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RateAnalysisExport implements WithMultipleSheets
{
    protected $items;
    protected $resources;
    protected $rateCard;
    protected $date;

    public function __construct(array $items, array $resources, $rateCard, $date)
    {
        $this->items = $items;
        $this->resources = $resources;
        $this->rateCard = $rateCard;
        $this->date = $date;
    }

    /**
     * The list of sheets to be created.
     */
    public function sheets(): array
    {
        $sheets = [];
        
        // Pass the ordered items to the analysis sheet
        $sheets[] = new RateAnalysisSheet($this->items, $this->rateCard, $this->date);

        // Pass the unique resources to the resources sheet
        $sheets[] = new ResourcesSheet($this->resources, $this->rateCard, $this->date);

        return $sheets;
    }
}
```

### 3. Sheet Class for RA

This class handles the main analysis sheet.

**File Location:** `app/Exports/Sheets/RateAnalysisSheet.php`
```php
// In app/Exports/Sheets/RateAnalysisSheet.php
namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class RateAnalysisSheet implements FromView, WithTitle, WithEvents
{
    // constructor to accept $items, $rateCard, $date

    public function view(): View
    {
        // The service logic would be called from the controller and passed in here.
        // This data would include the analysis for each item and cell coordinates.
        return view('exports.rate_analysis', ['items' => $this->items]);
    }

    public function title(): string
    {
        return 'Rate Analysis';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Loop through the items that were rendered
                foreach ($this->items as $item) {
                    // Assume the view has calculated the cell coordinate for the final rate.
                    $finalRateCell = $item['analysis_data']['final_rate_cell']; 
                    $namedRangeName = 'ra_' . $item->itemcode;

                    $event->sheet->getParent()->addNamedRange(
                        new NamedRange($namedRangeName, $event->sheet, $finalRateCell)
                    );
                }
            }
        ];
    }
}
```

**Blade Template:** `resources/views/exports/rate_analysis.blade.php`
```blade
{{-- This view now loops through a collection of items --}}
@foreach($items as $item)
    <table>
        {{-- Header for this specific item analysis --}}
        <thead><tr><th colspan="6">{{ $item->ItemShortDesc }}</th></tr></thead>

        <tbody>
            {{-- Loop through resources for this item --}}
            @foreach($item->skeletons as $skeleton)
            <tr>
                <td>...</td>
                {{-- Formula pointing to the resource sheet --}}
                <td>=res_{{ $skeleton->resource->resCode }}</td>
                <td>...</td>
            </tr>
            @endforeach

            {{-- Loop through sub-items for this item --}}
            @foreach($item->subItems as $subItemRelation)
            <tr>
                <td>...</td>
                {{-- Formula pointing to another RA named range --}}
                <td>=ra_{{ $subItemRelation->childItem->itemcode }}</td>
                <td>...</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            {{-- Final total row for this item --}}
            <tr>
                <td colspan="5">Rate per Unit</td>
                <td>={{-- Formula for this item's final rate --}}</td>
            </tr>
        </tfoot>
    </table>
    <br/> {{-- Add spacing between analyses --}}
@endforeach
```

### 4. Sheet Class for Resources

This class handles the "Resources" sheet.

**File Location:** `app/Exports/Sheets/ResourcesSheet.php`
```php
// In app/Exports/Sheets/ResourcesSheet.php
namespace App\Exports\Sheets;
// ... imports

class ResourcesSheet implements FromView, WithTitle, WithEvents
{
    // constructor to accept $resources, $rateCard, $date

    public function view(): View
    {
        // Logic to calculate the rate for each resource would be done
        // in the service and the results passed in here.
        return view('exports.resources', ['resources' => $this->resources]);
    }

    public function title(): string
    {
        return 'Resources';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                foreach ($this->resources as $index => $resource) {
                    $rowNum = $index + 2; // Assuming headers in row 1
                    $rateCell = 'C' . $rowNum; // Assuming rate is in column C
                    $namedRangeName = 'res_' . $resource->resCode;

                    $event->sheet->getParent()->addNamedRange(
                        new NamedRange($namedRangeName, $event->sheet, $rateCell)
                    );
                }
            }
        ];
    }
}
```

### 5. Controller Method to Trigger Export

The controller prepares all the data and initiates the main export class.

**File Location:** `app/Http/Controllers/ItemRateController.php`
```php
// In app/Http/Controllers/ItemRateController.php

public function export(Request $request, Item $item)
{
    $rateCard = RateCard::find($request->input('rate_card_id', 1));
    $date = new \DateTime($request->input('date', 'today'));

    // 1. Get the correctly ordered list of items to analyze
    $orderedItems = $this->rateCalculator->getAnalysisOrder($item);
    
    // 2. Get the unique list of all resources involved
    $uniqueResources = $this->rateCalculator->getUniqueResourcesForItems($orderedItems);

    $fileName = 'RA_Export_' . $item->ItemNo . '.xlsx';

    // 3. Pass all prepared data to the main export class
    return Excel::download(
        new RateAnalysisExport($orderedItems, $uniqueResources, $rateCard, $date),
        $fileName
    );
}
```
This architecture correctly implements the user's requirements in a clean, scalable, and maintainable way that aligns with modern Laravel development patterns.
