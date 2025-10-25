
# Skeleton Table

This table defines the resource skeleton for each SOR item.

## Old System

| Column | Type | My understanding of what the field is used for |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `sorid` | int | The ID of the SOR. |
| `resourceid` | int | The ID of the resource. Foreign key to the `resource` table. |
| `quantity` | double | The quantity of the resource required. |
| `unit` | int | The unit of measurement for the quantity. Foreign key to the `units` table. |
| `raitemid` | int | The Rate Analysis code of the item. Foreign key to the `item` table's `itemcode` field. |
| `app_date` | datetime | Application date. Not consistently used. |
| `res_desc` | text | A description of the resource in the context of this skeleton item. |
| `SrNo` | int | A serial number, likely for ordering the resources within the skeleton. |
| `insert_date` | datetime | The date the record was inserted. |
| `created_by` | int | The user who created the record. |
| `modify_date` | datetime | The date the record was last modified. |
| `modify_by` | int | The user who last modified the record. |
| `predate` | bigint | The start date for the validity of this skeleton entry. It's a timestamp. |
| `postdate` | int | The end date for the validity of this skeleton entry. It's a timestamp. |
| `canceled` | tinyint(1) | A flag to indicate if the skeleton entry is canceled. |
| `olditemcode` | int | An old item code, likely for historical data. |
| `dsritemid` | varchar(50) | DSR Item ID, likely for historical data from a "Delhi Schedule of Rates". |
| `dsrresid` | varchar(15) | DSR Resource ID, also likely for historical data. |
| `locked` | tinyint(1) | A flag to indicate if the skeleton entry is locked. |
| `factor` | double | A multiplication factor for the resource quantity. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | Changed to be more conventional with Laravel's naming standards. |
| `sorid` | `sor_id` | `unsignedBigInteger` | Foreign key to the `sors` table. | Follows Laravel's foreign key naming conventions. |
| `resourceid` | `resource_id` | `unsignedBigInteger` | Foreign key to the `resources` table. | Follows Laravel's foreign key naming conventions. |
| `quantity` | `quantity` | `decimal` | The quantity of the resource required. | Changed to `decimal` for better precision. |
| `unit` | `unit_id` | `unsignedBigInteger` | Foreign key to the `units` table. | Follows Laravel's foreign key naming conventions. |
| `raitemid` | `item_id` | `unsignedBigInteger` | Foreign key to the `items` table. | Renamed to follow Laravel's conventions. |
| `res_desc` | `resource_description` | `text` | A description of the resource in the context of this skeleton item. | Renamed for clarity. |
| `SrNo` | `sort_order` | `integer` | A serial number for ordering the resources within the skeleton. | Renamed for clarity. |
| `predate` | `valid_from` | `date` | The start date for the validity of this skeleton entry. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the validity of this skeleton entry. | Renamed for clarity and changed to `date` type. |
| `canceled` | `is_canceled` | `boolean` | A flag to indicate if the skeleton entry is canceled. | Renamed to follow boolean naming conventions. |
| `locked` | `is_locked` | `boolean` | A flag to indicate if the skeleton entry is locked. | Renamed to follow boolean naming conventions. |
| `factor` | `factor` | `decimal` | A multiplication factor for the resource quantity. | Changed to `decimal` for better precision. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |
| `created_by` | `created_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |
| `updated_by` | `updated_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `app_date` | This field was not consistently used in the old system and its purpose is unclear. |
| `insert_date` | Replaced by `created_at`. |
| `modify_date` | Replaced by `updated_at`. |
| `modify_by` | Replaced by `updated_by`. |
| `olditemcode` | This field contains historical data and is not needed in the new system. |
| `dsritemid` | This field contains historical data and is not needed in the new system. |
| `dsrresid` | This field contains historical data and is not needed in the new system. |
