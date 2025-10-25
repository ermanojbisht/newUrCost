### `application/models/Additemsmodel.php`

This model provides the database operations needed by the `Additemscontroller`. It contains a collection of methods for inserting, updating, and retrieving data related to the various components of the SOR system.

**Methods:**

*   **`insertChapter($data)` and `updateTable(...)`:**
    *   **Functionality:** These are generic methods for inserting and updating data in the database. `insertChapter` is specifically for adding new chapters, while `updateTable` is a more general-purpose method that can update any table given the table name, data, and matching conditions.
    *   **Dependencies:** None.

*   **`getChapterList($sorid)` and `getItemsList($chapterId=FALSE)`:**
    *   **Functionality:** These methods are used to retrieve lists of chapters and items. `getChapterList` returns a list of all chapters within a given SOR, while `getItemsList` can retrieve all items in the system or be filtered to show only the items within a specific chapter.
    *   **Dependencies:** None.

*   **`insertRatecard($data)` and `updateRatecard($data, $ratecardgetid)`:**
    *   **Functionality:** These methods are responsible for adding and updating rate cards in the database. `insertRatecard` adds a new rate card, while `updateRatecard` modifies an existing one.
    *   **Dependencies:** None.

*   **`saveresource($data)`:**
    *   **Functionality:** This method saves a new resource to the database. It takes an array of resource data as input and inserts it into the `resource` table.
    *   **Dependencies:** None.
