# Layout and Theme Documentation Guide - Part 2

## Code Examples (Continued)

### Complete Page Template Example
```blade
@extends('layouts.layout001.app')

@section('title', 'Products Management')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Products', 'route' => 'products.index'],
        ['label' => 'Management']
    ]" />
@endsection

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                {!! config('icons.shopping-bag') !!}
                <span class="ml-2">Products Management</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage your product inventory and pricing
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <button @click="$dispatch('open-filter')" class="btn-secondary flex items-center">
                {!! config('icons.filter') !!}
                <span class="ml-2 hidden sm:inline">Filter</span>
            </button>
            <a href="{{ route('products.create') }}" class="btn-primary flex items-center">
                {!! config('icons.add') !!}
                <span class="ml-2">Add Product</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div x-data="productManagement()" x-init="init()">
        <!-- Filter Panel -->
        <div x-show="filterOpen" 
             x-transition
             @open-filter.window="filterOpen = true"
             class="mb-6 card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Filters</h3>
                <button @click="filterOpen = false" class="text-gray-500 hover:text-gray-700">
                    {!! config('icons.x') !!}
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form.input 
                    name="search" 
                    placeholder="Search products..." 
                    x-model="filters.search"
                    @input.debounce.300ms="applyFilters"
                />
                <x-form.select 
                    name="category" 
                    x-model="filters.category"
                    @change="applyFilters">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-form.select>
                <x-form.select 
                    name="status" 
                    x-model="filters.status"
                    @change="applyFilters">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="out_of_stock">Out of Stock</option>
                </x-form.select>
            </div>
        </div>

        <!-- Products Grid/Table -->
        <div class="card p-0">
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="table-responsive">
                    <thead class="table-header">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll" 
                                       class="rounded border-gray-300 dark:border-gray-600">
                            </th>
                            <th class="px-6 py-3 text-left">Product</th>
                            <th class="px-6 py-3 text-left">Category</th>
                            <th class="px-6 py-3 text-left">Price</th>
                            <th class="px-6 py-3 text-left">Stock</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="product in products" :key="product.id">
                            <tr class="table-row">
                                <td class="px-6 py-4">
                                    <input type="checkbox" :value="product.id" x-model="selected"
                                           class="rounded border-gray-300 dark:border-gray-600">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img :src="product.thumbnail" :alt="product.name" 
                                             class="w-10 h-10 rounded-lg object-cover">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white" 
                                                 x-text="product.name"></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400" 
                                                 x-text="product.sku"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm" x-text="product.category"></td>
                                <td class="px-6 py-4 text-sm" x-text="formatCurrency(product.price)"></td>
                                <td class="px-6 py-4">
                                    <span class="text-sm" x-text="product.stock"></span>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-1">
                                        <div class="bg-blue-500 h-1.5 rounded-full" 
                                             :style="`width: ${Math.min(product.stock, 100)}%`"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge" 
                                          :class="{
                                              'badge-success': product.status === 'active',
                                              'badge-danger': product.status === 'inactive',
                                              'badge-warning': product.status === 'out_of_stock'
                                          }"
                                          x-text="product.status"></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <a :href="`/products/${product.id}/edit`" 
                                           class="text-blue-500 hover:text-blue-700">
                                            {!! config('icons.edit') !!}
                                        </a>
                                        <button @click="deleteProduct(product.id)" 
                                                class="text-red-500 hover:text-red-700">
                                            {!! config('icons.delete') !!}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden grid grid-cols-1 sm:grid-cols-2 gap-4 p-4">
                <template x-for="product in products" :key="product.id">
                    <div class="card relative">
                        <div class="absolute top-2 right-2">
                            <input type="checkbox" :value="product.id" x-model="selected"
                                   class="rounded border-gray-300 dark:border-gray-600">
                        </div>
                        <div class="flex items-start space-x-4">
                            <img :src="product.thumbnail" :alt="product.name" 
                                 class="w-16 h-16 rounded-lg object-cover">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="product.name"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="product.sku"></p>
                                <p class="text-sm mt-1">
                                    <span class="font-medium">Category:</span> 
                                    <span x-text="product.category"></span>
                                </p>
                                <p class="text-lg font-bold text-blue-500 mt-2" 
                                   x-text="formatCurrency(product.price)"></p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm">Stock: <strong x-text="product.stock"></strong></span>
                                <span class="badge" 
                                      :class="{
                                          'badge-success': product.status === 'active',
                                          'badge-danger': product.status === 'inactive',
                                          'badge-warning': product.status === 'out_of_stock'
                                      }"
                                      x-text="product.status"></span>
                            </div>
                            <div class="flex justify-end space-x-2 mt-3">
                                <a :href="`/products/${product.id}/edit`" 
                                   class="btn-secondary text-sm">Edit</a>
                                <button @click="deleteProduct(product.id)" 
                                        class="btn-danger text-sm">Delete</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-700 dark:text-gray-300 mb-4 sm:mb-0">
                        Showing <span class="font-medium" x-text="pagination.from"></span> to 
                        <span class="font-medium" x-text="pagination.to"></span> of 
                        <span class="font-medium" x-text="pagination.total"></span> results
                    </div>
                    <div class="flex space-x-1">
                        <button @click="goToPage(pagination.current_page - 1)" 
                                :disabled="pagination.current_page === 1"
                                class="px-3 py-1 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md disabled:opacity-50">
                            Previous
                        </button>
                        <template x-for="page in pagination.links" :key="page">
                            <button @click="goToPage(page)" 
                                    :class="{
                                        'bg-blue-500 text-white': page === pagination.current_page,
                                        'bg-white dark:bg-gray-700': page !== pagination.current_page
                                    }"
                                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md">
                                <span x-text="page"></span>
                            </button>
                        </template>
                        <button @click="goToPage(pagination.current_page + 1)" 
                                :disabled="pagination.current_page === pagination.last_page"
                                class="px-3 py-1 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md disabled:opacity-50">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div x-show="selected.length > 0" 
             x-transition
             class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-lg">
            <div class="flex items-center space-x-4">
                <span x-text="`${selected.length} items selected`"></span>
                <button @click="bulkDelete()" class="btn-danger text-sm">Delete Selected</button>
                <button @click="selected = []" class="text-gray-300 hover:text-white">Clear</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function productManagement() {
            return {
                products: [],
                selected: [],
                selectAll: false,
                filterOpen: false,
                loading: false,
                filters: {
                    search: '',
                    category: '',
                    status: ''
                },
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    from: 1,
                    to: 10,
                    total: 0,
                    links: []
                },

                init() {
                    this.fetchProducts();
                },

                async fetchProducts(page = 1) {
                    this.loading = true;
                    const params = new URLSearchParams({
                        page: page,
                        ...this.filters
                    });

                    try {
                        const response = await fetch(`/api/products?${params}`);
                        const data = await response.json();
                        this.products = data.data;
                        this.pagination = data.meta;
                    } catch (error) {
                        console.error('Error fetching products:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                applyFilters() {
                    this.fetchProducts(1);
                },

                goToPage(page) {
                    if (page >= 1 && page <= this.pagination.last_page) {
                        this.fetchProducts(page);
                    }
                },

                toggleSelectAll() {
                    this.selected = this.selectAll 
                        ? this.products.map(p => p.id) 
                        : [];
                },

                async deleteProduct(id) {
                    if (!confirm('Are you sure you want to delete this product?')) return;

                    try {
                        const response = await fetch(`/products/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            this.products = this.products.filter(p => p.id !== id);
                            // Show success message
                        }
                    } catch (error) {
                        console.error('Error deleting product:', error);
                    }
                },

                async bulkDelete() {
                    if (!confirm(`Delete ${this.selected.length} products?`)) return;

                    try {
                        const response = await fetch('/products/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ ids: this.selected })
                        });

                        if (response.ok) {
                            this.fetchProducts();
                            this.selected = [];
                        }
                    } catch (error) {
                        console.error('Error in bulk delete:', error);
                    }
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(amount);
                }
            };
        }
    </script>
    @endpush
