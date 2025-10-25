
# Units Table

This table stores the units of measurement.

## Old System

| Column | Type | Description |
|---|---|---|
| `PKID` | int | Not clear from the column name, but likely a primary key. |
| `ID` | int | The ID of the unit. |
| `vUnitName` | longtext | The name of the unit. |
| `vUnitCode` | longtext | The code of the unit. |
| `LanguageId` | int | The ID of the language. |
| `Alias` | longtext | An alias for the unit. |
| `iUnitgrpID` | int | The ID of the unit group. |
| `nConFac` | double | The conversion factor. |

## New System

| Old Column Name | New Column Name | Data Type | Description | Remarks |
|---|---|---|---|---|
| `ID` | `id` | `bigIncrements` | Primary key for the table. | `PKID` seems to be a redundant primary key. |
| `vUnitName` | `name` | `string` | The name of the unit. | Renamed for clarity. |
| `vUnitCode` | `code` | `string` | The code of the unit. | Renamed for clarity. |
| `Alias` | `alias` | `string` | An alias for the unit. | - |
| `iUnitgrpID` | `unit_group_id` | `unsignedBigInteger` | The ID of the unit group. | Renamed for clarity. |
| `nConFac` | `conversion_factor` | `decimal` | The conversion factor. | Renamed for clarity and changed to `decimal` for better precision. |

### Fields left behind

| Field | Reason for leaving behind |
|---|---|
| `PKID` | This seems to be a redundant primary key. `ID` will be used as the primary key. |
| `LanguageId` | This seems to be a redundant only 1 value |
