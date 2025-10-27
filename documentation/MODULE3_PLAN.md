# Module 3: Core Models CRUD Implementation Plan

## 1. Objective

The goal of this module is to implement robust CRUD (Create, Read, Update, Delete) functionality for the following core models of the urCost application:

- `Unit`
- `UnitGroup`
- `ResourceGroup`
- `TruckSpeed`
- `Sor`
- `ResourceCapacityRule`
- `PolSkeleton`
- `PolRate`
- `RateCard`

This implementation will adhere strictly to the project's established architecture, security, and UI/UX guidelines.

## 2. General Workflow for Each Model

I will process each model listed above sequentially, following these steps. The progress for each model will be tracked in the **Progress Tracker** section.

### Step 2.1: Permissions
- **Action:** Define granular permissions for the model (e.g., `view units`, `create units`, `edit units`, `delete units`).
- **File:** `database/seeders/RoleAndPermissionSeeder.php`
- **Details:** Assign the newly created permissions to the `sor-admin` role.

### Step 2.2: Policy
- **Action:** Create a model-specific policy to enforce the defined permissions.
- **File:** `app/Policies/{ModelName}Policy.php`
- **Details:** Implement `viewAny`, `view`, `create`, `update`, and `delete` methods. Register the policy in `app/Providers/AuthServiceProvider.php`.

### Step 2.3: Model & Relationships
- **Action:** Review the model and define any necessary Eloquent relationships with other models.
- **File:** `app/Models/{ModelName}.php`
- **Details:** Analyze the corresponding migration to ensure all fields are accounted for and relationships are correctly defined.

### Step 2.4: Routes
- **Action:** Add a resource route for the model.
- **File:** `routes/web.php`
- **Details:** The route will be protected by the `auth` middleware and will be grouped with other SOR administration routes.

### Step 2.5: Controller
- **Action:** Create a resource controller to handle all CRUD operations.
- **File:** `app/Http/Controllers/{ModelName}Controller.php`
- **Details:** Each method (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`) will use policy-based authorization and appropriate request validation.

### Step 2.6: Views
- **Action:** Develop Blade views for all user-facing CRUD operations.
- **Directory:** `resources/views/pages/{model-name}/`
- **Files:**
    - `index.blade.php`: Main listing view with a responsive table/card layout.
    - `create.blade.php`: View for creating a new entry.
    - `edit.blade.php`: View for editing an existing entry.
    - `_form.blade.php`: A reusable partial for the create and edit forms to ensure consistency.
- **Details:** All views will extend the main app layout and use the established UI components, icons, and Glassmorphism theme.

## 3. Rules & Obligatory Points

1.  **Code Quality:** All PHP code MUST be PSR-12 compliant.
2.  **UI/UX:** All views MUST adhere to the guidelines in `documentation/look_and_feel/layouts_themes_doc.md`. This includes:
    - Using the `layout001.app` layout.
    - Implementing both Dark and Light themes correctly.
    - Ensuring a mobile-first, responsive design.
    - Using SVG icons from `config/icons.php`.
    - Applying the Glassmorphism aesthetic where appropriate.
3.  **Security:**
    - All create, update, and delete operations MUST be protected by authorization policies.
    - All user input MUST be validated on both the client-side (using Alpine.js where necessary) and server-side (using Form Requests).
4.  **Documentation:** The `GEMINI.md` file will be updated with any global changes or patterns that emerge during this module's development. This plan file (`MODULE3_PLAN.md`) will be kept up-to-date to reflect the current progress.
5.  **Error Handling:** Implement robust `try-catch` blocks for database operations and provide user-friendly feedback for errors.
6.  **Atomic Commits:** Changes will be committed logically after completing a significant step for each model.

## 4. Progress Tracker

| Model                   | Status      | Permissions | Policy | Model & Relationships | Routes | Controller | Views |
| ----------------------- | ----------- | ----------- | ------ | --------------------- | ------ | ---------- | ----- |
| **Unit**                | **Done**        | ✅           | ✅      | ✅                     | ✅      | ✅          | ✅     |
| **UnitGroup**           | **Done**        | ✅           | ✅      | ✅                     | ✅      | ✅          | ✅     |
| **ResourceGroup**       | **Done**        | ✅           | ✅      | ✅                     | ✅      | ✅          | ✅     |
| **TruckSpeed**          | **Done**        | ✅           | ✅      | ✅                     | ✅      | ✅          | ✅     |
| **Sor**                 | **Done**        | ✅           | ✅      | ✅                     | ✅      | ✅          | ✅     |
| **ResourceCapacityRule**| **Done**        | ✅           | ✅      | ✅                     | ✅      | ✅          | ✅     |
| **PolSkeleton**         | **Done**        | ✅           | ✅      | ✅                     | ✅      | ✅          | ✅     |
| **PolRate**             | **Done**        | ✅           | ✅      | ✅                     | ✅      | ✅          | ✅     |
| **RateCard**            | **In Progress** | ✅           | ✅      | ✅                     | ✅      | ✅          | ☐     |

---
*I will update the checkboxes (`☐` to `✅`) as each step is completed.*
