
# Item Rate Table

This table stores the calculated rates for each SOR item.

## Old System

| Column | Type | My understanding of what the field is used for |
|---|---|---|
| `racode` | int | The Rate Analysis code of the item. This is a foreign key to the `item` table's `itemcode` field. |
| `rate` | double | The calculated rate of the item. |
| `laborcost` | double | The labor cost component of the rate. |
| `materialcost` | double | The material cost component of the rate. |
| `machinecost` | double | The machine cost component of the rate. |
| `ocost` | double | The overhead cost component of the rate. |
| `ratecard` | int | The ID of the rate card used for the calculation. Foreign key to the `ratecard` table. |
| `date` | datetime | The date when the rate was calculated. |
| `predate` | bigint | The start date for the rate's validity. It's a timestamp. |
| `postdate` | bigint | The end date for the rate's validity. It's a timestamp. |
| `UnitID` | int | The ID of the unit of measurement for the item. Foreign key to the `units` table. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `racode` | `item_id` | `unsignedBigInteger` | Foreign key to the `items` table. | Renamed to follow Laravel's conventions. |
| `rate` | `rate` | `decimal` | The calculated rate of the item. | Changed to `decimal` for better precision. |
| `laborcost` | `labor_cost` | `decimal` | The labor cost component of the rate. | Renamed and changed to `decimal`. |
| `materialcost` | `material_cost` | `decimal` | The material cost component of the rate. | Renamed and changed to `decimal`. |
| `machinecost` | `machine_cost` | `decimal` | The machine cost component of the rate. | Renamed and changed to `decimal`. |
| `ocost` | `overhead_cost` | `decimal` | The overhead cost component of the rate. | Renamed and changed to `decimal`. |
| `ratecard` | `rate_card_id` | `unsignedBigInteger` | Foreign key to the `rate_cards` table. | Renamed to follow Laravel's conventions. |
| `date` | `calculation_date` | `date` | The date when the rate was calculated. | Renamed for clarity. |
| `predate` | `valid_from` | `date` | The start date for the rate's validity. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the rate's validity. | Renamed for clarity and changed to `date` type. |
| `UnitID` | `unit_id` | `unsignedBigInteger` | Foreign key to the `units` table. | Renamed to follow Laravel's conventions. |

### Fields left behind

No fields were left behind.
