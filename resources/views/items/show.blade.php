@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Rate Analysis for: {{ $item->item_no }}</h1>
            <p class="text-gray-600">{{ $item->item_desc }}</p>
        </div>
        <div class="text-right">
            <span class="text-sm text-gray-500">Rate Card</span>
            <h2 class="text-xl font-bold">{{ $ratecard->ratecardname }}</h2>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Analysis Section --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            
            {{-- Resources --}}
            <h3 class="text-lg font-bold mb-4 border-b pb-2">Resources</h3>
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Resource</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Rate</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($analysis['resources'] as $resource)
                            <tr>
                                <td class="px-4 py-2">{{ $resource->name }}</td>
                                <td class="px-4 py-2 text-right">{{ $resource->quantity }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($resource->rate, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($resource->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center text-gray-500">No resources found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Sub-items --}}
            <h3 class="text-lg font-bold mb-4 border-b pb-2">Sub-items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Rate</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($analysis['sub_items'] as $sub_item)
                            <tr>
                                <td class="px-4 py-2">{{ $sub_item->name }}</td>
                                <td class="px-4 py-2 text-right">{{ $sub_item->quantity }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($sub_item->rate, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($sub_item->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center text-gray-500">No sub-items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Cost Summary Section --}}
        <div class="bg-white rounded-lg shadow-md p-6 h-fit">
            <h3 class="text-lg font-bold mb-4 border-b pb-2">Cost Summary</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Resource Cost:</span>
                    <span class="font-bold">{{ number_format(collect($analysis['resources'])->sum('amount'), 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Sub-item Cost:</span>
                    <span class="font-bold">{{ number_format(collect($analysis['sub_items'])->sum('amount'), 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Direct Cost:</span>
                    <span class="font-bold">{{ number_format($analysis['total_cost'], 2) }}</span>
                </div>
                <hr>
                {{-- Overheads --}}
                @foreach ($analysis['overheads'] as $overhead)
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ $overhead->name }}:</span>
                        <span class="font-bold">{{ number_format($overhead->amount, 2) }}</span>
                    </div>
                @endforeach
                <hr>
                <div class="flex justify-between text-xl font-bold mt-4">
                    <span>Rate per {{ $item->unit_id }}:</span> {{-- Placeholder for unit name --}}
                    <span>{{ number_format($analysis['rate'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
