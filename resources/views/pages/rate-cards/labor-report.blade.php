<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labor Resource Rates - {{ $rateCard->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-white p-8 text-gray-900 font-sans">

    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-wide">Labor Resource Rates</h1>
                <h2 class="text-lg text-gray-600 mt-1">{{ $rateCard->name }}</h2>
                @if($rateCard->description)
                    <p class="text-sm text-gray-500 mt-1">{{ $rateCard->description }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Generated on: {{ now()->format('d-M-Y H:i') }}</p>
                <button onclick="window.print()" class="no-print mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Print Report
                </button>
            </div>
        </div>

        <div class="border border-gray-300 rounded-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300 text-sm uppercase text-gray-600">
                        <th class="py-3 px-4 border-r border-gray-300 w-16 text-center">S.No.</th>
                        <th class="py-3 px-4 border-r border-gray-300 w-32">Code</th>
                        <th class="py-3 px-4 border-r border-gray-300">Description</th>
                        <th class="py-3 px-4 border-r border-gray-300 w-24 text-center">Unit</th>
                        <th class="py-3 px-4 border-r border-gray-300 w-32 text-right">Base Rate</th>
                        <th class="py-3 px-4 border-r border-gray-300 w-32 text-right">Index Cost</th>
                        <th class="py-3 px-4 border-r border-gray-300 w-32 text-right">Total Rate</th>
                        <th class="py-3 px-4 w-48">Remarks</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($reportData as $index => $data)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-2 px-4 border-r border-gray-200 text-center">{{ $index + 1 }}</td>
                            <td class="py-2 px-4 border-r border-gray-200 font-mono text-xs">{{ $data['resource']->secondary_code ?? $data['resource']->id }}</td>
                            <td class="py-2 px-4 border-r border-gray-200">{{ $data['resource']->name }}</td>
                            <td class="py-2 px-4 border-r border-gray-200 text-center">{{ $data['unit'] }}</td>
                            <td class="py-2 px-4 border-r border-gray-200 text-right font-medium">{{ number_format($data['base_rate'], 2) }}</td>
                            <td class="py-2 px-4 border-r border-gray-200 text-right font-medium text-gray-500">{{ number_format($data['index_cost'], 2) }}</td>
                            <td class="py-2 px-4 border-r border-gray-200 text-right font-bold">{{ number_format($data['total_rate'], 2) }}</td>
                            <td class="py-2 px-4 text-gray-500 text-xs">{{ $data['remarks'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">
                                No labor rates found for this rate card.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 text-xs text-gray-400 text-center border-t border-gray-200 pt-4">
            <p>End of Report</p>
        </div>
    </div>

</body>
</html>
