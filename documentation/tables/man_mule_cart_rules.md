
# Man Mule Cart Rules Table

This table stores the rules for manual and mule cartage.

## Old System

| Column | Type | Description |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `km` | double | The distance in kilometers. |
| `byVolumeOrWeight` | int | Not clear from the code, but likely related to the calculation method. |
| `factor` | double | The multiplication factor. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | - |
| `km` | `distance` | `decimal` | The distance in kilometers. | Renamed for clarity and changed to `decimal` for better precision. |
| `byVolumeOrWeight` | `calculation_method` | `integer` | The calculation method. | Renamed for clarity. |
| `factor` | `factor` | `decimal` | The multiplication factor. | Changed to `decimal` for better precision. |

### Fields left behind

No fields were left behind.
