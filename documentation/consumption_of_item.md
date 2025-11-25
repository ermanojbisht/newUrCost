# Item Consumption Page Implementation Plan

## Goal
Create a "Resource Consumption Report" page that displays a flattened, aggregated list of all base resources required for a specific item, including those from sub-items.

## Prerequisites
- `RateCalculationService` must exist (or be updated) to handle recursive resource calculation.
- `ItemRateController` must exist (or be created) to handle the request.
- Routes and Views need to be defined.

## Proposed Changes

### 1. Routes (`routes/web.php`)
- Add a GET route: `/sors/{sor}/items/{item}/consumption` -> `ItemRateController@consumption`.
- Note: The documentation suggested `/items/{item}/consumption`, but since items usually belong to an SOR context in this app, nesting it might be better or keeping it top-level if `Item` ID is unique globally (which it likely is). Let's stick to the user's likely existing pattern. *Correction*: The user's existing routes use `sors.items.ra` for Rate Analysis. Let's use `sors.items.consumption` to be consistent.

### 2. Service: `RateAnalysisService` (or `RateCalculationService`)
- **Note**: The documentation mentions `RateCalculationService`, but the existing codebase seems to use `RateAnalysisService` (checked in previous turns). I should verify if `RateCalculationService` exists or if I should add this method to `RateAnalysisService`.
- **Method**: Implement `getFlatResourceList(Item $item, RateCard $rateCard, $effectiveDate)`.
    - **Logic**:
        - Recursive function `buildFlatResourceList`.
        - Base case: Add item's direct resources (from `ItemSkeleton` / `pol_skeletons`).
        - Recursive step: For each sub-item, calculate factor (quantity / turnout) and recurse.
        - Aggregation: Sum quantities by `resource_id`.
        - Rate Calculation: Calculate rate for each resource using `RateAnalysisService` logic (or existing `getResourceRate`).

### 3. Controller: `ItemRateController` (or `SorController`?)
- The documentation suggests `ItemRateController`. If it doesn't exist, I'll create it. Or I could add it to `SorController` if it's lightweight. Given the specific nature, a dedicated controller or `ItemController` might be best. Let's check if `ItemRateController` exists.
- **Method**: `consumption(Request $request, Sor $sor, Item $item)`
    - Inputs: `rate_card_id` (optional, default 1), `date` (optional, default today).
    - Logic:
        - Fetch Rate Card and Date.
        - Call Service to get list.
        - Return view.

### 4. View: `sors/items/consumption.blade.php`
- Display Item Details.
- Table with columns: #, Group, Resource Name, Total Quantity, Unit, Rate, Total Amount.
- Grand Total footer.
- **Styling**: Use the existing glassmorphism/Tailwind styles.

## Step-by-Step Plan

1.  **Verify Services**: Check `App\Services\RateAnalysisService` and `App\Services\RateCalculationService`.
2.  **Verify Controllers**: Check if `ItemRateController` exists.
3.  **Implement Service Logic**: Add `getFlatResourceList` to the appropriate service.
4.  **Create/Update Controller**: Implement the `consumption` method.
5.  **Define Route**: Add the route in `web.php`.
6.  **Create View**: Build the Blade template.
7.  **Add Navigation**: Add a link to this page from the Item list or Rate Analysis page (e.g., in the context menu).

## Questions/Refinements
- **Sub-item Logic**: Does the `Item` model have a `subItems` relationship defined? Need to check `Item` model.
- **Skeleton Logic**: Does `Item` have `skeletons` relationship? Need to check `Item` model (likely `pol_skeletons` table).
