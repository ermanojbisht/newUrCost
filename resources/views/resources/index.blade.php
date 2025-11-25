@extends('layouts.layout001.app')

@section('title', 'Resources')

@section('headstyles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Resources</h1>
        <a href="{{ route('resources.create') }}" class="btn-glass-primary">Create Resource</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-6 p-4">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resource Group</label>
                <select id="filter_resource_group" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    @foreach($resourceGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Group</label>
                <select id="filter_unit_group" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    @foreach($unitGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit</label>
                <select id="filter_unit" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vol/Weight</label>
                <select id="filter_vol_weight" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    <option value="0">N/A</option>
                    <option value="1">Volume</option>
                    <option value="2">Weight</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capacity Rule</label>
                <select id="filter_capacity_rule" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    @foreach($capacityRules as $rule)
                        <option value="{{ $rule->id }}">{{ $rule->name ?? $rule->id }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capacity Group</label>
                <select id="filter_capacity_group" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All</option>
                    @foreach($capacityGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
             <button id="clear-filters" class="text-sm text-blue-600 hover:text-blue-800">Clear Filters</button>
        </div>
    </div>

    <div class="card">
        <table id="resources-datatable" class="display min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Sec Code</th>
                    <th>Group</th>
                    <th>Unit</th>
                    <th>Vol/Weight</th>
                    <th>Used In</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(function () {
            var table = $('#resources-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('resources.index') }}",
                    data: function (d) {
                        d.resource_group_id = $('#filter_resource_group').val();
                        d.unit_group_id = $('#filter_unit_group').val();
                        d.unit_id = $('#filter_unit').val();
                        d.volume_or_weight = $('#filter_vol_weight').val();
                        d.resource_capacity_rule_id = $('#filter_capacity_rule').val();
                        d.resource_capacity_group_id = $('#filter_capacity_group').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'secondary_code', name: 'secondary_code'},
                    {data: 'group.name', name: 'group.name', defaultContent: 'N/A'},
                    {data: 'unit.name', name: 'unit.name', defaultContent: 'N/A'},
                    {data: 'volume_or_weight', name: 'volume_or_weight'},
                    {data: 'items_using_count', name: 'items_using_count'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            // Reload table on filter change
            $('#filter_resource_group, #filter_unit_group, #filter_unit, #filter_vol_weight, #filter_capacity_rule, #filter_capacity_group').change(function() {
                table.draw();
            });

            $('#clear-filters').click(function() {
                $('select').val('');
                table.draw();
            });
        });

        function deleteResource(id) {
            if(confirm('Are you sure you want to delete this resource?')) {
                $.ajax({
                    url: '/resources/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.success) {
                            $('#resources-datatable').DataTable().ajax.reload();
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error deleting resource');
                    }
                });
            }
        }
    </script>
@endpush
