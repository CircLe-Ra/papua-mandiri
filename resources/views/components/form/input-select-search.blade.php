@props([
    'id' => null,
    'name' => null,
    'getData' => 'manual',
    'data' => [],
    'value' => 'id',
    'display_name' => 'name',
    'disabled' => false,
    'label' => null,
    'size' => 'default',
    'display_name_first' => 'Pilih?',
    'selected_first' => true,
    'mainClass' => null,
    'search_placeholder' => 'Cari...', // Menambahkan parameter untuk placeholder pencarian
])

@php
    $dn = explode(',', $display_name);
    $size = match ($size) {
        'xs' => 'block w-full p-2 text-xs text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
        'sm' => 'block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
        'md' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
        'lg' => 'block w-full px-4 py-3 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
    };
@endphp

<div class="mb-5 {{ $mainClass }}">
    @isset($label)
        <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
    @endisset

    <!-- Gabungkan Button Select dan Dropdown Search -->
    <div class="relative">
        <!-- Dropdown Button -->
        <button id="{{ $id }}" data-dropdown-toggle="dropdownSearch{{ $id }}" data-dropdown-placement="bottom" {{ $attributes->merge(['class' => $size]) }} @disabled($disabled) type="button">
            {{ $display_name_first }}
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
        </button>

        <!-- Dropdown menu -->
        <div id="dropdownSearch{{ $id }}" class="z-10 hidden bg-white rounded-lg shadow w-60 dark:bg-gray-700 absolute mt-2">
            <div class="p-3">
                <label for="input-group-search" class="sr-only">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" id="input-group-search" wire:model="search" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="{{ $search_placeholder }}" />
                </div>
            </div>

            <!-- Daftar opsi berdasarkan data -->
            <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200">
                @if ($getData == 'server' && count($data))
                    @foreach ($data as $dt)
                        <li>
                            <div class="flex items-center ps-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <input type="checkbox" value="{{ $dt->$value }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                <label for="checkbox-item-{{ $dt->$value }}" class="w-full py-2 ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">
                                    @foreach ($dn as $d)
                                        {{ $dt->$d }}
                                        @if($d != end($dn)) || @endif
                                    @endforeach
                                </label>
                            </div>
                        </li>
                    @endforeach
                @else
                    <li><span class="text-gray-500 dark:text-gray-400">Tidak ada data</span></li>
                @endif
            </ul>
        </div>
    </div>
</div>
