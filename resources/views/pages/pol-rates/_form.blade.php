<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-form.input label="Rate Date" name="rate_date" type="date" :value="old('rate_date', $polRate->rate_date ? $polRate->rate_date->format('Y-m-d') : '')" required />
    </div>
    <div>
        <x-form.input label="Diesel Rate" name="diesel_rate" :value="old('diesel_rate', $polRate->diesel_rate ?? '')" required />
    </div>
    <div>
        <x-form.input label="Mobile Oil Rate" name="mobile_oil_rate" :value="old('mobile_oil_rate', $polRate->mobile_oil_rate ?? '')" required />
    </div>
    <div>
        <x-form.input label="Laborer Charges" name="laborer_charges" :value="old('laborer_charges', $polRate->laborer_charges ?? '')" required />
    </div>
    <div>
        <x-form.input label="Hiring Charges" name="hiring_charges" :value="old('hiring_charges', $polRate->hiring_charges ?? '')" required />
    </div>
    <div>
        <x-form.input label="Overhead Charges" name="overhead_charges" :value="old('overhead_charges', $polRate->overhead_charges ?? '')" required />
    </div>
    <div>
        <x-form.input label="Mule Rate" name="mule_rate" :value="old('mule_rate', $polRate->mule_rate ?? '')" required />
    </div>
    <div>
        <x-form.input label="Valid From" name="valid_from" type="date" :value="old('valid_from', $polRate->valid_from ? $polRate->valid_from->format('Y-m-d') : '')" />
    </div>
    <div>
        <x-form.input label="Valid To" name="valid_to" type="date" :value="old('valid_to', $polRate->valid_to ? $polRate->valid_to->format('Y-m-d') : '')" />
    </div>
    <div>
        <x-form.input label="Published At" name="published_at" type="datetime-local" :value="old('published_at', $polRate->published_at ? $polRate->published_at->format('Y-m-d\\TH:i') : '')" />
    </div>
    <div>
        <x-form.checkbox label="Is Locked" name="is_locked" :checked="old('is_locked', $polRate->is_locked ?? false)" />
    </div>
</div>