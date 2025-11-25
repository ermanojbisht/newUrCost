@extends('layouts.layout001.app')

@section('title', 'Edit Resource')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ tab: 'details', showRateModal: false, isEdit: false }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Resource: {{ $resource->name }}</h1>
        <a href="{{ route('resources.index') }}" class="text-blue-600 hover:text-blue-800">Back to List</a>
    </div>

    <!-- Tabs -->
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
            <li class="mr-2" role="presentation">
                <button @click="tab = 'details'" :class="tab === 'details' ? 'border-blue-600 text-blue-600 dark:text-blue-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'" class="inline-block p-4 border-b-2 rounded-t-lg" type="button">Details</button>
            </li>
            <li class="mr-2" role="presentation">
                <button @click="tab = 'rates'" :class="tab === 'rates' ? 'border-blue-600 text-blue-600 dark:text-blue-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'" class="inline-block p-4 border-b-2 rounded-t-lg" type="button">Rates</button>
            </li>
        </ul>
    </div>

    <!-- Details Tab -->
    <div x-show="tab === 'details'" class="card max-w-2xl mx-auto p-6">
        <form action="{{ route('resources.update', $resource->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Name</label>
                <input type="text" name="name" id="name" value="{{ $resource->name }}" class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
            </div>

            <div class="mb-4">
                <label for="secondary_code" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Secondary Code</label>
                <input type="text" name="secondary_code" id="secondary_code" value="{{ $resource->secondary_code }}" class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="mb-4">
                <label for="resource_group_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Resource Group</label>
                <select name="resource_group_id" id="resource_group_id" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Select Group</option>
                    @foreach($resourceGroups as $group)
                        <option value="{{ $group->id }}" {{ $resource->resource_group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="unit_group_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Unit Group</label>
                <select name="unit_group_id" id="unit_group_id" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Select Unit Group</option>
                    @foreach($unitGroups as $group)
                        <option value="{{ $group->id }}" {{ $resource->unit_group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="unit_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Unit</label>
                <select name="unit_id" id="unit_id" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Select Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" data-group-id="{{ $unit->unit_group_id }}" {{ $resource->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="volume_or_weight" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Volume/Weight</label>
                <select name="volume_or_weight" id="volume_or_weight" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="0" {{ $resource->volume_or_weight == 0 ? 'selected' : '' }}>N/A</option>
                    <option value="1" {{ $resource->volume_or_weight == 1 ? 'selected' : '' }}>Volume</option>
                    <option value="2" {{ $resource->volume_or_weight == 2 ? 'selected' : '' }}>Weight</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Description</label>
                <textarea name="description" id="description" rows="3" class="form-textarea w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ $resource->description }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-glass-primary">Update Resource</button>
            </div>
        </form>
    </div>

    <!-- Rates Tab -->
    <div x-show="tab === 'rates'" class="card p-6">
        <div class="flex justify-end mb-4">
            <button @click="openAddRateModal()" class="btn-glass-primary">Add Rate</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->rateCard->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->rate }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->unit->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->valid_from ? $rate->valid_from->format('Y-m-d') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rate->valid_to ? $rate->valid_to->format('Y-m-d') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openEditRateModal({{ json_encode($rate) }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2">Edit</button>
                                <button @click="deleteRate({{ $rate->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('resources.partials.rate-modal')

</div>

<script>
    // Unit Filtering Logic (Same as Create)
    document.getElementById('unit_group_id').addEventListener('change', function() {
        var groupId = this.value;
        var unitSelect = document.getElementById('unit_id');
        var options = unitSelect.querySelectorAll('option');

        options.forEach(function(option) {
            if (option.value === "") return;
            if (groupId === "" || option.getAttribute('data-group-id') == groupId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        if (unitSelect.selectedOptions[0].style.display === 'none') {
            unitSelect.value = "";
        }
    });

    // Rate Management Functions
    function openAddRateModal() {
        document.getElementById('rateForm').reset();
        document.getElementById('rate_id').value = '';
        // Set default unit if available
        document.getElementById('rate_unit_id').value = "{{ $resource->unit_id }}";
        
        // Access Alpine data
        let alpineData = Alpine.$data(document.querySelector('[x-data]'));
        alpineData.isEdit = false;
        alpineData.showRateModal = true;
    }

    function openEditRateModal(rate) {
        document.getElementById('rate_id').value = rate.id;
        document.getElementById('rate_card_id').value = rate.rate_card_id;
        document.getElementById('rate_amount').value = rate.rate;
        document.getElementById('rate_unit_id').value = rate.unit_id;
        document.getElementById('valid_from').value = rate.valid_from ? rate.valid_from.split('T')[0] : '';
        document.getElementById('valid_to').value = rate.valid_to ? rate.valid_to.split('T')[0] : '';
        document.getElementById('remarks').value = rate.remarks;

        let alpineData = Alpine.$data(document.querySelector('[x-data]'));
        alpineData.isEdit = true;
        alpineData.showRateModal = true;
    }

    function saveRate() {
        const form = document.getElementById('rateForm');
        const formData = new FormData(form);
        const rateId = formData.get('rate_id');
        const isEdit = !!rateId;
        
        const url = isEdit 
            ? "{{ route('resources.rates.update', ['resource' => $resource->id, 'rate' => 'RATE_ID']) }}".replace('RATE_ID', rateId)
            : "{{ route('resources.rates.store', $resource->id) }}";
        
        // Convert FormData to JSON object
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        // For Edit, use POST with _method: PUT to avoid server configuration issues with PUT verbs
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
                    location.reload(); // Reload to see changes
                } else {
                    alert('Error saving rate');
                }
            },
            error: function(xhr) {
                console.error(xhr);
                alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
            }
        });
    }

    function deleteRate(id) {
        if(confirm('Are you sure you want to delete this rate?')) {
            $.ajax({
                url: "{{ route('resources.rates.destroy', ['resource' => $resource->id, 'rate' => 'RATE_ID']) }}".replace('RATE_ID', id),
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
    }
</script>
@endsection
