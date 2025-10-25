## Database Schema

This document outlines the structure of the `utechy6y_sor` database. Below is a description of the key tables and their columns.

Get table information from folder documentation/tables . each file reprsent one table from old system


## Table Relationships

This section describes the relationships between the key tables in the `utechy6y_sor`  database and may be replecated in new database `sornew`.

### `sor` and `item`

*   **Relationship:** One-to-Many
*   **Description:** The `sor` table has a one-to-many relationship with the `item` table. Each SOR in the `sor` table can have multiple items and chapters associated with it in the `item` table. The `item.sorId` column links to the `sor.sorid` column.

### `item` and `item` (Self-referencing)

*   **Relationship:** One-to-Many (Hierarchical)
*   **Description:** The `item` table has a self-referencing relationship to represent the hierarchical structure of chapters and items. The `item.chParentId` column links to the `item.chId` column of the parent chapter.

### `item` and `skeleton`

*   **Relationship:** One-to-Many
*   **Description:** The `item` table has a one-to-many relationship with the `skeleton` table. Each SOR item in the `item` table can have multiple resource entries in the `skeleton` table, defining its resource requirements. The `skeleton.raitemid` column links to the `item.itemcode` column.

### `item` and `subitem`

*   **Relationship:** One-to-Many
*   **Description:** The `item` table has a one-to-many relationship with the `subitem` table. Each SOR item in the `item` table can have multiple sub-items, which are themselves other SOR items. The `subitem.raitemid` column links to the `item.itemcode` of the main item, and the `subitem.subraitem` column links to the `item.itemcode` of the sub-item.

### `item` and `ohead`

*   **Relationship:** One-to-Many
*   **Description:** The `item` table has a one-to-many relationship with the `ohead` table. Each SOR item can have multiple overheads applied to it. The `ohead.raitemid` column links to the `item.itemcode` column.

### `resource` and `skeleton`

*   **Relationship:** One-to-Many
*   **Description:** The `resource` table has a one-to-many relationship with the `skeleton` table. Each resource can be used in the skeleton of multiple SOR items. The `skeleton.resourceid` column links to the `resource.code` column.

### `resource` and `rate`

*   **Relationship:** One-to-Many
*   **Description:** The `resource` table has a one-to-many relationship with the `rate` table. Each resource can have multiple rates defined for different rate cards. The `rate.resourceid` column links to the `resource.code` column.

### `ratecard` and `rate`

*   **Relationship:** One-to-Many
*   **Description:** The `ratecard` table has a one-to-many relationship with the `rate` table. Each rate card can have multiple resource rates defined for it. The `rate.ratecard` column links to the `ratecard.ratecardid` column.
