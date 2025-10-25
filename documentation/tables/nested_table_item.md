
# Nested Table Item Table

This table stores the nested set model for the items, which is used for efficient querying of hierarchical data.

## Old System

| Column | Type | Description |
|---|---|---|
| `lft` | int unsigned | The left value of the nested set model. |
| `rgt` | int unsigned | The right value of the nested set model. |
| `id` | int unsigned | Primary key for the table. |
| `ItemNo` | varchar(128) | The item number. |
| `sorId` | int | The ID of the SOR. |
| `itemOrChapter` | int | A flag to indicate if the node is an item or a chapter. |
| `parent_id` | int | The ID of the parent node. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `id` | `id` | `bigIncrements` | Primary key for the table. | - |
| `lft` | `_lft` | `integer` | The left value of the nested set model. | The underscore prefix is a convention for some nested set packages. |
| `rgt` | `_rgt` | `integer` | The right value of the nested set model. | The underscore prefix is a convention for some nested set packages. |
| `ItemNo` | `item_number` | `string` | The item number. | Renamed for clarity. |
| `sorId` | `sor_id` | `unsignedBigInteger` | Foreign key to the `sors` table. | Follows Laravel's foreign key naming conventions. |
| `itemOrChapter` | `is_chapter` | `boolean` | A flag to indicate if the node is a chapter. | Changed to a boolean for clarity. |
| `parent_id` | `parent_id` | `unsignedBigInteger` | The ID of the parent node. | - |

### Fields left behind

No fields were left behind.
