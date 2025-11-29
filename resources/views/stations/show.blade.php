@extends('layouts.layout001.app')

@section('title', 'Station Details - ' . $station->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                {!! config('icons.location') !!}
                <span class="ml-2">{{ $station->name }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Station Details and Associations
            </p>
        </div>
        <div class="flex space-x-2">
            <button id="sync-btn" class="btn-primary flex items-center">
                {!! config('icons.optimize') !!}
                <span class="ml-2">Sync Associations</span>
            </button>
            <a href="{{ route('stations.index') }}" class="btn-secondary flex items-center">
                {!! config('icons.arrow-left') !!}
                <span class="ml-2">Back to Stations</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Associated Resources -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    Associated Resources
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ count($resources) }}
                    </span>
                </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if(count($resources) > 0)
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($resources as $resource)
                            <li class="py-3 flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $resource->name }}</span>
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $resource->secondary_code }})</span>
                                </div>
                                <a href="{{ route('resources.lead-distances.index', $resource->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                    View Leads
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No resources associated yet.</p>
                @endif
            </div>
        </div>

        <!-- Associated Rate Cards -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    Associated Rate Cards
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ count($rateCards) }}
                    </span>
                </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if(count($rateCards) > 0)
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($rateCards as $card)
                            <li class="py-3">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $card->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No rate cards associated yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#sync-btn').click(function() {
        if(confirm('This will recalculate associated resources and rate cards based on Lead Distances. Continue?')) {
            $(this).prop('disabled', true).html('Syncing...');
            $.ajax({
                url: "{{ route('stations.sync', $station->id) }}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.success) {
                        alert(response.message + '\nResources: ' + response.data.resources_count + '\nRate Cards: ' + response.data.rate_cards_count);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                        $('#sync-btn').prop('disabled', false).html('{!! config('icons.optimize') !!} <span class="ml-2">Sync Associations</span>');
                    }
                },
                error: function() {
                    alert('Error syncing associations.');
                    $('#sync-btn').prop('disabled', false).html('{!! config('icons.optimize') !!} <span class="ml-2">Sync Associations</span>');
                }
            });
        }
    });
</script>
@endpush
