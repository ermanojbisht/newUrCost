
# Subitem Dependency Table

This table stores the hierarchical dependency information for sub-items.

## Old System

| Column | Type | My understanding of what the field is used for |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `raitemid` | int | The Rate Analysis code of the main item. Foreign key to the `item` table's `itemcode` field. |
| `subitem` | int | The `itemcode` of the sub-item itself. Foreign key to the `item` table. |
| `lvl` | tinyint | The level of the sub-item in the dependency tree. |
| `pos` | smallint | The position of the sub-item at its level. |
| `dResQty` | double | The quantity of the sub-item required. |
| `UnitID` | smallint | The unit of measurement for the quantity. Foreign key to the `units` table. |
| `PturnOutQty` | double | The turnout quantity of the parent item. |
| `PitemCarryOH` | tinyint(1) | A flag to indicate if the parent item carries overhead. |
| `Pohapplicability` | tinyint(1) | A flag to indicate if overhead is applicable to the parent item. |
| `predate` | bigint | The start date for the validity of this dependency. It's a timestamp. |
| `postdate` | int | The end date for the validity of this dependency. It's a timestamp. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | Changed to be more conventional with Laravel's naming standards. |
| `raitemid` | `item_id` | `unsignedBigInteger` | Foreign key to the `items` table (the main item). | Renamed to follow Laravel's conventions. |
| `subitem` | `sub_item_id` | `unsignedBigInteger` | Foreign key to the `items` table (the sub-item). | Renamed to follow Laravel's conventions. |
| `lvl` | `level` | `integer` | The level of the sub-item in the dependency tree. | Renamed for clarity. |
| `pos` | `position` | `integer` | The position of the sub-item at its level. | Renamed for clarity. |
| `dResQty` | `quantity` | `decimal` | The quantity of the sub-item required. | Renamed for clarity and changed to `decimal` for better precision. |
| `UnitID` | `unit_id` | `unsignedBigInteger` | Foreign key to the `units` table. | Renamed to follow Laravel's conventions. |
| `PturnOutQty` | `parent_turnout_quantity` | `decimal` | The turnout quantity of the parent item. | Renamed for clarity and changed to `decimal` for better precision. |
| `PitemCarryOH` | `parent_carries_overhead` | `boolean` | A flag to indicate if the parent item carries overhead. | Renamed for clarity and changed to `boolean`. |
| `Pohapplicability` | `parent_overhead_applicability` | `boolean` | A flag to indicate if overhead is applicable to the parent item. | Renamed for clarity and changed to `boolean`. |
| `predate` | `valid_from` | `date` | The start date for the validity of this dependency. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the validity of this dependency. | Renamed for clarity and changed to `date` type. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |

### Fields left behind

No fields were left behind.
