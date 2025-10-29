<div class="space-y-6">
    <div>
        <x-form.input label="Mechanical Capacity" name="mechanical_capacity" :value="old('mechanical_capacity', $resourceCapacityRule->mechanical_capacity ?? '')" />
    </div>
    <div>
        <x-form.input label="Net Mechanical Capacity" name="net_mechanical_capacity" :value="old('net_mechanical_capacity', $resourceCapacityRule->net_mechanical_capacity ?? '')" />
    </div>
    <div>
        <x-form.input label="Manual Capacity" name="manual_capacity" :value="old('manual_capacity', $resourceCapacityRule->manual_capacity ?? '')" />
    </div>
    <div>
        <x-form.input label="Net Manual Capacity" name="net_manual_capacity" :value="old('net_manual_capacity', $resourceCapacityRule->net_manual_capacity ?? '')" />
    </div>
    <div>
        <x-form.input label="Mule Factor" name="mule_factor" :value="old('mule_factor', $resourceCapacityRule->mule_factor ?? '')" />
    </div>
    <div>
        <x-form.textarea label="Sample Resource" name="sample_resource">{{ old('sample_resource', $resourceCapacityRule->sample_resource ?? '') }}</x-form.textarea>
    </div>
</div>