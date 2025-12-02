@extends('layouts.layout001.app')

@section('title', 'SOR Export Reports')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="sorExport()">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Generate SOR Report</h1>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="sor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select SOR</label>
                    <select id="sor_id" x-model="selectedSor" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select SOR</option>
                        @foreach($sors as $sor)
                            <option value="{{ $sor->id }}">{{ $sor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rate_card_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Rate Card</label>
                    <select id="rate_card_id" x-model="selectedRateCard" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Rate Card</option>
                        @foreach($rateCards as $rateCard)
                            <option value="{{ $rateCard->id }}">{{ $rateCard->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="format" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Format</label>
                    <select id="format" x-model="selectedFormat" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="xlsx">Excel (.xlsx)</option>
                        <option value="pdf">PDF (.pdf)</option>
                        <option value="csv">CSV (.csv)</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button @click="generateReport('standard')" :disabled="loading || !selectedSor || !selectedRateCard" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="loading" class="mr-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    Generate Report
                </button>
                <button @click="generateReport('detailed')" :disabled="loading || !selectedSor || !selectedRateCard" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="loading" class="mr-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    Generate Detailed Report
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Generated Reports</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SOR / Rate Card</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="file in files" :key="file.id">
                        <tr :class="{'bg-green-50 dark:bg-green-900': file.id === newFileId}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="formatDate(file.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="file.title"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <span x-text="file.sor ? file.sor.name : 'N/A'"></span> / 
                                <span x-text="file.rate_card ? file.rate_card.name : 'N/A'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                      :class="file.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                      x-text="file.status"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a :href="file.url" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-4">Download</a>
                                <button @click="deleteFile(file.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="files.length === 0">
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No reports generated yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function sorExport() {
        return {
            selectedSor: '',
            selectedRateCard: '',
            selectedFormat: 'xlsx',
            loading: false,
            files: [],
            newFileId: null,

            init() {
                this.fetchFiles();
            },

            fetchFiles() {
                fetch('{{ route("sor.export.list") }}')
                    .then(response => response.json())
                    .then(data => {
                        this.files = data;
                    });
            },

            generateReport(type = 'standard') {
                if (!this.selectedSor || !this.selectedRateCard) return;

                this.loading = true;
                this.newFileId = null;

                const url = `{{ url('/export/sor') }}/${this.selectedSor}/rate-card/${this.selectedRateCard}/format/${this.selectedFormat}?type=${type}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        this.loading = false;
                        if (data.file) {
                            this.newFileId = data.file.id;
                            this.fetchFiles();
                            // Scroll to bottom or new file
                            setTimeout(() => {
                                const newRow = document.querySelector('.bg-green-50');
                                if(newRow) newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }, 500);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.loading = false;
                        alert('Failed to generate report.');
                    });
            },

            deleteFile(id) {
                if (!confirm('Are you sure you want to delete this file?')) return;

                fetch(`{{ url('/export/delete') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        this.fetchFiles();
                    } else {
                        alert('Failed to delete file.');
                    }
                });
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
            }
        }
    }
</script>
@endsection
