@extends('layouts.layout001.app')

@section('title', __('POL Rates'))

@section('page-header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('POL Rates') }}
        </h2>
        @can('create polrates')
            <a href="{{ route('pol-rates.create') }}" class="btn-primary">
                {!! config('icons.add') !!}
                <span class="ml-2">Create POL Rate</span>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diesel Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mobile Oil Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Laborer Charges</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($polRates as $polRate)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polRate->rate_date->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polRate->diesel_rate }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polRate->mobile_oil_rate }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polRate->laborer_charges }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('pol-rates.show', $polRate) }}" class="btn-secondary btn-sm">
                                        {!! config('icons.show') !!}
                                    </a>
                                    @can('edit polrates')
                                        <a href="{{ route('pol-rates.edit', $polRate) }}" class="btn-secondary btn-sm">
                                            {!! config('icons.edit') !!}
                                        </a>
                                    @endcan
                                    @can('delete polrates')
                                        <form action="{{ route('pol-rates.destroy', $polRate) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
            @foreach($polRates as $polRate)
                <div class="card mb-4">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $polRate->rate_date->format('d-m-Y') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Diesel: {{ $polRate->diesel_rate }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Mobile Oil: {{ $polRate->mobile_oil_rate }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Laborer: {{ $polRate->laborer_charges }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('pol-rates.show', $polRate) }}" class="btn-secondary btn-sm">
                                {!! config('icons.show') !!}
                            </a>
                            @can('edit polrates')
                                <a href="{{ route('pol-rates.edit', $polRate) }}" class="btn-secondary btn-sm">
                                    {!! config('icons.edit') !!}
                                </a>
                            @endcan
                            @can('delete polrates')
                                <form action="{{ route('pol-rates.destroy', $polRate) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
            {{ $polRates->links() }}
        </div>
    </div>
</div>
@endsection
