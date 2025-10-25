### `application/models/Ranamodel.php`

This is the core model for Rate Analysis (RA). It contains the primary logic for calculating the cost of an item by analyzing its constituent resources, sub-items, and overheads. It is the engine that drives the entire rate calculation process.

**Methods:**

*   **`getanalysis($racode = FALSE, $dt = FALSE, $rcard = FALSE, $currentRateMode = true)`:**
    *   **Functionality:** This method fetches the resource skeleton of an item for a given date and rate card. The "skeleton" is the list of resources (man, material, machine) and their quantities that are required to produce the item. The method then enriches this skeleton with the calculated rate for each resource.
    *   **Dependencies:** `resratemodel`.

*   **`getOverHeadAnalysis($racode = FALSE, $dt = false)`:**
    *   **Functionality:** This method retrieves the overhead analysis for an item. Overheads are additional costs that are not directly tied to a specific resource, such as administrative costs or profit margins. This method fetches the list of applicable overheads and their calculation parameters.
    *   **Dependencies:** None.

*   **`getSubItems($racode, $dt, $ratecard = FALSE, $sorid = false)`:**
    *   **Functionality:** This method fetches the sub-items of an item. Sub-items are other SOR items that are used as components in the production of the main item. This method retrieves the list of sub-items and their calculated rates.
    *   **Dependencies:** `itemrate` table.

*   **`resConsumption($racode, $dt, $rcard, $detail = false)`:**
    *   **Functionality:** This is a powerful method that calculates the total resource consumption for an item, including the resources of its sub-items. It recursively calculates the resource requirements for all nested sub-items, taking into account factors like turnout quantity and overhead applicability. This provides a complete and accurate picture of all the resources needed to produce a single unit of the final item.
    *   **Dependencies:** `Soritemmodel`, `unitmodel`.

*   **`openFileExcel(...)`:**
    *   **Functionality:** This method generates a detailed Rate Analysis report in Excel format. The report provides a comprehensive breakdown of the item's cost, including the contribution of each resource, sub-item, and overhead. This is a valuable tool for auditing and understanding the cost structure of an item.
    *   **Dependencies:** `PHPExcel` library.
