@extends('layouts.layout001.app')

@section('title', __('Rate Cards Management'))

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H4a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                <span>{{ __('Rate Cards Management') }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Manage your rate cards') }}
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            @can('create ratecards')
            <a href="{{ route('rate-cards.create') }}" class="btn-primary flex items-center">
                {!! config('icons.add') !!}
                <span class="ml-2">{{ __('Add Rate Card') }}</span>
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    <div class="card p-0">
        <div class="hidden lg:block overflow-x-auto">
            <table class="table-responsive">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-3 text-left">{{ __('Name') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('Description') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($rateCards as $rateCard)
                        <tr class="table-row">
                            <td class="px-6 py-4">{{ $rateCard->name }}</td>
                            <td class="px-6 py-4">{{ $rateCard->description }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    @can('edit ratecards')
                                    <a href="{{ route('rate-cards.edit', $rateCard) }}" class="text-blue-500 hover:text-blue-700">
                                        {!! config('icons.edit') !!}
                                    </a>
                                    @endcan
                                    @can('delete ratecards')
                                    <form action="{{ route('rate-cards.destroy', $rateCard) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
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

        <div class="lg:hidden grid grid-cols-1 sm:grid-cols-2 gap-4 p-4">
            @foreach($rateCards as $rateCard)
                <div class="card">
                    <div class="flex items-start space-x-4">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $rateCard->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $rateCard->description }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-end space-x-2">
                            @can('edit ratecards')
                            <a href="{{ route('rate-cards.edit', $rateCard) }}" class="btn-secondary text-sm">{{ __('Edit') }}</a>
                            @endcan
                            @can('delete ratecards')
                            <form action="{{ route('rate-cards.destroy', $rateCard) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger text-sm">{{ __('Delete') }}</button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $rateCards->links() }}
        </div>
    </div>
@endsection