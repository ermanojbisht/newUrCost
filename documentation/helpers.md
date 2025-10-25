## Helpers

This document describes the helper files used in the urCost application.

### `checkdate_helper.php`

This helper provides functions for working with dates.

*   **`checkApplicabilityDate($isFutureDateDisabled, $applicabiltyDateInSession)`:** This function determines the applicability date for a given context, considering whether future dates are disabled and if a date is already set in the session.
*   **`dateFormIntegerHelp($dt)`:** This function formats a Unix timestamp into a `d-m-Y` date format.

### `numeralo_helper.php`

This helper provides a class `Numeralo` for converting between Roman numerals and natural numbers.

*   **`number_to_numerals($number)`:** Converts a natural number to a Roman numeral.
*   **`numerals_to_number($numerals)`:** Converts a Roman numeral to a natural number.

### `pdf_helper.php`

This helper provides functions for creating and formatting PDF and Excel files using the `cezpdf` and `PHPExcel` libraries.

*   **`prep_pdf($orientation = 'portrait')`:** Prepares a PDF document for content insertion, setting up the header, footer, and other basic properties.
*   **`resource_file_info($doctitle)` and `resource_file_infoPhpSpreadsheet($spreadsheet, $doctitle)`:** These functions set the metadata for generated Excel files.
*   **`intialWorkSheetSetting($startCol)` and `intialWorkSheetSettingPhpSpreadsheet($spreadsheet)`:** These functions set the initial row and column dimensions for a new worksheet.
*   **`allRowsAutoHeight($lastrowwritten)` and `allRowsAutoHeightPhpSpreadsheet($spreadsheet, $lastrowwritten)`:** These functions set the row height to automatically adjust to the content.
*   **`setPage()` and `setPagePHPSpreadsheet($spreadsheet)`:** These functions set the page orientation, size, and margins for a worksheet.
*   **`printfileFromPHPExcel($file, $fileaddress)` and `printfileFromPHPSpreadsheet($spreadsheet, $file, $fileaddress)`:** These functions save the generated Excel or PDF file to the server.

### `site_helper.php`

This helper provides a collection of miscellaneous site-wide functions.

*   **`clean($string)`:** Cleans a string by replacing spaces with hyphens and removing special characters.
*   **`lrtrim($str)`:** Trims commas from the beginning and end of a string.
*   **`sortUnique($array)`:** Sorts an array and removes duplicate values.
*   **`mergeRange($data)`:** Merges overlapping or adjacent number ranges in an array.
*   **`decideRateCardNoHelp($rcardinput, $sorId)`:** Determines the correct rate card to use based on the user's input and the SOR ID.
*   **`getRowcountHelp($text, $width = 35)`:** Calculates the number of rows required to display a given text in a fixed-width column.
