### `application/controllers/RateMap.php`

This controller is responsible for displaying a map-based visualization of resource rates.

**Methods:**

*   **`resourceMap($resourceid = 6336)`:**
    *   **Functionality:** This method provides a geographical representation of the rate of a specific resource across different regions (rate cards). It fetches the rate history for the given resource and organizes it by rate card. The data is then passed to a view that uses a map of Uttarakhand to visualize the rate variations, likely using color-coding to represent different rate levels in different blocks. This provides a quick and intuitive way to understand the geographical differences in resource costs.
    *   **Parameters:**
        *   `$resourceid`: The ID of the resource to be displayed on the map.
    *   **Dependencies:** `Rcardmodel`, `jobsmodel`.
