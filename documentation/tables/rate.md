
# Rate Table

This table stores the base rates for each resource on a given rate card.

## Old System

| Column | Type | Description |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `resourceid` | int | The ID of the resource. Foreign key to the `resource` table. |
| `ratecard` | int | The ID of the rate card. Foreign key to the `ratecard` table. |
| `appdate` | datetime | The application date of the rate. |
| `unit` | int | The unit of measurement for the rate. Foreign key to the `units` table. |
| `rate` | double | The base rate of the resource. |
| `insert_date` | datetime | The date the record was inserted. |
| `created_by` | int | The user who created the record. |
| `modify_date` | datetime | The date the record was last modified. |
| `modify_by` | int | The user who last modified the record. |
| `predate` | bigint | The start date for the rate's validity. It's a timestamp. |
| `postdate` | int | The end date for the rate's validity. It's a timestamp. |
| `remark` | text | A remark or description for the rate. |
| `locked` | tinyint(1) | A flag to indicate if the rate is locked. |
| `publish_date` | datetime | The date the rate was published. |
| `tax` | double | The tax amount included in the rate. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | Changed to be more conventional with Laravel's naming standards. |
| `resourceid` | `resource_id` | `unsignedBigInteger` | Foreign key to the `resources` table. | Follows Laravel's foreign key naming conventions. |
| `ratecard` | `rate_card_id` | `unsignedBigInteger` | Foreign key to the `rate_cards` table. | Follows Laravel's foreign key naming conventions. |
| `appdate` | `applicable_date` | `date` | The application date of the rate. | Renamed for clarity. |
| `unit` | `unit_id` | `unsignedBigInteger` | Foreign key to the `units` table. | Follows Laravel's foreign key naming conventions. |
| `rate` | `rate` | `decimal` | The base rate of the resource. | Changed to `decimal` for better precision. |
| `remark` | `remarks` | `text` | A remark or description for the rate. | Renamed for clarity. |
| `locked` | `is_locked` | `boolean` | A flag to indicate if the rate is locked. | Renamed to follow boolean naming conventions. |
| `publish_date` | `published_at` | `timestamp` | The date the rate was published. | Renamed for clarity. |
| `tax` | `tax` | `decimal` | The tax amount included in the rate. | Changed to `decimal` for better precision. |
| `predate` | `valid_from` | `date` | The start date for the rate's validity. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the rate's validity. | Renamed for clarity and changed to `date` type. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |
| `created_by` | `created_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |
| `updated_by` | `updated_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `insert_date` | Replaced by `created_at`. |
| `modify_date` | Replaced by `updated_at`. |
| `modify_by` | Replaced by `updated_by`. |
