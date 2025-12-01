<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
            color: #333;
        }
        h1 {
            font-size: 18pt;
            margin-bottom: 5px;
            color: #111827;
        }
        h2 {
            font-size: 14pt;
            margin-top: 0;
            margin-bottom: 20px;
            color: #4b5563;
        }
        .meta {
            margin-bottom: 20px;
            font-size: 9pt;
            color: #6b7280;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: monospace;
        }
        .total-rate {
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="meta">
        Date: {{ \Carbon\Carbon::parse($effectiveDate)->format('d-M-Y') }}<br>
        Generated: {{ now()->format('d-M-Y H:i') }}
    </div>

    <h1>{{ $title }}</h1>
    <h2>{{ $rateCard->name }}</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">S.No.</th>
                <th style="width: 80px;">Code</th>
                <th>Description</th>
                <th style="width: 40px;" class="text-center">Unit</th>
                <th style="width: 70px;" class="text-right">Base Rate</th>
                <th style="width: 70px;" class="text-right">Index Cost</th>
                <th style="width: 70px;" class="text-right">Total Rate</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $index => $data)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $data['resource']->secondary_code ?? $data['resource']->id }}</td>
                    <td>{{ $data['resource']->name }}</td>
                    <td class="text-center">{{ $data['unit'] }}</td>
                    <td class="text-right font-mono">{{ number_format($data['base_rate'], 2) }}</td>
                    <td class="text-right font-mono">{{ number_format($data['index_cost'], 2) }}</td>
                    <td class="text-right font-mono total-rate">{{ number_format($data['total_rate'], 2) }}</td>
                    <td style="font-size: 8pt; font-style: italic;">{{ $data['remarks'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        No rates found for this rate card on {{ \Carbon\Carbon::parse($effectiveDate)->format('d-M-Y') }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Page <span class="page-number"></span>
    </div>
    
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("sans-serif", "normal");
                $pdf->text(270, 800, "Page " . $PAGE_NUM . " of " . $PAGE_COUNT, $font, 8);
            ');
        }
    </script>
</body>
</html>
