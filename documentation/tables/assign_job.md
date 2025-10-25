
# Assign Job Table

This table is used to assign jobs to users.

## Old System

| Column | Type | Description |
|---|---|---|
| `sno` | int | Primary key for the table. |
| `userid` | int | The ID of the user. Foreign key to the `users` table. |
| `jobid` | int | The ID of the job. Foreign key to the `m_jobs` table. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `sno` | `id` | `bigIncrements` | Primary key for the table. | - |
| `userid` | `user_id` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |
| `jobid` | `job_id` | `unsignedBigInteger` | Foreign key to the `jobs` table. | Follows Laravel's foreign key naming conventions. |

### Fields left behind

No fields were left behind.
