
# Rate Card Table

This table stores the master list of all rate cards (regions).

## Old System

| Column | Type | Description |
|---|---|---|
| `id` | int | Primary key for the table. |
| `ratecardid` | int | The unique ID for the rate card. |
| `ratecardname` | varchar(255) | The name of the rate card (region). |
| `ratecardgrpid` | int | The ID of the rate card group. |
| `insert_date` | datetime | The date the record was inserted. |
| `created_by` | int | The user who created the record. |
| `modify_date` | datetime | The date the record was last modified. |
| `modify_by` | int | The user who last modified the record. |
| `description` | text | A description of the rate card. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `id` | `id` | `bigIncrements` | Primary key for the table. | - |
| `ratecardid` | `rate_card_code` | `string` | The unique code for the rate card. | Renamed for clarity. |
| `ratecardname` | `name` | `string` | The name of the rate card (region). | Renamed for clarity. |
| `description` | `description` | `text` | A description of the rate card. | - |
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
| `ratecardgrpid` | not needed |
