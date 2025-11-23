@props(['item'])

<div class="mb-6 rounded-xl bg-white/40 dark:bg-gray-900/40 backdrop-blur-md border border-white/20 dark:border-white/10 shadow-xl ring-1 ring-black/5 transition-all duration-300 hover:shadow-2xl hover:bg-white/50 dark:hover:bg-gray-900/50 group/card">
    <div class="px-6 py-4 border-b border-white/20 dark:border-white/5 flex items-center space-x-3">
        <div class="p-2 rounded-lg bg-gradient-to-br from-pink-500 to-rose-600 text-white shadow-lg shadow-pink-500/30 group-hover/card:scale-110 transition-transform duration-300">
            {!! config('icons.document') !!}
        </div>
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 tracking-wide">Item Details</h2>
    </div>
    
    <div class="p-6 space-y-4">
        <!-- Item Name -->
        <div>
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Description</h3>
            <p class="text-gray-900 dark:text-white text-base leading-relaxed whitespace-pre-wrap">{{ $item->name }}</p>
        </div>

        <!-- Specifications Grid -->
        @if($item->specification_code || $item->specification_page_number)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white/30 dark:bg-gray-800/30 rounded-lg p-4 backdrop-blur-sm">
                @if($item->specification_code)
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Specification Code</h3>
                        <p class="text-gray-900 dark:text-white font-mono text-sm">{{ $item->specification_code }}</p>
                    </div>
                @endif
                @if($item->specification_page_number)
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Page Number</h3>
                        <p class="text-gray-900 dark:text-white font-mono text-sm">{{ $item->specification_page_number }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Assumptions -->
        @if($item->assumptions)
            <div class="bg-blue-50/50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-100/50 dark:border-blue-800/30">
                <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Assumptions
                </h3>
                <p class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">{{ $item->assumptions }}</p>
            </div>
        @endif

        <!-- Footnotes -->
        @if($item->footnotes)
            <div class="bg-yellow-50/50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-100/50 dark:border-yellow-800/30">
                <h3 class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase tracking-wider mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Footnotes
                </h3>
                <p class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">{{ $item->footnotes }}</p>
            </div>
        @endif
    </div>
</div>
