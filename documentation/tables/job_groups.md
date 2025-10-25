
# Job Groups Table

This table stores the job groups.

## Old System

| Column | Type | Description |
|---|---|---|
| `grId` | int | Primary key for the table. |
| `grtitle` | varchar(255) | The title of the job group. |
| `parentGrId` | int | The ID of the parent job group. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `grId` | `id` | `bigIncrements` | Primary key for the table. | - |
| `grtitle` | `title` | `string` | The title of the job group. | Renamed for clarity. |
| `parentGrId` | `parent_id` | `unsignedBigInteger` | The ID of the parent job group. | Renamed for clarity. |

### Fields left behind

No fields were left behind.
