# SOR Chapter and Item Listing: Migration Plan

**Objective:** To migrate the functionality for listing and managing SOR (Schedule of Rates) chapters and items from the legacy CodeIgniter 3 application to a modern Laravel 12 implementation, enhancing the user experience and maintainability.

## 1. Analysis of the Legacy (CodeIgniter 3) System

The existing system provides three main ways to view and interact with SOR data.

### 1.1. Hierarchical Tree View

*   **Description:** An interactive tree view of the entire SOR, allowing users to see the hierarchy of chapters and items. It supports administrative actions like creating, editing, deleting, and re-ordering nodes via a context menu and drag-and-drop.
*   **Key Files & Methods:**
    *   **Controller:** `Sor.php` -> `showSORNode($sor)`
    *   **Model:** `Soritemmodel.php` -> `sornodejson($sor)` (generates JSON for the tree)
    *   **View:** `sornodes.php` (contains the jsTree implementation)
*   **Technology:** jsTree (JavaScript library)

### 1.2. Paginated Item List

*   **Description:** A flat, paginated list of all items within an SOR. It supports basic filtering by chapter and text search.
*   **Key Files & Methods:**
    *   **Controller:** `Sor.php` -> `soritems($sorcode, $ch)`
    *   **Model:** `Soritemmodel.php` -> `getsoritemlistWithRate(...)`
    *   **View:** `soritems.php` (a basic HTML table with CodeIgniter's pagination)
*   **Technology:** Standard HTML, CodeIgniter Pagination

### 1.3. Chapter Detail View

*   **Description:** Displays the immediate children (sub-chapters and items) of a specific chapter.
*   **Key Files & Methods:**
    *   **Controller:** `Sor.php` -> `chpterlist($chapterId, ...)`
    *   **Model:** `Soritemmodel.php` -> `sorChildItem($id, ...)`
    *   **View:** `chapterdetailview.php`
*   **Technology:** Standard HTML

### 1.4. Hierarchy Management

*   The hierarchy is managed using a **Nested Set Model** implemented with `lft` and `rgt` columns in the `nested_table_item` table. A stored procedure (`sor_tree_traversal`) is used for tree manipulation (insert, delete, move).
*   **Note:** The logic within the `sor_tree_traversal` stored procedure should be reviewed to ensure no custom business logic is missed when migrating to the `kalnoy/nestedset` package.

## 2. Proposed Laravel 12 Implementation

We will replicate and enhance the legacy functionality by creating three distinct, modern views, all powered by a robust, API-driven backend.

### 2.1. Technology Choices

*   **Hierarchy Management:** `kalnoy/nestedset`
    *   **Why:** This popular Laravel package provides an elegant and powerful API for working with nested set models. It eliminates the need for manual `lft`/`rgt` calculations and raw SQL queries or stored procedures, making the code cleaner and more maintainable.
*   **Backend API:** Laravel API Resources
    *   **Why:** To create a clean separation between the data layer and the presentation layer. This allows us to have a consistent and reusable way to format our JSON data for the frontend.
*   **Frontend Views:** Blade Components
    *   **Why:** For creating reusable UI elements, especially for the recursive hierarchical list.
*   **Interactive Tables:** `yajra/laravel-datatables-oracle`
    *   **Why:** This package is the standard for creating powerful, interactive tables in Laravel. It provides server-side processing for large datasets, along with searching, sorting, and filtering capabilities, offering a significant UX improvement over traditional pagination.
*   **Dynamic Frontend:** AJAX
    *   **Why:** To create a more responsive user experience by loading and manipulating data without requiring full page reloads. This will be used for the jsTree view and the Yajra DataTables view.

### 2.2. Proposed Views

#### View 1: Main 'Chapter and Items' SOR View (for General Users) -- (completed except Linked Items (`ref_from`))

*   **Description:** A read-only, indented list of all chapters and items in an SOR. Chapters will be collapsible to allow for easy navigation. This view will be the primary interface for users who need to browse the SOR.
*   **Key Features:**
    *   **Rate Filtering:** Users will be able to select a **Rate Card** and a **Date** from dropdowns/date pickers. The displayed rates for all items will update based on these selections.
    *   **Collapsible Chapters:** Users can expand and collapse chapters to navigate the tree.
    *   **Rate Display:** If a rate is not available for the selected criteria, an informational message (e.g., "N/A") will be displayed.
    *   **Linked Items (`ref_from`):** The view will correctly handle items that reference other items, displaying the referenced item's data and linking to it appropriately.
*   **Implementation:**
    *   A `SorController@show` method will fetch the tree structure and the relevant rates.
    *   A recursive Blade component will be used to render the hierarchy.
    *   Simple JavaScript (e.g., using Bootstrap's Collapse component) will be used for the collapsible functionality.

#### View 2: Advanced Data Table View -- (completed )

*   **Description:** A flat, searchable, and sortable table of all items in the SOR.
*   **Key Features:**
    *   Server-side processing for performance with large datasets.
    *   Global and column-specific search.
    *   Sorting by any column.
*   **Implementation:**
    *   We will use the **Yajra DataTables** package.
    *   The table will be populated by an AJAX call to a dedicated API endpoint that provides the data in the format required by DataTables.

#### View 3: Administrative Node Tree View -- (in progress)

*   **Description:** An interactive tree view for administrators to manage the SOR structure.
*   **Key Features:**
    *   **Drag and Drop:** Re-order chapters and items.
    *   **Context Menu:** Create, edit, and delete nodes (chapters/items).
*   **Implementation:**
    *   We will use the **jsTree** library, as in the legacy system.
    *   The tree will be populated by an AJAX call to a dedicated API endpoint.
    *   All actions (move, create, delete) will trigger AJAX calls to the backend, which will use the `kalnoy/nestedset` methods to update the tree.



## 3. Implementation Steps & Progress Tracking

This section will serve as a checklist for the implementation process. Please update the status of each step as you proceed.

**Step 1: Setup & Migration**
*   **Task:** Install `kalnoy/nestedset`. migrations for `sors`, `items` (with nested set columns), and `item_rates` tables already avilable.
*   **Status:** Completed

**Step 2: Data Migration & Seeding**
*   **Task:**  already done , all tables are avilable check
*   **Status:** Completed

**Step 3: Models**
*   **Task:** Revisit `Sor`, `Item`, and `ItemRate` models. Configure the `Item` model to use `kalnoy/nestedset`. Define the relationships if not exist: `Sor` hasMany `Item`, `Item` hasMany `ItemRate`. Add the `getRateFor($ratecard, $date)` method to the `Item` model.
*   **Status:** Completed

**Step 4: Chapter and Items View**
*   **Task:** Create the `SorController@show` method. Create the recursive Blade component for rendering the tree. Implement the filter UI and the collapsible chapter functionality.
*   **Status:** Completed

**Step 5: Advanced Data Table View**
*   **Task:** Install `yajra/laravel-datatables-oracle`. Create the controller method and API endpoint for the DataTables data. Create the view and initialize the DataTable with server-side processing.
*   **Status:** Completed

**Step 5: administrative Node Tree View**
*   **Status:** In Progress
    *   **Sub-task:** Create a new chapter via context menu and save its name.

#### **Sub-step 5.1: Implement Node Creation (`create_node.jstree`)**

*   **Status:** `✅ Completed`
*   **Actions Taken:**
    *   The `create_node.jstree` event handler is implemented in `resources/views/sors/admin.blade.php`.
    *   It correctly determines `parent_id` and `item_type`.
    *   It sends an AJAX `POST` request to the `api.sors.tree.create` endpoint.
    *   The `SorController@createNode` method on the backend handles the creation logic, including generating the correct `item_code` and `order_in_parent`.
    *   On success, the frontend updates the new node's ID and text with the data returned from the server.

---

#### **Sub-step 5.2: Implement Node Renaming (`rename_node.jstree`)**

*   **Status:** `✅ Completed`
*   **Actions Taken:**
    1.  **Frontend (`admin.blade.php`):**
        *   The `rename_node.jstree` event handler has been implemented.
        *   It captures the node's ID, new text, old text, and `item_type`.
        *   It parses the new text into `item_number` and `description` based on `item_type`.
        *   It sends an AJAX `PUT` request to the `api.sors.tree.update` endpoint.
        *   Error handling is in place to revert the node's text and show an alert on failure.
    2.  **Backend (`SorController.php`):**
        *   The `updateNode` method has been modified.
        *   It validates `description` (required) and `item_number` (nullable).
        *   It updates the `item` model's `description` and `item_number` fields.
        *   It returns a JSON success response.

---

#### **Sub-step 5.3: Implement Node Deletion (`delete_node.jstree`)**

*   **Status:** `✅ Completed`
*   **Actions Taken:**
    1.  **Frontend (`admin.blade.php`):**
        *   The `delete_node.jstree` event handler has been implemented.
        *   It prompts for user confirmation before deletion.
        *   It sends an AJAX `DELETE` request to the `api.sors.tree.delete` endpoint.
        *   Error handling is in place to refresh the tree and show an alert on failure.
    2.  **Backend (`SorController.php`):**
        *   The `deleteNode` method has been modified.
        *   It includes validation to prevent deletion of chapters/sub-chapters with children.
        *   It performs the deletion within a database transaction.
        *   It returns a JSON success response or an error response with a message.

---

#### **Sub-step 5.4: Implement Node Movement (`move_node.jstree`)**

*   **Status:** `✅ Completed`
*   **Actions Taken:**
    1.  **Frontend (`admin.blade.php`):**
        *   The `move_node.jstree` event handler has been implemented.
        *   It extracts the node ID, new parent ID, and new position.
        *   It sends an AJAX `POST` request to the `api.sors.tree.move` endpoint.
        *   Error handling is in place to refresh the tree and show an alert on failure.
    2.  **Backend (`SorController.php`):**
        *   The `moveNode` method has been modified.
        *   It validates the incoming data.
        *   It applies hierarchy rules to prevent items from becoming parents.
        *   It uses `kalnoy/nestedset` methods (`prependToNode`, `appendToNode`, `beforeNode`) to perform the move based on the `position`.
        *   It re-calculates and updates the `order_in_parent` for affected siblings and the moved item.
        *   It returns a JSON success response or an error response.
