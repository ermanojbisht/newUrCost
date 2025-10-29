<div class="space-y-6">
    <div>
        <x-form.input label="Name" name="name" :value="old('name', $unitGroup->name ?? '')" required />
    </div>
    <div>
        @php
            $options = $units->sortBy('name')->pluck('name', 'id');
        @endphp
        <x-form.select label="Base Unit" name="base_unit_id" :options="$options" :selected="old('base_unit_id', $unitGroup->base_unit_id ?? '')" class="select2" />
    </div>
</div>