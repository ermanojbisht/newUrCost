@extends('layouts.layout001.app')

@section('title', __('Schedule of Rates (SORs)'))

@section('page-header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Schedule of Rates (SORs)') }}
        </h2>
        @can('create sors')
            <a href="{{ route('sors.create') }}" class="btn-primary">
                {!! config('icons.add') !!}
                <span class="ml-2">Create SOR</span>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Short Name</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($sors as $sor)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><a href="{{ route('sors.show', $sor) }}">{{ $sor->name }}</a></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $sor->short_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('sors.show', $sor) }}" class="btn-secondary btn-sm">
                                        {!! config('icons.show') !!}
                                    </a>
                                    @can('edit sors')
                                        <a href="{{ route('sors.edit', $sor) }}" class="btn-secondary btn-sm">
                                            {!! config('icons.edit') !!}
                                        </a>
                                    @endcan
                                    @can('delete sors')
                                        <form action="{{ route('sors.destroy', $sor) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
            @foreach($sors as $sor)
                <div class="card mb-4">
                    <div class="flex justify-between">
                        <div>
                            <a href="{{ route('sors.show', $sor) }}"><h3 class="font-semibold">{{ $sor->name }}</h3></a>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $sor->short_name }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('sors.show', $sor) }}" class="btn-secondary btn-sm">
                                {!! config('icons.show') !!}
                            </a>
                            @can('edit sors')
                                <a href="{{ route('sors.edit', $sor) }}" class="btn-secondary btn-sm">
                                    {!! config('icons.edit') !!}
                                </a>
                            @endcan
                            @can('delete sors')
                                <form action="{{ route('sors.destroy', $sor) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
            {{ $sors->links() }}
        </div>
    </div>
</div>
@endsection
