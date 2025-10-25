@extends('layouts.layout001.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-4 text-gray-800 dark:text-white">Theme Switcher Demo</h1>
        <p class="mb-8 text-gray-700 dark:text-gray-300">This page demonstrates the dark and light theme functionality with various UI components.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Sample Form</h2>
                    <form>
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Name</label>
                            <input type="text" id="name" class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white" placeholder="John Doe">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Email</label>
                            <input type="email" id="email" class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white" placeholder="john.doe@example.com">
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Submit</button>
                    </form>
                </div>

                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Alerts</h2>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 dark:bg-green-900 dark:text-green-100" role="alert">
                        <p class="font-bold">Success</p>
                        <p>This is a success message.</p>
                    </div>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 dark:bg-red-900 dark:text-red-100" role="alert">
                        <p class="font-bold">Error</p>
                        <p>This is an error message.</p>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Buttons</h2>
                    <div class="flex space-x-4">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Primary</button>
                        <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-700 dark:text-white font-bold py-2 px-4 rounded-lg">Secondary</button>
                    </div>
                </div>

                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Typography</h2>
                    <p class="text-lg mb-2 text-gray-700 dark:text-gray-300">This is a paragraph of text.</p>
                    <p class="text-base mb-2 text-gray-700 dark:text-gray-300">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    <p class="text-sm mb-2 text-gray-700 dark:text-gray-300">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    <p class="text-xs text-gray-700 dark:text-gray-300">Ut enim ad minim veniam.</p>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Sample Table</h2>
            <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">Jane Cooper</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">Regional Paradigm Technician</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">jane.cooper@example.com</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">Admin</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">Cody Fisher</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">Product Directives Officer</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">cody.fisher@example.com</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">Owner</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection