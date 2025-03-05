<?php

use function Livewire\Volt\{state, layout, computed, usesPagination, mount, updated, on};
use App\Models\Program;
use App\Models\Reception;
use App\Models\Participant;
use App\Models\Absent;
use App\Models\AbsentDetail;
use App\Models\Opening;
use Masmerise\Toaster\Toaster;

usesPagination();
layout('layouts.app');
state(['reception_id' => fn($reception) => $reception, 'program_id' => fn($program) => $program])->locked();
state(['program', 'meeting','totalMeeting']);
state(['absents' => [], 'current_absent' => 0, 'date' => date('Y-m-d'), 'status' => 'present', 'dates' => [], 'disable_meeting' => false]);
state(['show' => '', 'search' => null, 'level' => 1])->url(keep: false);

$getDates = function () {
    $this->dates = Absent::where('program_id', $this->program_id)
        ->where('reception_id', $this->reception_id)
        ->where('level', $this->level)
        ->pluck('date')
        ->toArray();
    $this->dates = array_map(function($date) {
        return ['date' => $date];
    }, $this->dates);
    $today = \Carbon\Carbon::today()->format('Y-m-d');
    if (!in_array($today, array_column($this->dates, 'date'))) {
        $this->dates[] = ['date' => $today];
    }
};

mount(function () {
    $this->program = Program::where('id', $this->program_id)->first();
    $this->getDates();
    $this->totalMeeting = Opening::where('reception_id', $this->reception_id)
        ->where('program_id', $this->program_id)->first()->meeting;
    $meeting = Absent::where('program_id', $this->program_id)
        ->where('reception_id', $this->reception_id)
        ->where('level', $this->level)->latest()->first();
    if ($meeting && $meeting->date == $this->date) {
        $this->meeting = $meeting->meeting;
        $this->current_absent = $meeting->current_absent;
    }else{
        $this->meeting = ($meeting->meeting ?? 0) + 1;
    }
});

$participants = computed(function () {
    return Participant::with('user.personal_data', 'program')
        ->where('reception_id', $this->reception_id)
        ->where('program_id', $this->program_id)
        ->where('level', $this->level)
        ->where('payment', 'paid')
        ->where(function ($query) {
            $query->whereHas('user.personal_data', function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })->orWhereHas('program', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })->orWhereHas('user', function ($query) {
                $query->where('email', 'like', '%' . $this->search . '%');
            });
        })
        ->paginate($this->show, pageName: 'absent-participant-page');
});

on(['open-modal-loading' => function () {
    $this->absents = Participant::with('user.personal_data', 'program')
        ->where('reception_id', $this->reception_id)
        ->where('program_id', $this->program_id)
        ->where('level', $this->level)
        ->where('payment', 'paid')->get()->toArray();
    $this->dispatch('modal-loading-done', id: 'absenteeism-participant-modal-1')->self();
}, 'close-modal' => function () {
    $this->absents = [];
}]);

updated(['level' => function () {
    $this->dates = Absent::where('program_id', $this->program_id)
        ->where('reception_id', $this->reception_id)
        ->where('level', $this->level)
        ->pluck('date')
        ->toArray();
    $this->dates = array_map(function($date) {
        return ['date' => $date];
    }, $this->dates);
    $today = \Carbon\Carbon::today()->format('Y-m-d');
    if (!in_array($today, array_column($this->dates, 'date'))) {
        $this->dates[] = ['date' => $today];
    }
}, 'date' => function () {
    $absent = Absent::where('program_id', $this->program_id)
        ->where('reception_id', $this->reception_id)
        ->where('level', $this->level)
        ->where('date', $this->date)->first();
    if ($absent) {
        $this->meeting = $absent->meeting;
        $this->disable_meeting = true;
    }else{
        $this->meeting = Absent::where('program_id', $this->program_id)
            ->where('reception_id', $this->reception_id)
            ->where('level', $this->level)->latest()->first()->meeting + 1 ?? 1;
        $this->disable_meeting = false;
    }
}]);

$next = function ($participan_id) {
    $this->validate([
        'date' => 'required|date|after_or_equal:' . Absent::with('reception')->where('program_id', $this->program_id)->where('reception_id', $this->reception_id)->latest()->first()->reception->start_course,
        'meeting' => 'required|integer',
        'status' => 'required|string|in:present,absent,sick,excused',
    ]);

    if ($this->current_absent < count($this->absents) - 1) {
        $this->current_absent++;
    }else{
        $this->disable_meeting = false;
        $this->dispatch('close-modal', id: 'absenteeism-participant-modal-1')->self();
        Toaster::success('Absen Selesai');
    }
    try {
        $absent = Absent::updateOrCreate([
                'date' => $this->date,
                'meeting' => $this->meeting,
                'reception_id' => $this->reception_id,
                'program_id' => $this->program_id,
                'level' => $this->level,
            ],['current_absent' => $this->current_absent]
        );
        AbsentDetail::updateOrCreate( [
            'absent_id' => $absent->id,
            'participant_id' => $participan_id,
        ],['status' => $this->status]);
        $this->getDates();
    }catch (\Exception $e) {
        Toaster::error($e->getMessage());
    }
};

