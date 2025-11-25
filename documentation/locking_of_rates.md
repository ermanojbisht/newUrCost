# Rate Locking Logic

## Overview
The system implements a state-based locking mechanism for Resource Rates to ensure data integrity and historical accuracy. Rates transition through states from Experimental to Current, and finally to Old.

## Rate States (`is_locked`)

| Value | State | Description | Editable? |
| :--- | :--- | :--- | :--- |
| `0` | **Experimental** | Newly created rates that are being drafted or tested. | **Yes** |
| `1` | **Current** | The active, approved rate currently in use. | **No** |
| `2` | **Old** | Historical rates that are no longer active. | **No** |

## Workflow

1.  **Creation**:
    *   New rates are always created with `is_locked = 0` (Experimental).
    *   These rates can be edited or deleted freely.

2.  **Locking (Promotion to Current)**:
    *   An action (button) is available to "Lock" an Experimental rate.
    *   **Input Required**: `valid_from` date for the new Current rate.

    **Logic when locking Rate A (Experimental -> Current):**
    1.  **Identify Previous Current Rate**: Find the existing rate (Rate B) for the same **Resource** and **Rate Card** where `is_locked = 1`.
    2.  **Update Previous Rate (Rate B)**:
        *   Set `is_locked = 2` (Old).
        *   Set `valid_to` = `Rate A.valid_from - 1 day`.
    3.  **Update New Rate (Rate A)**:
        *   Set `is_locked = 1` (Current).
        *   Set `valid_from` = Supplied date (or existing `valid_from`).
        *   Set `valid_to` = `NULL`/2038-01-19 (or specific date if known, usually open-ended for current).

## Constraints & Rules

*   **Edit Restriction**: Rates with `is_locked` status `1` (Current) or `2` (Old) **cannot** be edited or deleted. The UI must hide/disable edit/delete actions, and the Controller must enforce this check.
*   **Single Current Rate**: There should ideally be only one `is_locked = 1` rate per Resource per Rate Card at any given time.
*   **History Preservation**: Old rates (`is_locked = 2`) provide the audit trail and historical pricing.

## Implementation Details

### UI Changes
*   **Rate List**:
    *   Show a "Lock" button/icon for rates with `is_locked = 0`.
    *   Hide/Disable "Edit" and "Delete" buttons for rates with `is_locked > 0`.
    *   Visual indicator of rate status (e.g., badge).
*   **Lock Modal**:
    *   A confirmation modal when clicking "Lock".
    *   Input field for `valid_from` date.

### Backend Changes
*   **Controller (`ResourceRateController`)**:
    *   `update`/`destroy`: Add check `if ($rate->is_locked > 0) abort(403);`.
    *   `lock` (New Method): Handle the transaction to update the new rate and the old rate.
