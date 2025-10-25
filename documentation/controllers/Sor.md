### `application/controllers/Sor.php`

This is the main controller for managing Schedule of Rates (SOR). It handles everything from listing SORs and their items to performing complex rate analysis and generating reports.

**Methods:**

*   **`sorlist($sorId = false)`:**
    *   **Functionality:** This method is responsible for displaying a list of all available Schedule of Rates (SORs). It checks if a user is logged in and then retrieves the list of SORs from the `Sormodel`. It also handles user permissions, showing all SORs to superusers and SOR managers, while restricting the view for other users based on their assigned permissions.
    *   **Parameters:**
        *   `$sorId` (optional): If an SOR ID is provided, it will only list the details for that specific SOR.
    *   **Dependencies:** `Sormodel`, `jobsmodel`.

*   **`soritems($sorcode = false, $ch = false, $currentRateMode = true)`:**
    *   **Functionality:** This method displays the items within a specific SOR. It's a feature-rich method that includes pagination, searching, and filtering capabilities. It can display either the current, locked-in rates or experimental rates, depending on the user's permissions. It also handles search functionality, allowing users to search for items by text or by region.
    *   **Parameters:**
        *   `$sorcode`: The code of the SOR to display.
        *   `$ch` (optional): A chapter ID to filter the items by a specific chapter.
        *   `$currentRateMode` (optional): A boolean to indicate whether to use the current rates (`true`) or experimental rates (`false`).
    *   **Dependencies:** `Sormodel`, `Soritemmodel`, `Rcardmodel`, `jobsmodel`.

*   **`raskeleton($racode = -111, $dt = false, $rcard = false)`:**
    *   **Functionality:** This is the core of the Rate Analysis (RA) functionality. It displays the detailed breakdown of a single SOR item, known as the RA skeleton. This includes a list of all the resources (man, material, machine) and their quantities, any sub-items that are part of the item's composition, and a detailed breakdown of all applicable overheads. It also generates graphical representations of the resource and overhead consumption.
    *   **Parameters:**
        *   `$racode`: The Rate Analysis code of the item to be analyzed.
        *   `$dt` (optional): The date for which to calculate the rates (in seconds). If not provided, it defaults to the current date.
        *   `$rcard` (optional): The Rate Card ID to be used for the calculation. If not provided, it defaults to the appropriate rate card based on the SOR.
    *   **Dependencies:** `Ranamodel`, `Raitemmodel`, `Soritemmodel`, `Rcardmodel`, `jobsmodel`, `Unitmodel`.

*   **`updaterate(...)` and `updaterate_forA_Ra(...)`:**
    *   **Functionality:** These are powerful methods used for batch-updating the rates of SOR items. `updaterate` can update the rates for an entire SOR, while `updaterate_forA_Ra` updates the rate for a single item and all of its dependent sub-items. These methods are crucial for keeping the SOR rates up-to-date when resource rates or other factors change.
    *   **Dependencies:** `Sormodel`, `Soritemmodel`, `Ranamodel`, `Rcardmodel`, `Site`.

*   **`makera(...)` and `makeSorInExcel(...)`:**
    *   **Functionality:** These methods are used to generate Excel reports. `makera` generates a detailed Rate Analysis report for a given SOR, while `makeSorInExcel` generates a complete SOR with all its items and rates.
    *   **Dependencies:** `Ranamodel`.
