### `application/models/Resratemodel.php`

This is a critical model that handles all data operations related to resources and their rates. It's the engine that drives the cost calculation for individual resources, taking into account various factors like regional price variations, lead distances, and other indices.

**Methods:**

*   **`getResourceBasicDetails($resoruceid)`:**
    *   **Functionality:** This method fetches the fundamental details of a resource, such as its name, code, group (labor, material, or machine), and capacity information. This information is used throughout the application to identify and categorize resources.
    *   **Dependencies:** None.

*   **`getrate($resourceid = false, $ratecard = 1, $dt = false, $mode = true)`:**
    *   **Functionality:** This is a core method that calculates the final, fully-loaded rate of a resource. It starts with the base rate and then applies any applicable adjustments, such as lead charges for materials and index-based adjustments for labor and machinery. This method is central to the entire rate analysis process.
    *   **Parameters:**
        *   `$resourceid`: The ID of the resource.
        *   `$ratecard`: The Rate Card ID, which determines the regional rates and indices to be applied.
        *   `$dt` (optional): The date for which to calculate the rate.
        *   `$mode` (optional): A boolean to specify whether to use the current, locked-in rates (`true`) or experimental rates (`false`).
    *   **Dependencies:** `truckpertripcost`.

*   **`getlead($resourceid, $ratecard, $dt)`:**
    *   **Functionality:** This method calculates the lead rate for a material resource. Lead is the cost associated with transporting a material from its source to the work site. This method calculates the cost based on mechanical, manual, and mule lead distances, each of which has its own rate structure.
    *   **Dependencies:** `truckpertripcost`.

*   **`getLaborIndex($resourceid, $ratecard, $dt, $tablename)`:**
    *   **Functionality:** This method fetches the labor or machinery index for a given resource, rate card, and date. These indices are percentage-based adjustments that are applied to the base rates of labor and machinery to account for regional variations in cost.
    *   **Dependencies:** None.

*   **`getAllResList(...)`:**
    *   **Functionality:** This method retrieves a comprehensive list of all resources in the system. It supports filtering by resource group and capacity group, as well as pagination to handle large datasets.
    *   **Dependencies:** None.
