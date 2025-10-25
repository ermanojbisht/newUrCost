
# Unit Groups Table

This table stores the groups for units of measurement.

## Old System

| Column | Type | Description |
|---|---|---|
| `ParentID` | int | The ID of the parent unit group. |
| `vUnitGrpName` | longtext | The name of the unit group. |
| `BaseUnitID` | int | The ID of the base unit for the group. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ParentID` | `id` | `bigIncrements` | Primary key for the table. | Renamed to be more conventional with Laravel's naming standards. |
| `vUnitGrpName` | `name` | `string` | The name of the unit group. | Renamed for clarity. |
| `BaseUnitID` | `base_unit_id` | `unsignedBigInteger` | The ID of the base unit for the group. | Renamed for clarity. |

### Fields left behind

No fields were left behind.
