@extends('layouts.layout001.app')

@section('title', 'Glass Morphism Theme Demo')

@section('content')
<div class="min-h-screen py-8">
    <!-- Hero Section with Gradient Background -->
    <div class="relative overflow-hidden rounded-3xl mx-4 mb-12">
        <div class="absolute inset-0 animated-gradient-bg opacity-80"></div>
        <div class="relative z-10 text-center py-20 px-6">
            <h1 class="text-5xl md:text-6xl font-bold gradient-text-rainbow mb-4">
                Glass Morphism Theme
            </h1>
            <p class="text-xl text-glass-secondary">
                Beautiful glassmorphic UI components for both light and dark themes
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 space-y-12">
        <!-- Glass Cards Section -->
        <section>
            <h2 class="text-3xl font-bold text-glass-primary mb-6 gradient-text-primary">Glass Cards</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Basic Glass Card -->
                <div class="card-glass">
                    <h3 class="text-lg font-semibold text-glass-primary mb-2">Basic Glass Card</h3>
                    <p class="text-glass-secondary">This is a basic glass card with backdrop blur and semi-transparent background.</p>
                    <div class="mt-4">
                        <button class="btn-glass w-full">Learn More</button>
                    </div>
                </div>

                <!-- Colored Glass Cards -->
                <div class="glass-blue rounded-2xl p-6 hover-glow">
                    <div class="flex items-center mb-3">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-300 ml-3">Blue Glass</h3>
                    </div>
                    <p class="text-blue-700/80 dark:text-blue-200/80">Colored glass variant with blue tint and glow effect on hover.</p>
                </div>

                <div class="glass-green rounded-2xl p-6 hover-glow">
                    <div class="flex items-center mb-3">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-green-700 dark:text-green-300 ml-3">Green Glass</h3>
                    </div>
                    <p class="text-green-700/80 dark:text-green-200/80">Success themed glass card perfect for positive notifications.</p>
                </div>
            </div>

            <!-- Glass with Different Opacity Levels -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="glass-sm rounded-2xl p-6">
                    <h4 class="font-semibold text-glass-primary">Light Glass</h4>
                    <p class="text-glass-secondary text-sm mt-2">Less opacity, subtle effect</p>
                </div>

                <div class="glass rounded-2xl p-6">
                    <h4 class="font-semibold text-glass-primary">Medium Glass</h4>
                    <p class="text-glass-secondary text-sm mt-2">Standard opacity level</p>
                </div>

                <div class="glass-lg rounded-2xl p-6">
                    <h4 class="font-semibold text-glass-primary">Heavy Glass</h4>
                    <p class="text-glass-secondary text-sm mt-2">More opacity, stronger effect</p>
                </div>
            </div>
        </section>

        <!-- Glass Buttons Section -->
        <section>
            <h2 class="text-3xl font-bold text-glass-primary mb-6 gradient-text-primary">Glass Buttons</h2>

            <div class="card-glass">
                <div class="flex flex-wrap gap-4">
                    <button class="btn-glass">Default Glass</button>
                    <button class="btn-glass-primary">Primary Glass</button>
                    <button class="btn-glass-success">Success Glass</button>
                    <button class="btn-glass-danger">Danger Glass</button>

                    <!-- Gradient Animated Button -->
                    <button class="btn-gradient-animated">
                        <span class="btn-gradient-animated-content">
                            Animated Gradient
                        </span>
                    </button>
                </div>

                <!-- Icon Buttons -->
                <div class="flex flex-wrap gap-4 mt-6">
                    <button class="btn-glass flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Item
                    </button>

                    <button class="btn-glass-primary flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download
                    </button>
                </div>
            </div>
        </section>

        <!-- Glass Form Elements -->
        <section>
            <h2 class="text-3xl font-bold text-glass-primary mb-6 gradient-text-primary">Glass Form Elements</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card-glass">
                    <h3 class="text-xl font-semibold text-glass-primary mb-4">Contact Form</h3>

                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-glass-secondary mb-2">Name</label>
                            <input type="text" class="input-glass" placeholder="Enter your name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-glass-secondary mb-2">Email</label>
                            <input type="email" class="input-glass" placeholder="your@email.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-glass-secondary mb-2">Category</label>
                            <select class="select-glass">
                                <option>General Inquiry</option>
                                <option>Support</option>
                                <option>Sales</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-glass-secondary mb-2">Message</label>
                            <textarea class="textarea-glass" rows="4" placeholder="Your message..."></textarea>
                        </div>

                        <button type="submit" class="btn-glass-primary w-full">Send Message</button>
                    </form>
                </div>

                <div class="card-glass">
                    <h3 class="text-xl font-semibold text-glass-primary mb-4">Advanced Settings</h3>

                    <div x-data="{ isOpen: false }" class="w-full">
                        <!-- Toggle Button -->
                        <button
                            @click="isOpen = !isOpen"
                            class="w-full glass px-4 py-3 rounded-xl flex items-center justify-between hover:bg-white/20 dark:hover:bg-white/10 transition-colors"
                        >
                            <span class="font-medium text-glass-primary">View Options</span>
                            <svg
                                :class="{ 'rotate-180': isOpen }"
                                class="w-5 h-5 text-glass-secondary transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Content -->
                        <div
                            x-show="isOpen"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-4 glass p-6 rounded-xl space-y-4"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-glass-primary">Dark Mode</span>
                                <button @click="toggleTheme()" class="relative inline-flex h-6 w-11 items-center rounded-full glass">
                                    <span :class="theme === 'dark' ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-blue-500 transition"></span>
                                </button>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-glass-primary">Notifications</span>
                                <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600">
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-glass-primary">Auto-save</span>
                                <input type="checkbox" checked class="rounded border-gray-300 dark:border-gray-600">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Glass Alerts and Badges -->
        <section>
            <h2 class="text-3xl font-bold text-glass-primary mb-6 gradient-text-primary">Alerts & Badges</h2>

            <div class="space-y-4">
                <div class="alert-glass-info">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-blue-700 dark:text-blue-300">Information Alert</h4>
                            <p class="text-blue-600/80 dark:text-blue-400/80 text-sm mt-1">This is an informational glass alert with backdrop blur effect.</p>
                        </div>
                    </div>
                </div>

                <div class="alert-glass-success">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-green-700 dark:text-green-300">Success Alert</h4>
                            <p class="text-green-600/80 dark:text-green-400/80 text-sm mt-1">Operation completed successfully!</p>
                        </div>
                    </div>
                </div>

                <!-- Badges -->
                <div class="card-glass">
                    <h3 class="text-lg font-semibold text-glass-primary mb-4">Glass Badges</h3>
                    <div class="flex flex-wrap gap-3">
                        <span class="badge-glass-primary">Primary</span>
                        <span class="badge-glass-success">Success</span>
                        <span class="badge-glass-warning">Warning</span>
                        <span class="badge-glass-danger">Danger</span>
                        <span class="badge-glass bg-purple-500/20 dark:bg-purple-500/30 text-purple-700 dark:text-purple-300 border border-purple-500/30">Custom</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Glass Table -->
        <section>
            <h2 class="text-3xl font-bold text-glass-primary mb-6 gradient-text-primary">Glass Table</h2>

            <div class="overflow-x-auto">
                <table class="table-glass">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-glass-secondary uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-glass-secondary uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-glass-secondary uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-glass-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-glass-primary">John Doe</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge-glass-success">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-glass-secondary">Administrator</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button class="text-blue-500 hover:text-blue-700 mr-3">Edit</button>
                                <button class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-glass-primary">Jane Smith</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge-glass-warning">Pending</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-glass-secondary">Editor</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button class="text-blue-500 hover:text-blue-700 mr-3">Edit</button>
                                <button class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Glass Modal Demo -->
        <section>
            <h2 class="text-3xl font-bold text-glass-primary mb-6 gradient-text-primary">Glass Modal</h2>

            <div x-data="{ modalOpen: false }" class="card-glass">
                <p class="text-glass-secondary mb-4">Click the button to see a glass morphism modal overlay.</p>
                <button @click="modalOpen = true" class="btn-glass-primary">Open Modal</button>

                <!-- Modal -->
                <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50" @click.self="modalOpen = false">
                    <!-- Overlay -->
                    <div class="modal-overlay-glass"></div>

                    <!-- Modal Content -->
                    <div x-show="modalOpen"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-90"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="relative z-10">
                        <div class="modal-content-glass">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-semibold gradient-text-primary">Glass Modal</h3>
                                <button @click="modalOpen = false" class="text-glass-secondary hover:text-glass-primary">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-glass-secondary mb-6">This is a modal with glass morphism effect. The background has a beautiful blur effect that works in both light and dark themes.</p>
                            <div class="flex justify-end space-x-3">
                                <button @click="modalOpen = false" class="btn-glass">Cancel</button>
                                <button @click="modalOpen = false" class="btn-glass-primary">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Progress Bars -->
        <section>
            <h2 class="text-3xl font-bold text-glass-primary mb-6 gradient-text-primary">Progress Indicators</h2>

            <div class="card-glass space-y-6">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-glass-primary">Project Progress</span>
                        <span class="text-sm text-glass-secondary">65%</span>
                    </div>
                    <div class="progress-glass">
                        <div class="progress-bar-glass" style="width: 65%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-glass-primary">Upload Progress</span>
                        <span class="text-sm text-glass-secondary">40%</span>
                    </div>
                    <div class="progress-glass">
                        <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full" style="width: 40%"></div>
                    </div>
                </div>

                <!-- Loading spinner -->
                <div class="flex items-center space-x-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    <span class="text-glass-secondary">Processing...</span>
                </div>
            </div>
        </section>

        <!-- Pagination -->
        <section>
            <h2 class="text-3xl font-bold text-glass-primary mb-6 gradient-text-primary">Pagination</h2>

            <div class="card-glass">
                <div class="pagination-glass">
                    <button class="pagination-item-glass">Previous</button>
                    <button class="pagination-item-glass">1</button>
                    <button class="pagination-item-glass-active">2</button>
                    <button class="pagination-item-glass">3</button>
                    <button class="pagination-item-glass">4</button>
                    <button class="pagination-item-glass">5</button>
                    <button class="pagination-item-glass">Next</button>
                </div>
            </div>
        </section>

        <!-- Theme Comparison -->
        <section>
            <h2 class="text-3xl font-bold text-glass-primary mb-6 gradient-text-primary">Theme Comparison</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Light Theme Preview -->
                <div class="rounded-2xl overflow-hidden">
                    <div class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50 p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Light Theme Preview</h3>
                        <div class="bg-white/60 backdrop-blur-xl border border-gray-200/50 shadow-lg rounded-xl p-4 space-y-3">
                            <div class="bg-blue-500/10 backdrop-blur-xl border border-blue-500/20 rounded-lg p-3">
                                <p class="text-blue-700">Blue glass element in light theme</p>
                            </div>
                            <button class="bg-white/60 backdrop-blur-xl border border-gray-200/50 px-4 py-2 rounded-lg text-gray-700 hover:bg-white/70">
                                Light Button
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dark Theme Preview -->
                <div class="rounded-2xl overflow-hidden">
                    <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 p-6">
                        <h3 class="text-xl font-semibold text-gray-100 mb-4">Dark Theme Preview</h3>
                        <div class="bg-gray-900/30 backdrop-blur-xl border border-white/10 shadow-2xl rounded-xl p-4 space-y-3">
                            <div class="bg-blue-500/20 backdrop-blur-xl border border-blue-400/30 rounded-lg p-3">
                                <p class="text-blue-300">Blue glass element in dark theme</p>
                            </div>
                            <button class="bg-gray-900/30 backdrop-blur-xl border border-white/10 px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-900/50">
                                Dark Button
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@endsection
