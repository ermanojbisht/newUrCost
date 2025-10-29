@extends('layouts.layout001.app')

@section('title', 'Theme Components Demo')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-800 dark:text-white">
                UI Components Library
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Complete collection of theme components with HTML entities &amp; special characters
            </p>
            <div class="mt-4 text-sm text-gray-500 dark:text-gray-500">
                &copy; {{ date('Y') }} urCost &bull; Made with &hearts; by Development Team
            </div>
        </div>

        <!-- HTML Entities Showcase -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">
                HTML Entities &amp; Special Characters
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Common Symbols Card -->
                <div class="card">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Common Symbols</h3>
                    <div class="space-y-2 text-gray-600 dark:text-gray-400">
                        <p>&copy; Copyright Symbol</p>
                        <p>&reg; Registered Trademark</p>
                        <p>&trade; Trademark</p>
                        <p>&sect; Section Sign</p>
                        <p>&para; Paragraph Sign</p>
                        <p>&dagger; Dagger &amp; &Dagger; Double Dagger</p>
                    </div>
                </div>

                <!-- Currency Symbols Card -->
                <div class="card">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Currency Symbols</h3>
                    <div class="space-y-2 text-gray-600 dark:text-gray-400">
                        <p>&euro; Euro: &euro;99.99</p>
                        <p>&pound; Pound: &pound;75.50</p>
                        <p>&yen; Yen: &yen;1000</p>
                        <p>&cent; Cent: 99&cent;</p>
                        <p>$ Dollar: $49.99</p>
                    </div>
                </div>

                <!-- Mathematical Symbols Card -->
                <div class="card">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Mathematical</h3>
                    <div class="space-y-2 text-gray-600 dark:text-gray-400">
                        <p>2 &plus; 2 = 4</p>
                        <p>10 &minus; 5 = 5</p>
                        <p>3 &times; 4 = 12</p>
                        <p>15 &divide; 3 = 5</p>
                        <p>&pi; &asymp; 3.14159</p>
                        <p>E = mc&sup2;</p>
                        <p>&radic;16 = 4</p>
                        <p>&infin; Infinity</p>
                    </div>
                </div>

                <!-- Arrows Card -->
                <div class="card">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Arrows</h3>
                    <div class="space-y-2 text-gray-600 dark:text-gray-400">
                        <p>&larr; Left &rarr; Right</p>
                        <p>&uarr; Up &darr; Down</p>
                        <p>&harr; Left-Right</p>
                        <p>&lArr; Double Left &rArr; Double Right</p>
                        <p>&uArr; Double Up &dArr; Double Down</p>
                        <p>&hArr; Double Left-Right</p>
                    </div>
                </div>

                <!-- Quotation Marks Card -->
                <div class="card">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Quotation Marks</h3>
                    <div class="space-y-2 text-gray-600 dark:text-gray-400">
                        <p>&ldquo;Double Quotes&rdquo;</p>
                        <p>&lsquo;Single Quotes&rsquo;</p>
                        <p>&laquo;Angle Quotes&raquo;</p>
                        <p>&Prime; Double Prime &prime; Prime</p>
                    </div>
                </div>

                <!-- Other Symbols Card -->
                <div class="card">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Other Symbols</h3>
                    <div class="space-y-2 text-gray-600 dark:text-gray-400">
                        <p>&bull; Bullet Point</p>
                        <p>&middot; Middle Dot</p>
                        <p>&hellip; Ellipsis</p>
                        <p>&ndash; En dash &mdash; Em dash</p>
                        <p>&spades; &clubs; &hearts; &diams; Card Suits</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Typography Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Typography</h2>

            <div class="card">
                <h1 class="text-4xl font-bold mb-2 text-gray-900 dark:text-white">Heading 1 &mdash; Main Title</h1>
                <h2 class="text-3xl font-bold mb-2 text-gray-800 dark:text-gray-100">Heading 2 &mdash; Section Title</h2>
                <h3 class="text-2xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Heading 3 &mdash; Subsection</h3>
                <h4 class="text-xl font-semibold mb-2 text-gray-700 dark:text-gray-300">Heading 4 &mdash; Sub-subsection</h4>
                <h5 class="text-lg font-medium mb-2 text-gray-700 dark:text-gray-300">Heading 5 &mdash; Minor Heading</h5>
                <h6 class="text-base font-medium mb-4 text-gray-600 dark:text-gray-400">Heading 6 &mdash; Small Heading</h6>

                <p class="mb-3 text-gray-700 dark:text-gray-300">
                    This is a paragraph with <strong>bold text</strong>, <em>italic text</em>, and <u>underlined text</u>.
                    You can also use <mark class="bg-yellow-200 dark:bg-yellow-800">highlighted text</mark> and
                    <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">inline code</code>.
                </p>

                <blockquote class="border-l-4 border-blue-500 pl-4 italic text-gray-600 dark:text-gray-400">
                    &ldquo;This is a blockquote with smart quotes. It&rsquo;s perfect for testimonials or important quotes.&rdquo;
                    <footer class="text-sm mt-2">&mdash; Author Name</footer>
                </blockquote>
            </div>
        </section>

        <!-- Buttons Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Buttons</h2>

            <div class="card">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Standard Buttons</h3>
                <div class="flex flex-wrap gap-3 mb-6">
                    <button class="btn-primary">Primary Button</button>
                    <button class="btn-secondary">Secondary Button</button>
                    <button class="btn-danger">Danger Button</button>
                    <button class="btn-primary" disabled>Disabled</button>
                </div>

                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Icon Buttons</h3>
                <div class="flex flex-wrap gap-3 mb-6">
                    <button class="btn-primary flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Item
                    </button>
                    <button class="btn-secondary flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download
                    </button>
                    <button class="btn-danger flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                </div>

                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Loading States</h3>
                <div class="flex flex-wrap gap-3">
                    <button class="btn-primary flex items-center" disabled>
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Processing&hellip;
                    </button>
                    <button class="btn-secondary opacity-50 cursor-not-allowed" disabled>
                        Disabled State
                    </button>
                </div>
            </div>
        </section>

        <!-- Forms Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Form Elements</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Input Fields</h3>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Text Input <span class="text-red-500">*</span>
                            </label>
                            <input type="text" class="input-field" placeholder="Enter text&hellip;" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email Input
                            </label>
                            <input type="email" class="input-field" placeholder="user@example.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Price Input
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">&euro;</span>
                                <input type="number" class="input-field pl-8" placeholder="0.00" step="0.01">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Select Dropdown
                            </label>
                            <select class="input-field">
                                <option>&mdash; Select Option &mdash;</option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Textarea
                            </label>
                            <textarea class="input-field" rows="3" placeholder="Enter description&hellip;"></textarea>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Checkboxes &amp; Radios</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Accept terms &amp; conditions</span>
                            </label>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" checked class="rounded border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Subscribe to newsletter</span>
                            </label>
                        </div>

                        <div class="pt-4">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Choose Option:</p>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="option" class="border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Option A &mdash; Basic</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="option" checked class="border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Option B &mdash; Standard</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="option" class="border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Option C &mdash; Premium</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-4">
                            <label class="flex items-center justify-between">
                                <span class="text-gray-700 dark:text-gray-300">Toggle Setting</span>
                                <button type="button" x-data="{ enabled: false }" @click="enabled = !enabled"
                                        :class="enabled ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600'"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                    <span :class="enabled ? 'translate-x-6' : 'translate-x-1'"
                                          class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                                </button>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Alerts Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Alerts &amp; Notifications</h2>

            <div class="space-y-4">
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 dark:bg-blue-900 dark:text-blue-100 p-4 rounded">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-bold">Information Alert</p>
                            <p>This is an informational message with important details.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900 dark:text-green-100 p-4 rounded">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-bold">Success!</p>
                            <p>Your operation completed successfully &check;</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-100 p-4 rounded">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-bold">Warning</p>
                            <p>Please review this important information before proceeding.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900 dark:text-red-100 p-4 rounded">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-bold">Error &cross;</p>
                            <p>An error occurred while processing your request.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tables Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Tables</h2>

            <div class="card p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Stock
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    Product A &trade;
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    &euro;99.99
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    150 units
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                        Active &check;
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    Product B &reg;
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    &pound;75.50
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    42 units
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                        Low Stock
                                    </span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    Product C &copy;
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    &yen;5000
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    0 units
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                        Out of Stock &cross;
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Progress Indicators Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Progress Indicators</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Progress Bars</h3>

                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Progress</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">25%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Completion</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">75%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Storage</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">90%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: 90%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Loading States</h3>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
