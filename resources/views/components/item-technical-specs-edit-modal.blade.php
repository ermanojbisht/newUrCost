@props(['item'])

<div x-data="itemSpecsEditor(@js($item->technicalSpec))" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-show="isOpen" x-transition>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="closeModal">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Edit Technical Specifications
                        </h3>
                        <div class="mt-4 space-y-6 max-h-[70vh] overflow-y-auto pr-2">
                            
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

                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" @click="save" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Save Changes
                </button>
                <button type="button" @click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('itemSpecsEditor', (initialData) => ({
            isOpen: false,
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
                
                window.addEventListener('open-specs-editor', () => {
                    this.isOpen = true;
                });
            },

            closeModal() {
                this.isOpen = false;
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
                        location.reload();
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
