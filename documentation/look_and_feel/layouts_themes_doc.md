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
