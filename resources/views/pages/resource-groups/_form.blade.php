<div class="space-y-6">
    <div>
        <x-form.input label="Name" name="name" :value="old('name', $resourceGroup->name ?? '')" required />
    </div>
</div>