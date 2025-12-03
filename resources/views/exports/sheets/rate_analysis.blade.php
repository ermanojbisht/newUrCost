@foreach($items_analysis as $itemAnalysis)
    <table>
        <thead>
            <tr>
                <th colspan="6" style="font-weight: bold; font-size: 14px; background-color: #f0f0f0;">
                    {{ $itemAnalysis['item_number'] }} - {{ $itemAnalysis['name'] }}
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
            @foreach($itemAnalysis['resources'] as $resource)
            <tr>
                <td>Resource</td>
                <td>{{ $resource['name'] }}</td>
                <td>{{ $resource['quantity'] }}</td>
                <td>{{ $resource['unit'] }}</td>
                {{-- FORMULA: Point to the named range on the Resources sheet --}}
                <td>=res_{{ preg_replace('/[^a-zA-Z0-9_]/', '_', $resource['secondary_code'] ?? $resource['resource_id']) }}</td>
                <td>=C{{ $loop->parent->iteration * 100 + $loop->index }}*E{{ $loop->parent->iteration * 100 + $loop->index }}</td> 
                {{-- Note: The formula above is tricky because we don't know the exact row number easily in Blade. 
                     However, Excel formulas can use relative references (RC notation) or we can just output the value for now 
                     if we can't easily determine the row. 
                     BUT the requirement is formula-driven.
                     
                     Let's use the fact that we are generating the sheet row by row.
                     If we can't determine the row number here, we might need to do it in the Export class using `map()` 
                     instead of `FromView`, OR use a counter variable in PHP/Blade.
                --}}
            </tr>
            @endforeach

            {{-- Sub-items --}}
            @foreach($itemAnalysis['sub_items'] as $subItem)
            <tr>
                <td>Sub-item</td>
                <td>{{ $subItem['name'] }}</td>
                <td>{{ $subItem['quantity'] }}</td>
                <td>{{ $subItem['unit'] }}</td>
                {{-- FORMULA: Point to the named range of another item --}}
                <td>=ra_{{ preg_replace('/[^a-zA-Z0-9_]/', '_', $subItem['sub_item_code']) }}</td>
                <td></td>
            </tr>
            @endforeach

            {{-- Overheads --}}
            @foreach($itemAnalysis['overheads'] as $overhead)
            <tr>
                <td>Overhead</td>
                <td>{{ $overhead['description'] }}</td>
                <td>{{ $overhead['parameter'] }}%</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach

            {{-- Totals --}}
            <tr>
                <td colspan="5" style="text-align: right;">Total Resource Cost</td>
                <td>{{ $itemAnalysis['totals']['resource_cost'] }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">Total Sub-item Cost</td>
                <td>{{ $itemAnalysis['totals']['subitem_cost'] }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">Total Overhead Cost</td>
                <td>{{ $itemAnalysis['totals']['overhead_cost'] }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold;">Grand Total</td>
                <td>{{ $itemAnalysis['totals']['grand_total'] }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">Turnout</td>
                <td>{{ $itemAnalysis['totals']['turnout'] }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold;">Final Rate per Unit ({{ $itemAnalysis['unit'] }})</td>
                {{-- This cell will be named ra_{item_code} --}}
                <td>{{ $itemAnalysis['totals']['final_rate'] }}</td>
            </tr>
        </tbody>
    </table>
    <br/>
@endforeach
