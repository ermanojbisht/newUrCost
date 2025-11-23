@extends('layouts.layout001.app')

@section('title', 'Rate Analysis - ' . $item->item_number)

@section('headstyles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
@endsection

@section('content')
<!-- Rate Card & Date Selection -->
@include('sors._filters', ['rateCards' => $rateCards, 'rateCardId' => $rateCardId, 'effectiveDate' => $effectiveDate])

<div class="flex flex-col md:flex-row md:items-center md:justify-between pb-2">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            {!! config('icons.calculator') !!}
            <span class="ml-2">Rate Analysis</span>
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $item->item_number }} , Code: {{ $item->item_code }},  ID:{{ $item->id }}
        </p>
    </div>
    <div class="mt-4 md:mt-0">
        <a href="{{ route('sors.admin', $sor->id) }}" class="btn-secondary flex items-center">
            {!! config('icons.arrow-left') !!}
            <span class="ml-2">Back to Tree</span>
        </a>
    </div>
</div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Item Details Section -->
            @include('sors.partials.item_details')

            <!-- Resources Section -->
            @include('sors.partials.resources')

            <!-- Sub-items Section -->
            @include('sors.partials.subitems')

            <!-- Overheads Section -->
            @include('sors.partials.overheads')
        </div>

        <!-- Summary Sidebar (1/3 width) -->
        <div class="lg:col-span-1">
            <div class="sticky top-4 rounded-xl bg-white/40 dark:bg-gray-900/40 backdrop-blur-md border border-white/20 dark:border-white/10 shadow-xl ring-1 ring-black/5 transition-all duration-300 hover:shadow-2xl hover:bg-white/50 dark:hover:bg-gray-900/50 group/card">
                <div class="px-6 py-4 border-b border-white/20 dark:border-white/5 flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 group-hover/card:scale-110 transition-transform duration-300">
                        {!! config('icons.calculator') !!}
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 tracking-wide">Cost Summary</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-sm group/item hover:bg-white/20 dark:hover:bg-gray-800/20 p-2 rounded-lg transition-colors">
                        <span class="text-gray-600 dark:text-gray-400 flex items-center font-medium">
                            <span class="mr-2 text-blue-500">{!! config('icons.material') !!}</span>
                            Material Cost:
                        </span>
                        <span id="summary-material-cost" class="font-bold text-gray-800 dark:text-gray-200">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-sm group/item hover:bg-white/20 dark:hover:bg-gray-800/20 p-2 rounded-lg transition-colors">
                        <span class="text-gray-600 dark:text-gray-400 flex items-center font-medium">
                            <span class="mr-2 text-green-500">{!! config('icons.labour') !!}</span>
                            Labor Cost:
                        </span>
                        <span id="summary-labor-cost" class="font-bold text-gray-800 dark:text-gray-200">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-sm group/item hover:bg-white/20 dark:hover:bg-gray-800/20 p-2 rounded-lg transition-colors">
                        <span class="text-gray-600 dark:text-gray-400 flex items-center font-medium">
                            <span class="mr-2 text-orange-500">{!! config('icons.machinery') !!}</span>
                            Machine Cost:
                        </span>
                        <span id="summary-machine-cost" class="font-bold text-gray-800 dark:text-gray-200">₹0.00</span>
                    </div>
                    
                    <div class="border-t border-gray-200/50 dark:border-gray-700/50 my-2"></div>
                    
                    <div class="flex justify-between text-sm p-2">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Total Resources:</span>
                        <span id="summary-resource-cost" class="font-bold text-gray-900 dark:text-white">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-sm p-2">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Sub-item Cost:</span>
                        <span id="summary-subitem-cost" class="font-bold text-gray-900 dark:text-white">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-sm p-2">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Overhead Cost:</span>
                        <span id="summary-overhead-cost" class="font-bold text-gray-900 dark:text-white">₹0.00</span>
                    </div>
                    
                    <div class="border-t border-gray-200/50 dark:border-gray-700/50 my-2"></div>
                    
                    <div class="bg-gray-50/50 dark:bg-gray-800/50 rounded-lg p-3 backdrop-blur-sm">
                        <div class="flex justify-between text-sm font-bold mb-2">
                            <span class="text-gray-700 dark:text-gray-300">Total Cost:</span>
                            <span id="summary-total-cost" class="text-gray-900 dark:text-white">₹0.00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Turnout:</span>
                            <span id="summary-turnout" class="font-mono">1.00</span>
                        </div>
                    </div>
                    
                    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 p-4 text-white shadow-lg shadow-blue-500/30">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 rounded-full bg-black/10 blur-xl"></div>
                        
                        <div class="relative flex justify-between items-end">
                            <div>
                                <p class="text-blue-100 text-xs font-medium uppercase tracking-wider mb-1">Final Rate</p>
                                <p class="text-blue-100 text-xs">per {{ $item->unit->name ?? 'unit' }}</p>
                            </div>
                            <span id="summary-final-rate" class="text-2xl font-bold tracking-tight">₹0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('sors.partials.modals')
@endsection

@push('styles')
<style>
    /* Fix Select2 in Dark Mode */
    .dark .select2-container--default .select2-selection--single {
        background-color: #374151;
        border-color: #4b5563;
        color: #fff;
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #fff;
    }
    .dark .select2-dropdown {
        background-color: #1f2937;
        border-color: #4b5563;
        color: #fff;
    }
    .dark .select2-results__option[aria-selected=true] {
        background-color: #374151;
    }
    .dark .select2-search__field {
        background-color: #374151;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
    <!-- Scripts -->
    @include('sors.partials.scripts')
@endpush
