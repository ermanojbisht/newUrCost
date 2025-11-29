# Bulk Index Editor

## Overview
The Bulk Index Editor is a centralized interface for managing indices across all resources and rate cards. It addresses the need for a more efficient way to view, filter, and manage indices without navigating to each individual resource page.

## Features

### 1. Centralized Listing
*   **All Indices View**: Displays a paginated list of all indices (Labor or Machine) in the system.
*   **Columns**:
    *   ID
    *   Resource Name
    *   Rate Card Name
    *   Index Value
    *   Valid From / Valid To
    *   Status (Experimental, Current, Old)
    *   Created By
    *   Actions (Edit, Lock, Delete)

### 2. Advanced Filtering
Users can filter the list to focus on specific data sets:
*   **Resource Filter**: Dropdown to select a specific resource (e.g., "Mason", "Excavator").
*   **Rate Card Filter**: Dropdown to select a specific Rate Card (e.g., "Standard", "Project X").
*   **Apply Filter**: Updates the table dynamically based on selected criteria.

### 3. Bulk Management Actions
*   **Add Index**:
    *   Allows creating a new index for *any* resource directly from this screen.
    *   User selects the Resource and Rate Card from dropdowns.
*   **Edit Experimental Rates**:
    *   Directly edit any index with "Experimental" status.
    *   Useful for batch updates or corrections before locking.
*   **Lock & Promote**:
    *   Promote Experimental rates to Current status directly from the list.
    *   Follows the standard locking lifecycle (archives old rate, sets new rate as current).

## Navigation
The Bulk Editors are accessible via the sidebar under **SOR Administration**:
*   **Labor Indices**: `/labor-indices/all`
*   **Machine Indices**: `/machine-indices/all`

## Use Cases
*   **Project Setup**: Quickly adding indices for all resources for a new Rate Card.
*   **Annual Updates**: Reviewing and updating inflation adjustments (Global Indices) or specific resource rates for the new year.
*   **Audit**: Verifying that all resources have the correct indices applied for a specific project.
