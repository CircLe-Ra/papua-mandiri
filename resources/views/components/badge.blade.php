@props(['color', 'size' => 'xs', 'outline' => false, 'pills' => false])

@php
    $color = match ($color) {
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
        'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        'pink' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };

    if ($outline) {
        $color = match ($color) {
            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' => 'bg-transparent text-blue-800 dark:text-blue-400 border border-blue-400',
            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' => 'bg-transparent text-gray-800 dark:text-gray-400 border border-gray-500',
            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => 'bg-transparent text-red-800 dark:text-red-400 border border-red-400',
            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => 'bg-transparent text-green-800 dark:text-green-400 border border-green-400',
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => 'bg-transparent text-yellow-800 dark:text-yellow-300 border border-yellow-300',
            'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300' => 'bg-transparent text-indigo-800 dark:text-indigo-400 border border-indigo-400',
            'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' => 'bg-transparent text-purple-800 dark:text-purple-400 border border-purple-400',
            'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300' => 'bg-transparent text-pink-800 dark:text-pink-400 border border-pink-400',
            default => 'bg-transparent text-gray-800 dark:text-gray-400 border border-gray-500',
        };
    }

    $size = match ($size) {
        'xs' => 'text-xs px-2.5 py-0.5',
        'sm' => 'text-sm px-3 py-0.5',
        default => 'text-xs px-2.5 py-0.5',
    };

    $rounded = $pills ? 'rounded-full' : 'rounded';
@endphp

<span {{ $attributes->merge(['class' => 'font-medium ' . $rounded . ' ' . $color . ' ' . $size]) }}>
    {{ $slot }}
</span>
