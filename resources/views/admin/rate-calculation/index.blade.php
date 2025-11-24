@extends('layouts.layout001.app')

@section('title', 'Rate Calculation')

@section('page-header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                {!! config('icons.calculator') !!}
                <span class="ml-2">Rate Calculation</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Calculate item rates based on dependencies and rate cards.
            </p>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Calculation Card -->
        <div class="bg-white/60 dark:bg-gray-900/30 backdrop-blur-xl border border-gray-200/50 dark:border-white/10 shadow-lg dark:shadow-2xl rounded-2xl p-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Run Calculation</h2>
            
            <form id="calculation-form" class="space-y-6">
                <div>
                    <label for="rate_card_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Rate Card</label>
                    <select id="rate_card_id" name="rate_card_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        @foreach($rateCards as $card)
                            <option value="{{ $card->id }}">{{ $card->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select SOR (Optional)</label>
                    <select id="sor_id" name="sor_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        <option value="">All SORs</option>
                        @foreach($sors as $sor)
                            <option value="{{ $sor->id }}">{{ $sor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center">
                    <input id="subitems_only" name="subitems_only" type="checkbox" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-800 dark:border-gray-600">
                    <label for="subitems_only" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Calculate Sub-items Only (Dependencies)
                    </label>
                </div>

                <div>
                    <label for="valid_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valid From</label>
                    <input type="date" id="valid_from" name="valid_from" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>

                <div class="flex items-center justify-between pt-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        This process may take a while depending on the number of items.
                    </div>
                    <button type="submit" id="btn-calculate" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btn-text">Start Calculation</span>
                        <svg id="btn-spinner" class="hidden ml-2 -mr-1 w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Section -->
        <div id="results-section" class="hidden mt-8 bg-white/60 dark:bg-gray-900/30 backdrop-blur-xl border border-gray-200/50 dark:border-white/10 shadow-lg dark:shadow-2xl rounded-2xl p-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Calculation Results</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-800">
                    <div class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Items</div>
                    <div id="res-total" class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1">-</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-100 dark:border-green-800">
                    <div class="text-sm font-medium text-green-600 dark:text-green-400">Processed</div>
                    <div id="res-processed" class="text-2xl font-bold text-green-900 dark:text-green-100 mt-1">-</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-4 border border-purple-100 dark:border-purple-800">
                    <div class="text-sm font-medium text-purple-600 dark:text-purple-400">Duration</div>
                    <div id="res-duration" class="text-2xl font-bold text-purple-900 dark:text-purple-100 mt-1">-</div>
                </div>
            </div>

            <div id="error-container" class="hidden">
                <h4 class="text-sm font-medium text-red-600 dark:text-red-400 mb-2">Errors</h4>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-100 dark:border-red-800 max-h-60 overflow-y-auto">
                    <ul id="error-list" class="list-disc list-inside text-sm text-red-800 dark:text-red-200 space-y-1"></ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('calculation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btn-calculate');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const resultsSection = document.getElementById('results-section');
        const errorContainer = document.getElementById('error-container');
        
        // Reset UI
        btn.disabled = true;
        btnText.textContent = 'Calculating...';
        btnSpinner.classList.remove('hidden');
        resultsSection.classList.add('hidden');
        errorContainer.classList.add('hidden');

        const formData = new FormData(this);

        fetch('{{ route("admin.rate-calculation.calculate") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Update Results
            document.getElementById('res-total').textContent = data.total;
            document.getElementById('res-processed').textContent = data.processed;
            document.getElementById('res-duration').textContent = parseFloat(data.duration).toFixed(2) + 's';
            
            if (data.errors && data.errors.length > 0) {
                const errorList = document.getElementById('error-list');
                errorList.innerHTML = '';
                data.errors.forEach(err => {
                    const li = document.createElement('li');
                    li.textContent = err;
                    errorList.appendChild(li);
                });
                errorContainer.classList.remove('hidden');
            }

            resultsSection.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during calculation.');
        })
        .finally(() => {
            btn.disabled = false;
            btnText.textContent = 'Start Calculation';
            btnSpinner.classList.add('hidden');
        });
    });
</script>
@endpush