@endsection
```

## Yajra DataTables Integration

### Server-Side Controller
```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class ProductDataTableController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with(['category', 'brand']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    return '<img src="'.$row->thumbnail.'" class="w-10 h-10 rounded-lg object-cover">';
                })
                ->addColumn('status_badge', function ($row) {
                    $class = match($row->status) {
                        'active' => 'badge-success',
                        'inactive' => 'badge-danger',
                        'out_of_stock' => 'badge-warning',
                        default => 'badge-secondary'
                    };
                    return '<span class="badge '.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('actions', function ($row) {
                    return view('products.partials.actions', compact('row'))->render();
                })
                ->rawColumns(['image', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('products.datatable');
    }
}
```

### DataTable Blade View
```blade
@extends('layouts.layout001.app')

@section('content')
<div class="card">
    <div class="card-header flex justify-between items-center">
        <h3 class="text-lg font-semibold">Products DataTable</h3>
        <button onclick="refreshTable()" class="btn-secondary">
            {!! config('icons.refresh') !!}
            Refresh
        </button>
    </div>
    
    <div class="card-body">
        <table id="products-table" class="table-responsive">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    /* Custom DataTable styles for dark mode */
    .dark .dataTables_wrapper .dataTables_length,
    .dark .dataTables_wrapper .dataTables_filter,
    .dark .dataTables_wrapper .dataTables_info,
    .dark .dataTables_wrapper .dataTables_processing,
    .dark .dataTables_wrapper .dataTables_paginate {
        color: #d1d5db;
    }
    
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #d1d5db !important;
    }
    
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: white !important;
    }
    
    .dark .dataTables_wrapper .dataTables_length select,
    .dark .dataTables_wrapper .dataTables_filter input {
        background-color: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    let table;
    
    $(document).ready(function() {
        table = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route("products.datatable") }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                { data: 'image', name: 'image', orderable: false },
                { data: 'name', name: 'name' },
                { data: 'category.name', name: 'category.name' },
                { data: 'price', name: 'price' },
                { data: 'stock', name: 'stock' },
                { data: 'status_badge', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            drawCallback: function() {
                // Re-initialize Alpine.js components after table redraw
                if (typeof Alpine !== 'undefined') {
                    Alpine.initTree(document.querySelector('#products-table'));
                }
            }
        });
    });
    
    function refreshTable() {
        table.ajax.reload();
    }
