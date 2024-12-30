<?php

use function Livewire\Volt\{state, layout, computed, usesPagination};
use App\Models\Program;
use App\Models\Reception;
use App\Models\Opening;
use Masmerise\Toaster\Toaster;

usesPagination();
layout('layouts.app');
state('reception' => fn($id) => Reception::where('id', $id)->first())->locked();
state(['show' => 5, 'search' => null])->url();

$openings = computed(function () {
    return Opening::where('reception_id', $this->reception->id)
        ->whereHas('program', function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->latest()->paginate($this->show, pageName: 'openings-absent-page');
});

?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],
            [
                'text' => 'Absensi'
            ],[
                'text' => 'Program Kursus'
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
        <div class="col-span-3 ">
            <x-card class="mt-2 w-full ">
                <x-slot name="header">
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white">Daftar Program</h5>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Silahkan pilih program kurusus yang diikuti peserta.</p>
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

                <x-table thead="#, Name, Mulai Kursus, Selesai Kursus, Status">
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
                                <td class="px-6 py-4 {{ $reception->status == 'active' ? 'text-green-500' : 'text-red-500'}}">
                                    {{ $reception->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </td>
                                <td class="space-x-2 space-y-2 pb-2">
                                    <x-a href="{{ route('admin.participant.absenteeism.detail', $reception->id) }}" color="blue" >
                                        Lihat Absensi
                                    </x-a>
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
