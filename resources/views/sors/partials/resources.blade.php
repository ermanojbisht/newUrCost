            <!-- Resources Card -->
            <!--
                This section displays the list of resources associated with the item.
                It includes a table with columns for Index, Name, Quantity, Rate, Amount, and Actions.
                The table body is populated dynamically via JavaScript (renderTables function).
            -->
            <div class="rounded-xl bg-white/40 dark:bg-gray-900/40 backdrop-blur-md border border-white/20 dark:border-white/10 shadow-xl ring-1 ring-black/5 transition-all duration-300 hover:shadow-2xl hover:bg-white/50 dark:hover:bg-gray-900/50 group/card">
                <div class="px-6 py-4 border-b border-white/20 dark:border-white/5 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/30 group-hover/card:scale-110 transition-transform duration-300">
                            {!! config('icons.list') !!}
                        </div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 tracking-wide">Resources</h2>
                    </div>
                    <button id="btnAddResource" class="py-2 px-4 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-semibold shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2">
                        {!! config('icons.add') !!}
                        <span>Add Resource</span>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/50 backdrop-blur-sm">
                            <tr>
                                <th class="w-8 px-4 py-3"></th>
                                <th class="w-10 px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                <th class="w-24 px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty</th>
                                <th class="w-24 px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rate (₹)</th>
                                <th class="w-24 px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount (₹)</th>
                                <th class="w-20 px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody id="resources-body"
                            class="divide-y divide-gray-200/50 dark:divide-gray-700/50 bg-transparent">
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 animate-pulse">Loading resources...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
