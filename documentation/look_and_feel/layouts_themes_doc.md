# Layouts and Themes

This document outlines the approach for handling layouts and themes in the urCost conversion project.

## CSS Framework

We will use **Tailwind CSS** for styling the application.

## Theming

The application will support both **dark** and **light** themes. The default theme will be **dark**. A mechanism will be provided for the user to switch between themes.

## Layout Structure

Layouts will be organized in the `resources/views/layouts` directory. Each layout will have its own directory, for example `layout001`.

Inside each layout directory, there will be a `_partials` directory to store reusable partial views.

Example structure:

```
resources/
└── views/
    └── layouts/
        └── layout001/
            ├── app.blade.php
            └── _partials/
                ├── header.blade.php
                ├── footer.blade.php
                └── sidebar.blade.php
```

The main layout file (e.g., `app.blade.php`) will include the partials using Blade's `@include` or `@extends` directives. The content of the pages will be injected using `@yield` and `@section`.

## Tailwind CSS v4 Configuration

This project uses Tailwind CSS v4 with Vite. The configuration is different from previous versions and follows a "CSS-first" approach.

1.  **Vite Configuration (`vite.config.js`):**
    The `@tailwindcss/vite` plugin is used. No other Tailwind or PostCSS configuration is needed here.

    ```javascript
    import { defineConfig } from 'vite';
    import laravel from 'laravel-vite-plugin';
    import tailwindcss from '@tailwindcss/vite';

    export default defineConfig({
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
    });
    ```

2.  **Main CSS File (`resources/css/app.css`):**
    This is the most important file for configuration. It uses the `@import` and `@source` directives.

    ```css
    /* Import all of Tailwind's styles */
    @import "tailwindcss";

    /* Tell Tailwind where to scan for classes */
    @source "../views/**/*.blade.php";

    /* Official plugins can be added here */
    @plugin '@tailwindcss/forms';
    ```

3.  **Simplified `tailwind.config.js`:**
    For a basic setup, the `content` array is no longer needed in this file, as its job is handled by the `@source` directive in the CSS.

