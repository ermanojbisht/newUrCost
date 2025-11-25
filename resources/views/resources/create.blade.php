@extends('layouts.layout001.app')

@section('title', 'Create Resource')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Resource</h1>
        <a href="{{ route('resources.index') }}" class="text-blue-600 hover:text-blue-800">Back to List</a>
    </div>

    <div class="card max-w-2xl mx-auto p-6">
        <form action="{{ route('resources.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Name</label>
                <input type="text" name="name" id="name" class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
            </div>

            <div class="mb-4">
                <label for="secondary_code" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Secondary Code</label>
                <input type="text" name="secondary_code" id="secondary_code" class="form-input w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="mb-4">
                <label for="resource_group_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Resource Group</label>
                <select name="resource_group_id" id="resource_group_id" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Select Group</option>
                    @foreach($resourceGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="unit_group_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Unit Group</label>
                <select name="unit_group_id" id="unit_group_id" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Select Unit Group</option>
                    @foreach($unitGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="unit_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Unit</label>
                <select name="unit_id" id="unit_id" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Select Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" data-group-id="{{ $unit->unit_group_id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="volume_or_weight" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Volume/Weight</label>
                <select name="volume_or_weight" id="volume_or_weight" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="0">N/A</option>
                    <option value="1">Volume</option>
                    <option value="2">Weight</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Description</label>
                <textarea name="description" id="description" rows="3" class="form-textarea w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-glass-primary">Create Resource</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('unit_group_id').addEventListener('change', function() {
        var groupId = this.value;
        var unitSelect = document.getElementById('unit_id');
        var options = unitSelect.querySelectorAll('option');

        options.forEach(function(option) {
            if (option.value === "") return; // Skip placeholder

            if (groupId === "" || option.getAttribute('data-group-id') == groupId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Reset selection if current selection is hidden
        if (unitSelect.selectedOptions[0].style.display === 'none') {
            unitSelect.value = "";
        }
    });
</script>
@endsection
