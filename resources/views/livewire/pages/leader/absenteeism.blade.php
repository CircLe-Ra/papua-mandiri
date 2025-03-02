<?php

use function Livewire\Volt\{state, layout, computed, usesPagination, mount, updated, on};
use App\Models\Program;
use App\Models\Reception;
use App\Models\Participant;
use App\Models\Absent;
use App\Models\AbsentDetail;
use App\Models\Opening;
use Illuminate\Support\Facades\DB;
use Masmerise\Toaster\Toaster;

usesPagination();
layout('layouts.app');
state(['period' => '','time_id','program_id','level' => 1])->url(keep: false);
state(['participants' => [], 'reception' => [],'periods' => [], 'reception_get' => true, 'periode_temp' => '']);

mount(function () {
    if ($this->period) {
        $this->periods = Reception::whereYear('start_course', $this->period)->get();
    }
});

$receptions = computed(function () {
    return Reception::select('period')->groupBy('period')->get();
});

$programs = computed(function () {
    return Program::all();
});

updated(['period' => function ($period) {
    if ($this->periode_temp !== $period) {
        $this->periods = Reception::whereYear('start_course', $period)->get();
        $this->periode_temp = $period;
        $this->reception_get = false;
        $this->time_id = '';
        $this->program_id = '';
    } elseif ($this->reception_get) {
        $this->periods = Reception::whereYear('start_course', $period)->get();
        $this->periode_temp = $period;
        $this->reception_get = false;
        $this->time_id = '';
        $this->program_id = '';
    }
}]);

$find = function () {
    $validator = \Illuminate\Support\Facades\Validator::make([
        'period' => $this->period,
        'time_id' => $this->time_id,
        'program_id' => $this->program_id,
        'level' => $this->level,
    ], [
        'period' => 'required',
        'time_id' => 'required',
        'program_id' => 'required',
        'level' => 'required',
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            Toaster::error($error);
        }
        return;
    }

    $this->reception = Reception::where('id', $this->time_id)->first();
    $this->participants = Participant::with('user.personal_data', 'program')
        ->where('reception_id', $this->reception->id)
        ->where('program_id', $this->program_id)
        ->where('level', $this->level)
        ->where('payment', 'paid')
        ->latest()->get();
};

$attendance = function ($participant_id) {
    $absent = Absent::select('reception_id', 'program_id', 'level')
        ->selectSub(function ($query) use ($participant_id) {
            $query->from('absent_details')
                ->selectRaw('COUNT(*)')
                ->where('participant_id', $participant_id)
                ->where('status', 'present')
                ->groupBy('participant_id');
        }, 'absent_count')
        ->where('reception_id', $this->reception->id)
        ->where('program_id', $this->program_id)
        ->where('level', $this->level)
        ->groupBy('reception_id', 'program_id', 'level')
        ->first();
    return $absent->absent_count ?? 0;
};

$meeting = function ($program_id) {
    return Opening::where('program_id', $program_id)->where('reception_id', $this->reception->id)->first()->meeting ?? 0;
}


?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],[
                'text' => 'Laporan'
            ],[
                'text' => 'Absensi Peserta',
                'href' => route('report.absenteeism')
            ]
        ]" />
    <div class="grid-cols-1 lg:grid-cols-3 grid gap-2 ">
        <div class="col-span-3">
            <x-card class="mt-2 w-full ">
                <x-slot name="header" class="grid grid-cols-1 xl:grid-cols-5 gap-2">
                    <div>
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white">Absensi Peserta</h5>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Cetak Laporan Absen Peserta</p>
                    </div>
                </x-slot>
                <x-slot name="sideHeader" >
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-2 col-span-4">
                        <x-form.input-select display_name="period" value="period" id="period" name="period" wire:model.live="period" size="xs" class="w-full" get-data="server" :data="$this->receptions" display_name_first="Periode?" :alert="false" />
                        <x-form.input-select display_name="start_course,complete_course" id="time_id" name="time_id" wire:model.live="time_id" size="xs" class="w-full" get-data="server" :data="$this->periods" display_name_first="Waktu Kursus?" :selected_first="true" :alert="false" />
                        <x-form.input-select id="program_id" name="program_id" wire:model.live="program_id" size="xs" class="w-full" get-data="server" :data="$this->programs" display_name_first="Program?" :alert="false" />
                        <x-form.input-select id="level" name="level" wire:model.live="level" size="xs" class="w-full" :alert="false">
                            <option value="1">Level Dasar (Basic)</option>
                            <option value="2">Level Menengah (Intermediate)</option>
                            <option value="3">Level Mahir (Advanced)</option>
                        </x-form.input-select>
                        <x-button wire:click="find" color="blue" size="xs" class="lg:col-span-1"
                                  wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed">Tampilkan
                            <svg class="rotate-180 inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/>
                            </svg>
                        </x-button>
                        <x-a href="/report/absent/{{ $this->period }}/{{ $this->time_id }}/{{ $this->program_id }}/{{ $this->level }}/print" target="_blank" color="blue" size="xs" class="lg:col-span-1" :disabled="count($this->participants) <= 0"
                                  wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed">Cetak
                            <svg class="rotate-45 inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/>
                            </svg>
                        </x-a>
                    </div>
                </x-slot>
                <x-table thead="#,Nama,Phone,Alamat,Kelas,Total Kehadiran,Pertemuan" :action="false">
                        @if(count($this->participants) > 0)
                            @foreach($this->participants as $key => $participant)
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                    <td class="px-6 py-4">
                                        {{ $loop->iteration }}
                                    </td>
                                    <th scope="row"
                                        class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                        <img class="w-10 h-10 rounded-full"
                                             src="{{ $participant->user->profile_photo_path ? asset('storage/' . $participant->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . $participant->user->personal_data->full_name }}"
                                             alt="Image Name">
                                        <div class="ps-3">
                                            <div
                                                class="text-base font-semibold">{{ $participant->user->personal_data->full_name }}</div>
                                            <div class="font-normal text-gray-500">{{ $participant->user->email }}</div>
                                        </div>
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ $participant->user->personal_data->phone }}
                                    </td>
                                    <th scope="row" class="px-6 py-4 items-center text-gray-900 dark:text-white">
                                        <div class="">
                                            <div
                                                class="text-base font-semibold">{{ $participant->user->personal_data->address }}</div>
                                            <div class="font-normal text-gray-500">
                                                RT {{ $participant->user->personal_data->rt }} :
                                                RW {{ $participant->user->personal_data->rw }}</div>
                                        </div>
                                    </th>
                                    <td class="px-6 py-4 text-nowrap">
                                        {{ $participant->program->name }}
                                    </td>
                                    <td class="px-6 py-4 text-nowrap">
                                        {{ $this->attendance($participant->id) }} x Hadir
                                    </td>
                                    <td class="px-6 py-4 text-nowrap">
                                        {{ $this->meeting($participant->program->id) }} Pertemuan
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
            </x-card>
        </div>
    </div>
</div>
