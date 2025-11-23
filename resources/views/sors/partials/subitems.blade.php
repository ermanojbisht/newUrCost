@props(['readonly' => false])
            <!-- Sub-items Card -->
            <!--
                This section displays the list of sub-items associated with the item.
                Sub-items are other items from the SOR that are used as components of this item.
                It includes a table with columns for Item, Quantity, Rate, Amount, and Actions.
                The table body is populated dynamically via JavaScript (renderTables function).
            -->
            <div class="rounded-xl bg-white/40 dark:bg-gray-900/40 backdrop-blur-md border border-white/20 dark:border-white/10 shadow-xl ring-1 ring-black/5 transition-all duration-300 hover:shadow-2xl hover:bg-white/50 dark:hover:bg-gray-900/50 group/card">
                <div class="px-6 py-4 border-b border-white/20 dark:border-white/5 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-lg bg-gradient-to-br from-purple-500 to-violet-600 text-white shadow-lg shadow-purple-500/30 group-hover/card:scale-110 transition-transform duration-300">
                            {!! config('icons.material') !!}
                        </div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 tracking-wide">Sub-items</h2>
                    </div>
                    @if(!$readonly)
                    <button id="btnAddSubitem" class="py-2 px-4 rounded-lg bg-gradient-to-r from-purple-600 to-violet-600 hover:from-purple-500 hover:to-violet-500 text-white text-sm font-semibold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2">
                        {!! config('icons.add') !!}
                        <span>Add Sub-item</span>
                    </button>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/50 backdrop-blur-sm">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rate (₹)</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount (₹)</th>
                                @if(!$readonly)
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="subitems-table"
                            data-readonly="{{ $readonly ? 'true' : 'false' }}"
                            class="divide-y divide-gray-200/50 dark:divide-gray-700/50 bg-transparent">
                            <tr>
                                <td colspan="{{ $readonly ? '4' : '5' }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 animate-pulse">Loading sub-items...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
