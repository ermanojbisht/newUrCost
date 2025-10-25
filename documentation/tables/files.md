
# Files Table

This table is used to store information about uploaded files.

## Old System

| Column | Type | Description |
|---|---|---|
| `id` | int | Primary key for the table. |
| `title` | varchar(255) | The title of the file. |
| `file_name` | varchar(255) | The name of the file. |
| `created` | datetime | The date the record was created. |
| `modified` | datetime | The date the record was last modified. |
| `status` | enum('1','0','2') | The status of the file (1: Active, 0: Inactive, 2: Deleted). |
| `typeofdoc` | tinyint(1) | The type of the document. |
| `ratecard` | int | The ID of the rate card. |
| `sorid` | int | The ID of the SOR. |
| `created_by` | int | The user who created the record. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `id` | `id` | `bigIncrements` | Primary key for the table. | - |
| `title` | `title` | `string` | The title of the file. | - |
| `file_name` | `filename` | `string` | The name of the file. | Renamed for clarity. |
| `status` | `status` | `enum` | The status of the file ('active', 'inactive', 'deleted'). | Changed to use more descriptive enum values. |
| `typeofdoc` | `document_type` | `string` | The type of the document. | Renamed for clarity. |
| `ratecard` | `rate_card_id` | `unsignedBigInteger` | Foreign key to the `rate_cards` table. | Follows Laravel's foreign key naming conventions. |
| `sorid` | `sor_id` | `unsignedBigInteger` | Foreign key to the `sors` table. | Follows Laravel's foreign key naming conventions. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |
| `created_by` | `created_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `created` | Replaced by `created_at`. |
| `modified` | Replaced by `updated_at`. |
