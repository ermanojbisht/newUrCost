<div class="space-y-6">
    <div>
        <x-form.input label="Name" name="name" :value="old('name', $unit->name ?? '')" required />
    </div>
    <div>
        <x-form.input label="Code" name="code" :value="old('code', $unit->code ?? '')" required />
    </div>
    <div>
        <x-form.input label="Alias" name="alias" :value="old('alias', $unit->alias ?? '')" />
    </div>
    <div>
        @php
            $options = $unitGroups->sortBy('name')->pluck('name', 'id');
        @endphp
        <x-form.select label="Unit Group" name="unit_group_id" :options="$options" :selected="old('unit_group_id', $unit->unit_group_id ?? '')" class="select2" />
    </div>
    <div>
        <x-form.input label="Conversion Factor" name="conversion_factor" :value="old('conversion_factor', $unit->conversion_factor ?? '')" />
    </div>
</div>
