<?php

use function Livewire\Volt\{state, layout, computed, usesPagination, mount};
use App\Models\Program;
use App\Models\Reception;
use App\Models\Participant;
use Masmerise\Toaster\Toaster;

usesPagination();
layout('layouts.app');
state(['reception' => fn($id) => Reception::where('id', $id)->first()])->locked();
state(['show' => 5, 'search' => null])->url();

$participants = computed(function () {
    return Participant::with('user.personal_data', 'program')
        ->where('reception_id', $this->reception->id)
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
        ->latest()->paginate($this->show, pageName: 'participant-page');
});

?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],
            [
                'text' => 'Peserta Kursus',
                'href' => route('admin.participants')
            ],[
                'text' => 'Peserta Terdaftar'
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
                    Peserta Pendaftar
                    <p class="text-sm text-gray-600 dark:text-gray-300">Peserta terdaftar di periode {{ \Carbon\Carbon::parse($this->reception->start_course)->locale('id')->isoFormat('DD MMMM YYYY') }} sampai {{ \Carbon\Carbon::parse($this->reception->complete_course)->locale('id')->isoFormat('DD MMMM YYYY') }}</p>
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
                        <x-a href="{{ route('admin.participant.absenteeism') }}" color="blue" size="xs">Absensi
                            <svg class="rotate-45 inline -mt-1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/></svg>
                        </x-a>
                    </div>
                </x-slot>

                <x-table thead="#, Nama, Phone, Kelas, level, Order ID, Pembayaran, Total Bayar" :action="false">
                    @if($this->participants->count() > 0)
                        @foreach($this->participants as $key => $participant)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>
                                <th scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                    <img class="w-10 h-10 rounded-full" src="{{ $participant->user->profile_photo_path ? asset('storage/' . $participant->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . $participant->user->personal_data->full_name }}" alt="Image Name">
                                    <div class="ps-3">
                                        <div class="text-base font-semibold">{{ $participant->user->personal_data->full_name }}</div>
                                        <div class="font-normal text-gray-500">{{ $participant->user->email }}</div>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    {{ $participant->user->personal_data->phone }}
                                </td>
                                <td class="px-6 py-4 text-nowrap">
                                    {{ $participant->program->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $participant->level == 1 ? 'Level Dasar (Basic) ' : ($participant->level == 2 ? 'Level Menengah (Intermediate)' : 'Level Mahir (Advanced)') }}
                                </td>
                                <td class="px-6 py-4 text-nowrap">
                                    #{{ $participant->order }}
                                </td>
                                <td class="px-6 py-4 {{ $participant->payment == 'paid' ? 'text-green-500' : 'text-red-500'}}">
                                    {{ $participant->payment == 'paid' ? 'Lunas' : 'Belum Lunas' }}
                                </td>
                                <td class="px-6 py-4 text-nowrap">
                                    Rp. {{ number_format($participant->amount, 2, ',', '.') }}
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
