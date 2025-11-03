@extends('layouts.layout001.app')

@section('title', $sor->name . ' - Admin View')

@section('headstyles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ $sor->name }} - Administrative View</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="card-glass">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">SOR Tree</h2>
                    <div id="sor-tree"></div>
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            <div class="card-glass">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Node Details</h2>
                    <div id="node-details">
                        <p class="text-glass-secondary">Select a node to see its details.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="rename-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Rename Node
                            </h3>
                            <div class="mt-2">
                                <form id="rename-form">
                                    <input type="hidden" id="rename-node-id">
                                    <div class="mb-4">
                                        <label for="rename-item-number" class="block text-sm font-medium text-gray-700">Item Number</label>
                                        <input type="text" name="item_number" id="rename-item-number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div class="mb-4">
                                        <label for="rename-item-code" class="block text-sm font-medium text-gray-700">Item Code</label>
                                        <input type="text" name="item_code" id="rename-item-code" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div class="mb-4">
                                        <label for="rename-name" class="block text-sm font-medium text-gray-700">Name</label>
                                        <input type="text" name="name" id="rename-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="rename-save" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button type="button" id="rename-cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script>
        $(function () {
            $('#sor-tree').jstree({
                'core' : {
                    'data' : {
                        'url' : '{{ route("api.sors.tree.data", $sor) }}',
                        'dataType' : 'json'
                    },
                    'check_callback' : true
                },
                'plugins' : [ 'contextmenu', 'dnd', 'types' ],
                'contextmenu' : {
                    'items' : function(node) {
                        var tmp = $.jstree.defaults.contextmenu.items();
                        delete tmp.create;

                        tmp.create_chapter = {
                            "separator_after": true,
                            "label": "New Chapter",
                            "action": function (data) {
                                var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);
                                inst.create_node(obj, { type : "chapter", text : "New Chapter" }, "last", function (new_node) {
                                    window.lastCreatedNodeId = new_node.id; // Store the new node ID globally
                                    setTimeout(function () { inst.edit(new_node); },0);
                                });
                            }
                        };

                        tmp.create_subchapter = {
                            "label": "New Sub-Chapter",
                            "action": function (data) {
                                var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);
                                inst.create_node(obj, { type : "subchapter", text : "New Sub-Chapter" }, "last", function (new_node) {
                                    setTimeout(function () { inst.edit(new_node); },0);
                                });
                            }
                        };

                        tmp.create_item = {
                            "label": "New Item",
                            "action": function (data) {
                                var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);
                                inst.create_node(obj, { type : "item", text : "New Item" }, "last", function (new_node) {
                                    setTimeout(function () { inst.edit(new_node); },0);
                                });
                            }
                        };

                        if(this.get_type(node) === "item") {
                            delete tmp.create_chapter;
                            delete tmp.create_subchapter;
                            delete tmp.create_item;
                        }
                        tmp.rename.action = function (data) {
                            var inst = $.jstree.reference(data.reference), obj = inst.get_node(data.reference);
                            $('#rename-node-id').val(obj.id);
                            $('#rename-item-number').val(obj.original.item_number);
                            $('#rename-item-code').val(obj.original.item_code);
                            $('#rename-name').val(obj.text);
                            $('#rename-modal').removeClass('hidden');
                        };
                        $('#rename-save').on('click', function () {
                            var nodeId = $('#rename-node-id').val();
                            var item_number = $('#rename-item-number').val();
                            var item_code = $('#rename-item-code').val();
                            var name = $('#rename-name').val();
                            $.ajax({
                                type: "PUT",
                                url: "/api/sors/{{$sor->id}}/tree/" + nodeId,
                                data: {
                                    text: name,
                                    item_number: item_number,
                                    item_code: item_code,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (data) {
                                    $('#rename-modal').addClass('hidden');
                                    var inst = $.jstree.reference('#sor-tree');
                                    inst.set_text(nodeId, item_code + ' - ' + name);
                                }
                            });
                        });
                        $('#rename-cancel').on('click', function () {
                            $('#rename-modal').addClass('hidden');
                        });
                        tmp.remove.action = function (data) {
                            var inst = $.jstree.reference(data.reference), obj = inst.get_node(data.reference);
                            if (confirm("Are you sure you want to delete this node?")) {
                                inst.delete_node(obj);
                                $.ajax({
                                    type: "DELETE",
                                    url: "/api/sors/{{$sor->id}}/tree/" + obj.id,
                                    data: { _token: '{{ csrf_token() }}' },
                                    success: function (data) {
                                        // Handle success
                                    }
                                });
                            }
                        };
                        return tmp;
                    }
                }
            }).on('contextmenu.jstree', function (e, data) {
                e.preventDefault();
            }).on('move_node.jstree', function (e, data) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('api.sors.tree.move', $sor) }}",
                    data: {
                        'id': data.node.id,
                        'parent': data.parent,
                        'position': data.position,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        // Handle success
                    }
                });
            }).on('rename_node.jstree', function (e, data) {
                console.log("rename_node.jstree event triggered!");
                console.log("Event data:", data);
                var inst = $.jstree.reference(data.instance),
                    node = inst.get_node(data.node);
                console.log("Node object:", node);

                // Determine if it's a new node or a renamed existing node
                var isNewNode = !node.original; // If original is null, it's a new node
                console.log("Is new node:", isNewNode);

                var url = isNewNode ? "/api/sors/{{$sor->id}}/tree" : "/api/sors/{{$sor->id}}/tree/" + node.id;
                var method = isNewNode ? "POST" : "PUT";

                $.ajax({
                    type: method,
                    url: url,
                    data: {
                        id: node.id,
                        parent: node.parent,
                        text: node.text,
                        type: node.type,
                        // For new nodes, item_number and item_code might be empty or generated on backend
                        item_number: node.original ? node.original.item_number : '',
                        item_code: node.original ? node.original.item_code : '',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        console.log("AJAX success:", response);
                        // Update the node ID if it was a new node and the backend returned a new ID
                        if (isNewNode && response.id) {
                            inst.set_id(node, response.id);
                        }
                        // Optionally refresh the node details panel if it's open
                        if ($('#node-id').val() === node.id) {
                            // Trigger select_node to refresh details
                            inst.trigger('select_node', { node: node });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error saving node:", error);
                        // Revert the node name if save fails
                        inst.set_text(node, data.old); // Revert to old text
                    }
                });
            }).on('select_node.jstree', function (e, data) {
                $.ajax({
                    type: "GET",
                    url: "/api/sors/{{$sor->id}}/tree/" + data.node.id,
                    success: function (data) {
                        var details = '<form id="node-details-form">';
                        details += '<input type="hidden" id="node-id" value="' + data.id + '">';
                        details += '<div class="mb-4"><label for="node-item-number" class="block text-sm font-medium text-gray-700">Item Number</label><input type="text" name="item_number" id="node-item-number" value="' + data.item_number + '" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></div>';
                        details += '<div class="mb-4"><label for="node-item-code" class="block text-sm font-medium text-gray-700">Item Code</label><input type="text" name="item_code" id="node-item-code" value="' + data.item_code + '" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></div>';
                        details += '<div class="mb-4"><label for="node-name" class="block text-sm font-medium text-gray-700">Name</label><input type="text" name="name" id="node-name" value="' + data.name + '" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></div>';
                        details += '<div class="mb-4"><label for="node-unit" class="block text-sm font-medium text-gray-700">Unit</label><input type="text" name="unit" id="node-unit" value="' + data.unit + '" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></div>';
                        details += '<div class="mb-4"><label for="node-rate" class="block text-sm font-medium text-gray-700">Rate</label><input type="text" name="rate" id="node-rate" value="' + data.rate + '" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></div>';
                        details += '<button type="button" id="node-details-save" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm">Save</button>';
                        details += '</form>';
                        $('#node-details').on('click', '#node-details-save', function () {
                            var nodeId = $('#node-id').val();
                            var item_number = $('#node-item-number').val();
                            var item_code = $('#node-item-code').val();
                            var name = $('#node-name').val();
                            $.ajax({
                                type: "PUT",
                                url: "/api/sors/{{$sor->id}}/tree/" + nodeId,
                                data: {
                                    text: name,
                                    item_number: item_number,
                                    item_code: item_code,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (data) {
                                    var inst = $.jstree.reference('#sor-tree');
                                    inst.set_text(nodeId, item_code + ' - ' + name);
                                }
                            });
                        });
                        $('#node-details').html(details);
                    }
                });
            });
        });
    </script>
@endpush