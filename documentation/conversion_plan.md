## CodeIgniter to Laravel Conversion Plan

This document outlines the plan for converting the existing urCost application from CodeIgniter 3 to a modern Laravel application with improved functionality. The legacy application is located at `/var/www/html/urCost`, and the new Laravel application will be developed at `/var/www/newUrCost`.

### Guiding Principles & Lessons Learned

*   **Schema Baseline:** The `documentation/tables` folder from the legacy project will serve as the baseline for creating new database tables and models.
*   **Field Mapping:** To ensure a smooth transition and aid in debugging, all new migrations and models will include comments that map the old system's field names to the new ones. This will be crucial when translating old business logic.
*   **Configuration over Database:** Settings and configurations (like the ones in the old `site` table) will be moved to Laravel configuration files (e.g., `config/site.php`) for better version control and environment management.
*   **Continuous Learning:** All issues, errors, and lessons learned during the migration process will be documented in `migration_learning.md`. This document should be consulted to avoid repeating past mistakes. Refer to it for guidance on common pitfalls. It is crucial to consider foreign key constraints and avoid naming conflicts with Laravel's default tables (e.g., `sessions`, `jobs`) when planning and executing migrations.

### Table Migration Strategy

This section outlines the strategy for migrating the tables from the old system to the new system.

For a detailed overview of the table migration status, including old and new table names, documentation paths, and any identified issues, please refer to the [Table Migration Summary](./tables_summary.md) document.

*   **Settings to be moved to config files:** The following tables will be replaced by configuration files in the new system.
    *   `site`: Site-wide settings will be stored in `config/site.php`.
* First start migration for Laravel default tables then plan for old system tables 

### Data Migration Strategy
As we have maping of table and field of each table listed as Migration Summary](./tables_summary.md)
and documention and mapping of fileds from old to new system is already saved in documentation file mentioned in this table Migration Summary](./tables_summary.md)
so migration of data can be done through sql between two db tables.


### Phase 1: Project Setup and Initial Migration

1.  **Set up a new Laravel project:**
    *   Create a new Laravel project at `/var/www/newUrCost` using Composer.

2.  **Configure the database:**
    *   Configure the `.env` file in the new Laravel project to connect to the existing `sornew` database. See the [Database Schema](./database.md) for details on the database structure.

3.  **Create Migrations:**
    *   Create database migrations for the existing tables to manage the database schema within the Laravel project. This will start with the most critical tables. and in section as **Tables(111)** in this page 
    

4.  **Create Models:**
    *   Create Eloquent models for the migrated tables. These models will represent the application's data and will be used to interact with the database.
    *   Refer to the [Model-Table Relationships](./relationships.md) for a mapping of the existing models to their corresponding tables. also refer documentation/tables/*.md
   

5.  **Create Initial Controllers and Routes:**
    *   Create a set of controllers and routes to handle the basic functionality of listing SORs and their items. This will replicate the initial functionality of the `Sor.php` controller.
    *   Refer to the [Sor Controller Documentation](./controllers/Sor.md) for details on the existing functionality.

### Phase 2: Rate Analysis and Calculation

1.  **Migrate Rate Analysis Tables:**
    *   Create migrations for the tables related to rate analysis:
        all table as per order decided above .
        one way may be tried to just map coresponding table in `utechy6y_sor` db and fields in new table here in db `sornew` then refer documentation/tables/*.md where * is table name of `sornew` respective table . then make a query a per documentation to pass data from one db table to another table

2.  **Create Rate Analysis Models:**
    *   Create Eloquent models for the rate analysis tables.

3.  **Implement Rate Calculation Logic:**
    *   Re-implement the rate calculation logic from the `Ranamodel` and `Resratemodel` in the new Laravel application. This will involve creating service classes or using model methods to encapsulate the complex calculation logic.
    *   Refer to the [Ranamodel Documentation](./models/Ranamodel.md) and [Resratemodel Documentation](./models/Resratemodel.md) for details on the existing logic.

### Phase 3: User Management and Administration

1.  **Migrate User and Job Tables:**
    *   Create migrations for the `users`, `m_jobs`, `m_jobGr`, and `assign_job` tables.

2.  **Implement User Authentication and Authorization:**
    *   Use Laravel's built-in authentication system to handle user login and registration.
    *   Implement a role-based authorization system to manage user permissions, similar to the existing `jobsmodel`.

3.  **Create Administrative Interfaces:**
    *   Re-create the administrative interfaces from the `Additemscontroller` for managing SORs, items, resources, and rate cards.
    *   Refer to the [Additemscontroller Documentation](./controllers/Additemscontroller.md) for details on the existing administrative functionality.

### Phase 4: Reporting and Advanced Features

1.  **Implement Excel and PDF Reporting:**
    *   Integrate a Laravel-compatible library for generating Excel and PDF reports, such as `maatwebsite/excel` and `barryvdh/laravel-dompdf`.
    *   Re-implement the report generation functionality from the `Sor.php` controller and `Ranamodel`.

2.  **Implement Map-based Visualization:**
    *   Integrate a mapping library (e.g., Leaflet.js) to re-create the map-based visualization of resource rates from the `RateMap.php` controller.

3.  **Review and Refactor for Better Functionality:**
    *   Throughout the conversion process, identify opportunities to improve the application's functionality, performance, and user experience. This may include:
        *   Refactoring complex queries for better performance.
        *   Improving the user interface and user experience.
        *   Adding new features that were not present in the original application.
