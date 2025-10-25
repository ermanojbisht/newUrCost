
# Users Table

This table stores the user accounts.

## Old System

| Column | Type | Description |
|---|---|---|
| `id` | int | Primary key for the table. |
| `name` | varchar(100) | The name of the user. |
| `loginname` | varchar(20) | The login name of the user. |
| `email` | varchar(100) | The email address of the user. |
| `password` | varchar(255) | The hashed password of the user. |
| `gender` | enum('Male','Female') | The gender of the user. |
| `phone` | varchar(15) | The phone number of the user. |
| `created` | datetime | The date the record was created. |
| `modified` | datetime | The date the record was last modified. |
| `status` | enum('1','0') | The status of the user (1: Active, 0: Inactive). |
| `user_type` | tinyint(1) | The type of the user. |
| `telid` | varchar(50) | The Telegram ID of the user. |
| `telmsgstatus` | tinyint(1) | The Telegram message status. |
| `sorlist_to_view` | varchar(50) | A list of SORs the user can view. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `id` | `id` | `bigIncrements` | Primary key for the table. | - |
| `name` | `name` | `string` | The name of the user. | - |
| `loginname` | `username` | `string` | The username of the user. | Renamed for clarity. |
| `email` | `email` | `string` | The email address of the user. | - |
| `password` | `password` | `string` | The hashed password of the user. | - |
| `gender` | `gender` | `enum` | The gender of the user ('male', 'female'). | Changed to use lowercase enum values. |
| `phone` | `phone` | `string` | The phone number of the user. | - |
| `status` | `status` | `enum` | The status of the user ('active', 'inactive'). | Changed to use more descriptive enum values. |
| `user_type` | `user_type` | `integer` | The type of the user. | - |
| `telid` | `telegram_id` | `string` | The Telegram ID of the user. | Renamed for clarity. |
| `telmsgstatus` | `telegram_message_status` | `boolean` | The Telegram message status. | Renamed for clarity and changed to `boolean`. |
| `sorlist_to_view` | `sor_list_to_view` | `text` | A list of SORs the user can view. | Renamed for clarity and changed to `text` to accommodate more data. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `created` | Replaced by `created_at`. |
| `modified` | Replaced by `updated_at`. |
