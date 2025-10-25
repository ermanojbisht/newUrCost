
# Session Management Table

This table is used to manage user sessions.

## Old System

| Column | Type | Description |
|---|---|---|
| `id` | int | Primary key for the table. |
| `username` | varchar(255) | The username of the user. |
| `password` | varchar(255) | The password of the user. |
| `ip_add` | varchar(255) | The IP address of the user. |
| `userid` | int | The ID of the user. |

## New System

This table will not be migrated to the new system. Laravel has its own session management system, which is more secure and robust. We will use Laravel's built-in session management.
