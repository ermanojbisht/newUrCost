@props(['item', 'editable' => false])

<div class="mt-8" id="technical-specs-container">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
            <span class="mr-2">📋</span> Technical Specifications
        </h2>
        @if($editable)
        <div class="flex space-x-2">
            @if($item->technicalSpec)
                <a href="{{ route('items.edit-specs', $item->id) }}" class="btn-secondary text-sm flex items-center">
                    <span class="mr-2">{!! config('icons.edit') !!}</span> Edit
                </a>
            @endif
            @if(!$item->technicalSpec)
                <button id="generate-specs-btn" class="btn-primary text-sm flex items-center">
                    <span class="mr-2">✨</span> Generate with AI
                </button>
            @endif
        </div>
        @endif
    </div>



    @if($item->technicalSpec)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Introduction -->
            <div class="col-span-1 md:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Introduction</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $item->technicalSpec->introduction }}</p>
            </div>

            <!-- Specifications -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center">
                    <span class="mr-2">🔧</span> Specifications
                </h3>
                <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300">
                    @foreach($item->technicalSpec->specifications ?? [] as $spec)
                        <li>{{ $spec }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Tests & Frequency -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center">
                    <span class="mr-2">🧪</span> Tests & Frequency
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2">Test</th>
                                <th class="px-4 py-2">Frequency</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->technicalSpec->tests_frequency ?? [] as $tf)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $tf['test'] ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $tf['frequency'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Do's and Don'ts -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center">
                    <span class="mr-2">✅</span> Do's & Don'ts
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-green-600 mb-2">Do's</h4>
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            @foreach($item->technicalSpec->dos_donts['dos'] ?? [] as $do)
                                <li>{{ $do }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-red-600 mb-2">Don'ts</h4>
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            @foreach($item->technicalSpec->dos_donts['donts'] ?? [] as $dont)
                                <li>{{ $dont }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Execution Sequence -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center">
                    <span class="mr-2">🔢</span> Execution Sequence
                </h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                    @foreach($item->technicalSpec->execution_sequence ?? [] as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ol>
            </div>

            <!-- Precautionary Measures -->
            <div class="col-span-1 md:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white flex items-center">
                    <span class="mr-2">🛡️</span> Precautionary Measures
                </h3>
                <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300">
                    @foreach($item->technicalSpec->precautionary_measures ?? [] as $measure)
                        <li>{{ $measure }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Reference Links -->
            @if(!empty($item->technicalSpec->reference_links))
            <div class="col-span-1 md:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white flex items-center">
                    <span class="mr-2">🔗</span> Reference Links
                </h3>
                <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300">
                    @foreach($item->technicalSpec->reference_links as $link)
                        <li>
                            <a href="{{ $link['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline">
                                {{ $link['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    @else
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400 mb-4">No technical specifications available for this item.</p>
            @if($editable)
            <div class="flex justify-center space-x-4">
                <button id="generate-specs-btn-empty" class="btn-primary">
                    <span class="mr-2">✨</span> Generate with AI
                </button>
                <a href="{{ route('items.edit-specs', $item->id) }}" class="btn-secondary">
                    <span class="mr-2">{!! config('icons.plus') !!}</span> Create Manually
                </a>
            </div>
            @endif
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('#generate-specs-btn, #generate-specs-btn-empty').forEach(btn => {
        btn.addEventListener('click', function() {
            if(confirm('Generate technical specifications using AI? This may take a few seconds.')) {
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = 'Generating...';
                
                fetch("{{ route('items.generate-specs', $item->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                        this.disabled = false;
                        this.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
            }
        });
    });
</script>
