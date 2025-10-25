
# Site Settings Table

This table stores site-wide settings.

## Old System

| Column | Type | Description |
|---|---|---|
| `1` | tinyint(1) | Not clear from the column name. |
| `whitelisted_ip` | varchar(20) | A whitelisted IP address. |
| `site_offline` | tinyint(1) | A flag to indicate if the site is offline. |
| `onlineon` | datetime | The date and time the site was last brought online. |
| `rateCalculationDate` | date | The date for rate calculation. |
| `rateCalculationAllowed` | tinyint(1) | A flag to indicate if rate calculation is allowed. |

## New System

In the new Laravel system, these settings will be stored in a configuration file (e.g., `config/site.php`) rather than a database table. This is a more conventional approach for storing site-wide settings in Laravel.

