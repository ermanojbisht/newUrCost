
# Lead Distance Table

This table stores the lead distances for material resources.

## Old System

| Column | Type | Description |
|---|---|---|
| `id` | int | Primary key for the table. |
| `ResID` | int | The ID of the resource. Foreign key to the `resource` table. |
| `RateCardID` | int | The ID of the rate card. Foreign key to the `ratecard` table. |
| `Lead` | double | The lead distance in kilometers. |
| `leadType` | tinyint(1) | The type of lead (1: Mechanical, 2: Manual, 3: Mule). |
| `macCapGr` | int | Not clear from the code. |
| `canceled` | tinyint(1) | A flag to indicate if the lead distance is canceled. |
| `predate` | bigint | The start date for the validity of this lead distance. It's a timestamp. |
| `postdate` | int | The end date for the validity of this lead distance. It's a timestamp. |
| `locked` | tinyint(1) | A flag to indicate if the lead distance is locked. |
| `appdate` | date | The application date of the lead distance. |
| `oldratecard` | int | An old rate card ID, likely for historical data. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `id` | `id` | `bigIncrements` | Primary key for the table. | - |
| `ResID` | `resource_id` | `unsignedBigInteger` | Foreign key to the `resources` table. | Follows Laravel's foreign key naming conventions. |
| `RateCardID` | `rate_card_id` | `unsignedBigInteger` | Foreign key to the `rate_cards` table. | Follows Laravel's foreign key naming conventions. |
| `Lead` | `distance` | `decimal` | The lead distance in kilometers. | Renamed for clarity and changed to `decimal` for better precision. |
| `leadType` | `type` | `integer` | The type of lead (1: Mechanical, 2: Manual, 3: Mule). | Renamed for clarity. |
| `appdate` | `applicable_date` | `date` | The application date of the lead distance. | Renamed for clarity. |
| `predate` | `valid_from` | `date` | The start date for the validity of this lead distance. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the validity of this lead distance. | Renamed for clarity and changed to `date` type. |
| `locked` | `is_locked` | `boolean` | A flag to indicate if the lead distance is locked. | Renamed to follow boolean naming conventions. |
| `canceled` | `is_canceled` | `boolean` | A flag to indicate if the lead distance is canceled. | Renamed to follow boolean naming conventions. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `macCapGr` | The purpose of this field is not clear from the code. |
| `oldratecard` | This field contains historical data and is not needed in the new system. |
