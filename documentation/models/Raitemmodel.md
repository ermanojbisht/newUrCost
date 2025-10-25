### `application/models/Raitemmodel.php`

This model acts as a helper for rate analysis, primarily dealing with item and chapter naming and hierarchy. It provides methods for constructing the full name of an item and for retrieving details about chapters.

**Methods:**

*   **`createName($racode)`:**
    *   **Functionality:** This method constructs the full, hierarchical name of an item by traversing up through its parent chapters. It concatenates the descriptions of the item and all its parent chapters to create a complete, descriptive name that reflects the item's position in the SOR structure.
    *   **Parameters:**
        *   `$racode`: The Rate Analysis code of the item.
    *   **Dependencies:** None.

*   **`getChapterDetails($chId)`:**
    *   **Functionality:** This method retrieves the details of a chapter, including its parent chapters. It recursively fetches the details of each parent chapter until it reaches the root of the SOR, providing a complete path from the root to the specified chapter.
    *   **Parameters:**
        *   `$chId`: The ID of the chapter.
    *   **Dependencies:** None.
