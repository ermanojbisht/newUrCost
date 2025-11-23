# **Detailed Note on SOR Item Skeleton and Rate Analysis View**

## **1. Overview**

Each SOR (Schedule of Rates) item consists of a predefined structure called the **Skeleton**.
The Skeleton represents the fundamental composition of an item in terms of:

* **Labour (Manpower)**
* **Materials**
* **Machinery**
* **Sub-items**
* **Overheads**

This structure allows systematic entry, modification, and computation of item rates based on selected **Rate Card** and **Effective Date**.

---

## **2. Resource Database**

A central **Resource Table** exists in the system containing all available resources. Each resource carries information regarding its:

* **Type:** Labour / Material / Machinery
* **Code:** Unique identifier
* **Name & Description**
* **Unit of Measurement (UoM)**
* **secondary_code **

Users can select resources from this master table while building or editing a Skeleton.

---

## **3. Skeleton Management Features**

### **3.1 Adding Resources**

When the user chooses to add a new resource to the Skeleton:

1. User clicks **Add Resource**
2. System prompts to select:
   * Resource Type (Labour / Material / Machinery)  --resource_group_id
   * Resource Name (from dropdown/table with smart search) --name
   * Or directly enter **Resource Code** (if known) --secondary_code 
   * Or directly enter **Resource Id** (if known) --id 
3. Resource is added to the Skeleton with **default quantity = 0**
4. Each row includes an **Edit** button for modifying:

   * Resource code / selection
   * Quantity
   * Unit

### **3.2 Editing Resources**

User can edit any resource row using the “Edit” option to change:

* Resource type
* Resource selection
* Quantity
* Unit

### **3.3 Adding Sub-Items**

Sub-items can also be added to the Skeleton:

1. User selects **Add Sub-Item**
2. Finds sub-item using:

   * Chapter
   * Item code
   * Smart search or filter
3. Sub-item is added with **default quantity = 0**
4. Quantity can be edited later.

### **3.4 Adding Overheads**

System supports overhead components such as:

* Labour cess
* GST
* Tools & plant
* Contractor’s profit
* Any defined overhead category

User workflow:

1. Select overhead type
2. Define the scope:

   * Applicable to the entire item
   * Applicable to a sub-head
   * Applicable only to materials
   * Applicable only to labour
3. Overhead added to Skeleton with adjustable values.

---

## **4. Rate Card and Date Selection**

* At the top of the form, user must select:

  * **Rate Card**
  * **Applicable Date**
* If stored in session, system uses session values.
* If not present, default values:

  * Rate Card = **1**
  * Date = **Current Date**
* The same session-handling logic is already implemented in other views and will be reused here.

---

## **5. Calculation of Item Rate**

* System calculates rate based on the Skeleton, Rate Card, and Date.
* If dynamic calculation is heavy, a **“Calculate Now”** button will be provided.
* On saving the Skeleton, calculation is automatically performed.

---

## **6. Copying Skeleton From Another Item**

User can copy Skeleton from an existing item, including:

* All resources
* Sub-items
* Overheads

This helps in quickly generating similar items.

---

## **7. Permission & Role Management**

* System will include role-based permissions defining which users can:

  * View Skeleton
  * Edit Skeleton
  * Add or delete components
* Only authorized users will have editing rights.

---

## **8. Dark/Light Theme Support**

* The Skeleton and Analysis view will fully support the application’s **Dark** and **Light** themes.
* Layout will be optimized to show many resources compactly.
* Padding and margins will be minimal to avoid clutter but maintain readability.

---

# **9. Rate Analysis View (Read-Only View)**

Rate Analysis View is different from Skeleton View:

### **Key Characteristics:**

* **Read-only**, no editing of resources, quantities, or overheads.
* Displays a comprehensive view of:

  * Resources
  * Sub-items
  * Overheads
  * Rate breakup
  * Total item rate

### **UI/UX Features:**

* Resources listed (left side)
* Overheads listed (right side) OR stacked layout
* Icons for:

  * Manpower
  * Material
  * Machinery
* Color-coded sections for better visibility
* **Pie charts** showing:

  * Percentage share of each resource group (Man/Material/Machinery)
  * Contribution of overheads
  * Cost distribution
* Footnotes and metadata including:

  * Item number
  * Item name
  * SOR reference
  * Specifications
  * Chapter links
  * Notes links

Aimed to be a **visually rich and comprehensive item representation**.

---

# **10. Summary**

The system provides complete functionality for:

* Building and editing SOR item Skeletons
* Adding/deleting resources, sub-items, overheads
* Managing rate card and date logic
* Copying existing Skeletons
* Performing rate analysis
* Presenting data in a compact, theme-compatible, user-friendly layout
* Offering advanced visualization for analysis

Both **Skeleton View (editable)** and **Analysis View (read-only)** collectively ensure accurate and transparent item rate composition in the SOR system.

---

## **11. Implementation Details & Learnings (Nov 2025)**

### **11.1 Overhead Management Implementation**
*   **Modal-Based Editing:** Implemented using a shared modal (`#modal-add-overhead`) for both adding and editing.
*   **Data Handling:**
    *   **Item ID vs Item Code:** The `oheads` table links to `items` via `item_id` column, but logically it stores the `item_code` (not the auto-increment `id`). This was a critical finding. All controller methods (`addOverhead`, `updateOverhead`, `removeOverhead`, `reorderOverheads`) must use `$item->item_code` for ownership checks and queries.
    *   **Percentage Storage:** Overhead parameters for non-lumpsum types are stored as decimals (e.g., 0.10 for 10%). The controller automatically divides user input by 100 if the calculation type is not 0 (Lumpsum). The service layer returns both `parameter` (percentage, e.g., 10) and `raw_parameter` (decimal, e.g., 0.10) to handle display and editing correctly.
    *   **Description Handling:** The service returns a formatted `description` (e.g., "10% on Labor") and a `raw_description` (user's custom text). The Edit Modal uses `raw_description` for the input field to avoid overwriting custom text with the auto-generated one.
    *   **Dropdown Display:** The Edit Modal's "Overhead Type" dropdown needs the base overhead name. `ItemSkeletonService` was updated to include `overhead_name` (from `OverheadMaster`) to ensure the dropdown displays "Labor Cess" instead of "1% on Labor".

### **11.2 Frontend-Backend Integration**
*   **Select2 Quirks:** When using Select2 inside a modal, the `dropdownParent` option MUST be set to the modal's ID (e.g., `$('#modal-add-overhead')`). Failure to do so results in the dropdown not opening or closing immediately.
*   **Route Definitions:** API-like routes for overhead management (`PUT`, `POST` for reorder) were defined in `web.php` to support the AJAX calls.
*   **Single Source of Truth:** `ItemSkeletonService` is the central place for calculating rates and preparing data for the view. It returns a comprehensive JSON structure including resources, sub-items, overheads, and totals, minimizing the need for separate AJAX calls for each section.

### **11.3 Current State (as of Nov 23, 2025)**
*   **Resources:** Fully functional (Add, Edit, Delete, Reorder).
*   **Overheads:** Fully functional (Add, Edit, Delete, Reorder).
*   **Sub-items:** Basic Add/Remove functionality exists.
*   **Rate Analysis:** The view correctly calculates and displays the final rate based on all components.


