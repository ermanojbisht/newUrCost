# Database Seeding Plan

This document outlines the plan for seeding the new `sornew` database from the legacy `utechy6y_sor` database.

## 1. Strategy

We will create a dedicated seeder class for each database table. This approach keeps the logic for each table separate, making it easier to manage, debug, and re-run if necessary. The seeders will directly query the legacy database, transform the data to fit the new schema, and insert it into the new Laravel database.

We will disable mass-assignment guarding on the models during the seeding process to allow for efficient data insertion.

## 2. Order of Execution

To respect foreign key constraints, the seeders must be run in a specific order. The `DatabaseSeeder` class will be updated to call each seeder in this sequence:

1.  **`SorSeeder`**: Populates the `sors` table.
2.  **`RatecardSeeder`**: Populates the `ratecards` table.
3.  **`ResourceSeeder`**: Populates the `resources` table.
4.  **`ItemSeeder`**: Populates the `items` table (depends on `sors`).
5.  **`RateSeeder`**: Populates the `rates` table (depends on `resources`, `ratecards`).
6.  **`SkeletonSeeder`**: Populates the `skeletons` table (depends on `items`, `resources`).
7.  **`SubitemSeeder`**: Populates the `subitems` table (depends on `items`).
8.  **`OheadSeeder`**: Populates the `oheads` table (depends on `items`).
9.  **`LeadDistanceSeeder`**: Populates the `lead_distances` table (depends on `resources`, `ratecards`).
10. **`LaborIndexSeeder`**: Populates the `labor_indices` table (depends on `resources`, `ratecards`).
11. **`MachineIndexSeeder`**: Populates the `machine_indices` table (depends on `resources`, `ratecards`).

## 3. First Step

The immediate next step is to create and execute the `SorSeeder`.
