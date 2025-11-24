@extends('layouts.layout001.app')

@section('title', 'Resource Details: ' . $resource->name)

@section('headstyles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <span class="mr-2 text-gray-500">
                    @if(str_contains(strtolower($resource->group->name ?? ''), 'labour'))
                        {!! config('icons.labour') !!}
                    @elseif(str_contains(strtolower($resource->group->name ?? ''), 'machine'))
                        {!! config('icons.machinery') !!}
                    @else
                        {!! config('icons.material') !!}
                    @endif
                </span>
                {{ $resource->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Code: {{ $resource->resource_code }} 
                @if($resource->secondary_code) | Secondary: {{ $resource->secondary_code }} @endif
            </p>
        </div>
        <a href="javascript:window.close();" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            Close Window
        </a>
    </div>
    <!-- Rate Card & Date Selection -->
    @include('sors._filters', ['rateCards' => $rateCards, 'rateCardId' => $rateCardId, 'effectiveDate' => $effectiveDate])
    <!-- Resource Selector -->
    <!-- Resource Selector -->
    <div x-data="{ expanded: false }" class="mb-6 rounded-xl bg-white/40 dark:bg-gray-900/40 backdrop-blur-md border border-white/20 dark:border-white/10 shadow-xl ring-1 ring-black/5 transition-all duration-300 hover:shadow-2xl hover:bg-white/50 dark:hover:bg-gray-900/50 group/card">
        
        <!-- Header / Toggle -->
        <div @click="expanded = !expanded" class="px-5 py-4 flex justify-between items-center cursor-pointer rounded-xl transition-colors">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/30 group-hover/card:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="flex flex-col">
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 tracking-wide">Select Resource</h3>
                    <!-- Summary when collapsed -->
                    <div x-show="!expanded" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center space-x-2 mt-0.5">
                        <span class="text-emerald-600 dark:text-emerald-400">
                            {{ $resource->name }}
                        </span>
                    </div>
                </div>
            </div>
            <button class="text-gray-400 dark:text-gray-500 p-1.5 rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                <svg x-bind:class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>
        
        <!-- Collapsible Content -->
        <div x-show="expanded" x-collapse class="border-t border-white/20 dark:border-white/5">
            <div class="px-5 py-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Group Filter -->
                    <div class="w-full">
                        <label for="resource_group_filter" class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5 ml-1">Filter by Group</label>
                        <div class="relative group">
                            <select id="resource_group_filter" class="w-full pl-3 pr-8 py-2 text-sm rounded-lg bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-gray-900 dark:text-white placeholder-gray-500 backdrop-blur-sm transition-all duration-200 shadow-sm group-hover:bg-white/80 dark:group-hover:bg-gray-800/80">
                                <option value="">All Groups</option>
                                @foreach($resourceGroups as $group)
                                    <option value="{{ $group->id }}" {{ $resource->group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Resource Search -->
                    <div class="w-full">
                        <label for="resource_search" class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5 ml-1">Search Resource</label>
                        <div class="relative group">
                            <select id="resource_search" class="w-full pl-3 pr-8 py-2 text-sm rounded-lg bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-gray-900 dark:text-white placeholder-gray-500 backdrop-blur-sm transition-all duration-200 shadow-sm group-hover:bg-white/80 dark:group-hover:bg-gray-800/80">
                                <option value="{{ $resource->id }}" selected>{{ $resource->name }} ({{ $resource->secondary_code }})</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Resource Details -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Resource Information</h2>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Group</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->group->name ?? 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $unit->name ?? 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit Group</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->unitGroup->name ?? 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->description ?? 'No description available.' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Rate Analysis -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Rate Analysis</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-2">
                    <span class="text-gray-600 dark:text-gray-400">Total Rate</span>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($rate, 2) }}</span>
                </div>

                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rate Breakdown</h4>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3 space-y-2">
                        @foreach($rateComponents as $component)
                            <div class="flex justify-between items-start text-sm">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $component['name'] }}</span>
                                    @if(!empty($component['description']))
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $component['description'] }}</p>
                                    @endif
                                </div>
                                <span class="text-gray-700 dark:text-gray-300 font-mono">+ ₹{{ number_format($component['amount'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                    <p><strong>Valid From:</strong> {{ $validFrom ? \Carbon\Carbon::parse($validFrom)->format('d M Y') : 'N/A' }}</p>
                    <p><strong>Valid To:</strong> {{ $validTo ? \Carbon\Carbon::parse($validTo)->format('d M Y') : 'Ongoing' }}</p>
                </div>
            </div>
            

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#effective_date", {
        dateFormat: "Y-m-d",
        defaultDate: "{{ $effectiveDate }}"
    });

    $(document).ready(function() {
        const $groupFilter = $('#resource_group_filter');
        const $resourceSearch = $('#resource_search');

        $resourceSearch.select2({
            placeholder: 'Search by Name, Code or ID',
            allowClear: true,
            ajax: {
                url: '{{ route("resources.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        group_id: $groupFilter.val() // filter by selected group
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: `[${item.secondary_code || item.id}] ${item.name}`,
                                item: item
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            dropdownParent: $resourceSearch.parent() // Ensure it renders correctly
        });

        // Reload Select2 when group changes to clear previous selection if needed or just to refresh context
        $groupFilter.on('change', function() {
            $resourceSearch.val(null).trigger('change');
        });

        // Redirect on selection
        $resourceSearch.on('select2:select', function (e) {
            var data = e.params.data;
            if (data.id) {
                window.location.href = '/resources/' + data.id;
            }
        });
    });
</script>
@endpush
