
# Old Jobs Table

This table stores the jobs.

## Old System

| Column | Type | Description |
|---|---|---|
| `jobid` | int | Primary key for the table. |
| `job_title` | varchar(255) | The title of the job. |
| `job_page` | varchar(255) | The page associated with the job. |
| `job_type` | tinyint(1) | The type of the job. |
| `sorder` | int | The sort order of the job. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `jobid` | `id` | `bigIncrements` | Primary key for the table. | Renamed to `old_jobs` table. |
| `job_title` | `title` | `string` | The title of the job. | Renamed for clarity. |
| `job_page` | `page` | `string` | The page associated with the job. | Renamed for clarity. |
| `job_type` | `type` | `integer` | The type of the job. | Renamed for clarity. |
| `sorder` | `sort_order` | `integer` | The sort order of the job. | Renamed for clarity. |

### Fields left behind

No fields were left behind.
