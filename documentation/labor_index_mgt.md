# Labor Index Management Process

## Overview
The Labor Index system allows for the adjustment of base labor rates based on specific Rate Cards and time periods. This ensures that cost estimations remain accurate across different project conditions and timelines without modifying the core resource base rates.

## Index Hierarchy & Fallback System

The system implements a hierarchical fallback logic to determine the applicable index for a given Resource and Rate Card.

### 1. Resource-Specific Index
*   **Priority**: Highest.
*   **Definition**: An index defined specifically for a Resource ID and a Rate Card ID.
*   **Use Case**: When a specific laborer (e.g., "Senior Mason") has a unique rate adjustment for a specific project/rate card.

### 2. Global (Fallback) Index
*   **Priority**: Secondary (used if no Resource-Specific index exists).
*   **Definition**: An index defined for the pseudo-resource "Global" (ID: 1).
*   **Use Case**: General adjustments applicable to ALL labor resources for a specific Rate Card (e.g., "2025 Inflation Adjustment" applying to all labor).

### 3. Default Rate Card Fallback
*   If no index is found for the specific Rate Card (neither specific nor global), the system falls back to the **Default Rate Card** (ID: 1).

**Lookup Order:**
1.  Specific Resource + Specific Rate Card
2.  Global Resource (ID 1) + Specific Rate Card
3.  Specific Resource + Default Rate Card (ID 1)
4.  Global Resource (ID 1) + Default Rate Card (ID 1)

---

## Locking System & Lifecycle

To ensure historical data integrity, indices follow a strict lifecycle managed by the `is_locked` status.

### Status Definitions

| Status ID | Name | Badge Color | Description | Permissions |
| :--- | :--- | :--- | :--- | :--- |
| **0** | **Experimental** | Blue | Newly created drafts. | **Editable, Deletable, Promotable** |
| **1** | **Current** | Green | The active, approved rate. | **Read-Only** (Cannot edit/delete) |
| **2** | **Old** | Gray | Historical/Archived rates. | **Read-Only** (Cannot edit/delete) |

### Workflow

1.  **Creation**:
    *   All new indices are created with **Status 0 (Experimental)**.
    *   Users can edit the value, dates, or delete the index freely in this state.

2.  **Locking (Promotion)**:
    *   Users click the **Lock Icon** to promote an Experimental index to Current.
    *   **Input**: User must confirm the `valid_from` date (Effective Date).

3.  **Transition Logic**:
    When an index (Rate A) is locked:
    *   **Step 1**: The system searches for the *existing* **Current** index (Rate B) for the same Resource and Rate Card.
    *   **Step 2**: If Rate B exists:
        *   Rate B status changes to **Old (2)**.
        *   Rate B `valid_to` is automatically set to `Rate A.valid_from - 1 day`.
    *   **Step 3**: Rate A status changes to **Current (1)**.
        *   Rate A `valid_to` is set to `NULL` (Open-ended) or preserved if manually set.

---

## Date Management

*   **Valid From**: Mandatory for all indices. Determines when the rate becomes effective.
*   **Valid To**: Optional.
    *   If `NULL`, the rate is valid indefinitely (until a new rate supersedes it).
    *   When a new "Current" rate is introduced, the previous rate's `valid_to` is strictly capped to the day before the new rate starts, ensuring no overlap and continuous coverage.

## Global Index Management

*   Accessed via a dedicated "Manage Global Indices" page (or via the link on any Resource Index page).
*   Functionality is identical to Resource-Specific management but applies to Resource ID 1.
*   Global indices appear as a footnote on Resource-Specific pages to inform users of the fallback values currently in effect.

## Bulk Management
For managing indices across multiple resources efficiently, use the **Bulk Index Editor**.
*   **Access**: Sidebar > SOR Administration > Labor Indices
*   **Features**: Filter by Resource/Rate Card, add new indices for any resource, and manage experimental rates in a centralized view.
*   See [Bulk Index Editor Documentation](indices_bulk_editor.md) for more details.
