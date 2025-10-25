
# Overhead Master Table

This table stores the master list of all overheads.

## Old System

| Column | Type | Description |
|---|---|---|
| `ID` | int | Primary key for the table. |
| `vOHCode` | longtext | The code of the overhead. |
| `Flag` | tinyint unsigned | Not clear from the code. |
| `OrgID` | int | The organization ID. |
| `vdescription` | longtext | The description of the overhead. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | - |
| `vOHCode` | `code` | `string` | The code of the overhead. | Renamed for clarity. |
| `Flag` | `flag` | `boolean` | Not clear from the code. | - |
| `vdescription` | `description` | `text` | The description of the overhead. | Renamed for clarity. |

### Fields left behind
OrgID as not needed
No fields were left behind.
