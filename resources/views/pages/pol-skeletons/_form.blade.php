<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-form.input label="Date" name="date" type="date" :value="old('date', $polSkeleton->date ? $polSkeleton->date->format('Y-m-d') : '')" required />
    </div>
    <div>
        <x-form.input label="Diesel Mileage" name="diesel_mileage" :value="old('diesel_mileage', $polSkeleton->diesel_mileage ?? '')" required />
    </div>
    <div>
        <x-form.input label="Mobile Oil Mileage" name="mobile_oil_mileage" :value="old('mobile_oil_mileage', $polSkeleton->mobile_oil_mileage ?? '')" required />
    </div>
    <div>
        <x-form.input label="Number of Laborers" name="number_of_laborers" :value="old('number_of_laborers', $polSkeleton->number_of_laborers ?? '')" required />
    </div>
    <div>
        <x-form.input label="Valid From" name="valid_from" type="date" :value="old('valid_from', $polSkeleton->valid_from ? $polSkeleton->valid_from->format('Y-m-d') : '')" />
    </div>
    <div>
        <x-form.input label="Valid To" name="valid_to" type="date" :value="old('valid_to', $polSkeleton->valid_to ? $polSkeleton->valid_to->format('Y-m-d') : '')" />
    </div>
    <div>
        <x-form.checkbox label="Is Locked" name="is_locked" :checked="old('is_locked', $polSkeleton->is_locked ?? false)" />
    </div>
</div>