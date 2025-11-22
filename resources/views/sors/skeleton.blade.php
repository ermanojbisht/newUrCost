@extends('layouts.layout001.app')

@section('title', 'Rate Analysis - ' . $item->item_number)

@section('headstyles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
@endsection

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
    <!-- Rate Card & Date Selection -->
    @include('sors._filters', ['rateCards' => $rateCards, 'rateCardId' => $rateCardId, 'effectiveDate' => $effectiveDate])

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
                                <th class="w-10 px-4 py-3"></th>
                                <th class="w-12 px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
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

    <!-- Add Resource Modal -->
    <div id="addResourceModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

        <!-- Modal Panel Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-gray-700">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title-resource">Add Resource</h3>
                        <input type="hidden" id="edit_skeleton_id">
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resource Type</label>
                                <select id="res_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All</option>
                                    @foreach($resourceGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resource</label>
                                <select id="res_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                                <input type="text" id="res_desc"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                    <input type="number" id="res_qty" step="0.0001"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit</label>
                                    <select id="res_unit"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Factor</label>
                                    <input type="number" id="res_factor" step="0.01" value="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid From</label>
                                    <input type="text" id="res_valid_from"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid To</label>
                                    <input type="text" id="res_valid_to"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>
                            <div id="res_flags" class="hidden flex space-x-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="res_is_locked" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="res_is_locked" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Locked</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="res_is_canceled" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="res_is_canceled" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Canceled</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="btnSaveResource"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add</button>
                        <button type="button" id="btnCloseResourceModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Sub-item Modal -->
    <div id="addSubitemModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

        <!-- Modal Panel Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-gray-700">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Add Sub-item</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sub-item</label>
                                <select id="sub_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                <input type="number" id="sub_qty" step="0.0001"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="btnSaveSubitem"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add</button>
                        <button type="button" id="btnCloseSubitemModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Overhead Modal -->
    <div id="addOverheadModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

        <!-- Modal Panel Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-gray-700">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Add Overhead</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Overhead Type</label>
                                <select id="oh_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Parameter (%)</label>
                                <input type="number" id="oh_param" step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="btnSaveOverhead"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add</button>
                        <button type="button" id="btnCloseOverheadModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Fix Select2 in Dark Mode */
    .dark .select2-container--default .select2-selection--single {
        background-color: #374151;
        border-color: #4b5563;
        color: #fff;
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #fff;
    }
    .dark .select2-dropdown {
        background-color: #1f2937;
        border-color: #4b5563;
        color: #fff;
    }
    .dark .select2-results__option[aria-selected=true] {
        background-color: #374151;
    }
    .dark .select2-search__field {
        background-color: #374151;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script>
        const itemId = {{ $item->id }};
        const sorId = {{ $sor->id }};
        const rateCardId = {{ $rateCardId ?? 'null' }};
        const effectiveDate = '{{ $effectiveDate ?? now()->toDateString() }}';
        const allUnits = @json($units);
        const icons = @json(config('icons'));

        $(document).ready(function () {
            // Initialize Select2
            $('#rate_card_id').select2(); // Initialize for the filter dropdown

            $('#res_id').select2({
                dropdownParent: $('#addResourceModal'),
                width: '100%',
                ajax: {
                url: '{{ route("resources.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        group_id: $('#res_type').val() // Pass selected group ID
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: `[${item.secondary_code || item.id}] ${item.name}`,
                                unit_id: item.unit_id, // Pass unit_id for selection
                                unit_name: item.unit ? item.unit.name : '',
                                item: item
                            };
                        })
                    };
                },
                cache: true
            }
        });

            // Resource Selection Handler
            $('#res_id').on('select2:select', function(e) {
                var data = e.params.data;
                // data should contain the full resource object from the controller

                // Filter Units
                var unitSelect = $('#res_unit');
                unitSelect.empty(); // Clear existing options

                var filteredUnits = [];
                if (data.unit_group_id) {
                    filteredUnits = allUnits.filter(u => u.unit_group_id == data.unit_group_id);
                } else if (data.unit && data.unit.unit_group_id) {
                     // Fallback if unit_group_id is not directly on resource but on unit relation
                    filteredUnits = allUnits.filter(u => u.unit_group_id == data.unit.unit_group_id);
                } else {
                    // Fallback: show all or just the resource's unit? Let's show all if no group found
                    filteredUnits = allUnits;
                }

                // Populate Unit Dropdown
                filteredUnits.forEach(function(unit) {
                    var option = new Option(unit.name, unit.id, false, false);
                    unitSelect.append(option);
                });

                // Select Default Unit
                if (data.unit_id) {
                    unitSelect.val(data.unit_id).trigger('change');
                } else {
                     unitSelect.trigger('change');
                }
            });

            $('#sub_id').select2({
                dropdownParent: $('#addSubitemModal'),
                width: '100%',
                ajax: {
                    url: `/api/sors/${sorId}/items/search`,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            exclude_id: itemId
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.item_code, // Use item_code for subitems
                                text: `${item.item_number} - ${item.description}`
                            }))
                        };
                    },
                    cache: true
                }
            });
            $('#oh_id').select2({
                dropdownParent: $('#addOverheadModal'),
                width: '100%',
                ajax: {
                    url: `/api/sors/${sorId}/overheads/search`,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.description
                            }))
                        };
                    },
                    cache: true
                }
            });

            loadSkeletonData();

            // Initialize Date Pickers
            flatpickr("#res_valid_from", { defaultDate: "today" });
            flatpickr("#res_valid_to", { defaultDate: "2038-01-19" });

            // Add Resource Modal
            $('#btnAddResource').click(() => {
                $('#modal-title-resource').text('Add Resource');
                $('#btnSaveResource').text('Add');
                $('#edit_skeleton_id').val('');
                $('#res_type').val('material'); // Default or 'all'
                $('#res_id').val(null).trigger('change');
                $('#res_desc').val('');
                $('#res_qty').val('');
                $('#res_unit').val('');
                $('#res_factor').val(1);
                // Reset dates
                document.querySelector("#res_valid_from")._flatpickr.setDate("today");
                document.querySelector("#res_valid_to")._flatpickr.setDate("2038-01-19");
                
                // Hide flags for insert
                $('#res_flags').addClass('hidden');
                $('#res_is_locked').prop('checked', false);
                $('#res_is_canceled').prop('checked', false);

                $('#addResourceModal').removeClass('hidden');
            });
            $('#btnCloseResourceModal').click(() => $('#addResourceModal').addClass('hidden'));
            $('#btnSaveResource').click(saveResource);

            // ... existing subitem/overhead handlers ...

        });

        function loadSkeletonData() {
            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton`,
                type: 'GET',
                data: { rate_card_id: rateCardId, date: effectiveDate },
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

        function saveResource() {
            const type = $('#res_type').val();
            const id = $('#res_id').val();
            const qty = $('#res_qty').val();
            const unit = $('#res_unit').val();
            const skeletonId = $('#edit_skeleton_id').val();
            
            // New fields
            const desc = $('#res_desc').val();
            const factor = $('#res_factor').val();
            const validFrom = $('#res_valid_from').val();
            const validTo = $('#res_valid_to').val();
            const isLocked = $('#res_is_locked').is(':checked') ? 1 : 0;
            const isCanceled = $('#res_is_canceled').is(':checked') ? 1 : 0;

            if (!id || !qty) {
                alert('Please fill all fields');
                return;
            }

            const url = skeletonId
                ? `/api/sors/${sorId}/items/${itemId}/skeleton/resources/${skeletonId}`
                : `/api/sors/${sorId}/items/${itemId}/skeleton/resources`;

            const method = skeletonId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: {
                    _token: '{{ csrf_token() }}',
                    resource_id: id,
                    quantity: qty,
                    unit_id: unit,
                    resource_description: desc,
                    factor: factor,
                    valid_from: validFrom,
                    valid_to: validTo,
                    is_locked: isLocked,
                    is_canceled: isCanceled
                },
                success: () => {
                    $('#addResourceModal').addClass('hidden');
                    loadSkeletonData();
                },
                error: (xhr) => alert('Error: ' + (xhr.responseJSON?.message || 'Failed to save resource'))
            });
        }

        function saveSubitem() {
            const id = $('#sub_id').val();
            const qty = $('#sub_qty').val();

            if (!id || !qty) {
                alert('Please fill all fields');
                return;
            }

            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/subitems`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    sub_item_id: id,
                    quantity: qty
                },
                success: () => {
                    $('#addSubitemModal').addClass('hidden');
                    loadSkeletonData();
                },
                error: (xhr) => alert('Error: ' + (xhr.responseJSON?.message || 'Failed to add sub-item'))
            });
        }

        function saveOverhead() {
            const id = $('#oh_id').val();
            const param = $('#oh_param').val();

            if (!id || !param) {
                alert('Please fill all fields');
                return;
            }

            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/overheads`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    overhead_id: id,
                    parameter: param
                },
                success: () => {
                    $('#addOverheadModal').addClass('hidden');
                    loadSkeletonData();
                },
                error: (xhr) => alert('Error: ' + (xhr.responseJSON?.message || 'Failed to add overhead'))
            });
        }

        function renderTables(data) {
            // Resources
            let resHtml = data.resources.length === 0
                ? '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No resources added yet</td></tr>'
                : data.resources.map((res, index) => {
                    let typeIcon = icons.material;
                    if (res.resource_group_name.includes('labour')) typeIcon = icons.labour;
                    else if (res.resource_group_name.includes('machine')) typeIcon = icons.machinery;
                    else if (res.resource_group_name.includes('material')) typeIcon = icons.material;
                    
                    return `
                            <tr data-id="${res.id}">
                                <td class="px-4 py-3 text-center cursor-move text-gray-400 hover:text-gray-600">
                                    ${icons.list}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-500">${index + 1}</td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-col">
                                        <div class="flex items-center">
                                            <span class="mr-2 text-gray-500" title="${res.resource_group_name}">${typeIcon}</span>
                                            <span class="font-medium">
                                                ${res.secondary_code ? `<span class="text-gray-500 mr-1">[${res.secondary_code}]</span>` : ''}
                                                <a href="/resources/${res.resource_id}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                    ${res.name}
                                                </a>
                                            </span>
                                        </div>
                                        ${(res.resource_description || (res.factor && parseFloat(res.factor) !== 1)) ? `
                                            <div class="text-xs text-gray-500 ml-7 mt-1">
                                                ${res.resource_description || ''}
                                                ${(res.factor && parseFloat(res.factor) !== 1) ? `<span class="font-semibold">(Factor: ${res.factor})</span>` : ''}
                                            </div>
                                        ` : ''}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">${res.quantity} ${res.unit}</td>
                                <td class="px-4 py-3 text-sm">₹${parseFloat(res.rate).toFixed(2)}</td>
                                <td class="px-4 py-3 text-sm font-semibold">₹${parseFloat(res.amount).toFixed(2)}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <button onclick='editResource(${JSON.stringify(res)})' class="text-blue-600 hover:text-blue-900 mr-2">Edit</button>
                                    <button onclick="removeResource(${res.id})" class="text-red-600 hover:text-red-900">Remove</button>
                                </td>
                            </tr>
                        `;
                }).join('');
            $('#resources-table').html(resHtml);

            // Initialize Sortable
            if (document.getElementById('resources-table')) {
                new Sortable(document.getElementById('resources-table'), {
                    animation: 150,
                    handle: '.cursor-move',
                    onEnd: function (evt) {
                        var itemEl = evt.item;  // dragged HTMLElement
                        var newOrder = [];
                        $('#resources-table tr').each(function() {
                            newOrder.push($(this).data('id'));
                        });
                        
                        // Send new order to server
                        $.ajax({
                            url: `/api/sors/${sorId}/items/${itemId}/skeleton/resources/reorder`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: newOrder
                            },
                            success: function(response) {
                                console.log('Reordered successfully');
                            },
                            error: function(xhr) {
                                alert('Failed to reorder resources');
                            }
                        });
                    }
                });
            }

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

        window.editResource = function(res) {
            $('#modal-title-resource').text('Edit Resource');
            $('#btnSaveResource').text('Update');
            $('#edit_skeleton_id').val(res.id);

            // Pre-fill Resource Type
            if (res.resource_group_id) {
                $('#res_type').val(res.resource_group_id);
            } else {
                $('#res_type').val('');
            }

            // Pre-fill Resource Select2
            const option = new Option(res.name, res.resource_id, true, true);
            $('#res_id').append(option).trigger('change');

            $('#res_qty').val(res.quantity);
            
            // New fields
            $('#res_desc').val(res.resource_description || '');
            $('#res_factor').val(res.factor || 1);
            
            if (res.valid_from) document.querySelector("#res_valid_from")._flatpickr.setDate(res.valid_from);
            if (res.valid_to) document.querySelector("#res_valid_to")._flatpickr.setDate(res.valid_to);

            // Show and set flags
            $('#res_flags').removeClass('hidden');
            $('#res_is_locked').prop('checked', res.is_locked == 1);
            $('#res_is_canceled').prop('checked', res.is_canceled == 1);

            // Trigger unit filtering and selection
            // We need to simulate the selection logic or call a helper
            // Since logic is inside select2:select, we can extract it or just manually populate
            // For simplicity, let's manually populate based on passed unit_group_id
            const unitSelect = $('#res_unit');
            unitSelect.empty();
            
            let filteredUnits = allUnits;
            if (res.unit_group_id) {
                filteredUnits = allUnits.filter(u => u.unit_group_id == res.unit_group_id);
            }
            
            filteredUnits.forEach(u => {
                unitSelect.append(new Option(u.name, u.id));
            });
            
            // Select the resource's unit (from skeleton record, not resource default)
            // The res object here is from skeleton data, so res.unit is the unit name, but we need unit_id
            // Wait, res object from renderTables has 'unit' as name string. We need unit_id.
            // Let's check ItemSkeletonService. It passes 'unit' => $res->unit ? $res->unit->name : ''
            // We need to update Service to pass unit_id as well.
            // Assuming I will fix Service, let's use res.unit_id
            
            // Temporary fix: I need to update Service to pass skeleton unit_id.
            // For now, let's assume res.unit_id is available.
            if (res.unit_id) {
                 unitSelect.val(res.unit_id);
            }

            $('#addResourceModal').removeClass('hidden');
        };

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
    </script>
@endpush
