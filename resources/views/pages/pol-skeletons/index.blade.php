@extends('layouts.layout001.app')

@section('title', __('POL Skeletons'))

@section('page-header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('POL Skeletons') }}
        </h2>
        @can('create polskeletons')
            <a href="{{ route('pol-skeletons.create') }}" class="btn-primary">
                {!! config('icons.add') !!}
                <span class="ml-2">Create POL Skeleton</span>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diesel Mileage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mobile Oil Mileage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Laborers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valid From</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valid To</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($polSkeletons as $polSkeleton)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polSkeleton->date->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polSkeleton->diesel_mileage }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polSkeleton->mobile_oil_mileage }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polSkeleton->number_of_laborers }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polSkeleton->valid_from->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $polSkeleton->valid_to->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('pol-skeletons.show', $polSkeleton) }}" class="btn-secondary btn-sm">
                                        {!! config('icons.show') !!}
                                    </a>
                                    @can('edit polskeletons')
                                        <a href="{{ route('pol-skeletons.edit', $polSkeleton) }}" class="btn-secondary btn-sm">
                                            {!! config('icons.edit') !!}
                                        </a>
                                    @endcan
                                    @can('delete polskeletons')
                                        <form action="{{ route('pol-skeletons.destroy', $polSkeleton) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
            @foreach($polSkeletons as $polSkeleton)
                <div class="card mb-4">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $polSkeleton->date->format('d-m-Y') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Diesel Mileage: {{ $polSkeleton->diesel_mileage }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Mobile Oil Mileage: {{ $polSkeleton->mobile_oil_mileage }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Laborers: {{ $polSkeleton->number_of_laborers }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('pol-skeletons.show', $polSkeleton) }}" class="btn-secondary btn-sm">
                                {!! config('icons.show') !!}
                            </a>
                            @can('edit polskeletons')
                                <a href="{{ route('pol-skeletons.edit', $polSkeleton) }}" class="btn-secondary btn-sm">
                                    {!! config('icons.edit') !!}
                                </a>
                            @endcan
                            @can('delete polskeletons')
                                <form action="{{ route('pol-skeletons.destroy', $polSkeleton) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
            {{ $polSkeletons->links() }}
        </div>
    </div>
</div>
@endsection
