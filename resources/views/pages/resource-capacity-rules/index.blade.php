@extends('layouts.layout001.app')

@section('title', __('Resource Capacity Rules'))

@section('page-header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Resource Capacity Rules') }}
        </h2>
        @can('create resourcecapacityrules')
            <a href="{{ route('resource-capacity-rules.create') }}" class="btn-primary">
                {!! config('icons.add') !!}
                <span class="ml-2">Create Rule</span>
            </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="card p-0">
        <div class="hidden md:block">
            <table class="table-responsive">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rule ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mechanical Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Net Mechanical Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Manual Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Net Manual Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mule Factor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sample Resource</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($resourceCapacityRules as $rule)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rule->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rule->mechanical_capacity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rule->net_mechanical_capacity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rule->manual_capacity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rule->net_manual_capacity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rule->mule_factor }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $rule->sample_resource }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('resource-capacity-rules.show', $rule) }}" class="btn-secondary btn-sm">
                                        {!! config('icons.show') !!}
                                    </a>
                                    @can('edit resourcecapacityrules')
                                        <a href="{{ route('resource-capacity-rules.edit', $rule) }}" class="btn-secondary btn-sm">
                                            {!! config('icons.edit') !!}
                                        </a>
                                    @endcan
                                    @can('delete resourcecapacityrules')
                                        <form action="{{ route('resource-capacity-rules.destroy', $rule) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">
                                                {!! config('icons.delete') !!}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden">
            @foreach($resourceCapacityRules as $rule)
                <div class="card mb-4">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Mech Cap: {{ $rule->mechanical_capacity }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Net Mech Cap: {{ $rule->net_mechanical_capacity }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manual Cap: {{ $rule->manual_capacity }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Net Manual Cap: {{ $rule->net_manual_capacity }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Mule Factor: {{ $rule->mule_factor }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('resource-capacity-rules.show', $rule) }}" class="btn-secondary btn-sm">
                                {!! config('icons.show') !!}
                            </a>
                            @can('edit resourcecapacityrules')
                                <a href="{{ route('resource-capacity-rules.edit', $rule) }}" class="btn-secondary btn-sm">
                                    {!! config('icons.edit') !!}
                                </a>
                            @endcan
                            @can('delete resourcecapacityrules')
                                <form action="{{ route('resource-capacity-rules.destroy', $rule) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">
                                        {!! config('icons.delete') !!}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4">
            {{ $resourceCapacityRules->links() }}
        </div>
    </div>
</div>
@endsection
