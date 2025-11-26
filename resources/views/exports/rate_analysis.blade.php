@php
    $currentRow = 0;
@endphp

@foreach($items as $item)
    @php
        $safeItemCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $item->item_code);
        // Header Row
        $currentRow++; 
        // Column Headers Row
        $currentRow++;
        $startRow = $currentRow + 1;
    @endphp
    <table>
        <thead>
            <tr>
                <th colspan="6" style="font-weight: bold; background-color: #cccccc;">
                    Item: {{ $item->item_code }} - {{ $item->description }}
                </th>
            </tr>
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            {{-- Resources --}}
            @foreach($item->skeletons as $skeleton)
                @php
                    $currentRow++;
                    $code = $skeleton->resource->secondary_code ?: 'RES_' . $skeleton->resource->id;
                    $safeResCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $code);
                @endphp
                <tr>
                    <td>Resource</td>
                    <td>{{ $skeleton->resource->name }}</td>
                    <td>{{ $skeleton->quantity }}</td>
                    <td>{{ $skeleton->resource->unit->name ?? '' }}</td>
                    <td>=res_{{ $safeResCode }}</td>
                    <td>=C{{ $currentRow }}*E{{ $currentRow }}</td> 
                </tr>
            @endforeach

            {{-- Sub-items --}}
            @foreach($item->subitems as $subItemRelation)
                @php
                    $currentRow++;
                    $childCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $subItemRelation->subItem->item_code);
                @endphp
                <tr>
                    <td>Sub-item</td>
                    <td>{{ $subItemRelation->subItem->item_code }}</td>
                    <td>{{ $subItemRelation->quantity }}</td>
                    <td>{{ $subItemRelation->subItem->unit->name ?? '' }}</td>
                    <td>=ra_{{ $childCode }}</td>
                    <td>=C{{ $currentRow }}*E{{ $currentRow }}</td>
                </tr>
            @endforeach

            @php
                $endDirectRow = $currentRow;
            @endphp

            {{-- Overheads --}}
            @foreach($item->overheads as $overhead)
                @php
                    $currentRow++;
                @endphp
                <tr>
                    <td>Overhead</td>
                    <td>{{ $overhead->overhead->description ?? 'OH' }}</td>
                    <td>{{ $overhead->parameter }}</td>
                    <td>%</td>
                    <td></td>
                    {{-- Simplified Overhead Formula: Parameter * Sum of Direct Costs --}}
                    {{-- This assumes overhead is on total direct cost. Real logic is complex. --}}
                    <td>=C{{ $currentRow }}*SUM(F{{ $startRow }}:F{{ $endDirectRow }})</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                $currentRow++;
            @endphp
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold;">Rate per Unit</td>
                <td style="font-weight: bold;">
                    {{-- Sum everything from start to before this row --}}
                    {{-- But we need to divide by turnout if applicable --}}
                    =SUM(F{{ $startRow }}:F{{ $currentRow - 1 }})
                </td>
            </tr>
        </tfoot>
    </table>
    <br/>
    @php
        $currentRow++; // For the break line
    @endphp
@endforeach
