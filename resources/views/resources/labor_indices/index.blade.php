@extends('layouts.layout001.app')

@section('title', 'Manage Indices - ' . $resource->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                {!! config('icons.chart') !!}
                <span class="ml-2">Manage Indices: {{ $resource->name }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Code: {{ $resource->secondary_code ?? 'N/A' }} | Group: {{ $resource->group->name ?? 'N/A' }}
            </p>
        </div>
        <div class="flex space-x-2">
            <button onclick="openModal()" class="btn-primary flex items-center">
                {!! config('icons.add') !!}
                <span class="ml-2">Add Index</span>
            </button>
            <a href="{{ route('resources.index') }}" class="btn-secondary flex items-center">
                {!! config('icons.arrow-left') !!}
                <span class="ml-2">Back to Resources</span>
            @if($resource->id != 1)
            <a href="{{ route('resources.index') }}" class="btn-secondary flex items-center">
                {!! config('icons.arrow-left') !!}
                <span class="ml-2">Back to Resources</span>
            </a>
            @else
            <a href="{{ route('resources.index') }}" class="btn-secondary flex items-center">
                {!! config('icons.arrow-left') !!}
                <span class="ml-2">Back to Resources</span>
            </a>
            @endif
        </div>
    </div>

    @if($resource->id != 1)
    <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
        <div class="flex items-start justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">Global / Fallback Indices</p>
                @if(count($fallbackIndices) > 0)
                    <p>
                        The following global indices apply when no specific index is found above 
                        (Showing latest {{ count($fallbackIndices) }} of {{ $totalFallback }}):
                    </p>
                    <ul class="list-disc list-inside mt-1 ml-2 space-y-1">
                        @foreach($fallbackIndices as $index)
                            <li>
                                <span class="font-semibold">{{ $index->index_value }}</span> 
                                ({{ $index->rateCard->name ?? 'N/A' }}) - 
                                Valid: {{ $index->valid_from ? $index->valid_from->format('d-M-Y') : 'Any' }} to {{ $index->valid_to ? $index->valid_to->format('d-M-Y') : 'Any' }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No global indices currently defined.</p>
                @endif
            </div>
            <a href="{{ route('labor-indices.global.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center whitespace-nowrap ml-4">
                Manage Global Indices &rarr;
            </a>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mt-6">
        <div class="p-4">
            <table id="indices-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Rate Card</th>
                        <th scope="col" class="px-6 py-3">Index Value</th>
                        <th scope="col" class="px-6 py-3">Valid From</th>
                        <th scope="col" class="px-6 py-3">Valid To</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Created By</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('modals')
<!-- Add/Edit Modal -->
<div id="indexModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-[10000]">
            <form id="indexForm">
                @csrf
                <input type="hidden" id="index_id" name="id">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Add Index</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="rate_card_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rate Card</label>
                            <select id="rate_card_id" name="rate_card_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                @foreach($rateCards as $card)
                                    <option value="{{ $card->id }}">{{ $card->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="index_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Index Value</label>
                            <input type="number" step="0.0001" id="index_value" name="index_value" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="valid_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid From</label>
                            <input type="date" id="valid_from" name="valid_from" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="valid_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid To</label>
                            <input type="date" id="valid_to" name="valid_to" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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

<!-- Lock Confirmation Modal -->
<div id="lockModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="lock-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeLockModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-[10000]">
            <form id="lockForm">
                @csrf
                <input type="hidden" id="lock_index_id" name="id">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="lock-modal-title">Lock Index</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Are you sure you want to lock this index? This will promote it to "Current" and archive the previous current index. This action cannot be undone.
                                </p>
                                <div class="mt-4">
                                    <label for="lock_valid_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid From (Effective Date)</label>
                                    <input type="date" id="lock_valid_from" name="valid_from" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Lock & Promote
                    </button>
                    <button type="button" onclick="closeLockModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
    // Define functions in global scope
    function openModal() {
        $('#indexForm')[0].reset();
        $('#index_id').val('');
        $('#modal-title').text('Add Index');
        $('#indexModal').removeClass('hidden');
    }

    function closeModal() {
        $('#indexModal').addClass('hidden');
    }

    function openLockModal(id, validFrom) {
        $('#lockForm')[0].reset();
        $('#lock_index_id').val(id);
        if (validFrom) {
            $('#lock_valid_from').val(validFrom.substring(0, 10));
        }
        $('#lockModal').removeClass('hidden');
    }

    function closeLockModal() {
        $('#lockModal').addClass('hidden');
    }

    function deleteIndex(id) {
        if(confirm('Are you sure you want to delete this index?')) {
            $.ajax({
                url: "/resources/labor-indices/" + id, // Shallow route for delete
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.success) {
                        $('#indices-table').DataTable().ajax.reload();
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    }

    $(document).ready(function() {
        var isGlobal = {{ $resource->id == 1 ? 'true' : 'false' }};
        var ajaxUrl = isGlobal ? "{{ route('labor-indices.global.index') }}" : "{{ route('resources.labor-indices.index', $resource->id) }}";

        var table = $('#indices-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: ajaxUrl,
            columns: [
                {data: 'id', name: 'id'},
                {data: 'rate_card.name', name: 'rateCard.name'},
                {data: 'index_value', name: 'index_value'},
                {data: 'valid_from', name: 'valid_from'},
                {data: 'valid_to', name: 'valid_to'},
                {data: 'is_locked', name: 'is_locked', render: function(data, type, row) {
                    if (data == 0) return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Experimental</span>';
                    if (data == 1) return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Current</span>';
                    if (data == 2) return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Old</span>';
                    return data;
                }},
                {data: 'created_by.name', name: 'createdBy.name', defaultContent: 'System'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#indexForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#index_id').val();
            var url;
            var method;

            if (id) {
                url = "/resources/labor-indices/" + id; // Shallow route for update
                method = 'PUT';
            } else {
                url = isGlobal ? "{{ route('labor-indices.global.store') }}" : "{{ route('resources.labor-indices.store', $resource->id) }}";
                method = 'POST';
            }

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
                    alert('Error saving index.');
                }
            });
        });

        $('#lockForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#lock_index_id').val();
            var url = "/labor-indices/" + id + "/lock";

            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        closeLockModal();
                        table.ajax.reload();
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error locking index.');
                }
            });
        });

        // Edit button click
        $('#indices-table').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            // Use shallow route for show/edit
            $.get("/resources/labor-indices/" + id, function(data) {
                $('#index_id').val(data.id);
                $('#rate_card_id').val(data.rate_card_id);
                $('#index_value').val(data.index_value);
                $('#valid_from').val(data.valid_from ? data.valid_from.substring(0, 10) : '');
                $('#valid_to').val(data.valid_to ? data.valid_to.substring(0, 10) : '');
                $('#modal-title').text('Edit Index');
                $('#indexModal').removeClass('hidden');
            });
        });
    });
</script>
@endpush
