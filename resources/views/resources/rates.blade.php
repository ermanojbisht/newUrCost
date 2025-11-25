@extends('layouts.layout001.app')

@section('title', 'Manage Rates: ' . $resource->name)

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ 
    showRateModal: false, 
    showLockModal: false,
    isEdit: false,
    rates: {{ json_encode($rates) }},
    
    openAddRateModal() {
        document.getElementById('rateForm').reset();
        document.getElementById('rate_id').value = '';
        document.getElementById('rate_unit_id').value = '{{ $resource->unit_id }}';
        this.isEdit = false;
        this.showRateModal = true;
    },

    openEditRateModal(rateId) {
        const rate = this.rates.find(r => r.id === rateId);
        if (!rate) return;

        document.getElementById('rate_id').value = rate.id;
        document.getElementById('rate_card_id').value = rate.rate_card_id;
        document.getElementById('rate_amount').value = rate.rate;
        document.getElementById('rate_unit_id').value = rate.unit_id;
        document.getElementById('valid_from').value = rate.valid_from ? rate.valid_from.split('T')[0] : '';
        document.getElementById('valid_to').value = rate.valid_to ? rate.valid_to.split('T')[0] : '';
        document.getElementById('remarks').value = rate.remarks;

        this.isEdit = true;
        this.showRateModal = true;
    },

    openLockRateModal(rateId) {
        const rate = this.rates.find(r => r.id === rateId);
        if (!rate) return;

        document.getElementById('lock_rate_id').value = rate.id;
        // Default valid_from to today or rate's valid_from
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('lock_valid_from').value = rate.valid_from ? rate.valid_from.split('T')[0] : today;
        
        this.showLockModal = true;
    },

    saveRate() {
        const form = document.getElementById('rateForm');
        const formData = new FormData(form);
        const rateId = formData.get('rate_id');
        const isEdit = !!rateId;
        
        const url = isEdit 
            ? '{{ route('resources.rates.update', ['resource' => $resource->id, 'rate' => 'RATE_ID']) }}'.replace('RATE_ID', rateId)
            : '{{ route('resources.rates.store', $resource->id) }}';
        
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        const method = 'POST';
        if (isEdit) {
            data['_method'] = 'PUT';
        }

        $.ajax({
            url: url,
            type: method,
            data: data,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                } else {
                    alert('Error saving rate');
                }
            },
            error: function(xhr) {
                console.error(xhr);
                alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
            }
        });
    },

    deleteRate(id) {
        if(confirm('Are you sure you want to delete this rate?')) {
            $.ajax({
                url: '{{ route('resources.rates.destroy', ['resource' => $resource->id, 'rate' => 'RATE_ID']) }}'.replace('RATE_ID', id),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        location.reload();
                    } else {
                        alert('Error deleting rate');
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert('Error deleting rate');
                }
            });
        }
    },

    lockRate() {
        const form = document.getElementById('lockRateForm');
        const formData = new FormData(form);
        const rateId = formData.get('rate_id');
        
        const url = '{{ route('resources.rates.lock', ['resource' => $resource->id, 'rate' => 'RATE_ID']) }}'.replace('RATE_ID', rateId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                valid_from: formData.get('valid_from'),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                } else {
                    alert('Error locking rate');
                }
            },
            error: function(xhr) {
                console.error(xhr);
                alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
            }
        });
    }
}">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manage Rates: {{ $resource->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('resources.edit', $resource->id) }}" class="btn-glass-secondary">Edit Resource</a>
            <a href="{{ route('resources.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">Back to List</a>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex justify-end mb-4">
            <button @click="openAddRateModal()" class="btn-glass-primary">Add Rate</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate Card</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valid From</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valid To</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($rates as $rate)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($rate->is_locked == 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Experimental</span>
                                @elseif($rate->is_locked == 1)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Current</span>
                                @elseif($rate->is_locked == 2)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Old</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Unknown</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->rateCard->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->rate }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->unit->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->valid_from ? $rate->valid_from->format('Y-m-d') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->valid_to ? $rate->valid_to->format('Y-m-d') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($rate->is_locked == 0)
                                    <button type="button" @click="openLockRateModal({{ $rate->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-2" title="Lock & Make Current">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </button>
                                    <button type="button" @click="openEditRateModal({{ $rate->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2" title="Edit">{!! config('icons.edit') !!}</button>
                                    <button type="button" @click="deleteRate({{ $rate->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">{!! config('icons.delete') !!}</button>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed mr-2" title="Locked">{!! config('icons.edit') !!}</span>
                                    <span class="text-gray-400 cursor-not-allowed" title="Locked">{!! config('icons.delete') !!}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('resources.partials.rate-modal')

    <!-- Lock Rate Modal -->
    <div x-show="showLockModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Lock Rate & Make Current</h3>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            This will make this rate the <strong>Current</strong> rate. The previous current rate (if any) will be marked as <strong>Old</strong> and its 'Valid To' date set to one day before the date you select below.
                        </p>
                        <form id="lockRateForm">
                            <input type="hidden" id="lock_rate_id" name="rate_id">
                            <div class="mb-4">
                                <label for="lock_valid_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid From (for new Current Rate)</label>
                                <input type="date" name="valid_from" id="lock_valid_from" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="lockRate()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Lock
                    </button>
                    <button type="button" @click="showLockModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
