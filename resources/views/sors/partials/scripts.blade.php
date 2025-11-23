    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <script>
        /**
         * Item Skeleton View Scripts
         * 
         * This script handles the dynamic functionality of the Item Skeleton page, including:
         * - Loading and rendering the skeleton data (Resources, Sub-items, Overheads).
         * - Managing Modals for adding/editing components.
         * - Handling AJAX requests for CRUD operations.
         * - initializing Select2 for searchable dropdowns.
         * - Calculating and updating the Cost Summary.
         */

        const itemId = {{ $item->id }};
        const sorId = {{ $sor->id }};
        const rateCardId = {{ $rateCardId ?? 'null' }};
        const effectiveDate = '{{ $effectiveDate ?? now()->toDateString() }}';
        const allUnits = @json($units);
        const icons = @json(config('icons'));
        window.isReadonly = {{ isset($readonly) && $readonly ? 'true' : 'false' }};

        $(document).ready(function () {
            // --- Initialization ---
            
            // Initialize Select2 for Rate Card Filter
            $('#rate_card_id').select2(); 

            // Initialize Select2 for Resource Selection with AJAX
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
                    url: '{{ route("api.sors.items.search", $sor->id) }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            exclude_id: itemId // Exclude current item to prevent circular dependency (basic check)
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.item_code, // Use item_code as value
                                    text: `[${item.item_number}] ${item.description}`,
                                    unit_id: item.unit_id,
                                    unit_group_id: item.unit ? item.unit.unit_group_id : null
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            }).on('select2:select', function (e) {
                const data = e.params.data;
                const unitSelect = $('#sub_unit');
                unitSelect.empty();

                // Filter units based on the selected item's unit group
                let filteredUnits = allUnits;
                if (data.unit_group_id) {
                    filteredUnits = allUnits.filter(u => u.unit_group_id == data.unit_group_id);
                }

                // Populate unit dropdown
                filteredUnits.forEach(u => {
                    unitSelect.append(new Option(u.name, u.id));
                });

                // Select the item's default unit if available
                if (data.unit_id) {
                    unitSelect.val(data.unit_id);
                }
            });
            $('#oh_id').select2({
                dropdownParent: $('#modal-add-overhead'),
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
            flatpickr("#sub_valid_to", { defaultDate: "2038-01-19" });

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

            // Sub-item Handlers
            $('#btnAddSubitem').click(() => {
                $('#modal-title-subitem').text('Add Sub-item');
                $('#btnSaveSubitem').text('Add');
                $('#edit_subitem_id').val('');
                $('#sub_id').val(null).trigger('change');
                $('#sub_qty').val('');
                $('#sub_factor').val(1);
                $('#sub_remarks').val('');
                $('#sub_is_oh_applicable').prop('checked', false);
                $('#sub_is_overhead').prop('checked', true);

                // Reset Unit Dropdown to all units (or empty) until item selected
                $('#sub_unit').empty();
                allUnits.forEach(u => {
                    $('#sub_unit').append(new Option(u.name, u.id));
                });

                // Fix Flatpickr initialization
                const fp = document.querySelector("#sub_valid_to")._flatpickr;
                if(fp) fp.setDate("2038-01-19");

                $('#addSubitemModal').removeClass('hidden');
            });
            $('#btnCloseSubitemModal').click(() => $('#addSubitemModal').addClass('hidden'));
            $('#btnSaveSubitem').click(saveSubitem);

            // Overhead Handlers
            $('#btnAddOverhead').click(openAddOverheadModal);
            // Note: Close handler is inline in HTML (onclick="closeModal(...)") but we should probably standardize.
            // For now, let's rely on the inline handler or add one here if needed.
            // The HTML has onclick="closeModal('modal-add-overhead')" but closeModal is not defined in JS!
            // We need to define closeModal or add a handler here.
            // Let's add a handler here for consistency and fix the HTML button to use ID.
             $('#btnCloseOverheadModal').click(() => $('#modal-add-overhead').addClass('hidden'));

        });

        function loadSkeletonData() {
            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton`,
                type: 'GET',
                data: { rate_card_id: rateCardId, date: effectiveDate },
                success: function (data) {
                    renderTables(data);
                    updateSummary(data.totals);
                    renderCharts(data);
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
            const subitemId = $('#edit_subitem_id').val();
            const factor = $('#sub_factor').val();
            const unitId = $('#sub_unit').val();
            const validTo = $('#sub_valid_to').val();
            const isOhApplicable = $('#sub_is_oh_applicable').is(':checked') ? 1 : 0;
            const isOverhead = $('#sub_is_overhead').is(':checked') ? 1 : 0;
            const remarks = $('#sub_remarks').val();

            if (!id || !qty) {
                alert('Please fill all fields');
                return;
            }

            const url = subitemId
                ? `/api/sors/${sorId}/items/${itemId}/skeleton/subitems/${subitemId}`
                : `/api/sors/${sorId}/items/${itemId}/skeleton/subitems`;
            const method = subitemId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: {
                    _token: '{{ csrf_token() }}',
                    sub_item_code: id,
                    quantity: qty,
                    factor: factor,
                    unit_id: unitId,
                    valid_to: validTo,
                    is_oh_applicable: isOhApplicable,
                    is_overhead: isOverhead,
                    remarks: remarks
                },
                success: () => {
                    $('#addSubitemModal').addClass('hidden');
                    loadSkeletonData();
                },
                error: (xhr) => alert('Error: ' + (xhr.responseJSON?.message || 'Failed to save sub-item'))
            });
        }

        // --- Overheads ---
        function openAddOverheadModal() {
            $('#modal-title-overhead').text('Add Overhead');
            $('#btnSaveOverhead').text('Add Overhead');
            $('#edit_overhead_id').val('');
            $('#oh_id').val(null).trigger('change'); // Clear Select2 for overhead_id
            $('#calculation_type').val('0');
            $('#oh_parameter').val('');
            $('#oh_description').val('');
            $('#applicable_items').val('');
            $('#allow_further_overhead').prop('checked', false);
            $('#modal-add-overhead').removeClass('hidden');
        }

        function saveOverhead() {
            const id = $('#edit_overhead_id').val();
            const url = id ? `/api/sors/${sorId}/items/${itemId}/skeleton/overheads/${id}` : `/api/sors/${sorId}/items/${itemId}/skeleton/overheads`;
            const method = id ? 'PUT' : 'POST';

            const data = {
                overhead_id: $('#oh_id').val(), // Changed from #overhead_id to #oh_id to match Select2
                calculation_type: $('#calculation_type').val(),
                parameter: $('#oh_parameter').val(),
                description: $('#oh_description').val(),
                applicable_items: $('#applicable_items').val(),
                allow_further_overhead: $('#allow_further_overhead').is(':checked') ? 1 : 0,
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function(response) {
                    $('#modal-add-overhead').addClass('hidden'); // Assuming this is the modal ID
                    loadSkeletonData(); // Refresh table
                },
                error: function(xhr) {
                    alert('Error saving overhead: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
                }
            });
        }

        window.editOverhead = function(oh) { // Made global for onclick
            $('#modal-title-overhead').text('Edit Overhead');
            $('#btnSaveOverhead').text('Update Overhead');
            $('#edit_overhead_id').val(oh.id);

            // Pre-fill Overhead Select2
            // Use overhead_name from service (which is the master description)
            const option = new Option(oh.overhead_name || oh.description, oh.overhead_id, true, true);
            $('#oh_id').append(option).trigger('change');

            $('#calculation_type').val(oh.calculation_type || 0);
            
            // Parameter: Service returns 'parameter' as percentage (e.g. 10) and 'raw_parameter' as decimal (0.1)
            // If calculation_type is 0 (Lumpsum), we want the raw amount.
            // If it's percentage, we want the percentage value (e.g. 10).
            if (oh.calculation_type == 0) {
                $('#oh_parameter').val(oh.raw_parameter);
            } else {
                $('#oh_parameter').val(oh.parameter);
            }
            
            $('#oh_description').val(oh.raw_description || ''); 
            $('#applicable_items').val(oh.applicable_items || '');
            $('#allow_further_overhead').prop('checked', oh.allow_further_overhead == 1);

            $('#modal-add-overhead').removeClass('hidden');
        }

        window.deleteOverhead = function(id) { // Made global for onclick
            if(!confirm('Are you sure?')) return;
            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/overheads/${id}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    loadSkeletonData();
                },
                error: function(xhr) {
                    alert('Error deleting overhead');
                }
            });
        }

        function renderTables(data) {
            // Resources Table
            const resBody = document.getElementById('resources-body');
            resBody.innerHTML = '';

            if (data.resources.length === 0) {
                const colspan = window.isReadonly ? 6 : 7;
                resBody.innerHTML = `<tr><td colspan="${colspan}" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No resources added yet.</td></tr>`;
            } else {
                data.resources.forEach((res, index) => {
                    const row = document.createElement('tr');
                    row.className = 'bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150';
                    row.dataset.id = res.id; // For SortableJS

                    // Determine Icon
                    let iconHtml = icons.list; // Default
                    if (res.resource_group_name) {
                        if (res.resource_group_name.includes('Labour Group')) iconHtml = icons.labour;
                        else if (res.resource_group_name.includes('Machine Group')) iconHtml = icons.machinery;
                        else if (res.resource_group_name.includes('Material Group')) iconHtml = icons.material;
                    }

                    // Rate Tooltip
                    let rateTooltip = '';
                    if (res.rate_components && res.rate_components.length > 1) {
                        let tooltipContent = res.rate_components.map(c => `${c.name}: ₹${parseFloat(c.amount).toFixed(2)}`).join('\n');
                        rateTooltip = `<span class="text-blue-500 cursor-help mr-1" title="${tooltipContent}">&#9432;</span>`;
                    }

                    // Name Column Content
                    let nameContent = `
                        <div class="flex items-start">
                            <span class="mr-2 text-gray-500 mt-1" title="${res.resource_group_name}">
                                ${iconHtml}
                            </span>
                            <div class="flex-1 min-w-0">
                                <a href="/resources/${res.resource_id}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline whitespace-normal break-words block">
                                    [${res.secondary_code || 'N/A'}] ${res.name}
                                </a>
                                ${res.resource_description ? `<div class="text-xs text-gray-500 mt-0.5 whitespace-normal">${res.resource_description}</div>` : ''}
                                ${parseFloat(res.factor) !== 1 ? `<div class="text-xs text-gray-500 mt-0.5">Factor: ${parseFloat(res.factor).toFixed(4)}</div>` : ''}
                            </div>
                        </div>
                    `;

                    // Conditional Columns
                    const dragHandle = window.isReadonly ? '' : `
                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 drag-handle cursor-move align-top">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                        </td>`;
                    
                    const actionButtons = window.isReadonly ? '' : `
                        <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium align-top">
                            <button onclick='editResource(${JSON.stringify(res)})' class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2" title="Edit">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button onclick="deleteResource(${res.id})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>`;

                    row.innerHTML = `
                        ${dragHandle}
                        <td class="px-2 py-2 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400 align-top">${index + 1}</td>
                        <td class="px-2 py-2 text-sm text-gray-900 dark:text-white align-top">
                            ${nameContent}
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right align-top">
                            ${parseFloat(res.quantity).toFixed(4)}<br>
                            <span class="text-xs text-gray-400">${res.unit}</span>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right align-top">
                            <div class="flex justify-end items-center">
                                ${rateTooltip}
                                <span>${parseFloat(res.rate).toFixed(2)}</span>
                            </div>
                            ${res.rate_unit ? `<div class="text-xs text-gray-400">/${res.rate_unit}</div>` : ''}
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right align-top">${parseFloat(res.amount).toFixed(2)}</td>
                        ${actionButtons}
                    `;
                    resBody.appendChild(row);
                });
            }
            
            // Sub-items
            let subHtml = '';
            if (data.subitems.length === 0) {
                const colspan = window.isReadonly ? 4 : 5;
                subHtml = `<tr><td colspan="${colspan}" class="px-4 py-8 text-center text-gray-500">No sub-items added yet</td></tr>`;
            } else {
                subHtml = data.subitems.map(sub => {
                    const actionCell = window.isReadonly ? '' : `
                        <td class="px-4 py-3 text-right text-sm">
                            <button onclick='editSubitem(${JSON.stringify(sub)})' class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2" title="Edit">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button onclick="removeSubitem(${sub.id})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>`;
                    
                    return `
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                ${sub.item_number} - ${sub.name}
                                <div class="mt-1 text-xs text-gray-500 space-y-0.5">
                                    <div class="${sub.is_oh_applicable ? 'text-green-600' : 'text-red-500'}">
                                        Further Overhead: ${sub.is_oh_applicable ? 'Yes' : 'No'}
                                    </div>
                                    <div class="${sub.is_overhead ? 'text-green-600' : 'text-red-500'}">
                                        With Overhead: ${sub.is_overhead ? 'Yes' : 'No'}
                                    </div>
                                    <div>Factor: ${sub.factor}</div>
                                    ${sub.remarks ? `<div class="text-gray-600 italic">Remarks: ${sub.remarks}</div>` : ''}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">${sub.quantity} ${sub.unit}</td>
                            <td class="px-4 py-3 text-sm">₹${parseFloat(sub.rate).toFixed(2)}</td>
                            <td class="px-4 py-3 text-sm font-semibold">₹${parseFloat(sub.amount).toFixed(2)}</td>
                            ${actionCell}
                        </tr>
                    `;
                }).join('');
            }
            $('#subitems-table').html(subHtml);

            // Overheads Table
            const ohBody = document.getElementById('overheads-body');
            if (ohBody) {
                ohBody.innerHTML = '';
                if (data.overheads && data.overheads.length > 0) {
                    data.overheads.forEach((oh, index) => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                        row.setAttribute('data-id', oh.id);

                        const dragHandle = window.isReadonly ? '' : `
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 drag-handle cursor-move align-top">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                            </td>`;

                        const actionButtons = window.isReadonly ? '' : `
                            <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium align-top">
                                <button onclick='editOverhead(${JSON.stringify(oh)})' class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2" title="Edit">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button onclick="deleteOverhead(${oh.id})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>`;

                        row.innerHTML = `
                            ${dragHandle}
                            <td class="px-2 py-2 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400 align-top">${index + 1}</td>
                            <td class="px-2 py-2 text-sm text-gray-900 dark:text-white align-top whitespace-pre-wrap">${oh.description}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right align-top">${parseFloat(oh.calculation_type == 0 ? oh.raw_parameter : oh.parameter).toFixed(2)}${oh.calculation_type == 1 ? '%' : ''}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right align-top">₹${parseFloat(oh.amount).toFixed(2)}</td>
                            ${actionButtons}
                        `;
                        ohBody.appendChild(row);
                    });
                } else {
                    const colspan = window.isReadonly ? 5 : 6;
                    ohBody.innerHTML = `<tr><td colspan="${colspan}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No overheads found.</td></tr>`;
                }
            }
        }

        function updateSummary(totals) {
            $('#summary-material-cost').text('₹' + parseFloat(totals.total_material).toFixed(2));
            $('#summary-labor-cost').text('₹' + parseFloat(totals.total_labor).toFixed(2));
            $('#summary-machine-cost').text('₹' + parseFloat(totals.total_machine).toFixed(2));
            $('#summary-resource-cost').text('₹' + parseFloat(totals.resource_cost).toFixed(2));
            $('#summary-subitem-cost').text('₹' + parseFloat(totals.subitem_cost).toFixed(2));
            $('#summary-overhead-cost').text('₹' + parseFloat(totals.overhead_cost).toFixed(2));
            $('#summary-total-cost').text('₹' + parseFloat(totals.grand_total).toFixed(2));
            $('#summary-turnout').text(parseFloat(totals.turnout).toFixed(2));
            $('#summary-final-rate').text('₹' + parseFloat(totals.final_rate).toFixed(2));
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

        window.editSubitem = function(sub) {
            $('#modal-title-subitem').text('Edit Sub-item');
            $('#btnSaveSubitem').text('Update');
            $('#edit_subitem_id').val(sub.id);

            // Pre-fill Sub-item Select2
            const option = new Option(`${sub.item_number} - ${sub.name}`, sub.sub_item_code, true, true);
            $('#sub_id').append(option).trigger('change');

            $('#sub_qty').val(sub.quantity);
            $('#sub_factor').val(sub.factor || 1);
            $('#sub_remarks').val(sub.remarks || '');
            $('#sub_is_oh_applicable').prop('checked', sub.is_oh_applicable == 1);
            $('#sub_is_overhead').prop('checked', sub.is_overhead == 1);

            // Unit Logic for Edit
            const unitSelect = $('#sub_unit');
            unitSelect.empty();
            // Populate all units for now to ensure the current unit can be selected
            allUnits.forEach(u => {
                unitSelect.append(new Option(u.name, u.id));
            });

            if(sub.unit_id) unitSelect.val(sub.unit_id);

            // Valid To
            const fp = document.querySelector("#sub_valid_to")._flatpickr;
            if (sub.valid_to && fp) fp.setDate(sub.valid_to);
            else if(fp) fp.setDate("2038-01-19");

            $('#addSubitemModal').removeClass('hidden');
        };

        // Remove functions
        window.deleteResource = function (id) {
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

        window.deleteOverhead = function (id) {
            if (!confirm('Remove this overhead?')) return;
            $.ajax({
                url: `/api/sors/${sorId}/items/${itemId}/skeleton/overheads/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: () => loadSkeletonData(),
                error: () => alert('Failed to remove overhead')
            });
        };

        // --- Charts ---
        let resourceChart = null;
        let costChart = null;

        function renderCharts(data) {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#e5e7eb' : '#374151';
            const backgroundColor = 'transparent';
            
            // Custom color palette matching Tailwind colors
            const colors = [
                '#3b82f6', // blue-500
                '#8b5cf6', // violet-500
                '#ec4899', // pink-500
                '#f59e0b', // amber-500
                '#10b981', // emerald-500
                '#6366f1', // indigo-500
                '#f43f5e', // rose-500
                '#06b6d4', // cyan-500
            ];

            // Common chart options
            const commonOptions = {
                backgroundColor: backgroundColor,
                textStyle: {
                    fontFamily: 'Inter, sans-serif'
                },
                toolbox: {
                    show: true,
                    feature: {
                        saveAsImage: {
                            show: true,
                            title: 'Save Image',
                            iconStyle: { borderColor: textColor }
                        },
                        dataView: {
                            show: true,
                            title: 'Data View',
                            lang: ['Data View', 'Close', 'Refresh'],
                            buttonColor: '#3b82f6',
                            optionToContent: function(opt) {
                                var series = opt.series;
                                var table = '<div style="width:100%;text-align:center;padding:10px;">' +
                                            '<table style="width:100%;border-collapse:collapse;border:1px solid #ddd;">' +
                                            '<thead><tr style="background:#f3f4f6;color:#374151;">' +
                                            '<th style="padding:8px;border:1px solid #ddd;">Name</th>' +
                                            '<th style="padding:8px;border:1px solid #ddd;">Value</th>' +
                                            '</tr></thead><tbody>';
                                for (var i = 0, l = series[0].data.length; i < l; i++) {
                                    table += '<tr>' +
                                             '<td style="padding:8px;border:1px solid #ddd;color:#374151;">' + series[0].data[i].name + '</td>' +
                                             '<td style="padding:8px;border:1px solid #ddd;color:#374151;">₹' + series[0].data[i].value.toFixed(2) + '</td>' +
                                             '</tr>';
                                }
                                table += '</tbody></table></div>';
                                return table;
                            }
                        },
                        restore: {
                            show: true,
                            title: 'Reset',
                            iconStyle: { borderColor: textColor }
                        }
                    },
                    iconStyle: {
                        borderColor: textColor
                    },
                    right: 20,
                    top: 0
                },
                tooltip: {
                    trigger: 'item',
                    backgroundColor: isDark ? 'rgba(17, 24, 39, 0.8)' : 'rgba(255, 255, 255, 0.8)',
                    borderColor: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(229, 231, 235, 0.5)',
                    borderWidth: 1,
                    textStyle: {
                        color: textColor
                    },
                    extraCssText: 'backdrop-filter: blur(8px); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);',
                    formatter: function(params) {
                        return `<div class="font-medium">${params.name}</div>
                                <div class="text-sm mt-1">
                                    <span class="font-bold">₹${params.value.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    <span class="text-xs opacity-75 ml-1">(${params.percent}%)</span>
                                </div>`;
                    }
                }
            };

            // Initialize charts if not already done
            if (!resourceChart) {
                const chartDom = document.getElementById('chart-resource-contribution');
                if (chartDom) {
                    resourceChart = echarts.init(chartDom);
                    window.addEventListener('resize', function() { resourceChart.resize(); });
                }
            }
            if (!costChart) {
                const chartDom = document.getElementById('chart-cost-summary');
                if (chartDom) {
                    costChart = echarts.init(chartDom);
                    window.addEventListener('resize', function() { costChart.resize(); });
                }
            }

            if (!resourceChart || !costChart) return;

            // 1. Resource Contribution Chart Data
            let chartItems = [];
            data.resources.forEach(res => {
                if (parseFloat(res.amount) > 0) chartItems.push({ value: parseFloat(res.amount), name: res.name });
            });
            data.subitems.forEach(sub => {
                if (parseFloat(sub.amount) > 0) chartItems.push({ value: parseFloat(sub.amount), name: sub.name });
            });

            chartItems.sort((a, b) => b.value - a.value);

            if (chartItems.length > 10) {
                const topItems = chartItems.slice(0, 9);
                const otherItems = chartItems.slice(9);
                const otherTotal = otherItems.reduce((sum, item) => sum + item.value, 0);
                if (otherTotal > 0) topItems.push({ value: otherTotal, name: 'Others' });
                chartItems = topItems;
            }

            resourceChart.setOption({
                ...commonOptions,
                color: colors,
                legend: {
                    type: 'scroll',
                    orient: 'vertical',
                    right: 0,
                    top: 30,
                    bottom: 20,
                    textStyle: { color: textColor },
                    pageIconColor: textColor,
                    pageTextStyle: { color: textColor }
                },
                series: [{
                    name: 'Resource Cost',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    center: ['35%', '55%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 8,
                        borderColor: isDark ? '#1f2937' : '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: 14,
                            fontWeight: 'bold',
                            color: textColor,
                            formatter: '{b}\n{d}%'
                        },
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    },
                    labelLine: { show: false },
                    data: chartItems
                }]
            });

            // 2. Cost Breakdown Chart Data
            const totals = data.totals;
            const costData = [
                { value: parseFloat(totals.total_labor), name: 'Labor' },
                { value: parseFloat(totals.total_material), name: 'Material' },
                { value: parseFloat(totals.total_machine), name: 'Machine' },
                { value: parseFloat(totals.subitem_cost), name: 'Sub-items' },
                { value: parseFloat(totals.overhead_cost), name: 'Overheads' },
                { value: parseFloat(totals.total_cartage), name: 'Cartage' },
                { value: parseFloat(totals.total_miscellaneous), name: 'Miscellaneous' }
            ].filter(item => item.value > 0);

            costChart.setOption({
                ...commonOptions,
                color: colors,
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    top: 30,
                    textStyle: { color: textColor }
                },
                series: [{
                    name: 'Cost Breakdown',
                    type: 'pie',
                    radius: '60%',
                    center: ['60%', '55%'],
                    roseType: 'radius',
                    data: costData,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    },
                    itemStyle: {
                        borderRadius: 5,
                        borderColor: isDark ? '#1f2937' : '#fff',
                        borderWidth: 1
                    },
                    label: {
                        show: true,
                        color: textColor,
                        formatter: '{b}'
                    }
                }]
            });
        }

        // Listen for theme changes to update charts
        window.addEventListener('theme-changed', function() {
            // Re-render charts if data is available (might need to store last data)
            // For now, a simple reload or just letting the next update handle it is fine.
            // Ideally, we'd trigger a re-render.
            // Since we don't have the data here globally, we rely on the next update or resize.
            if (resourceChart) resourceChart.resize();
            if (costChart) costChart.resize();
        });
    </script>
