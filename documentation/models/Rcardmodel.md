### `application/models/Rcardmodel.php`

This model is responsible for all database operations related to Rate Cards. Rate Cards are a crucial component of the system, as they define the regional variations in resource costs.

**Methods:**

*   **`getrcards()`:**
    *   **Functionality:** This method retrieves a list of all available rate cards from the database. This is used to populate dropdown menus and other UI elements where a user needs to select a rate card.
    *   **Dependencies:** None.

*   **`getRateCardName($ratecardid)`:**
    *   **Functionality:** A simple helper method that returns the name of a rate card when given its ID.
    *   **Parameters:**
        *   `$ratecardid`: The ID of the rate card.
    *   **Dependencies:** None.

*   **`getIndexValues($ratecard, $indextype, $onDateInSecond=false)`:**
    *   **Functionality:** This method is used to fetch the labor and machinery index values for a given rate card and date. These index values are percentage-based multipliers that are applied to the base rates of labor and machinery resources to account for regional cost variations.
    *   **Parameters:**
        *   `$ratecard`: The ID of the rate card.
        *   `$indextype`: The type of index to retrieve (1 for labor, 2 for machinery).
        *   `$onDateInSecond` (optional): The date for which to retrieve the index values (in seconds).
    *   **Dependencies:** None.
