@extends('layouts.layout001.app')

@section('title', 'Rate Analysis - ' . $item->item_number)

@section('page-title-area')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                {!! config('icons.calculator') !!}
                <span class="ml-2">Rate Analysis</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $item->item_number }} - {{ $item->description }}
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('sors.admin', $sor->id) }}" class="btn-secondary flex items-center">
                {!! config('icons.arrow-left') !!}
                <span class="ml-2">Back to Tree</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Resources Card -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Resources</h2>
                    <button id="btnAddResource" class="btn-primary text-sm">Add Resource</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Name</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Quantity</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Rate</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Amount</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody id="resources-table"
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sub-items Card -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Sub-items</h2>
                    <button id="btnAddSubitem" class="btn-primary text-sm">Add Sub-item</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Item</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Quantity</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Rate</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Amount</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody id="subitems-table"
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Overheads Card -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Overheads</h2>
                    <button id="btnAddOverhead" class="btn-primary text-sm">Add Overhead</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Description</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Parameter</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Amount</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody id="overheads-table"
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Sidebar (1/3 width) -->
        <div class="lg:col-span-1">
            <div class="card sticky top-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Cost Summary</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Resource Cost:</span>
                        <span id="summary-resource-cost" class="font-semibold">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Sub-item Cost:</span>
                        <span id="summary-subitem-cost" class="font-semibold">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Overhead Cost:</span>
                        <span id="summary-overhead-cost" class="font-semibold">₹0.00</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                        <div class="flex justify-between text-sm font-semibold">
                            <span>Total Cost:</span>
                            <span id="summary-total-cost">₹0.00</span>
                        </div>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Turnout:</span>
                        <span id="summary-turnout">1.00</span>
                    </div>
                    <div class="border-t-2 border-blue-500 pt-3 mt-3">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-900 dark:text-white">Final Rate:</span>
                            <span id="summary-final-rate" class="text-blue-600">₹0.00</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">per {{ $item->unit->name ?? 'unit' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const itemId = {{ $item->id }};
        const sorId = {{ $sor->id }};

        // Load skeleton data on page load
        $(document).ready(function () {
            loadSkeletonData();
        });

        function loadSkeletonData() {
            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton`,
                type: 'GET',
                success: function (data) {
                    renderTables(data);
                    updateSummary(data.totals);
                },
                error: function (xhr) {
                    console.error('Error loading data:', xhr);
                    alert('Failed to load skeleton data');
                }
            });
        }

        function renderTables(data) {
            // Resources
            let resHtml = data.resources.length === 0
                ? '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No resources added yet</td></tr>'
                : data.resources.map(res => `
                    <tr>
                        <td class="px-4 py-3 text-sm">${res.name}</td>
                        <td class="px-4 py-3 text-sm">${res.quantity} ${res.unit}</td>
                        <td class="px-4 py-3 text-sm">₹${parseFloat(res.rate).toFixed(2)}</td>
                        <td class="px-4 py-3 text-sm font-semibold">₹${parseFloat(res.amount).toFixed(2)}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <button onclick="removeResource(${res.id})" class="text-red-600 hover:text-red-900">Remove</button>
                        </td>
                    </tr>
                `).join('');
            $('#resources-table').html(resHtml);

            // Sub-items
            let subHtml = data.subitems.length === 0
                ? '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No sub-items added yet</td></tr>'
                : data.subitems.map(sub => `
                    <tr>
                        <td class="px-4 py-3 text-sm">${sub.item_number} - ${sub.name}</td>
                        <td class="px-4 py-3 text-sm">${sub.quantity} ${sub.unit}</td>
                        <td class="px-4 py-3 text-sm">₹${parseFloat(sub.rate).toFixed(2)}</td>
                        <td class="px-4 py-3 text-sm font-semibold">₹${parseFloat(sub.amount).toFixed(2)}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <button onclick="removeSubitem(${sub.id})" class="text-red-600 hover:text-red-900">Remove</button>
                        </td>
                    </tr>
                `).join('');
            $('#subitems-table').html(subHtml);

            // Overheads
            let ohHtml = data.overheads.length === 0
                ? '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No overheads added yet</td></tr>'
                : data.overheads.map(oh => `
                    <tr>
                        <td class="px-4 py-3 text-sm">${oh.description}</td>
                        <td class="px-4 py-3 text-sm">${oh.parameter}%</td>
                        <td class="px-4 py-3 text-sm font-semibold">₹${parseFloat(oh.amount).toFixed(2)}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <button onclick="removeOverhead(${oh.id})" class="text-red-600 hover:text-red-900">Remove</button>
                        </td>
                    </tr>
                `).join('');
            $('#overheads-table').html(ohHtml);
        }

        function updateSummary(totals) {
            $('#summary-resource-cost').text('₹' + parseFloat(totals.resource_cost).toFixed(2));
            $('#summary-subitem-cost').text('₹' + parseFloat(totals.subitem_cost).toFixed(2));
            $('#summary-overhead-cost').text('₹' + parseFloat(totals.overhead_cost).toFixed(2));
            $('#summary-total-cost').text('₹' + parseFloat(totals.grand_total).toFixed(2));
            $('#summary-turnout').text(parseFloat(totals.turnout).toFixed(2));
            $('#summary-final-rate').text('₹' + parseFloat(totals.final_rate).toFixed(2));
        }

        // Remove functions
        window.removeResource = function (id) {
            if (!confirm('Remove this resource?')) return;
            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/resources/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: () => loadSkeletonData(),
                error: () => alert('Failed to remove resource')
            });
        };

        window.removeSubitem = function (id) {
            if (!confirm('Remove this sub-item?')) return;
            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/subitems/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: () => loadSkeletonData(),
                error: () => alert('Failed to remove sub-item')
            });
        };

        window.removeOverhead = function (id) {
            if (!confirm('Remove this overhead?')) return;
            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/overheads/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: () => loadSkeletonData(),
                error: () => alert('Failed to remove overhead')
            });
        };

        // Add button handlers (simple prompt for now)
        $('#btnAddResource').click(function () {
            const resId = prompt("Enter Resource ID:");
            if (!resId) return;
            const qty = prompt("Enter Quantity:", "1");
            if (!qty) return;

            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/resources`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', resource_id: resId, quantity: qty },
                success: () => loadSkeletonData(),
                error: (xhr) => alert('Error: ' + (xhr.responseJSON?.message || 'Failed to add resource'))
            });
        });

        $('#btnAddSubitem').click(function () {
            const subId = prompt("Enter Sub-item Code:");
            if (!subId) return;
            const qty = prompt("Enter Quantity:", "1");
            if (!qty) return;

            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/subitems`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', sub_item_id: subId, quantity: qty },
                success: () => loadSkeletonData(),
                error: (xhr) => alert('Error: ' + (xhr.responseJSON?.message || 'Failed to add sub-item'))
            });
        });

        $('#btnAddOverhead').click(function () {
            const ohId = prompt("Enter Overhead ID:");
            if (!ohId) return;
            const param = prompt("Enter Parameter (%):", "10");
            if (!param) return;

            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/overheads`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', overhead_id: ohId, parameter: param },
                success: () => loadSkeletonData(),
                error: (xhr) => alert('Error: ' + (xhr.responseJSON?.message || 'Failed to add overhead'))
            });
        });
    </script>
@endpush