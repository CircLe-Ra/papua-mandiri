
@props(['header', 'footer','sideHeader'])

<div {{ $attributes->merge(['class' => ' p-4 bg-neutral-200 border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700']) }}>
    <div class="space-y-6">
        @isset($header)
            <div {{ $header->attributes->class(['flex justify-between items-center']) }}>
                {{ $header ?? 'header' }}
                @isset($sideHeader)
                    {{ $sideHeader }}
                @endisset
            </div>
        @endisset

        {{ $slot }}

        @isset($footer)
            <div class="flex justify-end space-x-2">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
