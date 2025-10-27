
# Overhead Table

This table defines the overheads that are applied to an SOR item.

## Old System

| Column | Type | My understanding of what the field is used for |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `raitemid` | int | The Rate Analysis code of the item. Foreign key to the `item` table's `itemcode` field. |
| `oheadid` | int | The ID of the overhead. |
| `oon` | int | This field determines the type of overhead calculation (e.g., percentage of labor, lumpsum). |
| `paramtr` | double(10,4) | The parameter for the overhead calculation (e.g., the percentage value). |
| `sorder` | int | A serial number, likely for ordering the overheads. |
| `onitm` | varchar(100) | This field likely specifies which items the overhead is applied to. |
| `ohdesc` | text | A description of the overhead. |
| `insert_date` | datetime | The date the record was inserted. |
| `created_by` | int | The user who created the record. |
| `modify_date` | datetime | The date the record was last modified. |
| `modify_by` | int | The user who last modified the record. |
| `OHID` | int | This seems to be a duplicate of `oheadid`. |
| `BasedonID` | int | This field is likely used to specify what the overhead is based on. |
| `predate` | bigint | The start date for the validity of this overhead entry. It's a timestamp. |
| `postdate` | int | The end date for the validity of this overhead entry. It's a timestamp. |
| `canceled` | tinyint(1) | A flag to indicate if the overhead entry is canceled. |
| `olditemcode` | int | An old item code, likely for historical data. |
| `furtherOhead` | int | A flag to indicate if further overheads can be applied. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | Changed to be more conventional with Laravel's naming standards. |
| `raitemid` | `item_id` | `unsignedBigInteger` | Foreign key to the `items` table. | Renamed to follow Laravel's conventions. |
| `oheadid` | `overhead_id` | `unsignedBigInteger` | The ID of the overhead. | Renamed for clarity. |
| `oon` | `calculation_type` | `integer` | The type of overhead calculation. | Renamed for clarity. |
| `paramtr` | `parameter` | `decimal` | The parameter for the overhead calculation. | Renamed for clarity and changed to `decimal` for better precision. |
| `sorder` | `sort_order` | `integer` | A serial number for ordering the overheads. | Renamed for clarity. |
| `onitm` | `applicable_items` | `string` | Specifies which items the overhead is applied to. | Renamed for clarity. |
| `ohdesc` | `description` | `text` | A description of the overhead. | Renamed for clarity. |
| `BasedonID` | `based_on_id` | `unsignedBigInteger` | Specifies what the overhead is based on. | Renamed to follow Laravel's conventions. |
| `predate` | `valid_from` | `date` | The start date for the validity of this overhead entry. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the validity of this overhead entry. | Renamed for clarity and changed to `date` type. |
| `canceled` | `is_canceled` | `boolean` | A flag to indicate if the overhead entry is canceled. | Renamed to follow boolean naming conventions. |
| `furtherOhead` | `allow_further_overhead` | `boolean` | A flag to indicate if further overheads can be applied. | Renamed for clarity and changed to `boolean`. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |
| `created_by` | `created_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |
| `modify_by` | `updated_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `insert_date` | Replaced by `created_at`. |
| `modify_date` | Replaced by `updated_at`. |
| `modify_by` | Replaced by `updated_by`. |
| `OHID` | This field seems to be a duplicate of `oheadid`. |
| `olditemcode` | This field contains historical data and is not needed in the new system. |
