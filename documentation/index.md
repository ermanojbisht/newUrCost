# urCost Application Documentation

This document provides a detailed explanation of the urCost application, a CodeIgniter 3 based system for managing and calculating Schedule of Rates (SOR).

## 1. Core Concepts

The application revolves around the following core concepts:

*   **Man, Material, Machine (MMM):** These are the basic resources that form the building blocks of any cost analysis.
*   **Rate Card:** Uttarakhand is divided into several regions, and each region has a "Rate Card" that defines the rates for different MMM resources at a particular time.
*   **Item:** An item represents a specific task or product and has a defined "skeleton" that specifies the quantity of MMM resources required, along with overhead percentages.
*   **Rate of an Item:** The rate of an item is calculated by applying the rates from a region's Rate Card to the item's skeleton.
*   **Schedule of Rates (SOR):** An SOR is a collection of items. It provides a comprehensive list of items and their calculated rates.
*   **Rate Analysis (RA):** This is the detailed breakdown of how the rate of an item is calculated, showing the contribution of each MMM resource and overhead.

## 2. Application Architecture

The application is built on the CodeIgniter 3 framework and follows a standard Model-View-Controller (MVC) architecture.

*   **Models:** Located in `application/models`, these are responsible for all database interactions.
*   **Views:** Located in `application/views`, these are responsible for the presentation layer and rendering HTML.
*   **Controllers:** Located in `application/controllers`, these handle user requests, process data from models, and load the appropriate views.

## 3. File-wise Documentation

This section provides a detailed description of each important file in the application.

### 3.1. Controllers

*   [Additemscontroller](./controllers/Additemscontroller.md)
*   [RateMap](./controllers/RateMap.md)
*   [Resourcectr](./controllers/Resourcectr.md)
*   [Sor](./controllers/Sor.md)

### 3.2. Models

*   [Additemsmodel](./models/Additemsmodel.md)
*   [Raitemmodel](./models/Raitemmodel.md)
*   [Ranamodel](./models/Ranamodel.md)
*   [Rcardmodel](./models/Rcardmodel.md)
*   [Resratemodel](./models/Resratemodel.md)
*   [Soritemmodel](./models/Soritemmodel.md)
*   [Sormodel](./models/Sormodel.md)

### 3.3. Database

*   [Database Schema](./database.md)

### 3.4. Relationships

*   [Controller-Model and Model-Table Relationships](./relationships.md)

### 3.5. Helpers

*   [Helpers](./helpers.md)

### 3.6. Libraries

*   [Libraries](./libraries.md)

### 3.7. Conversion Plan

*   [Conversion Plan](./conversion_plan.md)
