@extends('layouts.layout001.app')

@section('title', $sor->name . ' - SOR Details')

@section('headstyles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('breadcrumbs')
    <x-breadcrumbs :items="$breadcrumbs" />
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $sor->name }}</h1>

    @include('sors._filters', ['rateCards' => $rateCards, 'rateCardId' => $rateCardId, 'effectiveDate' => $effectiveDate])

    <div class="card-glass mt-2">
        @if($currentItem)
            <div class="p-6">
                <p class="text-glass-secondary ">Item Number: {{ $currentItem->item_number }}</p>
                <p class="text-sm font-semibold whitespace-pre-wrap">{{ $currentItem->name }}</p>
            </div>
        @endif

        <div class="p-6">
            <h2 class="text-xl font-semibold mb-2">Chapters & Items</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-4 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/12">#</th>
                            <th scope="col" class="px-4 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-2/12">Item Number</th>
                            <th scope="col" class="px-4 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-4 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-2/12">Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/60 dark:bg-gray-800/60 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($items as $key => $item)
                            <tr class="hover:bg-gray-100/50 dark:hover:bg-gray-700/50">
                                <td class="px-2 py-1 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $key + 1 }}</td>
                                <td class="px-2 py-1 text-sm text-gray-500 dark:text-gray-300">{{ $item->item_number }}</br><span class="text-gray-400 dark:text-gray-500">{{ $item->item_code }}</span></td>
                                <td class="px-2 py-1 text-sm break-words max-w-xs whitespace-pre-wrap">
                                    @if($item->item_type == 1 || $item->item_type == 2)
                                        <a href="{{ route('sors.show', [$sor, $item]) }}" class="hover:underline font-medium
                                            @if($item->item_type == 1) text-blue-600 dark:text-blue-400 @else text-green-600 dark:text-green-400 @endif"
                                            style="display: block; text-align: left;"
                                        >
                                            <span style="display: block; text-align: left;">{{ $item->description }}</span>
                                        </a>
                                    @else
                                        <span class="text-gray-800 dark:text-gray-200" style="display: block; text-align: left;">{{ $item->description }}</span>
                                    @endif
                                </td>
                                <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    @if($item->item_type != 1 && $item->item_type != 2 && $item->rate !== null)
                                        <a href="{{ route('sors.items.ra', [$sor->id, $item->id]) }}"  class="text-gray-900 dark:text-white hover:underline" target="_blank">{{ number_format($item->rate, 2) }}</a> / {{ $item->unit->name ?? '' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
