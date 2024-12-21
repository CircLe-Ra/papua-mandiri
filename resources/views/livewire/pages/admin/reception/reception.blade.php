<?php

use function Livewire\Volt\{state, layout, computed, on, usesPagination};
use App\Models\Program;
use App\Models\Reception;
use Masmerise\Toaster\Toaster;

usesPagination();
layout('layouts.app');
state(['period', 'id', 'start_course', 'complete_course']);
state(['show' => 5, 'search' => null])->url();

$receptions = computed(function () {
    return Reception::where('period', 'like', '%' . $this->search . '%')
        ->orWhere('start_course', 'like', '%' . $this->search . '%')
        ->orWhere('complete_course', 'like', '%' . $this->search . '%')
        ->latest()->paginate($this->show, pageName: 'receptions-page');
});

on(['refresh' => function () {
    $this->receptions = Reception::where('period', 'like', '%' . $this->search . '%')
        ->orWhere('start_course', 'like', '%' . $this->search . '%')
        ->orWhere('complete_course', 'like', '%' . $this->search . '%')
        ->latest()->paginate($this->show, pageName: 'receptions-page');
}]);

$store = function () {
    $this->validate([
        'period' => 'required|integer',
        'start_course' => 'required|date|before:complete_course',
        'complete_course' => 'required|date|after:start_course'
    ]);

    try {
        Reception::updateOrCreate(['id' => $this->id], [
            'period' => $this->period,
            'start_course' => $this->start_course,
            'complete_course' => $this->complete_course
        ]);
        unset($this->receptions);
        $this->reset(['period', 'id', 'start_course', 'complete_course']);
        $this->dispatch('refresh');
        Toaster::success('Periode berhasil disimpan');
    } catch (\Exception $e) {
        Toaster::error($e->getMessage());
        Toaster::error('Periode gagal disimpan');
    }
};

$destroy = function ($id) {
    try {
        $reception = Reception::find($id);
        $reception->delete();
        unset($this->receptions);
        $this->dispatch('refresh');
        Toaster::success('Berhasil menghapus data');
    }catch (\Exception $e){
        Toaster::error('Gagal menghapus data');
    }
};

$edit = function ($id){
    $reception = Reception::find($id);
    $this->id = $reception->id;
    $this->period = $reception->period;
    $this->start_course = $reception->start_course;
    $this->complete_course = $reception->complete_course;
};

$changeStatus = function ($id, $status) {
    if ($status === 'active') {
        Reception::where('status', 'active')
        ->where('id', '!=', $id)
        ->update(['status' => 'inactive']);
    }
    $reception = Reception::find($id);
    if ($reception) {
        $reception->status = $status;
        $reception->save();
    }
};


?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],
            [
                'text' => 'Pembukaan'
            ],
        ]">
        <x-slot name="actions">
            <x-form.input-icon id="search" name="search" wire:model.live="search" placeholder="Cari..." size="small">
                <x-slot name="icon">
                    <svg class="text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none"><path fill="currentColor" fill-opacity="0.25" fill-rule="evenodd" d="M12 19a7 7 0 1 0 0-14a7 7 0 0 0 0 14M10.087 7.38A5 5 0 0 1 12 7a.5.5 0 0 0 0-1a6 6 0 0 0-6 6a.5.5 0 0 0 1 0a5 5 0 0 1 3.087-4.62" clip-rule="evenodd"/><path stroke="currentColor" stroke-linecap="round" d="M20.5 20.5L17 17"/><circle cx="11" cy="11" r="8.5" stroke="currentColor"/></g></svg>
                </x-slot>
            </x-form.input-icon>
        </x-slot>
    </x-breadcrumbs>

    <div class="grid-cols-1 lg:grid-cols-3 grid gap-2 ">
        <div class="w-full col-span-3 lg:col-span-1">
            <x-card class="mt-2" >
                <x-slot name="header">
                    Tambah Periode
                </x-slot>
                <form wire:submit="store" class="max-w-sm mx-auto">
                    <x-form.input type="number" id="period" name="period" wire:model="period" label="Nama"
                                  placeholder="Masukan Periode"/>
                    <x-form.input type="date" id="start_course" name="start_course" wire:model="start_course" label="Mulai Kursus"/>
                    <x-form.input type="date" id="complete_course" name="complete_course" wire:model="complete_course" label="Selesai Kursus"/>
                    <div class="flex justify-end space-x-2">
                        <x-button type="reset" color="light">
                            Batal
                        </x-button>
                        <x-button type="submit" color="blue" wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed" wire:target="store">
                            Simpan
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
        <div class="col-span-2 ">
            <x-card class="mt-2 w-full ">
                <x-slot name="header">
                    Daftar Periode
                </x-slot>
                <x-slot name="sideHeader">
                    <x-form.input-select id="show" name="show" wire:model.live="show" size="xs" class="w-full">
                        <option value="">Semua</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </x-form.input-select>
                </x-slot>

                <x-table thead="#, Periode, Mulai Kursus, Selesai Kursus, Status">
                    @if($this->receptions->count() > 0)
                        @foreach($this->receptions as $key => $reception)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $reception->period }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($reception->start_course)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($reception->complete_course)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                   <button id="dropdownStatus{{ $key }}" data-dropdown-toggle="dropdown-status{{ $key }}"  data-dropdown-placement="right-end" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs px-7 py-2 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 w-full relative" type="button">
                                       <div class="-ml-2 whitespace-nowrap">
                                       {{ $reception->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                       </div>
                                       <svg class="absolute right-2 w-2.5 h-2.5 -rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                        </svg>
                                    </button>
                                    <!-- Dropdown menu -->
                                    <div id="dropdown-status{{ $key }}" class="z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownStatus{{ $key }}">
                                            <li>
                                                <a href="#" wire:click="changeStatus({{ $reception->id }}, 'active')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Aktifkan</a>
                                            </li>
                                            <li>
                                                <a href="#" wire:click="changeStatus({{ $reception->id }}, 'inactive')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Non Aktifkan</a>
                                            </li>
                                        </ul>
                                    </div>

                                </td>
                                <td class="space-x-2 space-y-2 pb-2">
                                    <x-a href="{{ route('admin.reception.opening', $reception->id) }}" color="blue" >
                                        Buka Program
                                    </x-a>
                                    <x-button color="yellow" wire:click="edit({{ $reception->id }})" :disabled="$reception->openings->count() > 0">
                                        Edit
                                    </x-button>
                                        <x-button color="red" wire:click="destroy({{ $reception->id }})" wire:confirm="Yakin?" :disabled="$reception->openings->count() > 0">
                                            Hapus
                                        </x-button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4 text-center" colspan="6">
                                Tidak ada data
                            </td>
                        </tr>
                    @endif
                </x-table>
                {{ $this->receptions->links('livewire.pagination') }}
            </x-card>
        </div>
    </div>

</div>
