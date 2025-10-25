# Layout and Theme Documentation Guide

## Table of Contents
1. [Overview](#overview)
2. [Technology Stack](#technology-stack)
3. [Directory Structure](#directory-structure)
4. [Theme Implementation](#theme-implementation)
5. [Layout Components](#layout-components)
6. [Blade File Standards](#blade-file-standards)
7. [Reusable Components](#reusable-components)
8. [Mobile-First Approach](#mobile-first-approach)
9. [Best Practices](#best-practices)
10. [Code Examples](#code-examples)
11. [HTML Entities & Special Characters](#html-entities--special-characters)
12. [Glass Morphism Theme](#glass-morphism-theme)
13. [Loading States & Skeletons](#loading-states--skeletons)
14. [Tooltips & Popovers](#tooltips--popovers)
15. [Charts & Data Visualization](#charts--data-visualization)

## Overview

This document serves as the definitive guide for creating and maintaining layouts, themes, and UI components in the urCost Laravel application. All new views and layouts MUST follow these guidelines to ensure consistency, maintainability, and optimal user experience.

## Technology Stack

### Core Technologies
- **Framework**: Laravel 12
- **CSS Framework**: Tailwind CSS v4.1+
- **JavaScript**: Alpine.js v3.15+
- **Build Tool**: Vite v7+
- **Icons**: Custom SVG icons stored in `config/icons.php`
- **Datatables**: Yajra DataTables

### Package Versions
```json
{
    "@tailwindcss/vite": "^4.0.0",
    "tailwindcss": "^4.0.0",
    "@tailwindcss/forms": "^0.5.10",
    "alpinejs": "^3.15.0",
    "laravel-vite-plugin": "^2.0.0"
}
```

## Directory Structure

### Layout Organization
```
resources/
└── views/
    ├── layouts/
    │   └── layout001/
    │       ├── app.blade.php           # Main layout file
    │       └── _partials/
    │           ├── header.blade.php    # Navigation header
    │           ├── footer.blade.php    # Page footer
    │           ├── sidebar.blade.php   # Sidebar navigation
    │           ├── mobile-menu.blade.php # Mobile navigation
    │           ├── flash-messages.blade.php # Alert messages
    │           ├── breadcrumbs.blade.php # Breadcrumb navigation
    │           └── theme-toggle-icons.blade.php # Theme switcher icons
    ├── components/
    │   ├── buttons/
    │   │   ├── primary.blade.php
    │   │   ├── secondary.blade.php
    │   │   └── danger.blade.php
    │   ├── forms/
    │   │   ├── input.blade.php
    │   │   ├── select.blade.php
    │   │   ├── textarea.blade.php
    │   │   └── checkbox.blade.php
    │   ├── alerts/
    │   │   ├── success.blade.php
    │   │   ├── error.blade.php
    │   │   ├── warning.blade.php
    │   │   └── info.blade.php
    │   └── cards/
    │       ├── basic.blade.php
    │       ├── stats.blade.php
    │       └── table.blade.php
    └── pages/
        └── [module-name]/
            ├── index.blade.php
            ├── create.blade.php
            ├── edit.blade.php
            └── show.blade.php
```

## Theme Implementation

### Dark Mode Configuration

#### 1. Tailwind CSS v4 Setup (`resources/css/app.css`)
```css
@import "tailwindcss";

/* Import Tailwind Forms plugin */
@plugin "@tailwindcss/forms";

/* Dark mode variant support for Tailwind CSS v4 */
@variant dark (&:where(.dark, .dark *));

/* Base styles for smooth transitions */
@layer base {
    * {
        @apply transition-colors duration-200;
    }

    html {
        @apply scroll-smooth;
    }

    body {
        @apply antialiased;
    }
}

/* Custom utilities for dark mode */
@layer utilities {
    .text-primary {
        @apply text-gray-900 dark:text-white;
    }

    .text-secondary {
        @apply text-gray-700 dark:text-gray-300;
    }

    .bg-primary {
        @apply bg-white dark:bg-gray-800;
    }

    .bg-secondary {
        @apply bg-gray-50 dark:bg-gray-700;
    }

    .border-primary {
        @apply border-gray-200 dark:border-gray-600;
    }
}

/* Reusable component styles */
@layer components {
    .btn-primary {
        @apply bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg 
               transition-all duration-200 hover:shadow-lg active:scale-95;
    }

    .btn-secondary {
        @apply bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 
               text-gray-800 dark:text-white font-bold py-2 px-4 rounded-lg 
               transition-all duration-200;
    }

    .btn-danger {
        @apply bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg 
               transition-all duration-200 hover:shadow-lg;
    }

    .btn-success {
        @apply bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg 
               transition-all duration-200 hover:shadow-lg;
    }

    .btn-warning {
        @apply bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg 
               transition-all duration-200 hover:shadow-lg;
    }

    .card {
        @apply bg-white dark:bg-gray-800 rounded-lg shadow-md p-6;
    }

    .card-hover {
        @apply card hover:shadow-xl transition-shadow duration-200 cursor-pointer;
    }

    .input-field {
        @apply w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 
               border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 
               focus:ring-blue-500 text-gray-900 dark:text-white 
               placeholder-gray-500 dark:placeholder-gray-400;
    }

    .label {
        @apply block text-gray-700 dark:text-gray-300 font-bold mb-2;
    }

    .table-responsive {
        @apply min-w-full divide-y divide-gray-200 dark:divide-gray-700;
    }

    .table-header {
        @apply bg-gray-50 dark:bg-gray-700;
    }

    .table-row {
        @apply hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors;
    }

    .badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }

    .badge-primary {
        @apply badge bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200;
    }

    .badge-success {
        @apply badge bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200;
    }

    .badge-danger {
        @apply badge bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200;
    }

    .badge-warning {
        @apply badge bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200;
    }
}
```

#### 2. Vite Configuration (`vite.config.js`)
```javascript
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

#### 3. Tailwind Configuration (`tailwind.config.js`)
```javascript
/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
        }
      }
    },
  },
  plugins: [],
}
```

#### 4. Alpine.js Setup (`resources/js/app.js`)
```javascript
import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Global Alpine stores
Alpine.store('theme', {
    current: localStorage.getItem('theme') || 'dark',
    
    toggle() {
        this.current = this.current === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.current);
        this.updateDOM();
    },
    
    updateDOM() {
        if (this.current === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    
    init() {
        this.updateDOM();
    }
});

// Global utility functions
Alpine.data('dropdown', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    }
}));

Alpine.start();
```

## Layout Components

### Main Layout Template (`resources/views/layouts/layout001/app.blade.php`)

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'urCost'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Head Styles -->
    @yield('headstyles')
    @stack('styles')
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans antialiased"
      x-data="{
          theme: localStorage.getItem('theme') || 'dark',
          sidebarOpen: false,
          mobileMenuOpen: false,
          
          toggleTheme() {
              this.theme = this.theme === 'light' ? 'dark' : 'light';
              localStorage.setItem('theme', this.theme);
              this.updateThemeClass();
          },
          
          updateThemeClass() {
              if (this.theme === 'dark') {
                  document.documentElement.classList.add('dark');
                  document.documentElement.classList.remove('light');
              } else {
                  document.documentElement.classList.add('light');
                  document.documentElement.classList.remove('dark');
              }
          }
      }"
      x-init="
          updateThemeClass();
          $watch('theme', () => updateThemeClass());
      ">

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Header -->
        @include('layouts.layout001._partials.header')

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>

        <!-- Main Content Area -->
        <div class="flex">
            <!-- Sidebar (if needed) -->
            @hasSection('sidebar')
                <aside class="fixed lg:static inset-y-0 left-0 z-50 w-64 transform lg:transform-none 
                             transition-transform duration-200 bg-white dark:bg-gray-800 shadow-lg"
                       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
                    @yield('sidebar')
                </aside>
            @endif

            <!-- Main Content -->
            <main class="flex-1 w-full">
                <!-- Breadcrumbs -->
                @hasSection('breadcrumbs')
                    <div class="bg-white dark:bg-gray-800 shadow-sm">
                        <div class="container mx-auto px-4 py-3">
                            @yield('breadcrumbs')
                        </div>
                    </div>
                @endif

                <!-- Page Header -->
                @hasSection('page-header')
                    <div class="bg-white dark:bg-gray-800 shadow">
                        <div class="container mx-auto px-4 py-6">
                            @yield('page-header')
                        </div>
                    </div>
                @endif

                <!-- Flash Messages -->
                <div class="container mx-auto px-4 mt-4">
                    @include('layouts.layout001._partials.flash-messages')
                </div>

                <!-- Main Content Container -->
                <div class="container mx-auto px-4 py-8">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Footer -->
        @include('layouts.layout001._partials.footer')
    </div>

    <!-- Mobile Action Button -->
    @hasSection('sidebar')
        <button @click="sidebarOpen = !sidebarOpen"
                class="fixed bottom-4 right-4 lg:hidden bg-blue-500 hover:bg-blue-600 
                       text-white p-3 rounded-full shadow-lg z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" 
                      stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" 
                      stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
```

### Flash Messages Partial (`resources/views/layouts/layout001/_partials/flash-messages.blade.php`)

```blade
@if(session('success'))
    <div x-data="{ show: true }"
         x-show="show"
         x-transition
         x-init="setTimeout(() => show = false, 5000)"
         class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900 dark:text-green-100 p-4 rounded">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-green-700 dark:text-green-100">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }"
         x-show="show"
         x-transition
         x-init="setTimeout(() => show = false, 5000)"
         class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900 dark:text-red-100 p-4 rounded">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-red-700 dark:text-red-100">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
@endif

@if(session('warning'))
    <div x-data="{ show: true }"
         x-show="show"
         x-transition
         x-init="setTimeout(() => show = false, 5000)"
         class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-100 p-4 rounded">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
            <button @click="show = false" class="text-yellow-700 dark:text-yellow-100">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
@endif

@if($errors->any())
    <div x-data="{ show: true }"
         x-show="show"
         x-transition
         class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900 dark:text-red-100 p-4 rounded">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="font-bold mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="text-red-700 dark:text-red-100 ml-4">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
@endif
```

## Blade File Standards

### 1. File Naming Convention
- Use kebab-case for blade files: `user-profile.blade.php`
- Partial files start with underscore: `_sidebar.blade.php`
- Component files are descriptive: `primary-button.blade.php`

### 2. Section Structure
Every page blade file MUST follow this structure:

```blade
@extends('layouts.layout001.app')

@section('title', 'Page Title')

@section('breadcrumbs')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-500">
                    {!! config('icons.home') !!}
                    <span class="ml-1">Home</span>
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-500 dark:text-gray-400">Current Page</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Page Title</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Brief description of the page
            </p>
        </div>
        <div class="mt-4 md:mt-0 space-x-2">
            <button class="btn-secondary">Secondary Action</button>
            <button class="btn-primary">Primary Action</button>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="card">
                <!-- Content here -->
            </div>
        </div>
        
        <!-- Sidebar -->
        <div>
            <div class="card">
                <!-- Sidebar content -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Page-specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize page-specific functionality
        });
    </script>
@endpush
```

## Reusable Components

### 1. Button Component (`resources/views/components/button.blade.php`)

```blade
@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, success, warning
    'size' => 'md', // sm, md, lg
    'fullWidth' => false,
    'disabled' => false,
    'loading' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    $variantClasses = [
        'primary' => 'bg-blue-500 hover:bg-blue-600 text-white focus:ring-blue-500',
        'secondary' => 'bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white focus:ring-gray-500',
        'danger' => 'bg-red-500 hover:bg-red-600 text-white focus:ring-red-500',
        'success' => 'bg-green-500 hover:bg-green-600 text-white focus:ring-green-500',
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white focus:ring-yellow-500',
    ];
    
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg',
    ];
    
    $classes = $baseClasses . ' ' . 
               $variantClasses[$variant] . ' ' . 
               $sizeClasses[$size] . ' ' .
               ($fullWidth ? 'w-full' : '') . ' ' .
               ($disabled || $loading ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-lg active:scale-95');
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
    {{ $disabled || $loading ? 'disabled' : '' }}
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif
    {{ $slot }}
</button>
```

### 2. Input Component (`resources/views/components/form/input.blade.php`)

```blade
@props([
    'type' => 'text',
    'label' => '',
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'helpText' => '',
    'icon' => null,
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                {!! $icon !!}
            </div>
        @endif
        
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => 'input-field' . 
                          ($icon ? ' pl-10' : '') . 
                          ($error || $errors->has($name) ? ' border-red-500 focus:ring-red-500' : '')
            ]) }}
        />
    </div>
    
    @if($helpText)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $helpText }}</p>
    @endif
    
    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
```

### 3. Card Component (`resources/views/components/card.blade.php`)

```blade
@props([
    'title' => '',
    'subtitle' => '',
    'footer' => null,
    'padding' => true,
    'hover' => false,
])

<div {{ $attributes->merge(['class' => 'card' . ($hover ? ' card-hover' : '') . ($padding ? '' : ' p-0')]) }}>
    @if($title || $subtitle)
        <div class="{{ $padding ? '' : 'px-6 pt-6' }} {{ $title || $subtitle ? 'mb-4' : '' }}">
            @if($title)
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    <div class="{{ $padding ? '' : 'px-6' }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="mt-6 {{ $padding ? '-mx-6 -mb-6' : '' }} px-6 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-b-lg">
            {{ $footer }}
        </div>
    @endif
</div>
```

### 4. Alert Component (`resources/views/components/alert.blade.php`)

```blade
@props([
    'type' => 'info', // info, success, warning, error
    'dismissible' => true,
    'icon' => true,
])

@php
    $typeClasses = [
        'info' => 'bg-blue-100 border-blue-500 text-blue-700 dark:bg-blue-900 dark:text-blue-100',
        'success' => 'bg-green-100 border-green-500 text-green-700 dark:bg-green-900 dark:text-green-100',
        'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-100',
        'error' => 'bg-red-100 border-red-500 text-red-700 dark:bg-red-900 dark:text-red-100',
    ];
    
    $icons = [
        'info' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>',
        'success' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
        'warning' => '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
        'error' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
    ];
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition
     @if($dismissible) x-init="setTimeout(() => show = false, 5000)" @endif
     {{ $attributes->merge(['class' => 'border-l-4 p-4 rounded ' . $typeClasses[$type]]) }}>
    <div class="flex items-start">
        @if($icon)
            <svg class="flex-shrink-0 w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                {!! $icons[$type] !!}
            </svg>
        @endif
        <div class="flex-1">
            {{ $slot }}
        </div>
        @if($dismissible)
            <button @click="show = false" class="flex-shrink-0 ml-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        @endif
    </div>
</div>
```

### 4. Alert Component (`resources/views/components/alert.blade.php`)

```blade
@props([
    'type' => 'info', // info, success, warning, error
    'dismissible' => true,
    'icon' => true,
])

@php
    $typeClasses = [
        'info' => 'bg-blue-100 border-blue-500 text-blue-700 dark:bg-blue-900 dark:text-blue-100',
        'success' => 'bg-green-100 border-green-500 text-green-700 dark:bg-green-900 dark:text-green-100',
        'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-100',
        'error' => 'bg-red-100 border-red-500 text-red-700 dark:bg-red-900 dark:text-red-100',
    ];
    
    $icons = [
        'info' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>',
        'success' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
        'warning' => '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
        'error' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
    ];
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition
     @if($dismissible) x-init="setTimeout(() => show = false, 5000)" @endif
     {{ $attributes->merge(['class' => 'border-l-4 p-4 rounded ' . $typeClasses[$type]]) }}>
    <div class="flex items-start">
        @if($icon)
            <svg class="flex-shrink-0 w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                {!! $icons[$type] !!}
            </svg>
        @endif
        <div class="flex-1">
            {{ $slot }}
        </div>
        @if($dismissible)
            <button @click="show = false" class="flex-shrink-0 ml-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        @endif
    </div>
</div>
```

### 5. Select Component (`resources/views/components/form/select.blade.php`)

```blade
@props([
    'label' => '',
    'name' => '',
    'options' => [],
    'selected' => null,
    'placeholder' => '-- Select --',
    'required' => false,
    'disabled' => false,
    'error' => null,
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'input-field' . 
                      ($error || $errors->has($name) ? ' border-red-500 focus:ring-red-500' : '')
        ]) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>
    
    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
```

## Mobile-First Approach

### Responsive Breakpoints
Always design for mobile first, then scale up:

```css
/* Mobile First Approach */
.element {
    /* Mobile styles (default) */
    @apply text-sm p-2;
    
    /* Tablet (md: 768px+) */
    @apply md:text-base md:p-4;
    
    /* Desktop (lg: 1024px+) */
    @apply lg:text-lg lg:p-6;
    
    /* Wide Desktop (xl: 1280px+) */
    @apply xl:text-xl xl:p-8;
}
```

### Mobile Navigation Pattern
```blade
<!-- resources/views/layouts/layout001/_partials/mobile-menu.blade.php -->
<div x-show="mobileMenuOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform -translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform -translate-y-2"
     @click.away="mobileMenuOpen = false"
     class="md:hidden absolute top-full left-0 right-0 bg-white dark:bg-gray-800 shadow-lg">
    <div class="px-4 py-2 space-y-1">
        <a href="{{ route('dashboard') }}" 
           class="block px-3 py-2 rounded-md text-base font-medium 
                  {{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Dashboard
        </a>
        <!-- Add more menu items -->
    </div>
</div>
```

### Mobile-Specific Components

#### Mobile Table (`resources/views/components/mobile-table.blade.php`)
```blade
@props(['items', 'columns', 'actions' => null])

<!-- Desktop Table -->
<div class="hidden md:block overflow-x-auto">
    <table class="table-responsive">
        <thead class="table-header">
            <tr>
                @foreach($columns as $column)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ $column['label'] }}
                    </th>
                @endforeach
                @if($actions)
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($items as $item)
                <tr class="table-row">
                    @foreach($columns as $column)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ data_get($item, $column['field']) }}
                        </td>
                    @endforeach
                    @if($actions)
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            {{ $actions($item) }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="md:hidden space-y-4">
    @foreach($items as $item)
        <div class="card">
            @foreach($columns as $column)
                <div class="flex justify-between py-2 {{ !$loop->last ? 'border-b border-gray-200 dark:border-gray-700' : '' }}">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $column['label'] }}:
                    </span>
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ data_get($item, $column['field']) }}
                    </span>
                </div>
            @endforeach
            @if($actions)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $actions($item) }}
                </div>
            @endif
        </div>
    @endforeach
</div>
```

## Best Practices

### 1. Performance Optimization
- Use `@once` directive for one-time includes
- Implement lazy loading for images: `loading="lazy"`
- Use pagination for large datasets (Yajra DataTables for complex tables)
- Minimize inline styles - use Tailwind classes
- Cache frequently accessed data
- Use `@production` directive for production-only code

### 2. Accessibility
- Always include ARIA labels for interactive elements
- Maintain proper heading hierarchy (h1 → h2 → h3)
- Ensure keyboard navigation support (tab order)
- Provide alt text for images
- Use semantic HTML elements
- Include skip navigation links
- Ensure color contrast ratios meet WCAG standards

### 3. Form Handling
```blade
<form method="POST" action="{{ route('resource.store') }}" 
      x-data="{ 
          loading: false,
          errors: {},
          validateField(field) {
              // Client-side validation logic
          }
      }" 
      @submit.prevent="
          loading = true;
          fetch($el.action, {
              method: 'POST',
              body: new FormData($el),
              headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Accept': 'application/json'
              }
          })
          .then(response => response.json())
          .then(data => {
              if (data.errors) {
                  errors = data.errors;
                  loading = false;
              } else {
                  window.location.href = data.redirect;
              }
          })
          .catch(error => {
              console.error('Error:', error);
              loading = false;
          })
      ">
    @csrf
    
    <x-form.input 
        name="name" 
        label="Name" 
        required 
        x-model="name"
        @blur="validateField('name')"
        :error="errors.name"
    />
    
    <x-form.input 
        type="email"
        name="email" 
        label="Email" 
        required 
        x-model="email"
        @blur="validateField('email')"
        :error="errors.email"
    />
    
    <div class="flex justify-end space-x-2 mt-6">
        <x-button type="button" variant="secondary" @click="window.history.back()">
            Cancel
        </x-button>
        <x-button type="submit" :loading="loading" x-bind:disabled="loading">
            <span x-show="!loading">Save</span>
            <span x-show="loading">Saving...</span>
        </x-button>
    </div>
</form>
```

### 4. AJAX Implementation with Error Handling
```blade
<div x-data="{
    items: [],
    loading: false,
    error: null,
    page: 1,
    hasMore: true,
    
    async fetchItems() {
        this.loading = true;
        this.error = null;
        
        try {
            const response = await fetch(`{{ route('api.items') }}?page=${this.page}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            this.items = [...this.items, ...data.data];
            this.hasMore = data.next_page_url !== null;
            this.page++;
        } catch (error) {
            console.error('Error:', error);
            this.error = 'Failed to load items. Please try again.';
        } finally {
            this.loading = false;
        }
    },
    
    async deleteItem(id) {
        if (!confirm('Are you sure?')) return;
        
        try {
            const response = await fetch(`{{ route('api.items.destroy', '') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                this.items = this.items.filter(item => item.id !== id);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to delete item');
        }
    }
}" x-init="fetchItems()">
    
    <!-- Error Message -->
    <div x-show="error" class="mb-4">
        <x-alert type="error" :dismissible="false" x-text="error"></x-alert>
    </div>
    
    <!-- Loading State -->
    <div x-show="loading && items.length === 0" class="text-center py-8">
        <svg class="inline animate-spin h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="mt-2 text-gray-500">Loading items...</p>
    </div>
    
    <!-- Items Grid -->
    <div x-show="!loading || items.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="item in items" :key="item.id">
            <div class="card relative">
                <button @click="deleteItem(item.id)" 
                        class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <h3 class="font-bold" x-text="item.name"></h3>
                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="item.description"></p>
            </div>
        </template>
    </div>
    
    <!-- Load More Button -->
    <div x-show="hasMore && !loading && items.length > 0" class="text-center mt-6">
        <button @click="fetchItems()" class="btn-primary">
            Load More
        </button>
    </div>
</div>
```

### 5. Icon Management System

#### Icon Configuration (`config/icons.php`)
```php
<?php

return [
    // Navigation Icons
    'home' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
    
    'user' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
    
    'settings' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.065-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
    
    // Action Icons
    'edit' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
    
    'delete' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>',
    
    'add' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>',
    
    'search' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>',
    
    'filter' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>',
    
    'download' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>',
    
    'upload' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>',
    
    // Status Icons
    'check' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
    
    'x' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
    
    'info' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    
    'warning' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
    
    // Arrows
    'arrow-left' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>',
    
    'arrow-right' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>',
    
    'chevron-down' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>',
    
    'chevron-up' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>',
];
```

#### Using Icons in Blade Templates
```blade
<!-- Basic usage -->
<button class="btn-primary flex items-center">
    {!! config('icons.add') !!}
    <span class="ml-2">Add New</span>
</button>

<!-- With custom color -->
<a href="{{ route('home') }}" class="flex items-center text-blue-500 hover:text-blue-700">
    <span class="text-current">{!! config('icons.home') !!}</span>
    <span class="ml-2">Home</span>
</a>

<!-- Icon-only button -->
<button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" 
        aria-label="Settings">
    {!! config('icons.settings') !!}
</button>

<!-- Dynamic icon with size -->
@php
    $iconSize = 'w-6 h-6';
    $icon = str_replace('w-5 h-5', $iconSize, config('icons.user'));
@endphp
<div class="inline-flex items-center">
    {!! $icon !!}
</div>
```

## Code Examples

### Complete Page Template Example
```blade
@extends('layouts.layout001.app')

@section('title', 'Products Management')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Products', 'route' => 'products.index'],
        ['label' => 'List']
    ]" />
@endsection

@section('content')
    <!-- See Part 2 documentation for complete example -->
@endsection
```

## Quick Reference

### Essential Tailwind Classes
- **Text**: `text-primary`, `text-secondary`
- **Background**: `bg-primary`, `bg-secondary`
- **Buttons**: `btn-primary`, `btn-secondary`, `btn-danger`, `btn-success`, `btn-warning`
- **Cards**: `card`, `card-hover`
- **Forms**: `input-field`, `label`
- **Tables**: `table-responsive`, `table-header`, `table-row`
- **Badges**: `badge`, `badge-primary`, `badge-success`, `badge-danger`, `badge-warning`

### Alpine.js Directives
- **x-data**: Initialize component state
- **x-show**: Toggle visibility
- **x-if**: Conditional rendering
- **x-for**: Loop through items
- **x-model**: Two-way binding
- **x-on/@**: Event handlers
- **x-init**: Run on initialization
- **x-transition**: Smooth transitions

### Blade Sections
- **@section('title')**: Page title
- **@section('breadcrumbs')**: Breadcrumb navigation
- **@section('page-header')**: Page header with actions
- **@section('sidebar')**: Sidebar content (optional)
- **@section('content')**: Main content
- **@yield('headstyles')**: Additional CSS
- **@stack('scripts')**: Additional JavaScript

## File References

### Core Files
- **Main Layout**: `/resources/views/layouts/layout001/app.blade.php`
- **CSS Config**: `/resources/css/app.css`
- **JS Config**: `/resources/js/app.js`
- **Vite Config**: `/vite.config.js`
- **Tailwind Config**: `/tailwind.config.js`
- **Icons Config**: `/config/icons.php`

### Documentation
- **Part 1**: This file (`layouts_themes_doc.md`)
- **Part 2**: Extended examples (`layouts_themes_doc_part2.md`)
- **UI Guide**: `/documentation/look_and_feel/UI_IMPLEMENTATION_GUIDE.md`
- **Project Rules**: `/GEMINI.md`

## Version History

### v1.0.0 (Current)
- Initial documentation creation
- Complete layout system implementation
- Dark/Light theme support
- Mobile-first responsive design
- Alpine.js integration
- Tailwind CSS v4 configuration
- Icon management system
- Reusable component library

## Contributing

When updating layouts or themes:
1. Follow the patterns established in this guide
2. Test on all supported browsers and devices
3. Update this documentation with any new patterns
4. Get approval from team lead before major changes
5. Document any module-specific variations

## Support

For questions or issues:
- Check this documentation first
- Review the troubleshooting section in Part 2
- Consult with the development team
- Update documentation with solutions to new issues

---

**Remember**: Consistency is key. When in doubt, refer to existing patterns and this documentation.

## HTML Entities & Special Characters

### Common HTML Entities
Use these entities in your Blade templates for proper character encoding:

```blade
<!-- Common Symbols -->
&copy;     <!-- © Copyright -->
&reg;      <!-- ® Registered -->
&trade;    <!-- ™ Trademark -->
&euro;     <!-- € Euro -->
&pound;    <!-- £ Pound -->
&yen;      <!-- ¥ Yen -->
&cent;     <!-- ¢ Cent -->
&sect;     <!-- § Section -->
&para;     <!-- ¶ Paragraph -->
&dagger;   <!-- † Dagger -->
&Dagger;   <!-- ‡ Double dagger -->

<!-- Mathematical Symbols -->
&plus;     <!-- + Plus -->
&minus;    <!-- − Minus -->
&times;    <!-- × Times -->
&divide;   <!-- ÷ Divide -->
&ne;       <!-- ≠ Not equal -->
&le;       <!-- ≤ Less than or equal -->
&ge;       <!-- ≥ Greater than or equal -->
&asymp;    <!-- ≈ Approximately equal -->
&infin;    <!-- ∞ Infinity -->
&radic;    <!-- √ Square root -->
&sum;      <!-- ∑ Sum -->

<!-- Arrows -->
&larr;     <!-- ← Left arrow -->
&rarr;     <!-- → Right arrow -->
&uarr;     <!-- ↑ Up arrow -->
&darr;     <!-- ↓ Down arrow -->
&harr;     <!-- ↔ Left-right arrow -->
&crarr;    <!-- ↵ Carriage return -->
&lArr;     <!-- ⇐ Left double arrow -->
&rArr;     <!-- ⇒ Right double arrow -->
&uArr;     <!-- ⇑ Up double arrow -->
&dArr;     <!-- ⇓ Down double arrow -->
&hArr;     <!-- ⇔ Left-right double arrow -->

<!-- Quotation -->
&ldquo;    <!-- " Left double quote -->
&rdquo;    <!-- " Right double quote -->
&lsquo;    <!-- ' Left single quote -->
&rsquo;    <!-- ' Right single quote -->
&laquo;    <!-- « Left angle quote -->
&raquo;    <!-- » Right angle quote -->

<!-- Spaces & Dashes -->
&nbsp;     <!-- Non-breaking space -->
&ensp;     <!-- En space -->
&emsp;     <!-- Em space -->
&thinsp;   <!-- Thin space -->
&ndash;    <!-- – En dash -->
&mdash;    <!-- — Em dash -->
&hellip;   <!-- … Ellipsis -->

<!-- Other Symbols -->
&bull;     <!-- • Bullet -->
&middot;   <!-- · Middle dot -->
&sdot;     <!-- ⋅ Dot operator -->
&clubs;    <!-- ♣ Clubs -->
&hearts;   <!-- ♥ Hearts -->
&diams;    <!-- ♦ Diamonds -->
&spades;   <!-- ♠ Spades -->
&male;     <!-- ♂ Male -->
&female;   <!-- ♀ Female -->
&phone;    <!-- ☎ Phone -->
&check;    <!-- ✓ Check mark -->
&cross;    <!-- ✗ Cross mark -->
```

### Usage Examples in Blade

```blade
<!-- Footer with copyright -->
<footer class="text-center text-glass-secondary">
    <p>&copy; {{ date('Y') }} urCost. All rights reserved.</p>
    <p>Made with &hearts; by Development Team</p>
</footer>

<!-- Pricing display -->
<div class="price-tag">
    <span class="currency">&euro;</span>
    <span class="amount">99.99</span>
</div>

<!-- Mathematical expressions -->
<p class="formula">
    E = mc&sup2; | &pi; &asymp; 3.14159 | &infin; possibilities
</p>

<!-- Navigation breadcrumbs -->
<nav aria-label="Breadcrumb">
    <ol class="inline-flex items-center">
        <li>Home</li>
        <li>&raquo;</li>
        <li>Products</li>
        <li>&raquo;</li>
        <li>Details</li>
    </ol>
</nav>

<!-- Status indicators -->
<span class="status">
    <span class="text-green-500">&check;</span> Active
</span>
<span class="status">
    <span class="text-red-500">&cross;</span> Inactive
</span>
```

## Glass Morphism Theme

### Implementation Overview
Glass morphism creates modern, elegant UI with semi-transparent elements and backdrop blur effects. See `/glass-demo` for live examples.

### Core Glass Classes

```css
/* Basic glass effect */
.glass {
    background-color: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(229, 231, 235, 0.5);
}

.dark .glass {
    background-color: rgba(17, 24, 39, 0.3);
    border-color: rgba(255, 255, 255, 0.1);
}
```

### Glass Component Reference

| Component | Class | Light Theme | Dark Theme |
|-----------|-------|-------------|------------|
| Card | `.card-glass` | White 60% opacity | Dark 30% opacity |
| Button | `.btn-glass` | White 60% opacity | Dark 30% opacity |
| Input | `.input-glass` | White 50% opacity | Dark 30% opacity |
| Alert | `.alert-glass` | Color 10% opacity | Color 20% opacity |
| Badge | `.badge-glass` | Color 20% opacity | Color 30% opacity |
| Table | `.table-glass` | White 60% opacity | Dark 30% opacity |
| Modal | `.modal-content-glass` | White 70% opacity | Dark 40% opacity |

### Glass Usage Examples

```blade
<!-- Glass card with gradient text -->
<div class="card-glass">
    <h3 class="text-xl font-bold gradient-text-primary">
        Glass Card Title
    </h3>
    <p class="text-glass-secondary">
        Content with beautiful backdrop blur effect.
    </p>
</div>

<!-- Glass form -->
<form class="card-glass">
    <input type="text" class="input-glass" placeholder="Name">
    <select class="select-glass">
        <option>Option 1</option>
        <option>Option 2</option>
    </select>
    <textarea class="textarea-glass" rows="4"></textarea>
    <button type="submit" class="btn-glass-primary">Submit</button>
</form>

<!-- Glass alert with icon -->
<div class="alert-glass-success">
    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
        <!-- SVG path -->
    </svg>
    Success! Operation completed.
</div>
```

## Loading States & Skeletons

### Loading
 Spinner
```blade
<div class="flex items-center justify-center p-8">
    <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
    <span class="ml-3 text-glass-secondary">Loading...</span>
</div>
```

### Skeleton Loader
```blade
<div class="animate-pulse">
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-4"></div>
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-4"></div>
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
</div>
```

### Loading Button
```blade
<button class="btn-primary" x-data="{ loading: false }" @click="loading = true">
    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
    <span x-show="!loading">Submit</span>
    <span x-show="loading">Processing...</span>
</button>
```

## Tooltips & Popovers

### Simple Tooltip
```blade
<div x-data="{ tooltip: false }" class="relative inline-block">
    <button @mouseenter="tooltip = true" @mouseleave="tooltip = false" class="btn-primary">
        Hover me
    </button>
    <div x-show="tooltip" 
         x-transition
         class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-sm text-white bg-gray-900 rounded-lg">
        This is a tooltip
        <svg class="absolute text-gray-900 h-2 w-full left-0 top-full" x="0px" y="0px" viewBox="0 0 255 127.5">
            <polygon class="fill-current" points="0,0 127.5,127.5 255,0"></polygon>
        </svg>
    </div>
</div>
```

### Popover
```blade
<div x-data="{ popover: false }" class="relative inline-block">
    <button @click="popover = !popover" class="btn-secondary">
        Click for info
    </button>
    <div x-show="popover" 
         @click.away="popover = false"
         x-transition
         class="absolute z-50 w-64 p-4 mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <h4 class="font-bold mb-2">Popover Title</h4>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            This is a popover with more detailed information.
        </p>
    </div>
</div>
```

## Charts & Data Visualization

### Progress Circle
```blade
<div class="relative w-32 h-32">
    <svg class="transform -rotate-90 w-32 h-32">
        <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none" 
                class="text-gray-200 dark:text-gray-700"></circle>
        <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none"
                stroke-dasharray="351.86" stroke-dashoffset="105.56"
                class="text-blue-500"></circle>
    </svg>
    <div class="absolute inset-0 flex items-center justify-center">
        <span class="text-2xl font-bold">70%</span>
    </div>
</div>
```

### Simple Bar Chart
```blade
<div class="flex items-end space-x-2 h-40">
    <div class="flex-1 bg-blue-500 rounded-t" style="height: 60%"></div>
    <div class="flex-1 bg-green-500 rounded-t" style="height: 80%"></div>
    <div class="flex-1 bg-yellow-500 rounded-t" style="height: 45%"></div>
    <div class="flex-1 bg-red-500 rounded-t" style="height: 90%"></div>
</div>
```

### Stats Card
```blade
<div class="card-glass">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-glass-secondary">Total Revenue</p>
            <p class="text-2xl font-bold gradient-text-primary">&euro;12,345</p>
            <p class="text-sm text-green-500">&uarr; 12% from last month</p>
        </div>
        <div class="p-3 bg-blue-500/20 rounded-full">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>
</div>
```