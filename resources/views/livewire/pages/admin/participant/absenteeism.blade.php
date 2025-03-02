<?php

use function Livewire\Volt\{state, layout, computed, usesPagination, mount, updated, on};
use App\Models\Program;
use App\Models\Reception;
use App\Models\Opening;
use App\Models\Participant;
use App\Models\Absent;
use Masmerise\Toaster\Toaster;

usesPagination();
layout('layouts.app');
state(['reception'])->locked();
state(['reception_id' => '', 'period_id' => '', 'period' => [], 'reception_get' => true, 'periode_temp' => '']);
state(['show' => 5, 'search' => null])->url();

mount(function () {
    $this->reception = Reception::where('status', 'active')->first();
    if ($this->reception == null) {
        return $this->redirect('information', navigate: true);
    }
});

$periods = computed(function () {
    return Reception::select('period')->groupBy('period')->get();
});

$programs = computed(function () {
    return Opening::with('program', 'reception')
        ->where('reception_id', $this->reception->id)
        ->where(function ($query) {
            $query->whereHas('program', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        })
        ->latest()->paginate($this->show, pageName: 'absent-program-page');
});

on(['close-modal' => function () {
    $this->reset(['reception_id', 'period_id', 'period', 'periode_temp']);
    $this->reception_get = true;
}]);

updated(['reception_id' => function ($period) {
    if ($this->periode_temp !== $period) {
        $this->period = Reception::whereYear('start_course', $period)->get();
        $this->periode_temp = $period;
        $this->reception_get = false;
    } elseif ($this->reception_get) {
        $this->period = Reception::whereYear('start_course', $period)->get();
        $this->periode_temp = $period;
        $this->reception_get = false;
    }
}]);

$find = function () {
    $this->validate([
        'reception_id' => 'required',
        'period_id' => 'required',
    ]);
    $this->reception = Reception::where('id', $this->period_id)->first();
    $this->programs = Opening::with('program', 'reception')
        ->where('reception_id', $this->reception->id)
        ->where(function ($query) {
            $query->whereHas('program', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        })
        ->latest()->paginate($this->show, pageName: 'absent-program-page');
    $this->dispatch('close-modal', id: 'absent-reception-modal');
};

$total_participants = function ($program_id) {
    return Participant::where('program_id', $program_id)->where('payment', 'paid')->where('reception_id', $this->reception->id)->count();
};

$total_meeting = function ($program_id) {
    return Absent::select(\Illuminate\Support\Facades\DB::raw('count(*) as meeting'), 'program_id', 'reception_id')->where('reception_id', $this->reception->id)->where('program_id', $program_id)->groupBy('reception_id', 'program_id')->first()->meeting ?? 0;
};

?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],[
                'text' => 'Absen Peserta'
            ],[
                'text' => 'Peserta Kursus',
                'href' => route('admin.participants')
            ],[
                'text' => \Carbon\Carbon::parse($this->reception->start_course)->locale('id')->isoFormat('DD MMMM YYYY') . ' s/d ' . \Carbon\Carbon::parse($this->reception->complete_course)->locale('id')->isoFormat('DD MMMM YYYY')
            ],
        ]">
        <x-slot name="actions">
            <x-form.input-icon id="search" name="search" wire:model.live="search" placeholder="Cari..." size="small">
                <x-slot name="icon">
                    <svg class="text-blue-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none"><path fill="currentColor" fill-opacity="0.25" fill-rule="evenodd" d="M12 19a7 7 0 1 0 0-14a7 7 0 0 0 0 14M10.087 7.38A5 5 0 0 1 12 7a.5.5 0 0 0 0-1a6 6 0 0 0-6 6a.5.5 0 0 0 1 0a5 5 0 0 1 3.087-4.62" clip-rule="evenodd"/><path stroke="currentColor" stroke-linecap="round" d="M20.5 20.5L17 17"/><circle cx="11" cy="11" r="8.5" stroke="currentColor"/></g></svg>
                </x-slot>
            </x-form.input-icon>
        </x-slot>
    </x-breadcrumbs>
    <x-modal id="absent-reception-modal">
        <x-slot name="header">
            <h5 class="text-xl font-medium text-gray-900 dark:text-white">Pilih Periode Kursus</h5>
        </x-slot>
        <x-slot name="content">
            <x-form.input-select display_name="period" value="period" id="reception_id" name="reception_id" wire:model.live="reception_id" size="sm" class="w-full mb-2" label="Periode Kursus" get-data="server" :data="$this->periods" />
            <x-form.input-select display_name="start_course,complete_course" id="period_id" name="period_id" wire:model="period_id" size="sm" class="w-full" label="Mulai & Selesai Kursus" get-data="server" :data="$this->period" />
        </x-slot>
        <x-slot name="footer">
            <x-button type="reset" color="light" class="mr-2">
                Batal
            </x-button>
            <x-button type="submit" color="blue" wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed" wire:target="find,reception_id" wire:click="find">
                Tampilkan
            </x-button>
        </x-slot>
    </x-modal>
    <div class="grid-cols-1 lg:grid-cols-3 grid gap-2 ">
        <div class="col-span-3 ">
            <x-card class="mt-2 w-full ">
                <x-slot name="header">
                    <div>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white">Program Kursus</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Program berikut berdasarkan periode aktif {{ \Carbon\Carbon::parse($this->reception->start_course)->locale('id')->isoFormat('DD-MM-YYYY') }} s/d {{ \Carbon\Carbon::parse($this->reception->complete_course)->locale('id')->isoFormat('DD-MM-YYYY') }}</p>
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
                        <x-button wire:click="$dispatch('open-modal', { id :'absent-reception-modal'})" size="xs" color="blue">Periode
                            <svg class="inline w-3 h-3 -mt-[5px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M6.94 2c.416 0 .753.324.753.724v1.46c.668-.012 1.417-.012 2.26-.012h4.015c.842 0 1.591 0 2.259.013v-1.46c0-.4.337-.725.753-.725s.753.324.753.724V4.25c1.445.111 2.394.384 3.09 1.055c.698.67.982 1.582 1.097 2.972L22 9H2v-.724c.116-1.39.4-2.302 1.097-2.972s1.645-.944 3.09-1.055V2.724c0-.4.337-.724.753-.724"/><path fill="currentColor" d="M22 14v-2c0-.839-.004-2.335-.017-3H2.01c-.013.665-.01 2.161-.01 3v2c0 3.771 0 5.657 1.172 6.828S6.228 22 10 22h4c3.77 0 5.656 0 6.828-1.172S22 17.772 22 14" opacity="0.5"/><path fill="currentColor" d="M18 17a1 1 0 1 1-2 0a1 1 0 0 1 2 0m0-4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m-5 4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m0-4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m-5 4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m0-4a1 1 0 1 1-2 0a1 1 0 0 1 2 0"/></svg>
                        </x-button>
                    </div>
                </x-slot>
                <x-table thead="#, Nama Program, Pertemuan Berjalan, Total Pertemuan, Total Peserta">
                    @if($this->programs->count() > 0)
                        @foreach($this->programs as $key => $program)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $program->program->name }}
                                </td>
                                    <td class="px-6 py-4 text-nowrap" >
                                        {{ $this->total_meeting($program->program_id) }} Pertemuan
                                    </td>
                                    <td class="px-6 py-4 text-nowrap">
                                        {{ $program->meeting }} Pertemuan
                                    </td>
                                <td class="px-6 py-4 text-nowrap">
                                    {{ $this->total_participants($program->program_id) }} Peserta
                                </td>
                                <td class="px-6 py-4 text-nowrap">
                                    <x-a wire:navigate size="xs" color="blue-outline" href="{{ route('admin.participant.absenteeism.detail', ['reception' => $this->reception->id, 'program' => $program->program_id]) }}">
                                        Lihat Peserta
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
                {{ $this->programs->links('livewire.pagination') }}
            </x-card>
        </div>
    </div>
</div>
