<div class="space-y-6">
    <div>
        <x-form.input label="Name" name="name" :value="old('name', $sor->name ?? '')" required />
    </div>
    <div>
        <x-form.input label="Short Name" name="short_name" :value="old('short_name', $sor->short_name ?? '')" />
    </div>
    <div>
        <x-form.input label="Filename" name="filename" :value="old('filename', $sor->filename ?? '')" />
    </div>
    <div>
        <x-form.input label="Display Details" name="display_details" :value="old('display_details', $sor->display_details ?? '')" />
    </div>
    <div>
        <x-form.checkbox label="Is Locked" name="is_locked" :checked="old('is_locked', $sor->is_locked ?? false)" />
    </div>
</div>