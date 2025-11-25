@extends('layouts.layout001.app')

@section('title', 'Edit Resource')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Resource: {{ $resource->name }}</h1>
        <div class="flex space-x-2">
             <a href="{{ route('resources.rates.index', $resource->id) }}" class="btn-glass-secondary">Manage Rates</a>
             <a href="{{ route('resources.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">Back to List</a>
        </div>
    </div>

    <div class="card max-w-2xl mx-auto p-6">
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
                <select name="unit_group_id" id="unit_group_id" x-model="selectedUnitGroupId" @change="filterUnits()" class="form-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
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
                        <option value="{{ $unit->id }}" data-group-id="{{ $unit->unit_group_id }}" {{ $resource->unit_id == $unit->id ? 'selected' : '' }} x-show="selectedUnitGroupId === '' || selectedUnitGroupId == {{ $unit->unit_group_id }}">{{ $unit->name }}</option>
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
</div>
@endsection
