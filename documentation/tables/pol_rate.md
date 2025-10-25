
# POL Rate Table

This table stores the rates for POL (Petrol, Oil, and Lubricants).

## Old System

| Column | Type | Description |
|---|---|---|
| `ID` | int unsigned | Primary key for the table. |
| `Ddate` | datetime | The date of the rate. |
| `DesilRate` | double | The rate of diesel. |
| `MobileRate` | decimal(7,2) | The rate of mobile oil. |
| `MazdoorCharges` | double | The charges for mazdoor (laborer). |
| `HiringCharges` | double | The hiring charges. |
| `OHCharges` | decimal(7,2) | The overhead charges. |
| `MuleRate` | decimal(7,2) | The rate for mules. |
| `predate` | bigint | The start date for the rate's validity. It's a timestamp. |
| `postdate` | int | The end date for the rate's validity. It's a timestamp. |
| `locked` | tinyint(1) | A flag to indicate if the rate is locked. |
| `publish_date` | datetime | The date the rate was published. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | - |
| `Ddate` | `rate_date` | `date` | The date of the rate. | Renamed for clarity. |
| `DesilRate` | `diesel_rate` | `decimal` | The rate of diesel. | Renamed for clarity and changed to `decimal` for better precision. |
| `MobileRate` | `mobile_oil_rate` | `decimal` | The rate of mobile oil. | Renamed for clarity. |
| `MazdoorCharges` | `laborer_charges` | `decimal` | The charges for laborers. | Renamed for clarity and changed to `decimal` for better precision. |
| `HiringCharges` | `hiring_charges` | `decimal` | The hiring charges. | Renamed for clarity and changed to `decimal` for better precision. |
| `OHCharges` | `overhead_charges` | `decimal` | The overhead charges. | Renamed for clarity. |
| `MuleRate` | `mule_rate` | `decimal` | The rate for mules. | Renamed for clarity. |
| `predate` | `valid_from` | `date` | The start date for the rate's validity. | Renamed for clarity and changed to `date` type. |
| `postdate` | `valid_to` | `date` | The end date for the rate's validity. | Renamed for clarity and changed to `date` type. |
| `locked` | `is_locked` | `boolean` | A flag to indicate if the rate is locked. | Renamed to follow boolean naming conventions. |
| `publish_date` | `published_at` | `timestamp` | The date the rate was published. | Renamed for clarity. |
| `created_at` | `created_at` | `timestamp` | The date the record was created. | Added to follow Laravel's conventions. |
| `updated_at` | `updated_at` | `timestamp` | The date the record was last updated. | Added to follow Laravel's conventions. |

### Fields left behind

No fields were left behind.
