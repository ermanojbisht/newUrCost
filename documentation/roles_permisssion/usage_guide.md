# Roles, Permissions, and Gates Usage Guide

This guide provides a comprehensive overview of how to use the roles, permissions, and authorization gates implemented in this project. This system is built upon the `spatie/laravel-permission` package.

## The Super Admin

A `super-admin` role has been configured to have unrestricted access to all parts of the application. This is achieved using Laravel's `Gate::before()` method in the `AuthServiceProvider`. Any user assigned the `super-admin` role will automatically pass all permission and role checks, regardless of what permissions are explicitly assigned to them.

```php
// app/Providers/AuthServiceProvider.php

Gate::before(function ($user, $ability) {
    return $user->hasRole('super-admin') ? true : null;
});
```

---

## Usage in Routes

You can protect routes by applying middleware. This is the recommended way to handle route-level authorization in Laravel 12.

### Protecting a Single Route

To protect a route, you can chain the `middleware` method to the route definition.

**By Permission:**
```php
// routes/web.php or routes/user-management.php

Route::get('/reports', function () {
    // ...
})->middleware('permission:view reports');
```

**By Role:**
```php
// routes/web.php or routes/user-management.php

Route::get('/admin/dashboard', function () {
    // ...
})->middleware('role:admin');
```

### Protecting a Group of Routes

It's often more convenient to group routes that share the same authorization requirements.

```php
// routes/web.php or routes/user-management.php

Route::middleware(['auth', 'permission:manage articles'])->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/create', [ArticleController::class, 'create']);
    Route::post('/articles', [ArticleController::class, 'store']);
});
```

### Multiple Roles or Permissions

You can require a user to have multiple roles or permissions by separating them with a `|` (pipe) character, which acts as an **OR** operator.

**Any Role:**
```php
// User must have the 'writer' OR 'editor' role
Route::get('/posts/edit', ...)->middleware('role:writer|editor');
```

**Any Permission:**
```php
// User must have the 'edit posts' OR 'publish posts' permission
Route::get('/posts/publish', ...)->middleware('permission:edit posts|publish posts');
```

---

## Usage in Controllers

While route middleware is the primary way to handle authorization, you can also perform checks within your controller methods.

### Authorizing within a Method

You can use the `can` method on the `User` model or the `request` object to check for permissions.

```php
// app/Http/Controllers/SomeController.php

public function update(Request $request, Post $post)
{
    if ($request->user()->can('edit', $post)) {
        // User can edit the post
    } else {
        abort(403, 'Unauthorized action.');
    }

    // Or, for a permission not tied to a model:
    if ($request->user()->can('publish articles')) {
        // ...
    }
}
```

---

## Usage in Blade Views

You can easily show or hide parts of your UI based on the user's roles and permissions using Blade directives.

### Checking for Permissions

The `@can` directive is used to check if a user has a specific permission.

```blade
{{-- resources/views/some-view.blade.php --}}

@can('edit articles')
    <a href="#" class="btn-primary">Edit Article</a>
@endcan
```

You can also use `@elsecan` and `@else`.

```blade
@can('publish articles')
    <button>Publish</button>
@elsecan('edit articles')
    <button>Save as Draft</button>
@else
    <p>You do not have permission to edit or publish.</p>
@endcan
```

### Checking for Roles

You can check for roles using the `@hasrole`, `@hasanyrole`, and `@hasallroles` directives.

**Single Role:**
```blade
@hasrole('writer')
    <p>You are a writer.</p>
@endhasrole
```

**Any Role (OR):**
```blade
@hasanyrole('writer|editor')
    <p>You are either a writer or an editor (or both).</p>
@endhasanyrole
```

**All Roles (AND):**
```blade
@hasallroles('writer|editor')
    <p>You are both a writer and an editor.</p>
@endhasallroles
```

This guide should provide a solid foundation for using the roles and permissions system throughout the application. For more advanced use cases, please refer to the official [Spatie Laravel Permission documentation](https://spatie.be/docs/laravel-permission/v6/).
