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

    <!-- Resource Selector -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Select Resource</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="resource_group_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Group</label>
                <select id="resource_group_filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    <option value="">All Groups</option>
                    @foreach($resourceGroups as $group)
                        <option value="{{ $group->id }}" {{ $resource->group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="resource_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Resource</label>
                <select id="resource_search" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    <option value="{{ $resource->id }}" selected>{{ $resource->name }} ({{ $resource->secondary_code }})</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('resources.show', $resource->id) }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="rate_card_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rate Card</label>
                <select name="rate_card_id" id="rate_card_id" onchange="this.form.submit()"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    @foreach($rateCards as $card)
                        <option value="{{ $card->id }}" {{ $rateCardId == $card->id ? 'selected' : '' }}>
                            {{ $card->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="effective_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Effective Date</label>
                <input type="text" name="effective_date" id="effective_date" value="{{ $effectiveDate }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                    onchange="this.form.submit()">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Apply
                </button>
            </div>
        </form>
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
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->unit->name ?? 'N/A' }}</dd>
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
