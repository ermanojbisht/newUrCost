# Guide: Exporting a Full SOR Rate Analysis to Excel

## 1. Objective & Goal

This document provides a comprehensive guide for developers to create a feature that exports all Rate Analyses (RA) for a given Schedule of Rates (SOR) into a single, multi-sheet, formula-driven Excel workbook.

The final output will be a dynamic `.xlsx` file with two interconnected worksheets:

1.  **"Resources" Sheet:** A master list of all unique resources (labor, materials, etc.) used across all items in the SOR. Each resource's rate will be defined in a **Named Range** (e.g., `res_PL10`).
2.  **"Rate Analysis" Sheet:** Contains the full, detailed analysis for every item and sub-item within the SOR. The items will be rendered in a logical, bottom-up order (dependencies appear before the items that use them). This sheet will use formulas to dynamically link to the named ranges on the "Resources" sheet and to the rates of other items on the same sheet.

This approach replicates the functionality of the legacy system but with a more robust, maintainable, and logical architecture.

## 2. Legacy System Reference

To understand what we are rebuilding, it's helpful to know how the old system worked. The logic is located in `application/models/Ranamodel.php`, primarily within the `openFileExcel()` and `writeRaToExcel()` methods.

**Legacy Process Summary:**

1.  **Data Collection:** A controller calls `openFileExcel`, which fetches a list of all RA items for a given SOR. It also gathers all sub-items, but importantly, this list is **not ordered** bottom-up.
2.  **Procedural Generation:** The code loops through this list of items. For each item, it calls `writeRaToExcel`, which programmatically builds an analysis block cell-by-cell on the main worksheet.
3.  **Named Ranges & Formulas:**
    *   As `writeRaToExcel` processes an item, it creates a named range for that item's final rate (e.g., `ra_1000256_1`).
    *   When adding a resource to an analysis, it writes a formula pointing to a resource's named range (e.g., `=r_1_PL10`).
    *   When adding a sub-item, it writes a formula pointing to that sub-item's named range (e.g., `=ra_1000256_1`).
4.  **Resource Sheet:** After the main sheet is built, a second "Resource List" sheet is created and populated. The named ranges for resource rates are created at this stage.
5.  **Resolution:** The system relies on Microsoft Excel to resolve all the formulas and named ranges upon opening the file, which works even though the items were not processed in a strict bottom-up order. Our new system will improve on this by logically ordering the items first.

## 3. Laravel Implementation Plan

We will use the `maatwebsite/excel` package to create a clean, modular, and testable export feature.

### Step 1: Installation

If not already installed, add `maatwebsite/excel` to your project.

```bash
composer require maatwebsite/excel
```

### Step 2: The Controller (Entry Point)

The controller's role is to handle the web request, delegate the complex logic to a service layer, and trigger the file download.

**File:** `app/Http/Controllers/SorController.php`
```php
namespace App\Http\Controllers;

use App\Exports\SorRateAnalysisExport;
use App\Models\RateCard;
use App\Models\Sor;
use App\Services\RateCalculationService; // To be injected
use Maatwebsite\Excel\Facades\Excel;

class SorController extends Controller
{
    // Inject the service via the constructor
    public function __construct(protected RateCalculationService $rateCalculator) {}

    public function exportSorRateAnalysis(Sor $sor, RateCard $rateCard)
    {
        $date = new \DateTime('today');

        // 1. Let the service do all the heavy lifting of data preparation
        $exportData = $this->rateCalculator->getFullSorAnalysisData($sor, $rateCard, $date);

        $fileName = "Rate_Analysis_{$sor->sorname}.xlsx";

        // 2. Pass the prepared data array to the main export class and download
        return Excel::download(new SorRateAnalysisExport($exportData), $fileName);
    }
}
```

### Step 3: The Service Layer (Data Preparation)

This is the most critical step. The `RateCalculationService` must be enhanced to prepare all data required for the entire workbook.

**File:** `app/Services/RateCalculationService.php`
```php
class RateCalculationService
{
    /**
     * The main public method to orchestrate data preparation for the export.
     */
    public function getFullSorAnalysisData(Sor $sor, RateCard $rateCard, \DateTime $date): array
    {
        $topLevelItems = $sor->items()->where('itemcode', '>', 0)->get();

        // Get a unique, bottom-up ordered list of all items and their children
        $allItemsOrdered = [];
        foreach ($topLevelItems as $item) {
            $this->buildAnalysisOrder($item, $allItemsOrdered);
        }
        $uniqueItemList = array_unique($allItemsOrdered, SORT_REGULAR);

        // Get a unique list of all resources used across all items
        $allResources = $this->getUniqueResourcesForItems($uniqueItemList, $rateCard, $date);

        // Calculate the detailed analysis for each item needed for the view
        $itemAnalyses = [];
        foreach ($uniqueItemList as $item) {
            // This method would be an enhanced version of calculateItemRateAnalysis
            // that returns a structured array for the view.
            $itemAnalyses[] = $this->getDetailedAnalysisForExport($item, $rateCard, $date);
        }

        return [
            'sor' => $sor,
            'rateCard' => $rateCard,
            'date' => $date,
            'ordered_items_analysis' => $itemAnalyses,
            'unique_resources' => $allResources,
        ];
    }
    
    /**
     * Recursively builds a bottom-up (post-order) list of items.
     */
    private function buildAnalysisOrder(Item $item, array &$list)
    {
        // 1. Recurse through children first
        foreach ($item->subItems as $subItemRelation) {
            $this->buildAnalysisOrder($subItemRelation->childItem, $list);
        }
        // 2. Add the parent after all its children have been added
        $list[] = $item;
    }

    // Other helper methods like getUniqueResourcesForItems() and getDetailedAnalysisForExport()...
}
```

