
<tr class="table-row">
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
        <div style="margin-left: {{ $level * 20 }}px;">
            {{ $item->item_number }}
        </div>
    </td>
    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $item->short_description }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->unit->name ?? '' }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">-</td>
</tr>

@if ($item->children->isNotEmpty())
    @foreach ($item->children as $child)
        @include('pages.sors._item', ['item' => $child, 'level' => $level + 1])
    @endforeach
@endif
