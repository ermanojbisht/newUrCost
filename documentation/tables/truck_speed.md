
# Truck Speed Table

This table stores the average speed of trucks for different lead distances.

## Old System

| Column | Type | Description |
|---|---|---|
| `LeadKm` | double | The lead distance in kilometers. |
| `AverageSpeed` | double | The average speed of the truck. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `LeadKm` | `lead_distance` | `decimal` | The lead distance in kilometers. | Renamed for clarity and changed to `decimal` for better precision. |
| `AverageSpeed` | `average_speed` | `decimal` | The average speed of the truck. | Renamed for clarity and changed to `decimal` for better precision. |

### Fields left behind

No fields were left behind.
