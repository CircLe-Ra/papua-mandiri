<?php

use function Livewire\Volt\{state, layout, title, mount, usesPagination, computed};
use App\Models\Participant;
use App\Models\Reception;
use App\Models\PersonalData;
use App\Models\AbsentDetail;
use App\Models\Absent;

layout('layouts.app');
title('Dashboard');

usesPagination();
state(['participant', 'reception']);
state(['show' => 5])->url();
mount(function () {
    $this->participant = Participant::with(['program'])->where('user_id', auth()->user()->id)->latest()->first();
    $this->reception = Reception::where('id', $this->participant->reception_id ?? 0)->first();
});

$absents = computed(function () {
    return Absent::with('absent_details')->where('reception_id', $this->reception->id ?? 0)
        ->where('program_id', $this->participant->program_id ?? 0)
        ->whereHas('absent_details', function($query) {
            $query->where('participant_id', $this->participant->id ?? 0);
        })->latest()->paginate($this->show, pageName: 'dashboard-absents-page');
})

?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ]
        ]">
        <x-slot:actions>
            <p class="my-[5px] dark:text-white">Selamat datang, {{ auth()->user()->personal_data?->full_name ?? auth()->user()->name  }}</p>
        </x-slot:actions>
    </x-breadcrumbs>
    <div class="w-full mb-16">
        <div class="grid grid-cols-1 gap-2 md:grid-cols-2 ">
            <x-card class="mt-2 w-full h-full">
                <h5 class="text-gray-900 dark:text-white text-center font-bold text-2xl">Selamat Datang</h5>
                <div class="flex justify-start items-center ">
                    <div class="mr-4">
                        <img class="w-48 rounded-full" src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' .  auth()->user()->personal_data?->full_name ?? auth()->user()->name  }}" alt="Image Name">
                    </div>
                    <div>
                        <p class="text-sm text-justify text-gray-600 dark:text-gray-300">Mari Membangun Masa Depan Papua yang Mandiri dan Sejahtera dengan
                            Memberdayakan Generasi Papua Melalui Pendidikan dan Pelatihan Keterampilan Kerja.</p>
                    </div>
                </div>
                    <div
                        class=" bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-2 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-300 font-bold flex justify-center mt-2">
                            Data Personal</p>
                        <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-600 dark:text-gray-300">Nama</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ auth()->user()->personal_data->full_name ?? auth()->user()->name }}</p>
                        </div>
                        <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-600 dark:text-gray-300">Email</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ auth()->user()->email }}</p>
                        </div>
                        <div
                            class="border-t-2 border-gray-300 dark:border-gray-700 mt-4 mb-2"></div>
                        <div class="flex justify-between ">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Nomor
                                Handphone</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ auth()->user()->personal_data->phone ?? '-' }}</p>
                        </div>

                    </div>
            </x-card>
            <x-card class="mt-2 w-full h-full">
                <div>
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white">Program Kursus</h5>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Program kursus yang dipilih</p>
                </div>
                    <div class=" bg-white dark:bg-gray-800 border  dark:border-gray-700 p-2 rounded-lg h-48">
                        <p class="text-sm text-gray-600 dark:text-gray-300 font-bold flex justify-center mt-2">Informasi Program</p>
                        <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                        @if($this->participant)
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-950 dark:text-gray-300">Program Belajar</p>
                            <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->participant?->program->name }}</p>
                        </div>
                        <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-950 dark:text-gray-300">Level/Tingkat</p>
                            <p class="text-sm text-green-500 dark:text-gray-300  px-3">{{ $this->participant?->level == 1 ? 'Level Dasar (Basic) ' : ($this->participant?->level == 2 ? 'Level Menengah (Intermediate)' : 'Level Mahir (Advanced)') }}</p>
                        </div>
                        <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-950 dark:text-gray-300">Tanggal Mulai</p>
                            <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->reception?->start_course }}</p>
                        </div>
                        <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-950 dark:text-gray-300">Tanggal Selesai</p>
                            <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->reception?->complete_course }}</p>
                        </div>
                        <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                        <div class="flex justify-between">
                        <p class="text-sm text-gray-950 dark:text-gray-300">Jumlah Hari Pertemuan</p>
                        <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->reception?->openings->where('program_id', $this->participant->program_id)->first()->meeting }} Pertemuan</p>
                    </div>
                        @else
                            <div class="flex justify-center">
                                <p class="text-sm text-gray-950 dark:text-gray-300">Anda Belum Melakukan Pendaftaran</p>
                            </div>
                        @endif
                </div>
                </x-card>
            <x-card class="mt-2 w-full h-full col-span-2 ">
                <x-slot name="header">
                    <div>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white">Absen Peserta</h5>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Daftar absen selama mengikuti program kursus</p>
                    </div>
                </x-slot>
                <x-slot name="sideHeader">
                    <x-form.input-select id="show" name="show" wire:model.live="show" size="xs" >
                        <option value="">Semua</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </x-form.input-select>
                </x-slot>

                <div class="">
                        <x-table thead="#,Tanggal,Kehadiran,Pertemuan" :action="false">
                            @if(count($this->absents) > 0)
                                @foreach($this->absents as $key => $absent)
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                        <td class="px-6 py-4">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 text-nowrap">
                                            {{ $absent->date }}
                                        </td>
                                        <td class="px-6 py-4 text-nowrap">
                                            @switch($absent->absent_details->where('participant_id', $this->participant->id)->first()->status)
                                                @case('present')
                                                    <span class="text-green-500">Hadir</span>
                                                @break
                                                @case('absent')
                                                    <span class="text-red-500">Tidak Hadir</span>
                                                @break
                                                @case('sick')
                                                    <span class="text-indigo-500">Sakit</span>
                                                @break
                                                @case('excused')
                                                    <span class="text-yellow-500">Izin</span>
                                                @break
                                            @endswitch
                                        </td>

                                        <td class="px-6 py-4 ">
                                            Ke {{ $absent->meeting }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <td class="px-6 py-4 text-center" colspan="8">
                                        Tidak ada data
                                    </td>
                                </tr>
                            @endif
                        </x-table>
                    </div>
                {{ $this->absents->links('livewire.pagination') }}
                </x-card>
        </div>
    </div>

</div>
