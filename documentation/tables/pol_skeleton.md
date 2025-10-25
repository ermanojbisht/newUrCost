
# POL Skeleton Table

This table stores the POL (Petrol, Oil, and Lubricants) skeleton data.

## Old System

| Column | Type | Description |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `Ddate` | datetime | The date of the data. |
| `DesilMailage` | double | The mileage of diesel. |
| `MobileMailage` | double | The mileage of mobile oil. |
| `NoofMazdoors` | int | The number of laborers. |
| `predate` | bigint | The start date for the data's validity. It's a timestamp. |
| `postdate` | bigint | The end date for the data's validity. It's a timestamp. |
| `locked` | tinyint(1) | A flag to indicate if the data is locked. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | - |
| `Ddate` | `date` | `date` | The date of the data. | Renamed for clarity. |
| `DesilMailage` | `diesel_mileage` | `decimal` | The mileage of diesel. | Renamed for clarity and changed to `decimal` for better precision. |
| `MobileMailage` | `mobile_oil_mileage` | `decimal` | The mileage of mobile oil. | Renamed for clarity and changed to `decimal` for better precision. |
| `NoofMazdoors` | `number_of_laborers` | `integer` | The number of laborers. | Renamed for clarity. |
| `predate` | `valid_from` | `date` | The start date for the data's validity. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the data's validity. | Renamed for clarity and changed to `date` type. |
| `locked` | `is_locked` | `boolean` | A flag to indicate if the data is locked. | Renamed to follow boolean naming conventions. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |

### Fields left behind

No fields were left behind.
