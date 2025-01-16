<?php

use function Livewire\Volt\{state, layout, mount};
use App\Models\Participant;

layout('layouts.app');
state(['participantCount']);

mount(function () {
    $this->participantCount = Participant::count();
});


?>

<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-4 gap-2">
        <x-card class="w-full">
            <div class="flex items-center text-gray-900 whitespace-nowrap dark:text-white">
                <svg class="w-20 h-20 " xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" fill-opacity="0.25" d="M3 11c0-3.771 0-5.657 1.172-6.828S7.229 3 11 3h2c3.771 0 5.657 0 6.828 1.172S21 7.229 21 11v2c0 3.771 0 5.657-1.172 6.828S16.771 21 13 21h-2c-3.771 0-5.657 0-6.828-1.172S3 16.771 3 13z"/><circle cx="12" cy="10" r="4" fill="currentColor"/><path fill="currentColor" fill-rule="evenodd" d="M18.946 20.253a.23.23 0 0 1-.14.25C17.605 21 15.836 21 13 21h-2c-2.835 0-4.605 0-5.806-.498a.23.23 0 0 1-.14-.249C5.483 17.292 8.429 15 12 15s6.517 2.292 6.946 5.253" clip-rule="evenodd"/></svg>
                <div class="ps-3">
                    <div class="text-2xl font-semibold">Peserta Kursus</div>
                    <div class="font-normal text-xl text-gray-500">{{ $this->participantCount }} Peserta</div>
                </div>
            </div>
        </x-card>
        <x-card class="w-full">
            <div class="flex items-center text-gray-900 whitespace-nowrap dark:text-white">
                <svg class="w-20 h-20 " xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M11.34 9.76L9.93 8.34c-.95-.94-2.2-1.46-3.54-1.46c-.63 0-1.25.12-1.82.34l1.04 1.04h2.28v2.14c.4.23.86.35 1.33.35c.73 0 1.41-.28 1.92-.8z" opacity="0.5"/><path fill="currentColor" d="m11 6.62l6 5.97V14h-1.41l-2.83-2.83l-.2.2c-.46.46-.99.8-1.56 1.03V15h6v2c0 .55.45 1 1 1s1-.45 1-1V6h-8z" opacity="0.5"/><path fill="currentColor" d="M9 4v1.38c-.83-.33-1.72-.5-2.61-.5c-1.79 0-3.58.68-4.95 2.05l3.33 3.33h1.11v1.11c.86.86 1.98 1.31 3.11 1.36V15H6v3c0 1.1.9 2 2 2h10c1.66 0 3-1.34 3-3V4zm-1.11 6.41V8.26H5.61L4.57 7.22a5.1 5.1 0 0 1 1.82-.34c1.34 0 2.59.52 3.54 1.46l1.41 1.41l-.2.2a2.7 2.7 0 0 1-1.92.8c-.47 0-.93-.12-1.33-.34M19 17c0 .55-.45 1-1 1s-1-.45-1-1v-2h-6v-2.59c.57-.23 1.1-.57 1.56-1.03l.2-.2L15.59 14H17v-1.41l-6-5.97V6h8z"/></svg>
                <div class="ps-3">
                    <div class="text-2xl font-semibold">Program Kursus</div>
                    <div class="font-normal text-xl text-gray-500">{{ $this->participantCount }} Program</div>
                </div>
            </div>
        </x-card>
        <x-card class="w-full">
            <div class="flex items-center text-gray-900 whitespace-nowrap dark:text-white">
                <svg class="w-20 h-20 " xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M23.835 8.5L12 .807L.165 8.5L12 16.192l8-5.2V16h2V9.693z" /><path fill="currentColor" d="M5 17.5v-3.665l7 4.55l7-4.55V17.5c0 1.47-1.014 2.615-2.253 3.338C15.483 21.576 13.802 22 12 22s-3.482-.424-4.747-1.162C6.014 20.115 5 18.97 5 17.5" opacity="0.5"/></svg>
                <div class="ps-3">
                    <div class="text-2xl font-semibold">Peserta Lulus</div>
                    <div class="font-normal text-xl text-gray-500">{{ $this->participantCount }} Peserta</div>
                </div>
            </div>
        </x-card>
        <x-card class="w-full">
            <div class="flex items-center text-gray-900 whitespace-nowrap dark:text-white">
                <svg class="w-20 h-20 " xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M6.94 2c.416 0 .753.324.753.724v1.46c.668-.012 1.417-.012 2.26-.012h4.015c.842 0 1.591 0 2.259.013v-1.46c0-.4.337-.725.753-.725s.753.324.753.724V4.25c1.445.111 2.394.384 3.09 1.055c.698.67.982 1.582 1.097 2.972L22 9H2v-.724c.116-1.39.4-2.302 1.097-2.972s1.645-.944 3.09-1.055V2.724c0-.4.337-.724.753-.724"/><path fill="currentColor" d="M22 14v-2c0-.839-.004-2.335-.017-3H2.01c-.013.665-.01 2.161-.01 3v2c0 3.771 0 5.657 1.172 6.828S6.228 22 10 22h4c3.77 0 5.656 0 6.828-1.172S22 17.772 22 14" opacity="0.5"/><path fill="currentColor" d="M18 17a1 1 0 1 1-2 0a1 1 0 0 1 2 0m0-4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m-5 4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m0-4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m-5 4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m0-4a1 1 0 1 1-2 0a1 1 0 0 1 2 0"/></svg>
                <div class="ps-3">
                    <div class="text-2xl font-semibold">Periode Aktif</div>
                    <div class="font-normal text-xl text-gray-500">{{ $this->participantCount }}</div>
                </div>
            </div>
        </x-card>
    </div>
</div>
