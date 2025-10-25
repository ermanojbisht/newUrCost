### `application/controllers/Resourcectr.php`

This controller is dedicated to managing the application's resources, which are the fundamental building blocks of the rate analysis system (Man, Material, Machine).

**Methods:**

*   **`reslist($sorcode, $resGrp=false, $resCapacityGr=false, $rowsinpagination=false)`:**
    *   **Functionality:** This method provides a comprehensive list of all resources associated with a particular Schedule of Rates (SOR). It supports pagination to handle large numbers of resources and allows for filtering by resource group (e.g., labor, material, machine) and by resource capacity group. This enables users to easily navigate and find specific resources within an SOR.
    *   **Parameters:**
        *   `$sorcode`: The code of the SOR for which to list the resources.
        *   `$resGrp` (optional): The resource group ID to filter the list.
        *   `$resCapacityGr` (optional): The resource capacity group ID to further filter the list.
        *   `$rowsinpagination` (optional): The number of resources to display per page.
    *   **Dependencies:** `resratemodel`, `jobsmodel`, `Rcardmodel`, `Sormodel`.

*   **`makeResFile($sor=4, $ratecard=1, $file='xlsx', $mode=true, $dt=false)`:**
    *   **Functionality:** This method generates an Excel file containing a list of resources and their corresponding rates for a specific SOR and rate card. This is useful for offline analysis and for sharing resource rate information with other stakeholders.
    *   **Parameters:**
        *   `$sor`: The ID of the SOR.
        *   `$ratecard`: The ID of the Rate Card.
        *   `$file` (optional): The file format for the report (defaults to 'xlsx').
        *   `$mode` (optional): A boolean to specify whether to use the current rates (`true`) or experimental rates (`false`).
        *   `$dt` (optional): The date for which to calculate the rates.
    *   **Dependencies:** `resratemodel`.

*   **`onmap($resourceid, $datatype=1)`:**
    *   **Functionality:** This method provides a geographical visualization of various resource-related data points on a map of Uttarakhand. It can display lead distances for materials, labor and machinery index values, or the final calculated rate of a resource. This allows for a quick and intuitive understanding of how costs vary across different regions.
    *   **Parameters:**
        *   `$resourceid`: The ID of the resource to be visualized.
        *   `$datatype`: An integer representing the type of data to be displayed on the map (e.g., 1 for mechanical lead, 4 for labor index, 6 for resource rate).
    *   **Dependencies:** `resratemodel`, `jobsmodel`.
