
# Subitem Rate Table

This table stores the calculated rates for sub-items.

## Old System

| Column | Type | Description |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `racode` | int | The Rate Analysis code of the sub-item. This is a foreign key to the `item` table's `itemcode` field. |
| `rate` | double | The calculated rate of the sub-item. |
| `laborcost` | double | The labor cost component of the rate. |
| `materialcost` | double | The material cost component of the rate. |
| `machinecost` | double | The machine cost component of the rate. |
| `ocost` | double | The overhead cost component of the rate. |
| `ratecard` | int | The ID of the rate card used for the calculation. Foreign key to the `ratecard` table. |
| `appdate` | datetime | The application date of the rate. |
| `predate` | bigint | The start date for the rate's validity. It's a timestamp. |
| `postdate` | int | The end date for the rate's validity. It's a timestamp. |
| `UnitID` | int | The ID of the unit of measurement for the sub-item. Foreign key to the `units` table. |
| `locked` | tinyint(1) | A flag to indicate if the rate is locked. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | - |
| `racode` | `sub_item_id` | `unsignedBigInteger` | Foreign key to the `items` table (the sub-item). | Renamed to follow Laravel's conventions. |
| `rate` | `rate` | `decimal` | The calculated rate of the sub-item. | Changed to `decimal` for better precision. |
| `laborcost` | `labor_cost` | `decimal` | The labor cost component of the rate. | Renamed and changed to `decimal`. |
| `materialcost` | `material_cost` | `decimal` | The material cost component of the rate. | Renamed and changed to `decimal`. |
| `machinecost` | `machine_cost` | `decimal` | The machine cost component of the rate. | Renamed and changed to `decimal`. |
| `ocost` | `overhead_cost` | `decimal` | The overhead cost component of the rate. | Renamed and changed to `decimal`. |
| `ratecard` | `rate_card_id` | `unsignedBigInteger` | Foreign key to the `rate_cards` table. | Renamed to follow Laravel's conventions. |
| `appdate` | `applicable_date` | `date` | The application date of the rate. | Renamed for clarity. |
| `predate` | `valid_from` | `date` | The start date for the rate's validity. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the rate's validity. | Renamed for clarity and changed to `date` type. |
| `UnitID` | `unit_id` | `unsignedBigInteger` | Foreign key to the `units` table. | Renamed to follow Laravel's conventions. |
| `locked` | `is_locked` | `boolean` | A flag to indicate if the rate is locked. | Renamed to follow boolean naming conventions. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |

### Fields left behind

No fields were left behind.
