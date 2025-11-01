@extends('layouts.layout001.app')

@section('title', $sor->name . ' - SOR Admin')

@section('headstyles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ $sor->name }} - Administrative Tree View</h1>

    <div class="card">
        <div id="jstree-sor-admin"></div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
    <script>
        $(function () {
            $('#jstree-sor-admin').jstree({
                'core' : {
                    'data' : {
                        'url' : function (node) {
                            return '{{ route('api.sors.tree.data', $sor) }}';
                        },
                        'data' : function (node) {
                            return { 'id' : node.id };
                        }
                    },
                    'check_callback' : true,
                    'themes' : { 'responsive' : false }
                },
                'plugins' : ['contextmenu', 'dnd', 'state', 'types'],
                'contextmenu' : {
                    'items' : function(node) {
                        var tmp = $.jstree.defaults.contextmenu.items();
                        delete tmp.create.action;
                        tmp.create.label = "New";
                        tmp.create.submenu = {
                            "create_chapter" : {
                                "separator_before" : false,
                                "separator_after" : false,
                                "label" : "Chapter",
                                "action" : function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                    inst.create_node(obj, { 'type' : 'chapter', 'text' : 'New Chapter' }, "last", function (new_node) {
                                        setTimeout(function () { inst.edit(new_node); },0);
                                    });
                                }
                            },
                            "create_item" : {
                                "separator_before" : false,
                                "separator_after" : false,
                                "label" : "Item",
                                "action" : function (data) {
                                    var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                    inst.create_node(obj, { 'type' : 'item', 'text' : 'New Item' }, "last", function (new_node) {
                                        setTimeout(function () { inst.edit(new_node); },0);
                                    });
                                }
                            }
                        };
                        if(this.get_type(node) === 'item') {
                            delete tmp.create;
                        }
                        return tmp;
                    }
                }
            })
            .on('create_node.jstree', function (e, data) {
                $.post('{{ route('api.sors.tree.create', $sor) }}', {
                    'parent_id' : data.parent === '#' ? null : data.parent,
                    'text' : data.node.text,
                    'item_type' : data.node.type,
                    '_token' : '{{ csrf_token() }}'
                })
                .done(function (d) {
                    data.instance.set_id(data.node, d.id);
                })
                .fail(function () {
                    data.instance.refresh();
                });
            })
            .on('rename_node.jstree', function (e, data) {
                $.ajax({
                    'url' : '{{ url('api/sors/' . $sor->id . '/tree/') }}' + data.node.id,
                    'type' : 'PUT',
                    'data' : {
                        'text' : data.text,
                        '_token' : '{{ csrf_token() }}'
                    }
                })
                .fail(function () {
                    data.instance.refresh();
                });
            })
            .on('delete_node.jstree', function (e, data) {
                $.ajax({
                    'url' : '{{ url('api/sors/' . $sor->id . '/tree/') }}' + data.node.id,
                    'type' : 'DELETE',
                    'data' : {
                        '_token' : '{{ csrf_token() }}'
                    }
                })
                .fail(function () {
                    data.instance.refresh();
                });
            })
            .on('move_node.jstree', function (e, data) {
                $.post('{{ route('api.sors.tree.move', $sor) }}', {
                    'id' : data.node.id,
                    'parent' : data.parent,
                    'position' : data.position,
                    '_token' : '{{ csrf_token() }}'
                })
                .fail(function () {
                    data.instance.refresh();
                });
            });
        });
    </script>
@endpush
