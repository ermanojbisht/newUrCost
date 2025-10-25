
# Labor Index Table

This table stores the labor index values for each rate card.

## Old System

| Column | Type | Description |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `ResID` | int | The ID of the resource. Foreign key to the `resource` table. |
| `RateCardID` | int | The ID of the rate card. Foreign key to the `ratecard` table. |
| `perIndex` | double | The index value as a percentage. |
| `canceled` | tinyint(1) | A flag to indicate if the index is canceled. |
| `predate` | bigint | The start date for the validity of this index. It's a timestamp. |
| `postdate` | bigint | The end date for the validity of this index. It's a timestamp. |
| `insert_date` | datetime | The date the record was inserted. |
| `created_by` | int | The user who created the record. |
| `locked` | tinyint(1) | A flag to indicate if the index is locked. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | - |
| `ResID` | `resource_id` | `unsignedBigInteger` | Foreign key to the `resources` table. | Follows Laravel's foreign key naming conventions. |
| `RateCardID` | `rate_card_id` | `unsignedBigInteger` | Foreign key to the `rate_cards` table. | Follows Laravel's foreign key naming conventions. |
| `perIndex` | `index_value` | `decimal` | The index value as a percentage. | Renamed for clarity and changed to `decimal` for better precision. |
| `predate` | `valid_from` | `date` | The start date for the validity of this index. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the validity of this index. | Renamed for clarity and changed to `date` type. |
| `locked` | `is_locked` | `boolean` | A flag to indicate if the index is locked. | Renamed to follow boolean naming conventions. |
| `canceled` | `is_canceled` | `boolean` | A flag to indicate if the index is canceled. | Renamed to follow boolean naming conventions. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |
| `created_by` | `created_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `insert_date` | Replaced by `created_at`. |
