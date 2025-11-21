# Documentation: Item Skeleton View (`sor/raskeleton`)

This document details the process of how the "Item Skeleton" or Rate Analysis view is generated in the CodeIgniter 3 application. The process is initiated by a URL like `http://localhost/urCost/index.php?/sor/raskeleton/5000680`.

## Summary

The view is a detailed cost breakdown of a Schedule of Rates (SOR) item. It shows the primary resources (labor, material, machinery), any constituent sub-items, and all applicable overhead charges. The final rate is calculated by summing these costs and dividing by the item's standard output quantity. The business logic for calculation is distributed between the Model and the View.

## File Paths Involved

*   **Controller:** `application/controllers/Sor.php`
*   **Model:** `application/models/Ranamodel.php`
*   **View:** `application/views/layout/raitemana.php`
*   **Constants:** `application/config/constants.php`

## Step-by-Step Data Flow

The generation of the page follows this sequence:

### 1. Routing: URL to Controller

The URL `.../sor/raskeleton/5000680` is parsed by CodeIgniter:
-   **Class:** `sor` maps to `Sor.php`.
-   **Method:** `raskeleton` is the function called within the `Sor` controller.
-   **Parameter:** `5000680` is passed as the `$racode` (the item ID) to the method.

### 2. Controller Logic (`Sor.php`)

The `raskeleton($racode, ...)` method acts as the orchestrator.

1.  **Authentication & Setup:** It verifies the user is logged in and sets up user-specific permissions and date/rate card information.
2.  **Model Loading:** It loads multiple models, most importantly `Ranamodel`.
3.  **Data Fetching:** It calls methods on `Ranamodel` to retrieve all the necessary data for the analysis.
    -   `$this->data['ranalysis'] = $this->Ranamodel->getanalysis($racode, ...);`
        - This fetches the primary resources (man, machine, material) that form the item.
    -   `$this->data['subItems'] = $this->Ranamodel->getSubItems($racode, ...);`
        - This fetches any pre-calculated sub-items that are part of the main item's composition.
    -   `$this->data['oheadana'] = $this->Ranamodel->getOverHeadAnalysis($racode, ...);`
        - This fetches the rules for overhead calculations (e.g., percentages to be applied to different cost categories).
    - Other models like `Soritemmodel` are used to get metadata like the item's name, path (for breadcrumbs), and next/previous item links.
4.  **View Loading:** The controller packages all the retrieved data into the `$this->data` array and passes it to the view file.
    -   The `THEME` constant is defined in `application/config/constants.php` as `'layout'`.
    -   `$this->data['content'] = $this->load->view('layout/raitemana', $this->data, true);`
    -   This rendered content is then loaded into a main dashboard/layout view.

### 3. Model Data Retrieval (`Ranamodel.php`)

This model is responsible for all the core database queries.

-   **`getanalysis($racode, ...)`**:
    -   Queries the `skeleton` table to find all primary resources linked to the `$racode`.
    -   Joins with the `resource` table to get resource names and details.
    -   It filters by date to ensure the correct version of the skeleton is used.
    -   For each resource, it calls the `getrate()` method from `resratemodel` to fetch its monetary value from the specified rate card.
    -   Returns an array of resource objects, each including quantity, unit, and the calculated rate.

-   **`getSubItems($racode, ...)`**:
    -   Queries the `subitem` table to find all child items linked to the `$racode`.
    -   It then joins with `itemrate` or `subitem_rate` tables to fetch the *pre-calculated rates* of these sub-items. This demonstrates a dependency in the calculation logic, where sub-items must have their own rates calculated before the parent item's rate can be finalized.
    -   Returns an array of sub-item objects.

-   **`getOverHeadAnalysis($racode, ...)`**:
    -   Queries the `ohead` table.
    -   This query returns the *rules* for calculating overheads, not the final values. For example, it might return a rule like "apply 15% on labor cost". The actual calculation happens in the view.

### 4. View Rendering (`layout/raitemana.php`)

This file is responsible for presenting the data and performing the final calculations.

