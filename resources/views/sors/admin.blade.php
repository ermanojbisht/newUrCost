@extends('layouts.layout001.app')

@section('title', 'SOR Admin: ' . $sor->name)

@section('headstyles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        /* Custom Icon Colors */
        .text-yellow-500 {
            color: #f59e0b;
        }

        .text-yellow-400 {
            color: #fbbf24;
        }

        .text-blue-500 {
            color: #3b82f6;
        }

        .text-gray-500 {
            color: #6b7280;
        }
    </style>
@endsection

@section('breadcrumbs')
    <x-breadcrumbs :items="[
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'SORs', 'route' => 'sors.index'],
            ['label' => $sor->name, 'route' => 'sors.show', 'params' => ['sor' => $sor->id]],
            ['label' => 'Admin']
        ]" />
@endsection

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                {!! config('icons.settings') !!}
                <span class="ml-2">SOR Admin: {{ $sor->name }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage the hierarchical structure of {{ $sor->name }}
            </p>
        </div>
        <div class="mt-4 md:mt-0 space-x-2">
            <a href="{{ route('sors.show', $sor->id) }}" class="btn-secondary flex items-center">
                {!! config('icons.arrow-left') !!}
                <span class="ml-2">Back to SOR View</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div id="sor-tree"></div>
    </div>

    <!-- Edit Node Modal -->
    <div id="editNodeModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

        <!-- Modal Panel Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <!-- Modal Panel -->
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-gray-700">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white mb-4" id="modal-title">
                                    Edit Node Details
                                </h3>

                                <div class="mt-4 grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                                    <input type="hidden" id="edit_node_id">

                                    <!-- Item Number -->
                                    <div class="sm:col-span-2">
                                        <label for="edit_item_number"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-200">Item
                                            Number</label>
                                        <input type="text" id="edit_item_number"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                                    </div>

                                    <!-- Description -->
                                    <div class="sm:col-span-6">
                                        <label for="edit_description"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-200">Description</label>
                                        <textarea id="edit_description" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"></textarea>
                                    </div>

                                    <!-- Item Only Fields Container (Sub-grid) -->
                                    <div id="item-only-fields"
                                        class="sm:col-span-6 grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6 hidden">

                                        <!-- Short Description -->
                                        <div class="sm:col-span-6">
                                            <label for="edit_short_description"
                                                class="block text-sm font-bold text-gray-700 dark:text-gray-200">Short
                                                Description</label>
                                            <input type="text" id="edit_short_description"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>

                                        <!-- Unit -->
                                        <div class="sm:col-span-3">
                                            <label for="edit_unit_id"
                                                class="block text-sm font-bold text-gray-700 dark:text-gray-200">Unit</label>
                                            <div class="mt-1 relative">
                                                <select id="edit_unit_id"
                                                    class="select2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="">Select Unit</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Turnout Quantity -->
                                        <div class="sm:col-span-3">
                                            <label for="edit_turnout_quantity"
                                                class="block text-sm font-bold text-gray-700 dark:text-gray-200">Turnout
                                                Quantity</label>
                                            <input type="number" step="0.01" id="edit_turnout_quantity"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>

                                        <!-- Spec Code -->
                                        <div class="sm:col-span-3">
                                            <label for="edit_specification_code"
                                                class="block text-sm font-bold text-gray-700 dark:text-gray-200">Spec
                                                Code</label>
                                            <input type="text" id="edit_specification_code"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>

                                        <!-- Spec Page -->
                                        <div class="sm:col-span-3">
                                            <label for="edit_specification_page_number"
                                                class="block text-sm font-bold text-gray-700 dark:text-gray-200">Spec
                                                Page</label>
                                            <input type="text" id="edit_specification_page_number"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>

                                        <!-- Assumptions -->
                                        <div class="sm:col-span-6">
                                            <label for="edit_assumptions"
                                                class="block text-sm font-bold text-gray-700 dark:text-gray-200">Assumptions</label>
                                            <textarea id="edit_assumptions" rows="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                        </div>

                                        <!-- Footnotes -->
                                        <div class="sm:col-span-6">
                                            <label for="edit_footnotes"
                                                class="block text-sm font-bold text-gray-700 dark:text-gray-200">Footnotes</label>
                                            <textarea id="edit_footnotes" rows="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                        </div>

                                        <!-- Is Canceled -->
                                        <div class="sm:col-span-6 flex items-center">
                                            <input type="checkbox" id="edit_is_canceled"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                                            <label for="edit_is_canceled"
                                                class="ml-2 block text-sm font-bold text-gray-900 dark:text-gray-200">Is
                                                Canceled</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" id="saveNodeDetails"
                        class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Save</button>
                    <button type="button" id="closeNodeModal"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-gray-600 dark:text-white dark:ring-gray-500 dark:hover:bg-gray-500">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
    <script>
        $(function () {
            var tree = $('#sor-tree').jstree({
                'core': {
                    'data': {
                        'url': function (node) {
                            return '{{ route('api.sors.tree.data', $sor->id) }}';
                        },
                        'data': function (node) {
                            return { 'id': node.id };
                        },
                        'dataType': 'json',
                        'cache': false
                    },
                    'check_callback': true,
                    'themes': {
                        'responsive': false,
                        'variant': 'large'
                    }
                },
                'plugins': ['contextmenu', 'dnd', 'state', 'types'],
                'types': {
                    'chapter': {
                        'icon': 'fa fa-folder text-yellow-500'
                    },
                    'sub-chapter': {
                        'icon': 'fa fa-folder-open text-yellow-400'
                    },
                    'item': {
                        'icon': 'fa fa-file-alt text-blue-500'
                    },
                    'default': {
                        'icon': 'fa fa-folder text-yellow-500'
                    }
                },
                'contextmenu': {
                    'items': function ($node) {
                        var tree = $('#sor-tree').jstree(true);
                        return {
                            'EditDetails': {
                                'separator_before': false,
                                'separator_after': false,
                                'label': 'Edit Details',
                                'action': function (obj) {
                                    editNodeDetails($node);
                                }
                            },
                            'RateAnalysis': {
                                'separator_before': false,
                                'separator_after': true,
                                'label': 'Rate Analysis',
                                '_disabled': $node.original.type !== 'item', // Only enable for items
                                'action': function (obj) {
                                    const sorId = {{ $sor->id }};
                                    const itemId = $node.id;
                                    window.location.href = `/sors/${sorId}/items/${itemId}/skeleton`;
                                }
                            },
                            'Create': {
                                'separator_before': false,
                                'separator_after': true,
                                'label': 'Create',
                                'action': false,
                                'submenu': {
                                    'Create_Chapter': {
                                        'seperator_before': false,
                                        'seperator_after': false,
                                        'label': 'Chapter',
                                        'action': function (obj) {
                                            $node = tree.create_node($node, {
                                                'type': 'chapter',
                                                'text': 'New Chapter',
                                                'icon': 'fa fa-folder'
                                            });
                                            tree.edit($node);
                                        }
                                    },
                                    'Create_SubChapter': {
                                        'seperator_before': false,
                                        'seperator_after': false,
                                        'label': 'Sub-Chapter',
                                        'action': function (obj) {
                                            $node = tree.create_node($node, {
                                                'type': 'subchapter',
                                                'text': 'New Sub-Chapter',
                                                'icon': 'fa fa-folder-open'
                                            });
                                            tree.edit($node);
                                        }
                                    },
                                    'Create_Item': {
                                        'seperator_before': false,
                                        'seperator_after': false,
                                        'label': 'Item',
                                        'action': function (obj) {
                                            $node = tree.create_node($node, {
                                                'type': 'item',
                                                'text': 'New Item',
                                                'icon': 'fa fa-file'
                                            });
                                            tree.edit($node);
                                        }
                                    }
                                }
                            },
                            'Rename': {
                                'separator_before': false,
                                'separator_after': false,
                                'label': 'Rename',
                                'action': function (obj) {
                                    tree.edit($node);
                                }
                            },
                            'Delete': {
                                'separator_before': false,
                                'separator_after': false,
                                'label': 'Delete',
                                'action': function (obj) {
                                    tree.delete_node($node);
                                }
                            }
                        };
                    }
                }
            }).on('create_node.jstree', function (e, data) {
                var parentId = data.parent === '#' ? null : data.parent;
                var itemType = 1; // Default to Chapter
                if (data.node.type === 'subchapter') {
                    itemType = 2;
                } else if (data.node.type === 'item') {
                    itemType = 3;
                }

                $.ajax({
                    url: '{{ route('api.sors.tree.create', $sor->id) }}',
                    type: 'POST',
                    data: {
                        'parent_id': parentId,
                        'description': data.node.text,
                        'item_number': '', // Will be set during edit
                        'item_type': itemType,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        data.instance.set_id(data.node, response.id);
                        // Update the node text with the actual item_number and description
                        data.instance.set_text(response.id, response.item_number + ' ' + response.description);

                        // Automatically open edit details for new items
                        if (itemType === 3) {
                            // Small delay to ensure node is fully created in UI
                            setTimeout(function () {
                                editNodeDetails(data.instance.get_node(response.id));
                            }, 500);
                        }
                    },
                    error: function (xhr) {
                        console.error('Error creating node:', xhr.responseText);
                        alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error creating node');
                        data.instance.refresh(); // Refresh tree on error
                    }
                });
            }).on('rename_node.jstree', function (e, data) {
                var instance = data.instance;
                var node = data.node;
                var oldText = data.old; // Store old text for potential revert

                var itemNumber = '';
                var description = node.text;

                // If it's an item, parse item_number and description
                if (node.original.item_type === 3) {
                    var parts = node.text.split(' ', 2); // Split by first space
                    if (parts.length > 1) {
                        itemNumber = parts[0];
                        description = parts[1];
                    } else {
                        // If no space, assume the whole thing is description and item_number is empty
                        itemNumber = '';
                        description = node.text;
                    }
                }

                $.ajax({
                    url: '{{ url('api/sors/' . $sor->id . '/tree') }}/' + node.id,
                    type: 'PUT',
                    data: {
                        'item_number': itemNumber,
                        'description': description,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        // The node text is already updated by jstree, no need to do anything here
                        // Optionally, display a success message
                        console.log('Node renamed successfully:', response);
                    },
                    error: function (xhr) {
                        console.error('Error renaming node:', xhr.responseText);
                        // Revert the node text on error
                        instance.set_text(node, oldText);
                        alert('Failed to rename node. Please try again.');
                    }
                });
            }).on('delete_node.jstree', function (e, data) {
                var instance = data.instance;
                var node = data.node;

                if (!confirm('Are you sure you want to delete this node and all its children? This action cannot be undone.')) {
                    instance.refresh(); // Revert deletion in UI if cancelled
                    return;
                }

                $.ajax({
                    url: '{{ url('api/sors/' . $sor->id . '/tree') }}/' + node.id,
                    type: 'DELETE',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        console.log('Node deleted successfully:', response);
                        // jsTree already removed the node from the UI
                    },
                    error: function (xhr) {
                        console.error('Error deleting node:', xhr.responseText);
                        instance.refresh(); // Revert deletion in UI on error
                        alert('Failed to delete node. Please try again. ' + xhr.responseJSON.message);
                    }
                });
            }).on('move_node.jstree', function (e, data) {
                var instance = data.instance;
                var node = data.node;
                var oldParent = data.old_parent;
                var oldPosition = data.old_position;

                $.ajax({
                    url: '{{ route('api.sors.tree.move', $sor->id) }}',
                    type: 'POST',
                    data: {
                        'id': node.id,
                        'parent': data.parent,
                        'position': data.position,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        console.log('Node moved successfully:', response);
                    },
                    error: function (xhr) {
                        console.error('Error moving node:', xhr.responseText);
                        alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error moving node');
                        $('#sor-tree').jstree(true).refresh(); // Force refresh to revert move
                    }
                });
            }).on('dblclick.jstree', function (e) {
                var instance = $.jstree.reference(this);
                var node = instance.get_node(e.target);
                if (node) {
                    editNodeDetails(node);
                }
            });

            function editNodeDetails(node) {
                // Fetch node details
                $.ajax({
                    url: '{{ url('api/sors/' . $sor->id . '/tree') }}/' + node.id + '/details',
                    type: 'GET',
                    success: function (response) {
                        var item = response.item;
                        var units = response.units;

                        $('#edit_node_id').val(item.id);
                        $('#edit_item_number').val(item.item_number);
                        $('#edit_description').val(item.description);
                        $('#edit_short_description').val(item.short_description);
                        $('#edit_specification_code').val(item.specification_code);
                        $('#edit_specification_page_number').val(item.specification_page_number);
                        $('#edit_turnout_quantity').val(item.turnout_quantity);
                        $('#edit_assumptions').val(item.assumptions);
                        $('#edit_footnotes').val(item.footnotes);
                        $('#edit_is_canceled').prop('checked', item.is_canceled);

                        // Populate Unit Dropdown
                        var unitSelect = $('#edit_unit_id');
                        unitSelect.empty();
                        unitSelect.append('<option value="">Select Unit</option>');
                        $.each(units, function (index, unit) {
                            unitSelect.append('<option value="' + unit.id + '">' + unit.name + ' (' + unit.code + ')</option>');
                        });
                        unitSelect.val(item.unit_id).trigger('change');

                        // Show/Hide fields based on type
                        if (item.item_type == 3) { // Item
                            $('#item-only-fields').removeClass('hidden');
                        } else {
                            $('#item-only-fields').addClass('hidden');
                        }

                        $('#editNodeModal').removeClass('hidden');

                        // Initialize Select2 after modal is visible
                        $('#edit_unit_id').select2({
                            dropdownParent: $('#editNodeModal'),
                            width: '100%'
                        });
                    },
                    error: function (xhr) {
                        console.error('Error fetching node details:', xhr.responseText);
                        alert('Failed to fetch node details.');
                    }
                });
            }

            $('#closeNodeModal').click(function () {
                $('#editNodeModal').addClass('hidden');
            });

            $('#saveNodeDetails').click(function () {
                var nodeId = $('#edit_node_id').val();
                var data = {
                    'item_number': $('#edit_item_number').val(),
                    'description': $('#edit_description').val(),
                    'short_description': $('#edit_short_description').val(),
                    'unit_id': $('#edit_unit_id').val(),
                    'specification_code': $('#edit_specification_code').val(),
                    'specification_page_number': $('#edit_specification_page_number').val(),
                    'turnout_quantity': $('#edit_turnout_quantity').val(),
                    'assumptions': $('#edit_assumptions').val(),
                    'footnotes': $('#edit_footnotes').val(),
                    'is_canceled': $('#edit_is_canceled').is(':checked') ? 1 : 0,
                    '_token': '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ url('api/sors/' . $sor->id . '/tree') }}/' + nodeId + '/details',
                    type: 'PUT',
                    data: data,
                    success: function (response) {
                        $('#editNodeModal').addClass('hidden');

                        // Update tree node text
                        var tree = $('#sor-tree').jstree(true);
                        var node = tree.get_node(nodeId);
                        var newText = response.item.item_number ? response.item.item_number + ' ' + response.item.description : response.item.description;
                        tree.rename_node(node, newText);

                        alert('Node details updated successfully.');
                    },
                    error: function (xhr) {
                        console.error('Error updating node details:', xhr.responseText);
                        alert('Failed to update node details. ' + (xhr.responseJSON.message || ''));
                    }
                });
            });
        });
    </script>
@endpush