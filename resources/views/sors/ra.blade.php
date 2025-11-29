@extends('layouts.layout001.app')

@section('title', 'Rate Analysis - ' . $item->item_number)

@section('headstyles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
@endsection

@section('content')
<!-- Rate Card & Date Selection -->
@include('sors._filters', ['rateCards' => $rateCards, 'rateCardId' => $rateCardId, 'effectiveDate' => $effectiveDate])

<div class="flex flex-col md:flex-row md:items-center md:justify-between pb-2">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            {!! config('icons.calculator') !!}
            <span class="ml-2">Rate Analysis (Read Only)</span>
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $item->item_number }} , Code: {{ $item->item_code }},  ID:{{ $item->id }}
        </p>
    </div>
    <div class="mt-4 md:mt-0 flex space-x-2">
        <a href="{{ route('sors.items.consumption', ['sor' => $sor->id, 'item' => $item->id]) }}" class="btn-secondary flex items-center">
            {!! config('icons.chart-pie') !!}
            <span class="ml-2">Consumption Report</span>
        </a>
        <a href="{{ route('sors.items.export', ['sor' => $sor->id, 'item' => $item->id, 'rate_card_id' => $rateCardId, 'date' => $effectiveDate]) }}" class="btn-secondary flex items-center">
            {!! config('icons.download') !!}
            <span class="ml-2">Export to Excel</span>
        </a>
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
        @include('sors.partials.item_details', ['readonly' => true])

        <!-- Resources Section -->
        @include('sors.partials.resources', ['readonly' => true])

        <!-- Sub-items Section -->
        @include('sors.partials.subitems', ['readonly' => true])

        <!-- Overheads Section -->
        @include('sors.partials.overheads', ['readonly' => true])
    </div>

    <!-- Summary Sidebar (1/3 width) -->
    <div class="lg:col-span-1">
        <div class="sticky top-4 rounded-xl bg-white/40 dark:bg-gray-900/40 backdrop-blur-md border border-white/20 dark:border-white/10 shadow-xl ring-1 ring-black/5 transition-all duration-300 hover:shadow-2xl hover:bg-white/50 dark:hover:bg-gray-900/50 group/card">
            <div class="px-6 py-2 border-b border-white/20 dark:border-white/5 flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 group-hover/card:scale-110 transition-transform duration-300">
                    {!! config('icons.calculator') !!}
                </div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 tracking-wide">Cost Summary</h2>
            </div>
            <div class="p-2 space-y-0">
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

<!-- Technical Specifications Section -->
<x-item-technical-specs :item="$item" :editable="false" />

<!-- Charts Section -->
<div class="mt-2 relative">
    <!-- Decorative background elements for glass effect -->
    <div class="absolute top-0 left-0 -mt-20 -ml-20 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 -mb-20 -mr-20 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative z-10">
        <!-- Resource Contribution Chart -->
        <div class="bg-white/60 dark:bg-gray-900/30 backdrop-blur-xl border border-gray-200/50 dark:border-white/10 shadow-lg dark:shadow-2xl rounded-2xl p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.01] group">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">
                    Resource Contribution
                </h3>
                <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform duration-300">
                    {!! config('icons.chart-pie', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>') !!}
                </div>
            </div>
            <div id="chart-resource-contribution" class="w-full h-[400px]"></div>
        </div>

        <!-- Cost Summary Chart -->
        <div class="bg-white/60 dark:bg-gray-900/30 backdrop-blur-xl border border-gray-200/50 dark:border-white/10 shadow-lg dark:shadow-2xl rounded-2xl p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.01] group">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-400 dark:to-pink-400">
                    Cost Breakdown
                </h3>
                <div class="p-2 rounded-lg bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform duration-300">
                    {!! config('icons.chart-bar', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>') !!}
                </div>
            </div>
            <div id="chart-cost-summary" class="w-full h-[400px]"></div>
        </div>
    </div>
</div>

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
    @include('sors.partials.scripts', ['readonly' => true])
@endpush
