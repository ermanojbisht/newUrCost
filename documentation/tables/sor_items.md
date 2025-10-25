
# SOR Items Table

This table seems to be a log or a temporary table for SOR items.

## Old System

| Column | Type | Description |
|---|---|---|
| `chId` | int | The chapter ID. |
| `sorid` | int | The ID of the SOR. |
| `chParentId` | int | The ID of the parent chapter. |
| `raitemid` | int | The Rate Analysis code of the item. |
| `insert_date` | datetime | The date the record was inserted. |
| `created_bt` | int | The user who created the record. |
| `modify_date` | datetime | The date the record was last modified. |
| `modify_by` | int | The user who last modified the record. |

## New System

It is not clear if this table is needed in the new system. It might be a log or a temporary table that is no longer required.
