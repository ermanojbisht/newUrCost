@props([
    'label' => '',
    'name' => '',
    'checked' => false,
    'required' => false,
    'disabled' => false,
])

<div class="flex items-center">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $checked ? 'checked' : '' }}
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50']) }}
    >
    @if($label)
        <label for="{{ $name }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif
</div>
