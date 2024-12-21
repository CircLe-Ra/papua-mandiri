<?php

use function Livewire\Volt\{state, layout, computed, on, usesPagination, updated};
use App\Models\Program;
use Masmerise\Toaster\Toaster;

layout('layouts.app');
usesPagination();
state(['name', 'id', 'amount']);
state(['show' => 5, 'search' => null])->url();

    $programs = computed(function () {
        return Program::where('name', 'like', '%' . $this->search . '%')
            ->latest()->paginate($this->show, pageName: 'programs-page');
    });

    on(['refresh' => function () {
        $this->programs = Program::where('name', 'like', '%' . $this->search . '%')
            ->latest()->paginate($this->show, pageName: 'programs-page');
    }]);

    updated(['amount' => function() {
        $rawValue = preg_replace('/[^0-9]/', '', $this->amount);
        $rawValue = (int)$rawValue;
        $this->amount = number_format($rawValue, 0, ',', '.');
    }]);

    $store = function () {
        $this->validate([
            'name' => 'required|unique:programs,name' . ($this->id ? ',' . $this->id : ''),
            'amount' => 'required'
        ]);
        try {
            Program::updateOrCreate(['id' => $this->id], [
                'name' => $this->name,
                'amount' => preg_replace('/[^0-9]/', '', $this->amount)
            ]);
            unset($this->programs);
            $this->reset(['name', 'id','amount']);
            $this->dispatch('refresh');
            Toaster::success('Program berhasil disimpan');
        } catch (\Exception $e) {
            Toaster::error('Program gagal disimpan');
        }
    };

    $destroy = function ($id) {
        try {
        $program = Program::find($id);
        $program->delete();
        unset($this->programs);
        $this->dispatch('refresh');
            Toaster::success('Berhasil menghapus data');
        }catch (\Exception $e){
            Toaster::error('Gagal menghapus data');
        }
    };

    $edit = function ($id){
        $program = Program::find($id);
        $this->id = $program->id;
        $this->name = $program->name;
        $this->amount = $program->amount;
    };


?>

<div>
        <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],
            [
                'text' => 'Master Data'
            ],
            [
                'text' => 'Program'
            ]
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
                    Tambah Program
                </x-slot>
                <form wire:submit="store" class="max-w-sm mx-auto">
                    <x-form.input id="name" name="name" wire:model="name" label="Nama" placeholder="Masukan Nama Program"/>
                    <div x-data="{
                            amount: @entangle('amount') || '0',
                            formatRupiah(value) {
                                value = value || '0';
                                let rawValue = value.replace(/[^\d]/g, '');
                                return rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            },
                            updateFormattedValue(event) {
                                let rawValue = event.target.value.replace(/[^\d]/g, '');
                                this.amount = rawValue;
                                event.target.value = this.formatRupiah(rawValue);
                            }
                        }"
                         x-init="amount = formatRupiah(amount)"
                    >
                    <x-form.input
                        x-model="amount"
                        x-on:input="updateFormattedValue($event)"
                        id="amount" name="amount" wire:model.live="amount" label="Harga" placeholder="Masukan Harga Program"/>
                    </div>
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
                        Daftar Program
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

                <x-table thead="#, Nama, Harga, Dibuat">
                    @if($this->programs->count() > 0)
                        @foreach($this->programs as $program)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $program->name }}
                                </td>
                                <td class="px-6 py-4">
                                    Rp {{ number_format($program->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $program->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-button color="yellow" wire:click="edit({{ $program->id }})">
                                        Edit
                                    </x-button>
                                    <x-button color="red" wire:click="destroy({{$program->id}})" wire:confirm="Yakin?">
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
                {{ $this->programs->links('livewire.pagination') }}
            </x-card>
        </div>
    </div>

</div>
