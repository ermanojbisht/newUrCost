### `application/models/Sormodel.php`

This model is responsible for handling all database operations related to the Schedule of Rates (SORs). It provides the fundamental methods for retrieving and managing SOR data.

**Methods:**

*   **`getsorlist($sorId=false, $sormgtuser=FALSE, $superUser =FALSE)`:**
    *   **Functionality:** This method fetches a list of SORs from the database. It includes logic to handle user permissions, ensuring that users can only see the SORs they are authorized to view. Superusers and SOR managers can see all SORs, while other users are restricted to a specific list of SORs.
    *   **Parameters:**
        *   `$sorId` (optional): If an SOR ID is provided, the method will fetch only that specific SOR.
        *   `$sormgtuser` (optional): A boolean indicating if the user is an SOR manager.
        *   `$superUser` (optional): A boolean indicating if the user is a superuser.
    *   **Dependencies:** None.

*   **`getsorname($i)`:**
    *   **Functionality:** This is a simple helper method that returns the name of an SOR given its ID.
    *   **Parameters:**
        *   `$i`: The ID of the SOR.
    *   **Dependencies:** None.
