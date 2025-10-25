# Item Table

This table stores the core information about each SOR item and chapter.

## Old System

| Column | Type | My understanding of what the field is used for |
|---|---|---|
| `chId` | int | The primary identifier for an item or chapter within the tree structure. |
| `sorId` | int | The ID of the SOR to which this item or chapter belongs. |
| `chParentId` | int | The ID of the parent chapter, used to create the hierarchical structure. |
| `itemcode` | int | The unique code for the item. This is 0 for chapters. |
| `itemname` | text | The full, hierarchical name of the item, constructed from the names of its parent chapters. |
| `orderInParent` | int | The order of the item within its parent chapter. Not directly used in the model, but likely used for ordering. |
| `SpcCode` | longtext | The specification code, used to link to external specification documents. |
| `SpcPageNO` | longtext | The specification page number in the specification document. |
| `chIdtype` | smallint | The type of the chapter ID. Not heavily used in the model. |
| `maybeusedforsorting` | longtext | A field that might be used for sorting. Its exact usage is not clear from the model. |
| `ItemNo` | longtext | The item number (e.g., "1.1", "1.2a"). |
| `ItemDesc` | longtext | The detailed description of the item. |
| `ItemShortDesc` | longtext | A shorter description of the item. |
| `TurnOutQuantity` | double | The quantity of the item produced by the given resources. |
| `Assumption` | longtext | Assumptions made for the item. |
| `FootNote` | longtext | Footnotes for the item. |
| `UnitID` | int | The ID of the unit of measurement for the item. Foreign key to the `units` table. |
| `canceled` | tinyint(1) | A flag to indicate if the item is canceled. |
| `orderFromNestedList` | int | The order of the item from a nested list. This is updated by the `updateOrderInItemTable` method. |
| `subitemlvl` | tinyint(1) | The level of the item in the sub-item hierarchy. |
| `noOfSubitem` | tinyint | The number of sub-items that this item contains. |
| `olditemcode` | int | The old item code. Not used in the model. |
| `dsr16id` | varchar(50) | The DSR 16 ID. Not used in the model. |
| `locked` | tinyint(1) | A flag to indicate if the item is locked, preventing it from being updated. |
| `insert_date` | datetime | The date the item was inserted. Not used in the model. |
| `created_by` | int | The user who created the item. Not used in the model. |
| `modify_date` | datetime | The date the item was last modified. Not used in the model. |
| `modify_by` | int | The user who last modified the item. Not used in the model. |
| `ref_from` | bigint | The reference from which the item was created. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `chId` | `id` | `bigIncrements` | Primary key for the items table. | Changed to be more conventional with Laravel's naming standards. |
| `sorId` | `sor_id` | `unsignedBigInteger` | Foreign key to the `sors` table. | Follows Laravel's foreign key naming conventions. |
| `chParentId` | `parent_id` | `unsignedBigInteger` | Foreign key to the same table for self-referencing. | Follows Laravel's foreign key naming conventions. |
| `itemcode` | `item_code` | `string` | The unique code for the item. | Changed to string to accommodate alphanumeric codes. |
| `itemname` | `name` | `text` | The full, hierarchical name of the item. | Simplified the name for clarity. |
| `orderInParent` | `order_in_parent` | `integer` | The order of the item within its parent chapter. | Changed to be more descriptive. |
| `SpcCode` | `specification_code` | `text` | The specification code. | Changed to be more descriptive and to `text` to accommodate longer codes. |
| `SpcPageNO` | `specification_page_number` | `text` | The specification page number. | Changed to be more descriptive and to `text` to accommodate longer numbers. |
| `chIdtype` | `item_type` | `string` | The type of the item (e.g., chapter, item). | Changed to be more descriptive. |
| `maybeusedforsorting` | `sort_order` | `text` | A field that might be used for sorting. | Changed to be more descriptive and to `text` to accommodate longer values. |
| `ItemNo` | `item_number` | `text` | The item number (e.g., "1.1", "1.2a"). | Changed to be more descriptive and to `text` to accommodate longer numbers. |
| `ItemDesc` | `description` | `text` | The detailed description of the item. | Simplified the name for clarity. |
| `ItemShortDesc` | `short_description` | `text` | A shorter description of the item. | Changed to be more descriptive. |
| `TurnOutQuantity` | `turnout_quantity` | `decimal` | The quantity of the item produced by the given resources. | Changed to be more descriptive. |
| `Assumption` | `assumptions` | `text` | Assumptions made for the item. | Simplified the name for clarity. |
| `FootNote` | `footnotes` | `text` | Footnotes for the item. | Simplified the name for clarity. |
| `UnitID` | `unit_id` | `unsignedBigInteger` | Foreign key to the `units` table. | Follows Laravel's foreign key naming conventions. |
| `canceled` | `is_canceled` | `boolean` | A flag to indicate if the item is canceled. | Changed to be more descriptive and follow boolean naming conventions. |
| `orderFromNestedList` | `nested_list_order` | `integer` | The order of the item from a nested list. | Changed to be more descriptive. |
| `subitemlvl` | `sub_item_level` | `integer` | The level of the item in the sub-item hierarchy. | Changed to be more descriptive. |
| `noOfSubitem` | `sub_item_count` | `integer` | The number of sub-items that this item contains. | Changed to be more descriptive. |
| `olditemcode` | `old_item_code` | `string` | The old item code. | Changed to be more descriptive. This field seems to be for historical data and may not be actively used. |
| `dsr16id` | `dsr_16_id` | `string` | The DSR 16 ID. | Changed to be more descriptive. This field seems to be for historical data and may not be actively used. |
| `locked` | `is_locked` | `boolean` | A flag to indicate if the item is locked. | Changed to be more descriptive and follow boolean naming conventions. |
| `insert_date` | `created_at` | `timestamp` | The date the item was created. | Using Laravel's default timestamp. |
| `created_by` | `created_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |
| `modify_date` | `updated_at` | `timestamp` | The date the item was last updated. | Using Laravel's default timestamp. |
| `modify_by` | `updated_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |
| `ref_from` | `reference_from` | `unsignedBigInteger` | The reference from which the item was created. | Changed to be more descriptive. |