</script>
@endpush
@endsection
```

## Testing Guidelines

### 1. Cross-Browser Testing Checklist
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

### 2. Responsive Design Testing
- [ ] Mobile (320px - 767px)
- [ ] Tablet (768px - 1023px)
- [ ] Desktop (1024px - 1279px)
- [ ] Wide Desktop (1280px+)

### 3. Theme Testing
- [ ] Light mode displays correctly
- [ ] Dark mode displays correctly
- [ ] Theme switch persists on reload
- [ ] No flash of unstyled content (FOUC)
- [ ] All components support both themes

### 4. Accessibility Testing
- [ ] Keyboard navigation works
- [ ] Screen reader compatible
- [ ] ARIA labels present
- [ ] Color contrast meets WCAG standards
- [ ] Focus indicators visible

### 5. Performance Testing
- [ ] Page load time < 3 seconds
- [ ] Time to Interactive < 5 seconds
- [ ] Images are optimized
- [ ] CSS/JS are minified
- [ ] Lazy loading works

## Troubleshooting Guide

### Common Issues and Solutions

#### 1. Dark Mode Not Working
**Problem**: Theme toggle doesn't switch between light and dark modes.

**Solutions**:
- Verify Alpine.js is loaded: Check browser console for Alpine object
- Check localStorage: Ensure theme preference is being saved
- Verify Tailwind config: Ensure `darkMode: 'class'` is set
- Check HTML element: Ensure `.dark` class is being added/removed

#### 2. Responsive Layout Breaking
**Problem**: Layout breaks on certain screen sizes.

**Solutions**:
- Use responsive utility classes consistently
- Test with browser DevTools responsive mode
- Avoid fixed widths; use max-width instead
- Ensure proper viewport meta tag is present

#### 3. Icons Not Displaying
**Problem**: SVG icons from config/icons.php not showing.

**Solutions**:
- Check if config is cached: Run `php artisan config:clear`
- Verify SVG syntax is valid
- Ensure proper escaping with `{!! !!}` in Blade
- Check if icon key exists in config

#### 4. Alpine.js Components Not Working
**Problem**: x-data, x-show, or other Alpine directives not functioning.

**Solutions**:
- Verify Alpine is initialized after DOM loads
- Check for JavaScript errors in console
- Ensure Alpine.js is loaded before usage
- Verify syntax of Alpine expressions

#### 5. Flash Messages Not Dismissing
**Problem**: Alert messages don't auto-dismiss after timeout.

**Solutions**:
- Check if Alpine.js is loaded
- Verify x-init timeout is set correctly
- Check for JavaScript errors blocking execution
- Ensure proper x-show and x-transition directives

## Deployment Checklist

### Pre-Deployment
- [ ] Run `npm run build` for production assets
- [ ] Clear all caches: `php artisan optimize:clear`
- [ ] Test in production environment
- [ ] Verify environment variables are set
- [ ] Check error logging configuration

### Post-Deployment
- [ ] Verify all assets load correctly
- [ ] Test theme switching functionality
- [ ] Check responsive layouts
- [ ] Monitor error logs
- [ ] Test critical user flows

## Summary

This documentation provides a comprehensive guide for implementing and maintaining the layout and theme system in the urCost Laravel application. By following these guidelines, developers can ensure:

1. **Consistency**: All UI components follow the same patterns
2. **Maintainability**: Code is organized and reusable
3. **Performance**: Optimized for fast loading and smooth interactions
4. **Accessibility**: Usable by all users regardless of abilities
5. **Responsiveness**: Works perfectly on all device sizes

Remember to:
- Always test on multiple devices and browsers
- Keep components small and focused
- Use semantic HTML and ARIA labels
- Follow the mobile-first approach
- Maintain consistent spacing and typography
- Document any deviations from these guidelines

For questions or updates to this guide, please consult with the development team lead.

## Glass Morphism Theme Implementation

### Overview
Glass morphism creates a modern, elegant UI with semi-transparent elements and backdrop blur effects. This theme works beautifully in both light and dark modes, creating depth and visual hierarchy.

### Key Characteristics
1. **Backdrop Blur**: Creates frosted glass effect by blurring content behind elements
2. **Transparency**: Semi-transparent backgrounds allow underlying content to show through
3. **Subtle Borders**: Light borders define element edges
4. **Soft Shadows**: Gentle shadows create depth
5. **Gradient Accents**: Animated gradients for visual interest

### CSS Implementation

#### Core Glass Classes
```css
/* Universal glass effect */
.glass {
    @apply bg-white/60 dark:bg-gray-900/30 backdrop-blur-xl 
           border border-gray-200/50 dark:border-white/10 
           shadow-lg dark:shadow-2xl;
}

