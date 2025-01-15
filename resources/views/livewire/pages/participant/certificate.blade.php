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
state(['period' => ''])->url(keep: false);
state(['time_id']);
    state(['participants' => [], 'reception' => [],'periods' => [], 'reception_get' => true, 'periode_temp' => '']);

mount(function () {
    if ($this->period) {
        $this->periods = Reception::whereYear('start_course', $this->period)->get();
    }
});

$receptions = computed(function () {
    return Reception::select('period')->groupBy('period')->get();
});

updated(['period' => function ($period) {
    if ($this->periode_temp !== $period) {
        $this->periods = Reception::whereYear('start_course', $period)->get();
        $this->periode_temp = $period;
        $this->reception_get = false;
    } elseif ($this->reception_get) {
        $this->periods = Reception::whereYear('start_course', $period)->get();
        $this->periode_temp = $period;
        $this->reception_get = false;
    }
}]);

$find = function () {
    $validator = \Illuminate\Support\Facades\Validator::make([
        'period' => $this->period,
        'time_id' => $this->time_id,
    ], [
        'period' => 'required',
        'time_id' => 'required',
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            Toaster::error($error);
        }
        return;
    }

    $this->reception = Reception::where('id', $this->time_id)->first();
    $this->participants = Participant::with(['reception', 'program'])->where('reception_id', $this->reception->id)
        ->where('user_id', auth()->user()->id)
        ->where('payment', 'paid')
        ->latest()->get();
};

?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],[
                'text' => 'Seritifikat'
            ],[
                'text' => 'Unduh Sertifikat',
                'href' => route('participant.certificate.download')
            ]
        ]" />
    <div class="grid-cols-1 lg:grid-cols-3 grid gap-2 ">
        <div class="col-span-3 ">
            <x-card class="mt-2 w-full ">
                <x-slot name="header" class="grid grid-cols-1 xl:grid-cols-8 gap-2">
                    <div class="col-span-4">
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white">Sertifikat Peserta</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Bukti sertifikat peserta yang telah mengikuti kursus.</p>
                    </div>
                </x-slot>
                <x-slot name="sideHeader" >
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 col-span-4">
                        <x-form.input-select display_name="period" value="period" id="period" name="period" wire:model.live="period" size="xs" class="w-full" get-data="server" :data="$this->receptions" display_name_first="Periode?" :alert="false" />
                        <x-form.input-select display_name="start_course,complete_course" id="time_id" name="time_id" wire:model="time_id" size="xs" class="w-full" get-data="server" :data="$this->periods" display_name_first="Waktu Kursus?" :selected_first="true" :alert="false" />
                        <x-button wire:click="find" color="blue" size="xs"
                                  wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed">Tampilkan
                            <svg class="rotate-180 inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/>
                            </svg>
                        </x-button>
                    </div>
                </x-slot>
                <x-table thead="#,Periode, Kelas, Level, Sertifikat" :action="false">
                    @if(count($this->participants) > 0)
                        @foreach($this->participants as $key => $participant)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>
                                <th scope="row"
                                    class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="ps-3">
                                        <div
                                            class="text-base font-semibold">Tahun {{ $participant->reception->period }}</div>
                                        <div class="font-normal text-gray-500">
                                            {{ \Carbon\Carbon::parse($participant->reception->start_course)->locale('id')->isoFormat('DD MMMM YYYY') . ' - ' . \Carbon\Carbon::parse($participant->reception->complete_course)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                        </div>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    {{ $participant->program->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $participant->level == 1 ? 'Level Dasar (Basic) ' : ($participant->level == 2 ? 'Level Menengah (Intermediate)' : 'Level Mahir (Advanced)') }}
                                </td>
                                <td class="px-6 py-4 text-nowrap">
                                    <x-a size="xs" color="blue" :disabled="$participant->certificate == null" href="{{ $participant->certificate != null ? asset('storage/' . $participant->certificate) : '#' }}" target="{{  $participant->certificate != null ? '_blank' : '' }}">
                                        Lihat
                                        <svg class="rotate-45 inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/></svg>
                                    </x-a>
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
