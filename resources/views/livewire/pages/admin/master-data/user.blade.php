<?php

use function Livewire\Volt\{state, layout, title, computed, on, usesPagination};
use App\Models\User;
use Spatie\Permission\Models\Role;
use Masmerise\Toaster\Toaster;

layout('layouts.app');
title(__('User Management'));

usesPagination();

state(['name' => '', 'email' => '', 'password' => '', 'role_name' => '', 'idData']);
state(['showing' => 5])->url();
state(['search' => null])->url();

$users = computed(function () {
    return User::with('roles')->where('name', 'like', '%' . $this->search . '%')
        ->orWhere('email', 'like', '%' . $this->search . '%')
        ->latest()->paginate($this->showing, pageName: 'user-page');
});

$store = function () {
    $this->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email' . ($this->idData ? ',' . $this->idData : ''),
        'password' => ($this->idData ? 'nullable' : 'required|min:6'),
        'role_name' => 'required|exists:roles,name',
    ]);

    try {
        if ($this->idData) {
            $user = User::find($this->idData);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];
            if ($this->password) {
                $data['password'] = bcrypt($this->password);
            }
            $user->update($data);
            $user->removeRole($user->roles->first()->name);
            $user->assignRole($this->role_name);
            unset($this->users);
            $this->reset(['name', 'email', 'password', 'role_name', 'idData']);
            $this->dispatch('refresh');
            Toaster::success(__('User has been updated'));
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
            ]);
            $user->syncRoles([$this->role_name]);
            unset($this->users);
            $this->reset(['name', 'email', 'password', 'role_name', 'idData']);
            $this->dispatch('refresh');
            Toaster::success(__('User has been created'));
        }
    } catch (\Throwable $th) {
        Toaster::error(__('User could not be created'));
        Toaster::error($th->getMessage());
    }
};

$edit = function ($id) {
    $user = User::find($id);
    $this->idData = $id;
    $this->name = $user->name;
    $this->email = $user->email;
    $this->role_name = $user->roles->first()->name  ?? null;
};

$destroy = function ($id) {
    try {
        $user = User::find($id);
        $user->delete();
        unset($this->users);
        $this->dispatch('refresh');
        Toaster::success(__('User has been deleted'));
    } catch (\Throwable $th) {
        Toaster::error(__('User could not be deleted'));
    }
};

?>

<div>
    <x-breadcrumbs :crumbs="[
        ['text' => __('Dashboard'), 'href' => '/dashboard'],
        ['text' => __('Users'), 'href' => route('admin.master-data.user')]
    ]">
        <x-slot name="actions">
            <x-form.input-icon id="search" name="search" wire:model.live="search" placeholder="Cari..." size="small">
                <x-slot name="icon">
                    <svg class="text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g fill="none">
                            <path fill="currentColor" fill-opacity="0.25" fill-rule="evenodd" d="M12 19a7 7 0 1 0 0-14a7 7 0 0 0 0 14M10.087 7.38A5 5 0 0 1 12 7a.5.5 0 0 0 0-1a6 6 0 0 0-6 6a.5.5 0 0 0 1 0a5 5 0 0 1 3.087-4.62" clip-rule="evenodd"/>
                            <path stroke="currentColor" stroke-linecap="round" d="M20.5 20.5L17 17"/>
                            <circle cx="11" cy="11" r="8.5" stroke="currentColor"/>
                        </g>
                    </svg>
                </x-slot>
            </x-form.input-icon>
        </x-slot>
    </x-breadcrumbs>

    <div class="grid-cols-1 lg:grid-cols-3 grid gap-2">
        <!-- Form Input User -->
        <div class="w-full col-span-3 lg:col-span-1">
            <x-card class="mt-2">
                <x-slot name="header">
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white">{{ __('Tambah User') }}</h5>
                </x-slot>
                <form wire:submit="store" class="max-w-sm mx-auto">
                    <input type="hidden" wire:model="idData">
                    <x-form.input id="name" name="name" wire:model="name" main-class="mb-2" label="Nama" placeholder="Masukkan Nama" />
                    <x-form.input type="email" id="email" name="email" wire:model="email" main-class="mb-2" label="Email" placeholder="Masukkan Email" />
                    <x-form.input type="password" id="password" name="password" wire:model="password" main-class="mb-2" label="Sandi" placeholder="Masukkan Sandi" />

                    <!-- Dropdown untuk memilih role -->
                    <div class="mt-3">
                        <x-form.input-select id="role_name" name="role_name" wire:model="role_name" label="Role">
                            <option value="">{{ __('Pilih Role') }}</option>
                            @foreach (Role::all() as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </x-form.input-select>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <x-button type="reset" color="light">{{ __('Batal') }}</x-button>
                        <x-button type="submit" color="blue" wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed">{{ __('Simpan') }}</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Daftar User -->
        <div class="col-span-2">
            <x-card class="mt-2 w-full">
                <x-slot name="header">
                    <h5 class="text-xl font-medium text-gray-900 dark:text-white">{{ __('Daftar User') }}</h5>
                </x-slot>
                <x-slot name="sideHeader">
                    <x-form.input-select id="show" name="show" wire:model.live="showing" size="xs">
                        <option value="">Semua</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </x-form.input-select>
                </x-slot>

                <x-table thead="No.,Nama,Email,Role,Created At">
                    @if($this->users->count() > 0)
                        @foreach($this->users as $user)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">{{ $user->roles->first()->name ?? __('No Role') }}</td>
                                <td class="px-6 py-4">{{ $user->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-nowrap">
                                    <x-button color="yellow" wire:click="edit({{ $user->id }})">Edit</x-button>
                                    <x-button color="red" wire:click="destroy({{ $user->id }})" wire:confirm="Yakin?">Hapus</x-button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4 text-center" colspan="6">{{ __('Tidak ada data') }}</td>
                        </tr>
                    @endif
                </x-table>
                {{ $this->users->links('livewire.pagination') }}
            </x-card>
        </div>
    </div>
</div>

