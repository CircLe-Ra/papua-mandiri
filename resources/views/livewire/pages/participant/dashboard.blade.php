<?php

use function Livewire\Volt\{state, layout, title};

layout('layouts.app');
title('Dashboard');



?>

<div >
            <div class="mb-4">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white ">Dashboard Peserta</h1>
                <p class=" text-sm text-gray-600 dark:text-gray-300">Halo, {{ auth()->user()->name }} </p>
            </div>
        <div class="w-full">
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 ">
            <x-card class="mt-2 w-full h-full">
                <h5 class="text-gray-900 dark:text-white text-center font-bold text-2xl">Selamat Datang {{ auth()->user()->name }}</h5>
                <div class="flex justify-start items-center ">
                    <div class="mr-4">
                        <img class="w-48 rounded-full" src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . auth()->user()->personal_data->full_name }}" alt="Image Name">
                    </div>
                    <div>
                        <p class="text-sm text-justify text-gray-600 dark:text-gray-300">Mari Membangun Masa Depan Papua yang Mandiri dan Sejahtera dengan
                            Memberdayakan Generasi Papua Melalui Pendidikan dan Pelatihan Keterampilan Kerja.</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
