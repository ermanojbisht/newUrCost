
# Resource Table

This table stores the master list of all resources (man, material, machine).

## Old System

| Column | Type | My understanding of what the field is used for |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `name` | text | The name of the resource. |
| `code` | int | A unique code for the resource. |
| `resgr` | int | The resource group (1: Labor, 2: Machine, 3: Material). |
| `resCode` | varchar(50) | Another code for the resource. |
| `UnitGrpId` | tinyint | The ID of the unit group. |
| `TechUnitID` | int | The ID of the unit. |
| `byVolumeWeight` | int | Not clear from the code. |
| `description` | text | A description of the resource. |
| `numItemUsed` | int | The number of items that use this resource. |
| `insert_date` | datetime | The date the record was inserted. |
| `created_by` | int | The user who created the record. |
| `modify_date` | datetime | The date the record was last modified. |
| `modify_by` | int | The user who last modified the record. |
| `resCapacityGr` | int | The resource capacity group. |
| `resCapacityGrId` | int | The ID of the resource capacity group. |
| `dsrcode` | varchar(15) | DSR code, likely for historical data from a "Delhi Schedule of Rates". |
| `canceled` | tinyint(1) | A flag to indicate if the resource is canceled. |
| `resCode_new` | varchar(255) | A new resource code, likely for migration purposes. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | -- | `bigIncrements` | Primary key for the table. |Dropped as of no use |
| `name` | `name` | `string` | The name of the resource. | - |
| `code` | `id` | `bigIncrements` | A unique code for the resource. | Renamed for clarity.  |
| `resgr` | `resource_group_id` | `unsignedBigInteger` | The resource group. | Renamed for clarity. |
| `resCode` | `secondary_code` | `string` | Another code for the resource. | Renamed for clarity. |
| `UnitGrpId` | `unit_group_id` | `unsignedBigInteger` | The ID of the unit group. | Renamed for clarity. |
| `TechUnitID` | `unit_id` | `unsignedBigInteger` | The ID of the unit. | Renamed for clarity. |
| `description` | `description` | `text` | A description of the resource. | - |
| `numItemUsed` | `items_using_count` | `integer` | The number of items that use this resource. | Renamed for clarity. |
| `resCapacityGr` | `resource_capacity_rule_id` | `integer` | The resource capacity group. | Renamed for clarity. |
| `resCapacityGrId` | `capacity_group_id` | `unsignedBigInteger` | The ID of the resource capacity group. | Renamed for clarity. |
| `dsrcode` | `dsr_code` | `string` | DSR code for historical data. | Renamed for clarity. |
| `canceled` | `is_canceled` | `boolean` | A flag to indicate if the resource is canceled. | Renamed to follow boolean naming conventions. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |
| `created_by` | `created_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |
| `updated_by` | `updated_by` | `unsignedBigInteger` | Foreign key to the `users` table. | Follows Laravel's foreign key naming conventions. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `byVolumeWeight` | The purpose of this field is not clear from the code. |
| `insert_date` | Replaced by `created_at`. |
| `modify_date` | Replaced by `updated_at`. |
| `modify_by` | Replaced by `updated_by`. |
| `resCode_new` | This field was likely used for a one-time migration and is not needed in the new system. |


ResourceGroup Model created as in legecy system no such master avilabe