1.  **Display Item Metadata:** It first renders the item's breadcrumb navigation path and its full name.
2.  **Render Resources Table:** It loops through the `$ranalysis` array. For each resource, it displays its name, quantity, rate, and calculated amount (`quantity * rate`). During this loop, it also sums the costs into separate PHP variables for each cost category (`$lbcst` for labor, `$mtcst` for material, etc.).
3.  **Render Sub-Items:** It then loops through the `$subItems` array and adds them to the same table, contributing their cost to the total.
4.  **Calculate and Render Overheads Table:** This is a critical step where significant business logic resides.
    -   The view loops through the `$oheadana` array (which contains the overhead *rules*).
    -   A large `switch` statement inside the loop evaluates the type of each overhead rule (`$ohana->oon`).
    -   Based on the rule type, it calculates the overhead amount by applying the rule's parameter to the corresponding cost category variable. For example, if the rule is `oon = 11` (on labor), it calculates `amount = $ohana->paramtr * $lbcst`.
    -   Each calculated overhead amount is displayed in a row and added to a running total overhead cost (`$ocst`).
5.  **Display Final Totals:**
    -   The view sums the total resource/sub-item amount and the total overhead cost (`$totalAmount + $ocst`).
    -   It retrieves the item's standard output quantity (`$ratedetails->TurnOutQuantity`).
    -   The final rate is calculated: `Rate = ($totalAmount + $ocst) / $ratedetails->TurnOutQuantity`.
    -   The total amount and the final rate per unit are displayed at the bottom of the page.

## Recommendation for Laravel Migration

When migrating to Laravel, the following structure is recommended to improve separation of concerns:

1.  **Route:** Define a clear route in `routes/web.php`:
    ```php
    Route::get('/sor/item-skeleton/{item}', [ItemSkeletonController::class, 'show']);
    ```
2.  **Controller:** The controller should be lean, only responsible for handling the request and returning the view.
    ```php
    class ItemSkeletonController extends Controller
    {
        public function show(Item $item, Request $request)
        {
            // Use a dedicated Service class for business logic
            $skeletonData = (new ItemSkeletonService)->generate($item, $request->date, $request->rate_card);
            return view('sor.item_skeleton', ['skeleton' => $skeletonData]);
        }
    }
    ```
3.  **Service Class (New):** Create a new `app/Services/ItemSkeletonService.php`. This class should contain **all the calculation logic** that currently exists in the CodeIgniter view (`raitemana.php`).
    -   It would have a `generate()` method that takes an `Item` model.
    -   It would fetch resources, sub-items, and overhead rules from the database using Eloquent models.
    -   It would perform all the loops and `switch` statements to calculate the costs of resources, sub-items, and overheads.
    -   It would calculate the final total and the rate per unit.
    -   It should return a single, structured data object (or array) containing all the display-ready information (resource lists, overhead lists, totals, final rate).
4.  **Models:** Use Eloquent models (`Item`, `Resource`, `Skeleton`, `OverheadRule`) with defined relationships (e.g., `Item` `hasMany` `Skeleton` entries).
5.  **View (Blade Template):** The Blade template (`resources/views/sor/item_skeleton.blade.php`) should be simple and contain almost no PHP logic. It should only loop through the data provided by the service class and display it.
    ```blade
    {{-- Resources Table --}}
    @foreach ($skeleton->resources as $resource)
        <tr>
            <td>{{ $resource->name }}</td>
            <td>{{ $resource->quantity }}</td>
            <td>{{ $resource->rate }}</td>
            <td>{{ $resource->amount }}</td>
        </tr>
    @endforeach

    {{-- Overheads Table --}}
    @foreach ($skeleton->overheads as $overhead)
        <tr>
            <td>{{ $overhead->description }}</td>
            <td>{{ $overhead->rate_display }}</td>
            <td>{{ $overhead->amount }}</td>
        </tr>
    @endforeach

    {{-- Totals --}}
    <strong>Total Cost: {{ $skeleton->total_cost }}</strong>
    <strong>Final Rate: {{ $skeleton->final_rate }} / {{ $skeleton->unit }}</strong>
    ```
This approach moves the complex business logic out of the view and into a dedicated service class, making the code much cleaner, easier to test, and more maintainable, which is a primary goal of moving to a modern framework like Laravel.