/* Glass variants */
.glass-sm { /* Light glass */ }
.glass-lg { /* Heavy glass */ }
.glass-frost { /* Frosted overlay */ }

/* Colored glass */
.glass-blue { /* Blue tinted glass */ }
.glass-green { /* Green tinted glass */ }
.glass-red { /* Red tinted glass */ }
```

### Component Examples

#### Glass Card
```blade
<div class="card-glass">
    <h3 class="text-lg font-semibold text-glass-primary mb-2">
        Glass Card Title
    </h3>
    <p class="text-glass-secondary">
        Content with beautiful backdrop blur effect.
    </p>
    <button class="btn-glass w-full mt-4">Action</button>
</div>
```

#### Glass Form
```blade
<form class="card-glass space-y-4">
    <div>
        <label class="block text-sm font-medium text-glass-secondary mb-2">
            Email
        </label>
        <input type="email" class="input-glass" placeholder="you@example.com">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-glass-secondary mb-2">
            Message
        </label>
        <textarea class="textarea-glass" rows="4" placeholder="Your message..."></textarea>
    </div>
    
    <button type="submit" class="btn-glass-primary w-full">
        Send Message
    </button>
</form>
```

#### Glass Modal
```blade
<div x-data="{ open: false }">
    <button @click="open = true" class="btn-glass-primary">Open Modal</button>
    
    <div x-show="open" class="fixed inset-0 z-50" @click.self="open = false">
        <!-- Overlay -->
        <div class="modal-overlay-glass"></div>
        
        <!-- Modal Content -->
        <div class="modal-content-glass">
            <h3 class="text-xl font-semibold gradient-text-primary mb-4">
                Modal Title
            </h3>
            <p class="text-glass-secondary mb-6">
                Modal content with glass effect.
            </p>
            <div class="flex justify-end space-x-3">
                <button @click="open = false" class="btn-glass">Cancel</button>
                <button @click="open = false" class="btn-glass-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>
