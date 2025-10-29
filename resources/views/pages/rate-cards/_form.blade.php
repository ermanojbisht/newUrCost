<div class="space-y-6">
    <div>
        <label for="name" class="label">{{ __('Name') }}</label>
        <input type="text" name="name" id="name" value="{{ old('name', $rateCard->name ?? '') }}" class="input-field" required>
        @error('name')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="description" class="label">{{ __('Description') }}</label>
        <textarea name="description" id="description" rows="4" class="input-field">{{ old('description', $rateCard->description ?? '') }}</textarea>
        @error('description')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
