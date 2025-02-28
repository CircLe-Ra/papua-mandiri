@props(['label', 'id', 'name', 'size' => 'base', 'disabled' => false, 'placeholder', 'mainClass' => null, 'alert' => true])

@php
    $size = match ($size) {
        'large' => ' p-4 text-base',
        'base' => ' p-2.5 text-sm',
        'small' => ' p-2 text-xs',
    }
@endphp

<div class="{{ $mainClass }}">
    @isset($label)
        <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label ?? 'label' }}</label>
    @endisset
    <input {{ $attributes->whereStartsWith('wire:model') }} id="{{$id}}" name="{{ $name }}" {{ $attributes->merge(['class' => 'block w-full text-gray-900 border border-gray-300 rounded-lg bg-neutral-100 text-base focus:ring-gray-400 focus:border-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500' . $size]) }} @disabled($disabled) placeholder="{{ $placeholder ?? '' }}">
    @if($alert)
        @error($name)
            <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
        @enderror
    @endif
</div>
