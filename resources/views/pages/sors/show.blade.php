@extends('layouts.layout001.app')

@section('title', $sor->name)

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>{{ $sor->name }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Details of the SOR.') }}
            </p>
        </div>
        <div class="flex items-center mt-4 md:mt-0">
            <label for="ratecard" class="mr-2">Rate Card:</label>
            <select name="ratecard" id="ratecard" class="input-field">
                @foreach ($ratecards as $ratecard)
                    <option value="{{ $ratecard->id }}">{{ $ratecard->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="card p-0">
        <div class="hidden md:block">
            <table class="table-responsive">
                <thead class="table-header">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($items as $item)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->item_no }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $item->item_short_desc }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->unit->name ?? '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">-</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No items found in this SOR.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="md:hidden">
            @foreach($items as $item)
                <div class="card mb-4">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $item->item_no }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->item_short_desc }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->unit->name ?? '' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">-</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection