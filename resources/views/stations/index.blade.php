@extends('layouts.layout001.app')

@section('title', 'Manage Stations')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                {!! config('icons.location') !!}
                <span class="ml-2">Manage Stations</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage stations for resource sourcing and lead calculations.
            </p>
        </div>
        <div class="flex space-x-2">
            <button onclick="openModal()" class="btn-primary flex items-center">
                {!! config('icons.add') !!}
                <span class="ml-2">Add Station</span>
            </button>
            <a href="{{ route('dashboard') }}" class="btn-secondary flex items-center">
                {!! config('icons.arrow-left') !!}
                <span class="ml-2">Back to Dashboard</span>
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mt-6">
        <div class="p-4">
            <table id="stations-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('modals')
<!-- Add/Edit Modal -->
<div id="stationModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-[10000]">
            <form id="stationForm">
                @csrf
                <input type="hidden" id="station_id" name="id">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Add Station</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" id="name" name="name" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@endsection

@push('scripts')
<script>
    function openModal() {
        $('#stationForm')[0].reset();
        $('#station_id').val('');
        $('#modal-title').text('Add Station');
        $('#stationModal').removeClass('hidden');
    }

    function closeModal() {
        $('#stationModal').addClass('hidden');
    }

    function deleteStation(id) {
        if(confirm('Are you sure you want to delete this station?')) {
            $.ajax({
                url: "/stations/" + id,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.success) {
                        $('#stations-table').DataTable().ajax.reload();
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    }

    $(document).ready(function() {
        var table = $('#stations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('stations.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#stationForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#station_id').val();
            var url = id ? "/stations/" + id : "{{ route('stations.store') }}";
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        closeModal();
                        table.ajax.reload();
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error saving station.');
                }
            });
        });

        $('#stations-table').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.get("/stations/" + id, function(data) {
                $('#station_id').val(data.id);
                $('#name').val(data.name);
                $('#modal-title').text('Edit Station');
                $('#stationModal').removeClass('hidden');
            });
        });
    });
</script>
@endpush