```

### Theme-Specific Styling

#### Light Theme Glass
```css
.glass-light {
    background-color: rgba(255, 255, 255, 0.6);
    border-color: rgba(229, 231, 235, 0.5);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}
```

#### Dark Theme Glass
```css
.glass-dark {
    background-color: rgba(17, 24, 39, 0.3);
    border-color: rgba(255, 255, 255, 0.1);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
}
```

### Animated Gradients
```blade
<!-- Gradient background -->
<div class="animated-gradient-bg opacity-80 rounded-3xl p-8">
    <h1 class="text-5xl font-bold gradient-text-rainbow">
        Gradient Title
    </h1>
</div>

<!-- Animated button -->
<button class="btn-gradient-animated">
    <span class="btn-gradient-animated-content">
        Animated Button
    </span>
</button>
```

### Best Practices

#### 1. Performance Considerations
- Use `backdrop-filter` sparingly on mobile devices
- Provide fallback styles for browsers without support
- Limit the number of glass elements on screen
- Use `will-change: transform` for animated elements

#### 2. Accessibility
- Ensure sufficient contrast ratios
- Test readability with different backgrounds
- Provide clear focus indicators
- Use solid backgrounds for critical text

#### 3. Browser Compatibility
```css
/* Fallback for browsers without backdrop-filter support */
@supports not (backdrop-filter: blur(12px)) {
    .glass {
        background-color: rgba(255, 255, 255, 0.95);
    }
    .dark .glass {
        background-color: rgba(17, 24, 39, 0.95);
    }
}
```

#### 4. Responsive Design
```blade
<!-- Mobile-optimized glass card -->
<div class="glass-sm md:glass rounded-xl md:rounded-2xl p-4 md:p-6">
    <!-- Reduce blur on mobile for better performance -->
    <div class="backdrop-blur-md md:backdrop-blur-xl">
        <!-- Content -->
    </div>
</div>
```

### Implementation Checklist
- [ ] Test backdrop-filter support across browsers
- [ ] Verify contrast ratios meet WCAG standards
- [ ] Check performance on mobile devices
- [ ] Ensure theme transition is smooth
- [ ] Test with different background images/colors
- [ ] Validate glass effects in both themes
- [ ] Add loading states for glass components
- [ ] Implement keyboard navigation support

### Demo Page
Visit `/glass-demo` to see all glass morphism components in action with both light and dark theme variations.