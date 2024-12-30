@props(['color', 'size' => 'xs', 'variant' => 'default', 'icon' => null, 'label' => null, 'disabled' => false])
@php
    $color = match ($color) {
        'red' => 'text-white bg-red-700 hover:bg-red-800 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800',
        'blue' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
        'grey' => 'text-white bg-grey-700 hover:bg-grey-800 focus:ring-grey-300 dark:bg-grey-600 dark:hover:bg-grey-700 dark:focus:ring-grey-800',
        'yellow' => 'text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-yellow-300 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800',
        'dark' => 'text-white bg-gray-800 hover:bg-gray-900 focus:ring-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700',
        'light' => 'text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700',
        default => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800'
    };

    $size = match ($size) {
        'xs' => 'px-3 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
        'xl' => 'px-6 py-3.5 text-base',
    };

    $variantClass = match ($variant) {
        'outline' => 'border hover:text-white focus:ring-4',
        'pills' => 'rounded-full',
        'icon' => 'p-2.5 rounded-md',
        'loader' => 'inline-flex items-center justify-center',
        'with-icon' => 'inline-flex items-center',
        'label' => 'inline-flex items-center justify-between',
        default => 'rounded-lg'
    };

    $iconClass = $icon ? 'me-2' : '';
    $disabledClass = $disabled ? 'opacity-50 cursor-not-allowed' : '';
@endphp

<button {{ $attributes->merge([
    'type' => 'button',
    'class' => "focus:outline-none font-medium text-center $color $size $variantClass $disabledClass"
]) }} @disabled($disabled)>
    @if ($icon)
        <span class="{{ $iconClass }}">
            {!! $icon !!}
        </span>
    @endif
    {{ $slot }}
    @if ($label)
        <span class="inline-flex items-center justify-center w-4 h-4 ms-2 text-xs font-semibold bg-blue-200 text-blue-800 rounded-full">
            {{ $label }}
        </span>
    @endif
</button>
