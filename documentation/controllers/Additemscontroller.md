### `application/controllers/Additemscontroller.php`

This controller is the administrative heart of the application, responsible for managing all the core components of the Schedule of Rates (SOR) system. It provides a suite of methods for adding, updating, and publishing SORs, chapters, items, resources, and rate cards.

**Methods:**

*   **`addsoritem($chId)`:**
    *   **Functionality:** This method displays a form that allows authorized users to add a new item to a specific chapter within an SOR. It fetches the necessary data, such as the list of units and the details of the parent chapter, to populate the form.
    *   **Parameters:**
        *   `$chId`: The ID of the chapter to which the new item will be added.
    *   **Dependencies:** `Additemsmodel`, `Raitemmodel`, `Unitmodel`, `jobsmodel`.

*   **`updatesoritem()`:**
    *   **Functionality:** This method processes the data submitted from the `addsoritem` form. It validates the input, sanitizes the data, and then updates the item's details in the database. It also handles the generation of a new item code and the creation of the item's full name.
    *   **Dependencies:** `Additemsmodel`, `Raitemmodel`, `Soritemmodel`.

*   **`createChapter($chId)`:**
    *   **Functionality:** This method provides an interface for editing the descriptive text (language) of a chapter. It fetches the existing chapter details and displays them in a form for modification.
    *   **Parameters:**
        *   `$chId`: The ID of the chapter to be edited.
    *   **Dependencies:** `Raitemmodel`, `Sormodel`, `jobsmodel`.

*   **`updatechapter()`:**
    *   **Functionality:** This method handles the submission of the `createChapter` form. It validates the input and updates the chapter's short and long descriptions in the database.
    *   **Dependencies:** `Additemsmodel`, `Soritemmodel`.

*   **`addsor()` and `savesor()`:**
    *   **Functionality:** These methods work in tandem to create a new Schedule of Rates (SOR). `addsor` displays the form for creating a new SOR, and `savesor` processes the submitted data, validates it, and inserts the new SOR into the database.
    *   **Dependencies:** `Sormodel`, `Editsormodel`, `jobsmodel`.

*   **`editsor($sorid)` and `updatesor()`:**
    *   **Functionality:** These methods are used for editing and updating an existing SOR. `editsor` displays a form pre-filled with the SOR's current details, and `updatesor` processes the form submission to update the SOR in the database.
    *   **Dependencies:** `Sormodel`, `Editsormodel`, `Additemsmodel`, `jobsmodel`.

*   **`publishsor($sorid, $locked)`:**
    *   **Functionality:** This method allows an administrator to publish or unpublish an SOR, making it visible or hidden to regular users.
    *   **Dependencies:** `Editsormodel`, `jobsmodel`.

*   **`editskeleton($racode = false, $chId = false)`:**
    *   **Functionality:** This is a crucial administrative method that allows for the detailed editing of an item's Rate Analysis (RA) skeleton. It provides a comprehensive interface for modifying the resources, sub-items, and overheads that make up an item's cost structure. This is where the fundamental composition of an item is defined and maintained.
    *   **Dependencies:** `Ranamodel`, `Raitemmodel`, `Soritemmodel`, `Rcardmodel`, `jobsmodel`.

*   **`addresource()` and `saveresource()`:**
    *   **Functionality:** These methods are used to add new resources (man, material, or machine) to the system. `addresource` displays the form for adding a new resource, and `saveresource` processes the submitted data and saves the new resource to the database.
    *   **Dependencies:** `Additemsmodel`, `Unitmodel`, `resratemodel`, `jobsmodel`.
