@extends('layouts.layout001.app')

@section('title', __('Truck Speeds'))

@section('page-header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Truck Speeds') }}
        </h2>
        @can('create truckspeeds')
            <a href="{{ route('truck-speeds.create') }}" class="btn-primary">
                {!! config('icons.add') !!}
                <span class="ml-2">Create Truck Speed</span>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lead Distance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Average Speed</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($truckSpeeds as $truckSpeed)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $truckSpeed->lead_distance }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $truckSpeed->average_speed }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('truck-speeds.show', $truckSpeed) }}" class="btn-secondary btn-sm">
                                        {!! config('icons.show') !!}
                                    </a>
                                    @can('edit truckspeeds')
                                        <a href="{{ route('truck-speeds.edit', $truckSpeed) }}" class="btn-secondary btn-sm">
                                            {!! config('icons.edit') !!}
                                        </a>
                                    @endcan
                                    @can('delete truckspeeds')
                                        <form action="{{ route('truck-speeds.destroy', $truckSpeed) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
            @foreach($truckSpeeds as $truckSpeed)
                <div class="card mb-4">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $truckSpeed->lead_distance }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $truckSpeed->average_speed }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('truck-speeds.show', $truckSpeed) }}" class="btn-secondary btn-sm">
                                {!! config('icons.show') !!}
                            </a>
                            @can('edit truckspeeds')
                                <a href="{{ route('truck-speeds.edit', $truckSpeed) }}" class="btn-secondary btn-sm">
                                    {!! config('icons.edit') !!}
                                </a>
                            @endcan
                            @can('delete truckspeeds')
                                <form action="{{ route('truck-speeds.destroy', $truckSpeed) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
            {{ $truckSpeeds->links() }}
        </div>
    </div>
</div>
@endsection
