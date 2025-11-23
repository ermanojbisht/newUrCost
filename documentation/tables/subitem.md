
# Subitem Table

This table defines the sub-items that are part of an SOR item's composition.

## Old System

| Column | Type | My understanding of what the field is used for |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `raitemid` | int | The Rate Analysis code of the main item. Foreign key to the `item` table's `itemcode` field. |
| `ItemID` | int | This field seems to be a duplicate of `raitemid` or `subraitem`. Its exact purpose is not clear. |
| `dResQty` | double | The quantity of the sub-item required. |
| `Percentage` | smallint | further oh will be applicable or not. |
| `BasedonID` | int | Specifies overhead of item is applicable or not default is 1. |
| `SrNo` | int | A serial number for ordering the sub-items within the main item. |
| `UnitID` | int | The unit of measurement for the quantity. Foreign key to the `units` table. |
| `Remark` | longtext | A remark or description for the sub-item. |
| `predate` | bigint | The start date for the validity of this sub-item relationship. It's a timestamp. |
| `postdate` | int | The end date for the validity of this sub-item relationship. It's a timestamp. |
| `subraitem` | int | The `itemcode` of the sub-item itself. Foreign key to the `item` table. |
| `olditemcode` | int | An old item code, likely for historical data. |
| `oldsubitemcode` | int | An old sub-item code, likely for historical data. |
| `turnOutQty` | double | The turnout quantity of the main item. This seems to be a denormalized field from the `item` table. |
| `dsr16id` | varchar(50) | DSR 16 ID, likely for historical data. |
| `insert_date` | datetime | The date the record was inserted. |
| `created_by` | int | The user who created the record. |
| `modify_date` | datetime | The date the record was last modified. |
| `modify_by` | int | The user who last modified the record. |
| `factor` | double | A multiplication factor for the sub-item quantity. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | Changed to be more conventional with Laravel's naming standards. |
| `raitemid` | `item_id` | `unsignedBigInteger` | Foreign key to the `items` table (the main item). | Renamed to follow Laravel's conventions. |
| `subraitem` | `sub_item_id` | `unsignedBigInteger` | Foreign key to the `items` table (the sub-item). | Renamed to follow Laravel's conventions. |
| `dResQty` | `quantity` | `decimal` | The quantity of the sub-item required. | Renamed for clarity and changed to `decimal` for better precision. |
| `Percentage` | `is_oh_applicable` | `integer` | further oh will be applicable or not . | Changed to `integer`. |
| `BasedonID` | `is_overhead` | `Integer` | Specifies overhead of item is applicable or not default is 1. | Renamed to follow Laravel's conventions. |
| `SrNo` | `sort_order` | `integer` | A serial number for ordering the sub-items. | Renamed for clarity. |
| `UnitID` | `unit_id` | `unsignedBigInteger` | Foreign key to the `units` table. | Renamed to follow Laravel's conventions. |
| `Remark` | `remarks` | `text` | A remark or description for the sub-item. | Renamed for clarity. |
| `predate` | `valid_from` | `date` | The start date for the validity of this relationship. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the validity of this relationship. | Renamed for clarity and changed to `date` type. |
| `factor` | `factor` | `decimal` | A multiplication factor for the quantity. | Changed to `decimal` for better precision. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |
| `created_by` | `created_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |
| `updated_by` | `updated_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `ItemID` | This field seems to be a duplicate of `raitemid` or `subraitem` and its purpose is unclear. |
| `insert_date` | Replaced by `created_at`. |
| `modify_date` | Replaced by `updated_at`. |
| `modify_by` | Replaced by `updated_by`. |
| `olditemcode` | This field contains historical data and is not needed in the new system. |
| `oldsubitemcode` | This field contains historical data and is not needed in the new system. |
| `turnOutQty` | This is a denormalized field from the `item` table. It can be calculated on the fly. |
| `dsr16id` | This field contains historical data and is not needed in the new system. |

mkb:
ItemID,turnOutQty currently droped but may be used in future . ItemID was chid of items table subraitem in previous system
