@extends('layouts.layout001.app')

@section('title', 'Material Resource Rates - ' . $rateCard->name)

@section('headstyles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-text {
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            background-image: linear-gradient(to right, #22d3ee, #a855f7, #ec4899);
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            .glass {
                background: none !important;
                backdrop-filter: none !important;
                border: none !important;
            }
            .gradient-text {
                background: none !important;
                color: black !important;
                -webkit-text-fill-color: black !important;
            }
            .print-border {
                border: 1px solid #e5e7eb;
            }
            /* Hide layout elements */
            nav, header, footer, .sidebar {
                display: none !important;
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Rate Card & Date Selection -->
    <div class="no-print">
        @include('sors._filters', ['rateCards' => $rateCards, 'rateCardId' => $rateCardId, 'effectiveDate' => $effectiveDate])
        
        <!-- SOR Filter -->
        <div class="mt-4 glass rounded-xl p-4 shadow-lg">
            <div class="flex items-center gap-4">
                <label for="sor-filter" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                    Filter by SOR:
                </label>
                <select id="sor-filter" 
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        onchange="window.location.href = updateUrlParameter(window.location.href, 'sor', this.value)">
                    <option value="">All Resources</option>
                    @foreach($sors as $sor)
                        <option value="{{ $sor->id }}" {{ $sorId == $sor->id ? 'selected' : '' }}>
                            {{ $sor->name }}
                        </option>
                    @endforeach
                </select>
                @if($sorId)
                    <a href="{{ route('rate-cards.material-report', ['rate_card_id' => $rateCardId, 'effective_date' => $effectiveDate]) }}" 
                       class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Clear Filter
                    </a>
                @endif
            </div>
        </div>
    </div>

    <script>
        function updateUrlParameter(url, param, value) {
            const urlObj = new URL(url);
            if (value) {
                urlObj.searchParams.set(param, value);
            } else {
                urlObj.searchParams.delete(param);
            }
            return urlObj.toString();
        }
    </script>

    <div class="w-full">
        
        <!-- Header Section -->
        <div class="glass rounded-2xl p-6 mb-8 shadow-lg print-border">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold uppercase tracking-wide gradient-text">Material Resource Rates</h1>
                    <h2 class="text-xl font-semibold mt-2 text-gray-700 dark:text-gray-300">{{ $rateCard->name }}</h2>
                    @if($rateCard->description)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $rateCard->description }}</p>
                    @endif
                </div>
                
                <div class="flex flex-col items-end gap-3 no-print">
                    <div class="flex gap-2">
                        <a href="{{ route('rate-cards.material-report.pdf', ['rate_card_id' => $rateCardId, 'effective_date' => $effectiveDate]) }}" class="relative overflow-hidden rounded-lg p-px group">
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 group-hover:from-purple-400 group-hover:to-pink-400 transition-all"></div>
                            <div class="relative bg-white dark:bg-gray-900 px-4 py-2 rounded-[7px]">
                                <span class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-purple-500 to-pink-500 group-hover:from-purple-400 group-hover:to-pink-400">
                                    Export PDF
                                </span>
                            </div>
                        </a>
                        <button onclick="window.print()" class="relative overflow-hidden rounded-lg p-px group">
                            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-500 group-hover:from-cyan-400 group-hover:to-blue-400 transition-all"></div>
                            <div class="relative bg-white dark:bg-gray-900 px-4 py-2 rounded-[7px]">
                                <span class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-cyan-500 to-blue-500 group-hover:from-cyan-400 group-hover:to-blue-400">
                                    Print Report
                                </span>
                            </div>
                        </button>
                    </div>
                </div>
                <!-- Print Only Date Display -->
                <div class="hidden print:block text-right">
                    <p class="text-sm text-gray-500">Report Date: {{ \Carbon\Carbon::parse($effectiveDate)->format('d-M-Y') }}</p>
                    <p class="text-xs text-gray-400">Generated: {{ now()->format('d-M-Y H:i') }}</p>
                </div>
            </div>
        </div>


        <!-- Table Section -->
        <div class="glass rounded-2xl overflow-hidden shadow-lg print-border">
            <table id="material-rates-table" class="w-full text-left border-collapse display compact">
                <thead>
                    <tr class="bg-gray-100/50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700 text-xs uppercase tracking-wider text-gray-600 dark:text-gray-400">
                        <th class="py-2 px-3 font-semibold text-center">S.No.</th>
                        <th class="py-2 px-3 font-semibold">Code</th>
                        <th class="py-2 px-3 font-semibold">Description</th>
                        <th class="py-2 px-3 font-semibold text-center">Unit</th>
                        <th class="py-2 px-3 font-semibold text-right">Base Rate</th>
                        <th class="py-2 px-3 font-semibold text-right">Lead Cost</th>
                        <th class="py-2 px-3 font-semibold text-right">Total Rate</th>
                        <th class="py-2 px-3 font-semibold">Remarks</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($reportData as $index => $data)
                        <tr class="hover:bg-white/50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="py-2 px-3 text-center text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-2 px-3 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $data['resource']->secondary_code ?? $data['resource']->id }}</td>
                            <td class="py-2 px-3 font-medium text-gray-800 dark:text-gray-200">{{ $data['resource']->name }}</td>
                            <td class="py-2 px-3 text-center text-gray-600 dark:text-gray-400">{{ $data['unit'] }}</td>
                            <td class="py-2 px-3 text-right font-mono text-gray-600 dark:text-gray-400">{{ number_format($data['base_rate'], 2) }}</td>
                            <td class="py-2 px-3 text-right font-mono text-gray-500 dark:text-gray-500">{{ number_format($data['lead_cost'], 2) }}</td>
                            <td class="py-2 px-3 text-right font-bold font-mono text-gray-900 dark:text-white">{{ number_format($data['total_rate'], 2) }}</td>
                            <td class="py-2 px-3 text-xs text-gray-500 dark:text-gray-500 italic">{{ $data['remarks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8 text-xs text-gray-400 text-center pt-4 border-t border-gray-200 dark:border-gray-800">
            <p>End of Report</p>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#material-rates-table').DataTable({
                pageLength: 25,
                lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
                order: [[1, 'asc']], // Sort by Code column
                responsive: true,
                dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 no-print"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4 no-print"ip>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search resources...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ resources",
                    infoEmpty: "No resources found",
                    infoFiltered: "(filtered from _MAX_ total resources)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                columnDefs: [
                    { orderable: false, targets: 0 } // Disable sorting on S.No. column
                ]
            });
        });
    </script>
    @endpush
@endsection
