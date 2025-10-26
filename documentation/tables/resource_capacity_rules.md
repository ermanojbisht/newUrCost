
# Resource Capacity Rules Table

This table stores the capacity rules for resources.

## Old System table name rescaprules

| Column | Type | Description |
|---|---|---|
| `groupId` | int unsigned | Primary key for the table. |
| `nos` | bigint | Not clear from the code. |
| `MechCapacity` | double | The mechanical capacity. |
| `MechNetCapacity` | double | The net mechanical capacity. |
| `ManCapacity` | double | The manual capacity. |
| `ManNetCapacity` | double | The net manual capacity. |
| `MuleFactor` | double | The mule factor. |
| `sampleResource` | text | A sample resource. |

## New System table name resource_capacity_rules

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `groupId` | `id` | `bigIncrements` | Primary key for the table. | - |
| `MechCapacity` | `mechanical_capacity` | `decimal` | The mechanical capacity. | Renamed for clarity and changed to `decimal` for better precision. |
| `MechNetCapacity` | `net_mechanical_capacity` | `decimal` | The net mechanical capacity. | Renamed for clarity and changed to `decimal` for better precision. |
| `ManCapacity` | `manual_capacity` | `decimal` | The manual capacity. | Renamed for clarity and changed to `decimal` for better precision. |
| `ManNetCapacity` | `net_manual_capacity` | `decimal` | The net manual capacity. | Renamed for clarity and changed to `decimal` for better precision. |
| `MuleFactor` | `mule_factor` | `decimal` | The mule factor. | Renamed for clarity and changed to `decimal` for better precision. |
| `sampleResource` | `sample_resource` | `text` | A sample resource. | Renamed for clarity. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `nos` | The purpose of this field is not clear from the code. |
