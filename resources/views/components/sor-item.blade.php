@props(['item'])

<li class="border-l-2 border-gray-200 dark:border-gray-700 pl-4 py-1" x-data="{ open: false }">
    <div class="flex items-center space-x-2">
        @if($item->children->count())
            <button @click="open = !open" class="text-gray-500 dark:text-gray-400 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-90': open }">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        @else
            <div class="w-4 h-4"></div>
        @endif
        <span class="text-gray-800 dark:text-gray-200 font-medium {{ $item->item_type === 'chapter' ? 'font-bold text-blue-500 dark:text-blue-400' : '' }}">
            @if($item->item_code)
                {{ $item->item_code }} - 
            @endif
            {{ $item->name }}
        </span>
    </div>
    <ul class="ml-6" x-show="open" x-transition>
        @foreach ($item->children as $child)
            <x-sor-item :item="$child" />
        @endforeach
    </ul>
</li>
