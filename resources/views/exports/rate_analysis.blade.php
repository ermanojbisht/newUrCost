@php
    $currentRow = 1; // Track current row number
@endphp

@foreach($itemsWithAnalysis as $itemIndex => $itemData)
    @php
        $item = $itemData['item'];
        $analysis = $itemData['analysis'];
    @endphp

    {{-- Item Header --}}
    <table>
        <tr>
            @php $currentRow++; @endphp
            <td colspan="7" style="font-weight: bold; font-size: 14px; background-color: #4472C4; color: white; padding: 8px;">
                Item: {{ $item->item_code }} - {{ $item->description }}
            </td>
        </tr>
        <tr>
            @php $currentRow++; @endphp
            <td colspan="7" style="padding: 4px;">
                Turnout: {{ $item->turnout_quantity > 0 ? $item->turnout_quantity : 1 }} {{ $item->unit->name ?? 'unit' }}
            </td>
        </tr>
    </table>
    <br/>
    @php $currentRow++; @endphp

    {{-- Resources Section --}}
    @if(count($analysis['resources']) > 0)
    <table>
        <thead>
            <tr style="background-color: #D9E1F2; font-weight: bold;">
                @php $currentRow++; @endphp
                <th style="padding: 6px;">Type</th>
                <th style="padding: 6px;">Code</th>
                <th style="padding: 6px;">Description</th>
                <th style="padding: 6px;">Quantity</th>
                <th style="padding: 6px;">Unit</th>
                <th style="padding: 6px;">Rate</th>
                <th style="padding: 6px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($analysis['resources'] as $resource)
            @php
                $safeResCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $resource['secondary_code'] ?: 'RES_' . $resource['resource_id']);
                $resourceRow = $currentRow + 1;
                $currentRow++;
            @endphp
            <tr>
                <td style="padding: 4px;">{{ $resource['resource_group_name'] }}</td>
                <td style="padding: 4px;">{{ $resource['secondary_code'] }}</td>
                <td style="padding: 4px;">{{ $resource['name'] }}</td>
                <td style="padding: 4px; text-align: right;">{{ number_format($resource['quantity'], 4) }}</td>
                <td style="padding: 4px;">{{ $resource['unit'] }}</td>
                <td style="padding: 4px; text-align: right;">=res_{{ $safeResCode }}</td>
                <td style="padding: 4px; text-align: right;">=D{{ $resourceRow }}*F{{ $resourceRow }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br/>
    @php $currentRow++; @endphp
    @endif

    {{-- Subitems Section --}}
    @if(count($analysis['subitems']) > 0)
    <table>
        <thead>
            <tr style="background-color: #E2EFDA; font-weight: bold;">
                @php $currentRow++; @endphp
                <th style="padding: 6px;">Type</th>
                <th style="padding: 6px;">Item Code</th>
                <th style="padding: 6px;">Description</th>
                <th style="padding: 6px;">Quantity</th>
                <th style="padding: 6px;">Unit</th>
                <th style="padding: 6px;">Rate</th>
                <th style="padding: 6px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($analysis['subitems'] as $subitem)
            @php
                $safeSubCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $subitem['sub_item_code']);
                $subitemRow = $currentRow + 1;
                $currentRow++;
            @endphp
            <tr>
                <td style="padding: 4px;">Sub-item</td>
                <td style="padding: 4px;">{{ $subitem['sub_item_code'] }}</td>
                <td style="padding: 4px;">{{ $subitem['name'] }}</td>
                <td style="padding: 4px; text-align: right;">{{ number_format($subitem['quantity'], 4) }}</td>
                <td style="padding: 4px;">{{ $subitem['unit'] }}</td>
                <td style="padding: 4px; text-align: right;">=ra_{{ $safeSubCode }}</td>
                <td style="padding: 4px; text-align: right;">=D{{ $subitemRow }}*F{{ $subitemRow }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br/>
    @php $currentRow++; @endphp
    @endif

    {{-- Overheads Section --}}
    @if(count($analysis['overheads']) > 0)
    <table>
        <thead>
            <tr style="background-color: #FCE4D6; font-weight: bold;">
                @php $currentRow++; @endphp
                <th style="padding: 6px;" colspan="2">Overhead Description</th>
                <th style="padding: 6px;">Parameter</th>
                <th style="padding: 6px;" colspan="3"></th>
                <th style="padding: 6px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($analysis['overheads'] as $overhead)
            @php $currentRow++; @endphp
            <tr>
                <td style="padding: 4px;" colspan="2">{{ $overhead['description'] }}</td>
                <td style="padding: 4px; text-align: right;">{{ number_format($overhead['parameter'], 2) }}%</td>
                <td style="padding: 4px;" colspan="3"></td>
                <td style="padding: 4px; text-align: right;">{{ number_format($overhead['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br/>
    @php $currentRow++; @endphp
    @endif

    {{-- Summary Section --}}
    <table>
        <tr style="background-color: #F2F2F2;">
            @php $currentRow++; @endphp
            <td style="padding: 6px; font-weight: bold;">Summary</td>
            <td style="padding: 6px; text-align: right; font-weight: bold;">Amount (₹)</td>
        </tr>
        <tr>
            @php $currentRow++; @endphp
            <td style="padding: 4px;">Material Cost</td>
            <td style="padding: 4px; text-align: right;">{{ number_format($analysis['totals']['total_material'], 2) }}</td>
        </tr>
        <tr>
            @php $currentRow++; @endphp
            <td style="padding: 4px;">Labor Cost</td>
            <td style="padding: 4px; text-align: right;">{{ number_format($analysis['totals']['total_labor'], 2) }}</td>
        </tr>
        <tr>
            @php $currentRow++; @endphp
            <td style="padding: 4px;">Machine Cost</td>
            <td style="padding: 4px; text-align: right;">{{ number_format($analysis['totals']['total_machine'], 2) }}</td>
        </tr>
        <tr style="background-color: #F8F8F8;">
            @php $currentRow++; @endphp
            <td style="padding: 4px; font-weight: bold;">Total Resources</td>
            <td style="padding: 4px; text-align: right; font-weight: bold;">{{ number_format($analysis['totals']['resource_cost'], 2) }}</td>
        </tr>
        <tr>
            @php $currentRow++; @endphp
            <td style="padding: 4px;">Sub-item Cost</td>
            <td style="padding: 4px; text-align: right;">{{ number_format($analysis['totals']['subitem_cost'], 2) }}</td>
        </tr>
        <tr>
            @php $currentRow++; @endphp
            <td style="padding: 4px;">Overhead Cost</td>
            <td style="padding: 4px; text-align: right;">{{ number_format($analysis['totals']['overhead_cost'], 2) }}</td>
        </tr>
        <tr style="background-color: #E7E6E6;">
            @php $currentRow++; @endphp
            <td style="padding: 6px; font-weight: bold;">Grand Total</td>
            <td style="padding: 6px; text-align: right; font-weight: bold;">{{ number_format($analysis['totals']['grand_total'], 2) }}</td>
        </tr>
        <tr style="background-color: #4472C4; color: white;">
            @php
                $finalRateRow = $currentRow + 1;
                $currentRow++;
            @endphp
            <td style="padding: 8px; font-weight: bold; font-size: 12px;">Final Rate per {{ $item->unit->name ?? 'unit' }}</td>
            <td style="padding: 8px; text-align: right; font-weight: bold; font-size: 12px;">{{ number_format($analysis['totals']['final_rate'], 2) }}</td>
        </tr>
    </table>

    {{-- Spacing between items --}}
    <br/>
    <br/>
    @php $currentRow += 2; @endphp
@endforeach
