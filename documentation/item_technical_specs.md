# Item Technical Specifications Documentation

## Overview
The **Item Technical Specifications** module allows users to view, manage, and generate detailed technical specifications for construction items. This feature is integrated into the Item Skeleton view and supports AI-assisted generation, manual editing, and JSON-based data exchange.

## Database Schema
The data is stored in the `item_technical_specs` table, which has a one-to-one relationship with the `items` table.

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | BigInt | Primary Key |
| `item_id` | BigInt | Foreign Key to `items` table |
| `introduction` | Text | General introduction/description |
| `specifications` | JSON | Array of specification strings |
| `tests_frequency` | JSON | Array of objects: `{ "test": "...", "frequency": "..." }` |
| `dos_donts` | JSON | Object with arrays: `{ "dos": [...], "donts": [...] }` |
| `execution_sequence` | JSON | Array of step strings |
| `precautionary_measures` | JSON | Array of measure strings |
| `reference_links` | JSON | Array of objects: `{ "title": "...", "url": "..." }` |
| `created_at` | Timestamp | Creation timestamp |
| `updated_at` | Timestamp | Update timestamp |

## Features

### 1. View Specifications
- **Location**: Item Skeleton Page (`/sors/{sor}/items/{item}/skeleton`)
- **Display**: Renders specifications in a structured, readable format with icons and sections.
- **Empty State**: Shows "Generate with AI" or "Create Manually" buttons if no specs exist.

### 2. AI Generation
- **Provider**: Groq API (Llama-3.3-70b-versatile)
- **Trigger**: "Generate with AI" button.
- **Process**: Sends the item's description to the AI service, which returns a structured JSON object. This is automatically saved to the database.

### 3. Manual Editing
- **Location**: Dedicated Edit Page (`/items/{item}/specs/edit`)
- **Access**: Click "Edit" (if specs exist) or "Create Manually" (if empty).
- **Interface**: A full-page form with dynamic fields for adding/removing items in lists (Specs, Tests, Do's/Don'ts, etc.).

### 4. JSON Import & AI Prompt Helper
- **Import**: Users can paste a JSON object into the "Import from JSON" text area to auto-populate the form.
- **Copy AI Prompt**: A helper button generates a prompt containing the item's description and the required JSON structure. Users can copy this prompt, paste it into an external AI tool (like ChatGPT or Claude), and then paste the resulting JSON back into the system.

## Technical Implementation

### Routes (`routes/web.php`)
```php
// Generate Specs via AI
Route::post('items/{item}/generate-specs', [ItemTechnicalSpecController::class, 'generate']);

// Edit Page
Route::get('items/{item}/specs/edit', [ItemTechnicalSpecController::class, 'edit']);

// Update Specs (Save)
Route::put('items/{item}/specs', [ItemTechnicalSpecController::class, 'update']);
```

### Controller (`App\Http\Controllers\ItemTechnicalSpecController`)
- `generate(Item $item)`: Calls `ItemTechnicalSpecService` to generate and save specs.
- `edit(Item $item)`: Returns the `items.technical-specs.edit` view.
- `update(Request $request, Item $item)`: Validates and updates the `ItemTechnicalSpec` model.

### Service (`App\Services\ItemTechnicalSpecService`)
- Handles the interaction with the Groq API.
- Constructs the prompt and parses the JSON response.

### Views
- **Component**: `resources/views/components/item-technical-specs.blade.php`
    - Displays the specifications on the Skeleton page.
    - Handles the "Generate with AI" AJAX call.
- **Edit Page**: `resources/views/items/technical-specs/edit.blade.php`
    - Full-page Alpine.js application for editing.
    - Handles form state, dynamic array manipulation, and JSON import/export.

## Usage Guide

### Generating Specs with AI
1. Go to the Item Skeleton page.
2. Scroll to "Technical Specifications".
3. Click **"Generate with AI"**.
4. Wait for the process to complete (page will reload).

### Manually Editing
1. Click **"Edit"** on the Technical Specifications section.
2. You will be taken to the Edit page.
3. Modify fields as needed. Use the **"+"** buttons to add new rows and **Trash** icons to remove them.
4. Click **"Save Changes"** to persist.

### Using External AI (JSON Import)
1. On the Edit page, click **"Import from JSON"**.
2. Click **"Copy AI Prompt"**.
3. Paste the prompt into your preferred AI tool.
4. Copy the JSON output from the AI tool.
5. Paste it into the text area on the Edit page.
6. Click **"Apply JSON"**.
7. Review the populated form and click **"Save Changes"**.
