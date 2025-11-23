@props(['rateCards', 'rateCardId', 'effectiveDate'])

<div x-data="{ expanded: false }" 
     class="mb-6 rounded-xl bg-white/40 dark:bg-gray-900/40 backdrop-blur-md border border-white/20 dark:border-white/10 shadow-xl ring-1 ring-black/5 transition-all duration-300 hover:shadow-2xl hover:bg-white/50 dark:hover:bg-gray-900/50 group/card">
    
    <!-- Header / Toggle -->
    <div @click="expanded = !expanded" 
         class="px-5 py-3 flex justify-between items-center cursor-pointer rounded-xl transition-colors">
        <div class="flex items-center space-x-3">
            <div class="p-2 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 group-hover/card:scale-110 transition-transform duration-300">
                {!! config('icons.filter') !!}
            </div>
            <div class="flex flex-col">
                <h2 class="text-base font-bold text-gray-800 dark:text-gray-100 tracking-wide">Filter Rates</h2>
                <!-- Summary when collapsed -->
                <div x-show="!expanded" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center space-x-2 mt-0.5">
                    <span class="text-indigo-600 dark:text-indigo-400">
                        {{ $rateCards->find($rateCardId)->name ?? 'Default' }}
                    </span>
                    <span class="w-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></span>
                    <span>{{ $effectiveDate }}</span>
                </div>
            </div>
        </div>
        <button class="text-gray-400 dark:text-gray-500 p-1.5 rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
            <svg x-bind:class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </div>

    <!-- Collapsible Content -->
    <div x-show="expanded" 
         x-collapse 
         class="border-t border-white/20 dark:border-white/5">
        <div class="px-5 py-5">
            <form action="{{ url()->current() }}" method="GET">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <!-- Rate Card Select -->
                    <div class="flex-1 w-full">
                        <label for="rate_card_id" class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5 ml-1">Rate Card</label>
                        <div class="relative group">
                            <select name="rate_card_id" id="rate_card_id" 
                                    class="w-full pl-3 pr-8 py-2 text-sm rounded-lg bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-gray-900 dark:text-white placeholder-gray-500 backdrop-blur-sm transition-all duration-200 shadow-sm group-hover:bg-white/80 dark:group-hover:bg-gray-800/80">
                                @foreach($rateCards as $card)
                                    <option value="{{ $card->id }}" {{ $rateCardId == $card->id ? 'selected' : '' }}>
                                        {{ $card->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Effective Date Input -->
                    <div class="flex-1 w-full">
                        <label for="effective_date" class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5 ml-1">Effective Date</label>
                        <div class="relative group">
                            <input type="text" name="effective_date" id="effective_date" value="{{ $effectiveDate }}"
                                   class="w-full pl-3 pr-9 py-2 text-sm rounded-lg bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-gray-900 dark:text-white placeholder-gray-500 backdrop-blur-sm transition-all duration-200 shadow-sm group-hover:bg-white/80 dark:group-hover:bg-gray-800/80"
                                   placeholder="Select Date">
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400 group-hover:text-indigo-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full md:w-auto py-2 px-6 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2">
                            <span>Apply</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#effective_date", {
            dateFormat: "Y-m-d",
            allowInput: true,
            static: true // Ensures calendar doesn't get cut off in overflow containers
        });
    </script>
@endpush
