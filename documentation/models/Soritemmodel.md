### `application/models/Soritemmodel.php`

This model is responsible for managing SOR items, including their properties and their position in the SOR's hierarchical structure. It provides a wide range of methods for retrieving, manipulating, and organizing SOR items.

**Methods:**

*   **`getsoritemlistWithRate(...)` and `getsoritemlist(...)`:**
    *   **Functionality:** These methods are used to fetch a list of SOR items. `getsoritemlistWithRate` retrieves the items along with their calculated rates for a specific rate card, while `getsoritemlist` fetches the items without their rates. Both methods support pagination and filtering.
    *   **Dependencies:** `itemrate` table.

*   **`getsoritemBasicDetails($raitem)`:**
    *   **Functionality:** This method retrieves the basic details of a single SOR item, such as its name, description, turnout quantity, and unit. This is a frequently used method for getting the fundamental properties of an item.
    *   **Dependencies:** None.

*   **`itemPath($rcode, $sorid)`:**
    *   **Functionality:** This method returns the hierarchical path of an item within the SOR. It traverses the `nested_table_item` table to construct a path from the root of the SOR down to the specified item, showing the parent-child relationships.
    *   **Dependencies:** `nested_table_item` table.

*   **`sornodejson($sor, $namingOption=1)`:**
    *   **Functionality:** This method generates a JSON representation of the SOR's tree structure. This is specifically designed to be used with a JavaScript library like jsTree to display an interactive, hierarchical view of the SOR, allowing users to browse and manage the SOR's structure.
    *   **Dependencies:** `nested_table_item` table.

*   **`deleteNode($sorId, $nodeId)`, `moveNode($sorId, $nodeId, $under_node_id)`, `orderNode($sorId, $nodeId, $under_node_id)`:**
    *   **Functionality:** These methods are used to manage the tree structure of the SOR. They call a stored procedure (`sor_tree_traversal`) to perform the actual database operations for deleting, moving, and reordering nodes in the `nested_table_item` table. This provides a robust way to manage the SOR's hierarchy.
    *   **Dependencies:** `sor_tree_traversal` stored procedure.
