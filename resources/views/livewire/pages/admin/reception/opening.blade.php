<?php

use function Livewire\Volt\{state, layout, computed, on, usesPagination};
use App\Models\Program;
use App\Models\Opening;
use App\Models\Reception;
use Masmerise\Toaster\Toaster;
use Illuminate\Validation\Rule;

layout('layouts.app');
usesPagination();
state(['reception' => fn($period) => Reception::where('id', $period)->first()]);
state(['id', 'program_id', 'meeting ']);
state(['show' => 5, 'search' => null])->url();

$programs = computed(function () {
    return Program::all();
});

$openings = computed(function () {
    return Opening::whereHas('program', function ($query) {
        $query->where('name', 'like', '%' . $this->search . '%');
        })->where('reception_id', $this->reception->id)
        ->latest()
        ->paginate($this->show, pageName: 'openings-page');
});

on(['refresh' => function () {
    $this->openings = Opening::whereHas('program', function ($query) {
        $query->where('name', 'like', '%' . $this->search . '%');
        })->where('reception_id', $this->reception->id)
        ->latest()
        ->paginate($this->show, pageName: 'openings-page');
}]);

$store = function () {
    $validate = $this->validate([
        'program_id' => ['required', $this->id ? Rule::unique('openings')->where(function ($query) {
                                                    return $query->where('reception_id', $this->reception->id)
                                                            ->where('program_id', $this->program_id);
                                                })->ignore($this->id) : Rule::unique('openings')->where(function ($query) {
                                                    return $query->where('reception_id', $this->reception->id)
                                                        ->where('program_id', $this->program_id);
                                                    }),
                                                ],
        'meeting' => ['required'],
    ]);
    $validate['reception_id'] = $this->reception->id;
    try {
        Opening::updateOrCreate(['id' => $this->id], $validate);
        unset($this->receptions);
        $this->reset(['program_id', 'id', 'meeting']);
        $this->dispatch('refresh');
        Toaster::success('Program berhasil ditambahkan');
    } catch (\Exception $e) {
        Toaster::error('Program gagal ditambahkan');

        Toaster::error($e->getMessage());
    }
};

$destroy = function ($id) {
    try {
        $opening = Opening::find($id);
        $opening->delete();
        unset($this->openings);
        $this->dispatch('refresh');
        Toaster::success('Berhasil menghapus data');
    }catch (\Exception $e){
        Toaster::error('Gagal menghapus data');
    }
};

$edit = function ($id){
    $opening = Opening::find($id);
    $this->id = $opening->id;
    $this->program_id = $opening->program_id;
    $this->meeting = $opening->meeting;
};


?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],
            [
                'text' => 'Pembukaan',
                'href' => route('admin.reception')
            ],[
                'text' => 'Periode' . ' ' . $this->reception->period
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
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white">Pembukaan Program Pembelajaran</h5>
                </x-slot>
                <form wire:submit="store" class="max-w-sm mx-auto">
                    <x-form.input-select main-class="mb-2" id="program_id" name="program_id" wire:model="program_id" size="md" get-data="server" :data="$this->programs" label="Program" :disabled="$this->id"/>
                    <x-form.input type="number" id="meeting" name="meeting" wire:model="meeting" main-class="mb-5" label="Pertemuan" placeholder="Masukan Jumlah Pertemuan"/>
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
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white">Daftar Program Pembelajaran Periode {{ $this->reception->period }}</h5>
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

                <x-table thead="#, Program, Pertemuan, Dibuat">
                    @if($this->openings->count() > 0)
                        @foreach($this->openings as $opening)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $opening->program->name }}
                                </td>
                                <td class="px-6 py-4 text-nowrap">
                                    {{ $opening->meeting ?? 0 }} Pertemuan
                                </td>
                                <td class="px-6 py-4">
                                    {{ $opening->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 space-y-2">
                                    <x-button color="yellow" wire:click="edit({{ $opening->id }})">
                                        Edit
                                    </x-button>
                                    <x-button color="red" wire:click="destroy({{  $opening->id }})" wire:confirm="Yakin?">
                                        Hapus
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4 text-center" colspan="4">
                                Tidak ada data
                            </td>
                        </tr>
                    @endif
                </x-table>
                {{ $this->openings->links('livewire.pagination') }}
            </x-card>
        </div>
    </div>

</div>