### Step 4: The Multi-Sheet Export Architecture

Create a main export class that defines the sheets in the workbook.

**File:** `app/Exports/SorRateAnalysisExport.php`
```php
namespace App\Exports;

use App\Exports\Sheets\RateAnalysisSheet;
use App\Exports\Sheets\ResourcesSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SorRateAnalysisExport implements WithMultipleSheets
{
    // The data array prepared by the service
    public function __construct(protected array $data) {}

    public function sheets(): array
    {
        // Define the classes that will render each sheet
        return [
            new RateAnalysisSheet($this->data['ordered_items_analysis'], $this->data),
            new ResourcesSheet($this->data['unique_resources'], $this->data['rateCard']),
        ];
    }
}
```

### Step 5: The Sheet-Specific Classes and Views

Create a dedicated class and Blade view for each sheet.

**Resources Sheet Class:**
**File:** `app/Exports/Sheets/ResourcesSheet.php`
```php
namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class ResourcesSheet implements FromView, WithTitle, WithEvents
{
    public function __construct(protected array $resources, protected $rateCard) {}

    public function title(): string { return 'Resources'; }

    public function view(): View
    {
        return view('exports.sheets.resources', ['resources' => $this->resources]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // After the sheet is created, add the named ranges
                foreach ($this->resources as $index => $resource) {
                    $rowNum = $index + 2; // +1 for header, +1 for 0-index
                    $rateCell = 'D' . $rowNum; // Assume Rate is in Column D
                    $namedRangeName = 'res_' . $resource['resCode'];

                    $event->sheet->getParent()->addNamedRange(
                        new NamedRange($namedRangeName, $event->sheet, "'Resources'!" . $rateCell)
                    );
                }
            }
        ];
    }
}
```
**Resources Blade View:**
**File:** `resources/views/exports/sheets/resources.blade.php`
```blade
<table>
    <thead>
        <tr>
            <th>Code</th><th>Name</th><th>Unit</th><th>Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resources as $resource)
            <tr>
                <td>{{ $resource['resCode'] }}</td>
                <td>{{ $resource['name'] }}</td>
                <td>{{ $resource['unit'] }}</td>
                <td>{{ $resource['rate'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
```
---
**Rate Analysis Sheet Class:**
A similar class, `app/Exports/Sheets/RateAnalysisSheet.php`, would be created. Its `registerEvents` method would create the `ra_{item_code}` named ranges.

**Rate Analysis Blade View:**
**File:** `resources/views/exports/sheets/rate_analysis.blade.php`
```blade
{{-- This view loops through the PRE-ORDERED items --}}
@foreach($items_analysis as $itemAnalysis)
    {{-- Render the full analysis block for each item --}}
    <table>
        <thead>
            <tr><th colspan="6">{{ $itemAnalysis['name'] }}</th></tr>
            {{-- More headers for quantity, unit, etc. --}}
        </thead>
        <tbody>
            {{-- Loop through resources for this item --}}
            @foreach($itemAnalysis['resources'] as $resource)
            <tr>
                <td>Resource: {{ $resource['name'] }}</td>
                <td>{{ $resource['quantity'] }}</td>
                {{-- FORMULA: Point to the named range on the Resources sheet --}}
                <td>=res_{{ $resource['resCode'] }}</td>
                <td>={{-- Formula to multiply qty * rate --}}</td>
            </tr>
            @endforeach

            {{-- Loop through sub-items for this item --}}
            @foreach($itemAnalysis['sub_items'] as $subItem)
            <tr>
                <td>Sub-item: {{ $subItem['name'] }}</td>
                <td>{{ $subItem['quantity'] }}</td>
                {{-- FORMULA: Point to the named range of another item on THIS sheet --}}
                <td>=ra_{{ $subItem['itemcode'] }}</td>
                <td>={{-- Formula to multiply qty * rate --}}</td>
            </tr>
            @endforeach
            {{-- ... overheads and totals ... --}}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Final Rate per Unit</td>
                {{-- The cell that will be captured for this item's named range --}}
                <td>={{-- Final Rate Formula --}}</td>
            </tr>
        </tfoot>
    </table>
    <br/>
@endforeach
```
This comprehensive approach provides a robust, maintainable, and highly organized solution for generating complex, interconnected spreadsheets in Laravel.
