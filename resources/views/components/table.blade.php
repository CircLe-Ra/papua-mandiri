@props(['thead', 'action' => true, 'theadCol' => null])
@php
    $thead = \Str::of($thead)->explode(',');
@endphp

<div class="relative overflow-x-auto border dark:border-gray-700 sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr class="text-neutral-500 dark:text-neutral-50">
            @foreach ($thead as $key => $th)
                @php
                    $mergeCell = Str::of($th)->explode(':');
                    $count = $mergeCell->count();
                @endphp
                <th scope="col" class="px-6 py-3" colspan="{{ $theadCol ?? '' }}">
                    {{ $th }}</th>
            @endforeach
            @if ($action ?? false)
                <th scope="col" class="px-6 py-3">Aksi</th>
            @endif
        </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