$prev = function () {
    if ($this->current_absent > 0) {
        $this->current_absent--;
    }
};

$attendance = function ($participant_id) {
    return Absent::with(['absent_details' => function ($query) use ($participant_id) {
            $query->where('participant_id', $participant_id)->select('status', 'participant_id', 'absent_id');
        }])
        ->where('reception_id', $this->reception_id)
        ->where('program_id', $this->program_id)
        ->where('level', $this->level)
        ->where('date', $this->date)
        ->whereHas('absent_details', function ($query) use ($participant_id) {
            $query->where('participant_id', $participant_id);
        })
        ->first();
};


?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],
            [
                'text' => 'Absen Peserta',
                'href' => route('admin.participant.absenteeism')
            ],[
                'text' => $this->program->name,
            ],[
                'text' => ($this->level == 1 ? 'Level Dasar (Basic) ' : ($this->level == 2 ? 'Level Menengah (Intermediate)' : 'Level Mahir (Advanced)'))
            ]
        ]">
        <x-slot name="actions">
            <x-form.input-icon id="search" name="search" wire:model.live="search" placeholder="Cari..." size="small">
                <x-slot name="icon">
                    <svg class="text-blue-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24"
                         height="24" viewBox="0 0 24 24">
                        <g fill="none">
                            <path fill="currentColor" fill-opacity="0.25" fill-rule="evenodd"
                                  d="M12 19a7 7 0 1 0 0-14a7 7 0 0 0 0 14M10.087 7.38A5 5 0 0 1 12 7a.5.5 0 0 0 0-1a6 6 0 0 0-6 6a.5.5 0 0 0 1 0a5 5 0 0 1 3.087-4.62"
                                  clip-rule="evenodd"/>
                            <path stroke="currentColor" stroke-linecap="round" d="M20.5 20.5L17 17"/>
                            <circle cx="11" cy="11" r="8.5" stroke="currentColor"/>
                        </g>
                    </svg>
                </x-slot>
            </x-form.input-icon>
        </x-slot>
    </x-breadcrumbs>
    <x-modal id="absenteeism-participant-modal-1">
        <x-slot name="header">
            Mulai Absen
        </x-slot>
        @if(count($this->absents) > 0)
            <x-slot name="content">

                <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-500">
                    <div class="flex flex-col pb-3">
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            <div class="flex-shrink-0">
                                <img class="w-10 h-10 rounded-full"
                                     src="{{ $this->absents[$this->current_absent]['user']['profile_photo_path'] ? asset('storage/' . $this->absents[$this->current_absent]['user']['profile_photo_path']) : 'https://ui-avatars.com/api/?name=' . $this->absents[$this->current_absent]['user']['personal_data']['full_name'] }}"
                                     alt="Image Name">
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400">Nama</dt>
                                <dd class="text-lg font-semibold">{{ $this->absents[$this->current_absent]['user']['personal_data']['full_name'] }}</dd>
                            </div>
                        </div>

                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col py-3">
                            <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400">Pertemuan</dt>
                            <x-form.input-select id="meeting" name="meeting" wire:model="meeting" class="w-full">
                                @for ($i = 1; $i <= $this->totalMeeting; $i++)
                                    <option value="{{ $i }}" >
                                        {{ $i }}
                                    </option>
                                @endfor
                            </x-form.input-select>
                        </div>

                        <div class="flex flex-col py-3">
                            <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400">Tanggal</dt>
                            <x-form.input id="date" name="date" type="date" required main-class="w-full" wire:model.live="date" />
                        </div>
                    </div>
                    <div class="flex flex-col py-3">
                        <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400">Status</dt>
                        <x-form.input-select id="status" name="status" wire:model="status" class="w-full">
                            <option value="present">Hadir</option>
                            <option value="absent">Tidak Hadir</option>
                            <option value="sick">Sakit</option>
                            <option value="excused">Izin</option>
                        </x-form.input-select>
                    </div>
                </dl>

            </x-slot>
            <x-slot name="footer" class="flex justify-between items-center">
                <x-button color="blue" :disabled="$this->current_absent <= 0" size="xs" wire:click="prev">
                    <svg class="-rotate-90 inline -mt-[5px] w-3 h-3" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/>
                    </svg>
                    Kembali
                </x-button>
                <x-button color="blue" size="xs" wire:click="next({{$this->absents[$this->current_absent]['id']}})">
                    @if($this->current_absent >= count($this->absents) - 1)
                        Selesai
                        <svg class=" inline -mt-[5px] w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="m4 12l6 6L20 6"/>
                        </svg>
                    @else
                        Lanjut
                        <svg class="rotate-90 inline -mt-[5px] w-3 h-3" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/>
                        </svg>
                    @endif
                    </x-button>
            </x-slot>
        @else
            <div class="text-sm text-gray-600 dark:text-gray-300 p-4 text-center">
                <svg class="mx-auto" xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24">
                    <g fill="currentColor" fill-opacity="0" stroke="currentColor" stroke-linecap="round"
                       stroke-linejoin="round" stroke-width="2">
                        <path stroke-dasharray="64" stroke-dashoffset="64" d="M12 3l9 17h-18l9 -17Z">
                            <animate fill="freeze" attributeName="stroke-dashoffset" dur="1.5s" values="64;0"/>
                        </path>
                        <path stroke-dasharray="6" stroke-dashoffset="6" d="M12 10v4">
                            <animate fill="freeze" attributeName="stroke-dashoffset" begin="1.5s" dur="0.5s"
                                     values="6;0"/>
                            <animate attributeName="stroke-width" begin="4.875s" dur="7.5s" keyTimes="0;0.1;0.2;0.3;1"
                                     repeatCount="indefinite" values="2;3;3;2;2"/>
                        </path>
                        <path stroke-dasharray="2" stroke-dashoffset="2" d="M12 17v0.01">
                            <animate fill="freeze" attributeName="stroke-dashoffset" begin="2s" dur="0.5s"
                                     values="2;0"/>
                            <animate attributeName="stroke-width" begin="5.625s" dur="7.5s" keyTimes="0;0.1;0.2;0.3;1"
                                     repeatCount="indefinite" values="2;3;3;2;2"/>
                        </path>
                        <animate fill="freeze" attributeName="fill-opacity" begin="2.75s" dur="0.375s" values="0;0.3"/>
                    </g>
                </svg>

                Absen tidak dapat dilakukan karena tidak ada peserta terdatar di
                kelas {{ $this->program->name }} {{ $this->level == 1 ? 'Level Dasar (Basic)' : ($this->level == 2 ? 'Level Menengah (Intermediate)' : 'Level Mahir (Advanced)') }}
            </div>
        @endif

    </x-modal>
    <div class="grid-cols-1 lg:grid-cols-3 grid gap-2 ">
        <div class="col-span-3 ">
            <x-card class="mt-2 w-full ">
                <x-slot name="header">
                    <div>
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white">Absen Peserta</h5>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Daftar Peserta Kelas {{ $this->program->name }}</p>
                    </div>
                </x-slot>
                <x-slot name="sideHeader">
                    <div class="flex items-center gap-2">
                        <x-form.input-select id="show" name="show" wire:model.live="show" size="xs" class="w-full">
                            <option value="">Semua</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </x-form.input-select>
                        <x-form.input-select id="level" name="level" wire:model.live="level" size="xs" class="w-full">
                            <option value="1">Level Dasar (Basic)</option>
                            <option value="2">Level Menengah (Intermediate)</option>
                            <option value="3">Level Mahir (Advanced)</option>
                        </x-form.input-select>
                        <x-form.input-select id="date" name="date" wire:model.live="date" size="xs" class="w-full" :alert="false">
                            @foreach(collect($this->dates)->reverse() as $key => $item)
                                <option value="{{ $item['date'] }}" @selected($item['date'] == date('Y-m-d'))>{{ $item['date'] }}</option>
                            @endforeach
                        </x-form.input-select>
                        <x-button wire:click="$dispatch('open-modal-loading')" color="blue" size="xs"
                                  wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed">Mulai Absen
                            <svg class="rotate-45 inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/>
                            </svg>
                        </x-button>
                    </div>
                </x-slot>
                <x-table thead="#,Nama,Phone,Jenis Kelamin,Alamat, Kehadiran Hari ini" :action="false">
                    @if($this->participants->count() > 0)
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
                                <td class="px-6 py-4">
                                    {{ $participant->user->personal_data->gender == 'M' ? 'Laki-Laki' : 'Perempuan' }}
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
                                <td class="px-6 py-4">
                                    @if (optional($this->attendance($participant->id))->count() ?? false)
                                        @foreach ($this->attendance($participant->id)->absent_details as $absent)
                                            @switch($absent->status)
                                                @case('present')
                                                    <x-badge color="green" size="xs">Hadir</x-badge>
                                                    @break
                                                @case('absent')
                                                    <x-badge color="red" size="xs">Tidak Hadir</x-badge>
                                                    @break
                                                @case('excused')
                                                    <x-badge color="yellow" size="xs">Izin</x-badge>
                                                    @break
                                                @case('sick')
                                                    <x-badge color="indigo" size="xs">Sakit</x-badge>
                                                    @break
                                                @default
                                                    <x-badge color="purple" size="xs">Belum Absen</x-badge>
                                                    @break
                                            @endswitch
                                        @endforeach
                                    @else
                                        <x-badge color="purple" size="xs">Belum Absen</x-badge>
                                    @endif
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
                {{ $this->participants->links('livewire.pagination') }}
            </x-card>
        </div>
    </div>
</div>
