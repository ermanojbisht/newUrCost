@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $sor->sorname }}</h1>
        <div class="flex items-center">
            <label for="ratecard" class="mr-2">Rate Card:</label>
            <select name="ratecard" id="ratecard" class="border-gray-300 rounded-md shadow-sm">
                @foreach ($ratecards as $ratecard)
                    <option value="{{ $ratecard->id }}">{{ $ratecard->ratecardname }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->item_no }}</td>
                            <td class="px-6 py-4">{{ $item->item_short_desc }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->unit_id }}</td> {{-- Placeholder for unit name --}}
                            <td class="px-6 py-4 whitespace-nowrap">-</td> {{-- Placeholder for rate --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No items found in this SOR.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $items->links() }}
        </div>
    </div>
@endsection
