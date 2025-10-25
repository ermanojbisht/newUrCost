
# SORs Table

This table stores the Schedule of Rates (SORs).

## Old System

| Column | Type | Description |
|---|---|---|
| `sorid` | int | Primary key for the table. |
| `sorname` | varchar(255) | The name of the SOR. |
| `insert_date` | datetime | The date the record was inserted. |
| `created_by` | int | The user who created the record. |
| `modify_date` | datetime | The date the record was last modified. |
| `modify_by` | int | The user who last modified the record. |
| `locked` | tinyint(1) | A flag to indicate if the SOR is locked. |
| `filename` | varchar(255) | The name of the file associated with the SOR. |
| `display_details` | tinyint(1) | A flag to indicate if the details should be displayed. |
| `shortname` | varchar(10) | A short name for the SOR. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `sorid` | `id` | `bigIncrements` | Primary key for the table. | - |
| `sorname` | `name` | `string` | The name of the SOR. | Renamed for clarity. |
| `locked` | `is_locked` | `boolean` | A flag to indicate if the SOR is locked. | Renamed to follow boolean naming conventions. |
| `filename` | `filename` | `string` | The name of the file associated with the SOR. | - |
| `display_details` | `display_details` | `boolean` | A flag to indicate if the details should be displayed. | - |
| `shortname` | `short_name` | `string` | A short name for the SOR. | Renamed for clarity. |
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
