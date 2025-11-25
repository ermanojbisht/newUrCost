@extends('layouts.layout001.app')

@section('title', 'Consumption Report - ' . $item->item_number)

@section('content')
<!-- Rate Card & Date Selection -->
<div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
    <form method="GET" action="{{ route('sors.items.consumption', ['sor' => $sor->id, 'item' => $item->id]) }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label for="rate_card_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rate Card</label>
            <select name="rate_card_id" id="rate_card_id" class="form-select block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                @foreach($rateCards as $rc)
                    <option value="{{ $rc->id }}" {{ $rc->id == $rateCard->id ? 'selected' : '' }}>
                        {{ $rc->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Effective Date</label>
            <input type="date" name="date" id="date" value="{{ $date }}" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
        </div>
        <div>
            <button type="submit" class="btn-primary">
                Apply
            </button>
        </div>
    </form>
</div>

<div class="flex flex-col md:flex-row md:items-center md:justify-between pb-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            {!! config('icons.chart-pie') !!}
            <span class="ml-2">Resource Consumption Report</span>
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $item->item_number }} - {{ $item->description }}
        </p>
    </div>
    <div class="mt-4 md:mt-0 flex space-x-2">
        <a href="{{ route('sors.items.ra', ['sor' => $sor->id, 'item' => $item->id]) }}" class="btn-secondary flex items-center">
            {!! config('icons.calculator') !!}
            <span class="ml-2">Rate Analysis</span>
        </a>
        <a href="{{ route('sors.admin', $sor->id) }}" class="btn-secondary flex items-center">
            {!! config('icons.arrow-left') !!}
            <span class="ml-2">Back to Tree</span>
        </a>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Group</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Resource Name</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Quantity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @php $grandTotal = 0; @endphp
                @forelse($consumptionList as $resource)
                    @php $grandTotal += $resource['amount']; @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $resource['group'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{-- <a href="{{ route('resources.show', $resource['resource_id']) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline"> --}}
                                {{ $resource['name'] }}
                            {{-- </a> --}}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right font-mono">{{ number_format($resource['quantity'], 4) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $resource['unit'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right font-mono">{{ number_format($resource['rate'], 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right font-bold font-mono">{{ number_format($resource['amount'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No resources found for this item.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold">
                <tr>
                    <td colspan="6" class="px-6 py-4 text-right text-sm text-gray-900 dark:text-white uppercase tracking-wider">Grand Total</td>
                    <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-white font-mono">{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
