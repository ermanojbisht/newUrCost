
# Region Indexing Table

This table stores the indexing values for different regions.

## Old System

| Column | Type | Description |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `regname` | varchar(255) | The name of the region. |
| `percentage` | double | The index value as a percentage. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | - |
| `regname` | `region_name` | `string` | The name of the region. | Renamed for clarity. |
| `percentage` | `index_value` | `decimal` | The index value as a percentage. | Renamed for clarity and changed to `decimal` for better precision. |

### Fields left behind

No fields were left behind.
