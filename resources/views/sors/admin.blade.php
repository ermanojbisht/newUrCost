@extends('layouts.layout001.app')

@section('title', 'SOR Admin: ' . $sor->name)

@section('headstyles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />
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
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
    <script>
        $(function () {
            $('#sor-tree').jstree({
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
                        'icon': 'fa fa-folder text-blue-500'
                    },
                    'subchapter': {
                        'icon': 'fa fa-folder-open text-green-500'
                    },
                    'item': {
                        'icon': 'fa fa-file text-gray-500'
                    },
                    'default': {
                        'icon': 'fa fa-folder'
                    }
                },
                'contextmenu': {
                    'items': function ($node) {
                        var tree = $('#sor-tree').jstree(true);
                        return {
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
                    },
                    error: function (xhr) {
                        console.error('Error creating node:', xhr.responseText);
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
                    url: '{{ route('api.sors.tree.update', ['sor' => $sor->id, 'item' => '']) }}' + node.id,
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
                    url: '{{ route('api.sors.tree.delete', ['sor' => $sor->id, 'item' => '']) }}' + node.id,
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
                        instance.refresh(); // Revert move in UI on error
                        alert('Failed to move node. Please try again. ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endpush
