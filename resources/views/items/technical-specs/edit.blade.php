@extends('layouts.layout001.app')

@section('title', 'Edit Technical Specifications - ' . $item->item_number)

@section('content')
<div class="container mx-auto px-4 py-8" x-data="itemSpecsEditor(@js($item->technicalSpec))">
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Edit Technical Specifications
            </h1>
            <a href="{{ route('sors.items.skeleton', [$item->sor_id, $item->id]) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                {!! config('icons.close') ?? 'X' !!}
            </a>
        </div>

        <div class="p-6">
            <!-- JSON Import Section -->
            <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-center mb-2 cursor-pointer" @click="showJsonImport = !showJsonImport">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white flex items-center">
                        <span class="mr-2">{!! config('icons.document') !!}</span> Import from JSON
                    </h4>
                    <span class="text-xs text-blue-600 hover:text-blue-800" x-text="showJsonImport ? 'Hide' : 'Show'"></span>
                </div>
                <div x-show="showJsonImport" x-transition>
                    <div class="mb-2 flex justify-end">
                        <button type="button" @click="copyAiPrompt" class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center bg-indigo-50 dark:bg-indigo-900 px-2 py-1 rounded border border-indigo-200 dark:border-indigo-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            Copy AI Prompt
                        </button>
                    </div>
                    <textarea x-model="jsonInput" rows="5" placeholder="Paste your JSON here..." class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs font-mono"></textarea>
                    <div class="mt-2 flex justify-end">
                        <button type="button" @click="applyJson" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-3 py-1 bg-indigo-600 text-xs font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Apply JSON
                        </button>
                    </div>
                </div>
            </div>

            <form @submit.prevent="save" class="space-y-6">
                
                <!-- Introduction -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Introduction</label>
                    <textarea x-model="formData.introduction" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>

                <!-- Specifications -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specifications</label>
                    <template x-for="(spec, index) in formData.specifications" :key="index">
                        <div class="flex gap-2 mb-2">
                            <input type="text" x-model="formData.specifications[index]" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <button type="button" @click="removeSpecification(index)" class="text-red-600 hover:text-red-800">
                                {!! config('icons.delete') !!}
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="addSpecification" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        {!! config('icons.plus') !!} Add Specification
                    </button>
                </div>

                <!-- Tests & Frequency -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tests & Frequency</label>
                    <template x-for="(tf, index) in formData.tests_frequency" :key="index">
                        <div class="flex gap-2 mb-2">
                            <input type="text" x-model="formData.tests_frequency[index].test" placeholder="Test Name" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <input type="text" x-model="formData.tests_frequency[index].frequency" placeholder="Frequency" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <button type="button" @click="removeTest(index)" class="text-red-600 hover:text-red-800">
                                {!! config('icons.delete') !!}
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="addTest" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        {!! config('icons.plus') !!} Add Test
                    </button>
                </div>

                <!-- Do's -->
                <div>
                    <label class="block text-sm font-medium text-green-700 dark:text-green-400 mb-2">Do's</label>
                    <template x-for="(item, index) in formData.dos_donts.dos" :key="index">
                        <div class="flex gap-2 mb-2">
                            <input type="text" x-model="formData.dos_donts.dos[index]" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <button type="button" @click="removeDo(index)" class="text-red-600 hover:text-red-800">
                                {!! config('icons.delete') !!}
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="addDo" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        {!! config('icons.plus') !!} Add Do
                    </button>
                </div>

                <!-- Don'ts -->
                <div>
                    <label class="block text-sm font-medium text-red-700 dark:text-red-400 mb-2">Don'ts</label>
                    <template x-for="(item, index) in formData.dos_donts.donts" :key="index">
                        <div class="flex gap-2 mb-2">
                            <input type="text" x-model="formData.dos_donts.donts[index]" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <button type="button" @click="removeDont(index)" class="text-red-600 hover:text-red-800">
                                {!! config('icons.delete') !!}
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="addDont" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        {!! config('icons.plus') !!} Add Don't
                    </button>
                </div>

                <!-- Execution Sequence -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Execution Sequence</label>
                    <template x-for="(step, index) in formData.execution_sequence" :key="index">
                        <div class="flex gap-2 mb-2">
                            <span class="text-gray-500 py-2" x-text="index + 1 + '.'"></span>
                            <input type="text" x-model="formData.execution_sequence[index]" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <button type="button" @click="removeExecutionStep(index)" class="text-red-600 hover:text-red-800">
                                {!! config('icons.delete') !!}
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="addExecutionStep" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        {!! config('icons.plus') !!} Add Step
                    </button>
                </div>

                <!-- Precautionary Measures -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Precautionary Measures</label>
                    <template x-for="(measure, index) in formData.precautionary_measures" :key="index">
                        <div class="flex gap-2 mb-2">
                            <input type="text" x-model="formData.precautionary_measures[index]" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <button type="button" @click="removePrecautionaryMeasure(index)" class="text-red-600 hover:text-red-800">
                                {!! config('icons.delete') !!}
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="addPrecautionaryMeasure" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        {!! config('icons.plus') !!} Add Measure
                    </button>
                </div>

                <!-- Reference Links -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reference Links</label>
                    <template x-for="(link, index) in formData.reference_links" :key="index">
                        <div class="flex gap-2 mb-2">
                            <input type="text" x-model="formData.reference_links[index].title" placeholder="Title" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <input type="text" x-model="formData.reference_links[index].url" placeholder="URL" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <button type="button" @click="removeReferenceLink(index)" class="text-red-600 hover:text-red-800">
                                {!! config('icons.delete') !!}
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="addReferenceLink" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        {!! config('icons.plus') !!} Add Link
                    </button>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('sors.items.skeleton', [$item->sor_id, $item->id]) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('itemSpecsEditor', (initialData) => ({
            showJsonImport: false,
            jsonInput: '',
            formData: {
                introduction: '',
                specifications: [],
                tests_frequency: [],
                dos_donts: { dos: [], donts: [] },
                execution_sequence: [],
                precautionary_measures: [],
                reference_links: []
            },

            init() {
                if (initialData) {
                    this.formData = JSON.parse(JSON.stringify(initialData));
                    // Ensure structure exists even if null in DB
                    this.formData.specifications = this.formData.specifications || [];
                    this.formData.tests_frequency = this.formData.tests_frequency || [];
                    this.formData.dos_donts = this.formData.dos_donts || { dos: [], donts: [] };
                    this.formData.execution_sequence = this.formData.execution_sequence || [];
                    this.formData.precautionary_measures = this.formData.precautionary_measures || [];
                    this.formData.reference_links = this.formData.reference_links || [];
                }
            },

            copyAiPrompt() {
                const prompt = @js($aiPrompt);
                
                navigator.clipboard.writeText(prompt).then(() => {
                    alert('AI Prompt copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    alert('Failed to copy prompt. Please check console.');
                });
            },

            applyJson() {
                try {
                    const data = JSON.parse(this.jsonInput);
                    
                    // Basic validation/mapping
                    if(data.introduction) this.formData.introduction = data.introduction;
                    if(Array.isArray(data.specifications)) this.formData.specifications = data.specifications;
                    if(Array.isArray(data.tests_frequency)) this.formData.tests_frequency = data.tests_frequency;
                    if(data.dos_donts) {
                        if(Array.isArray(data.dos_donts.dos)) this.formData.dos_donts.dos = data.dos_donts.dos;
                        if(Array.isArray(data.dos_donts.donts)) this.formData.dos_donts.donts = data.dos_donts.donts;
                    }
                    if(Array.isArray(data.execution_sequence)) this.formData.execution_sequence = data.execution_sequence;
                    if(Array.isArray(data.precautionary_measures)) this.formData.precautionary_measures = data.precautionary_measures;
                    if(Array.isArray(data.reference_links)) this.formData.reference_links = data.reference_links;

                    alert('JSON applied successfully!');
                    this.showJsonImport = false;
                    this.jsonInput = '';

                } catch (e) {
                    alert('Invalid JSON: ' + e.message);
                }
            },

            addSpecification() { this.formData.specifications.push(''); },
            removeSpecification(index) { this.formData.specifications.splice(index, 1); },

            addTest() { this.formData.tests_frequency.push({ test: '', frequency: '' }); },
            removeTest(index) { this.formData.tests_frequency.splice(index, 1); },

            addDo() { this.formData.dos_donts.dos.push(''); },
            removeDo(index) { this.formData.dos_donts.dos.splice(index, 1); },

            addDont() { this.formData.dos_donts.donts.push(''); },
            removeDont(index) { this.formData.dos_donts.donts.splice(index, 1); },

            addExecutionStep() { this.formData.execution_sequence.push(''); },
            removeExecutionStep(index) { this.formData.execution_sequence.splice(index, 1); },

            addPrecautionaryMeasure() { this.formData.precautionary_measures.push(''); },
            removePrecautionaryMeasure(index) { this.formData.precautionary_measures.splice(index, 1); },

            addReferenceLink() { this.formData.reference_links.push({ title: '', url: '' }); },
            removeReferenceLink(index) { this.formData.reference_links.splice(index, 1); },

            save() {
                fetch("{{ route('items.update-specs', $item->id) }}", {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = "{{ route('sors.items.skeleton', [$item->sor_id, $item->id]) }}";
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
            }
        }));
    });
</script>
@endsection
