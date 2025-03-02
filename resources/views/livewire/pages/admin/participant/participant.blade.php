<?php

use function Livewire\Volt\{state, layout, computed, usesPagination, mount, updated, on, usesFileUploads};
use App\Models\Program;
use App\Models\Reception;
use App\Models\Participant;
use Masmerise\Toaster\Toaster;

usesPagination();
usesFileUploads();
layout('layouts.app');
state(['reception'])->locked();
state(['reception_id' => '', 'period_id' => '', 'period' => [], 'reception_get' => true, 'periode_temp' => '', 'certificate' => '', 'dataId' => '', 'certificate_status' => false, 'participant' => null]);
state(['full_name','photo','information']);
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

$participants = computed(function () {
    return Participant::with('user.personal_data', 'program')
        ->where('reception_id', $this->reception->id)
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

on(['close-modal' => function () {
    $this->reset(['reception_id', 'period_id', 'period', 'periode_temp','dataId','participant','information','full_name','photo']);
    $this->reception_get = true;
    $this->certificate_status = false;

}, 'open-modal' => function ($participant_id, $status) {
    $this->dataId = $participant_id;
    $this->certificate_status = $status;
}, 'open-modal-loading' => function ($participant_id) {
    $this->participant = Participant::with('user.personal_data', 'program')->where('id', $participant_id)->first();
    $this->full_name = $this->participant->user->personal_data->full_name;
    $this->photo = $this->participant->user->profile_photo_path;
    $this->dispatch('modal-loading-done', id: 'aborted-modal');
}, 'open-modal-period' => function ($id) {
    $this->dispatch('modal-loading-done', id: $id);
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
    $this->participants = Participant::with('user.personal_data', 'program')
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
    $this->dispatch('close-modal', id: 'participant-reception-modal');
};

$uploadCertificate = function () {
    $this->validate([
        'certificate' => 'required|mimetypes:image/jpeg,image/png,application/pdf|max:2048|file',
    ]);

    $participant = Participant::where('id', $this->dataId)->first();
    if ($participant->certificate) {
        \Storage::disk('public')->delete($participant->certificate);
    }
    $path = $this->certificate->store('certificates', 'public');
    $participant->certificate = $path;
    $participant->status = 'complete';
    $participant->save();

    $this->dispatch('close-modal', id: 'upload-certificate-modal');
    $this->dispatch('pond-reset');
    $this->reset(['certificate']);

    Toaster::success('Sertifikat berhasil diupload');
};

$aborted = function () {
    $this->validate([
        'information' => 'required',
    ]);
    $this->participant->status = 'incomplete';
    $this->participant->information = $this->information;
    $this->participant->save();
    $this->dispatch('close-modal', id: 'aborted-modal');
    Toaster::success('Peserta berhasil digugurkan');
}

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
    <x-modal id="participant-reception-modal">
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
            <x-button type="submit" color="blue" wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed" wire:target="find" wire:click="find">
                Tampilkan
            </x-button>
        </x-slot>
    </x-modal>
    <x-modal id="upload-certificate-modal">
        <x-slot name="header">
            <h5 class="text-xl font-medium text-gray-900 dark:text-white">Upload Sertifikat</h5>
        </x-slot>
        <x-slot name="content">
            <div x-data="{ show: @entangle('certificate_status') }">
            <x-alert x-show="show"
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-90"
                     color="warning" value="Sertifikat sudah diupload sebelumnya, anda dapat mengubah sertifikat dengan mengupload sertifikat baru !" />
            </div>
            <x-input-label for="certificate" value="Sertifikat" />
            <x-form.filepond wire:model="certificate" id="certificate" />
            @error('certificate')
                <span class="text-sm text-red-600 dark:text-red-500">{{ $message }}</span>
            @enderror
        </x-slot>
        <x-slot name="footer">
            <x-button color="light" class="mr-2" wire:click="$dispatch('close-modal', {id: 'upload-certificate-modal'})">
                Batal
            </x-button>
            <x-button color="blue" wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed" wire:target="uploadCertificate" wire:click="uploadCertificate">
                Upload
            </x-button>
        </x-slot>
    </x-modal>
    <x-modal id="aborted-modal">
        <x-slot name="header">
            <h5 class="text-xl font-medium text-gray-900 dark:text-white">Pengguguran Peserta</h5>
        </x-slot>
        <x-slot name="content">
            <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-500">
                <div class="flex flex-col pb-3">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <div class="flex-shrink-0">
                            <img class="w-10 h-10 rounded-full"
                                 src="{{ $this->photo ? asset('storage/' . $this->photo) : 'https://ui-avatars.com/api/?name=' . $this->full_name ?? 'Null' }}"
                                 alt="Image Name">
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="mb-1 text-gray-500 text-sm dark:text-gray-400">Nama</dt>
                            <dd class="text-lg font-semibold">{{ $this->full_name ?? 'Null' }}</dd>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col py-3">
                    <x-form.textarea name="information" id="information" label="Keterangan" wire:model="information" placeholder="Alasan Pengguguran" />
                </div>
            </dl>
        </x-slot>
        <x-slot name="footer">
            <x-button color="light" class="mr-2" wire:click="$dispatch('close-modal', {id: 'aborted-modal'})">
                Batal
            </x-button>
            <x-button color="red" wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed" wire:target="aborted" wire:click="aborted">
                Gugurkan
            </x-button>
        </x-slot>
    </x-modal>
    <div class="grid-cols-1 lg:grid-cols-3 grid gap-2 ">
        <div class="col-span-3 ">
            <x-card class="mt-2 w-full ">
                <x-slot name="header">
                    <div>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white">Peserta Pendaftar</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Peserta terdaftar berikut berdasarkan periode aktif {{ \Carbon\Carbon::parse($this->reception->start_course)->locale('id')->isoFormat('DD-MM-YYYY') }} s/d {{ \Carbon\Carbon::parse($this->reception->complete_course)->locale('id')->isoFormat('DD-MM-YYYY') }}</p>
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
                        <x-button wire:click="$dispatch('open-modal-period', { id :'participant-reception-modal'})" size="xs" color="blue">Periode
                            <svg class="inline w-3 h-3 -mt-[5px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M6.94 2c.416 0 .753.324.753.724v1.46c.668-.012 1.417-.012 2.26-.012h4.015c.842 0 1.591 0 2.259.013v-1.46c0-.4.337-.725.753-.725s.753.324.753.724V4.25c1.445.111 2.394.384 3.09 1.055c.698.67.982 1.582 1.097 2.972L22 9H2v-.724c.116-1.39.4-2.302 1.097-2.972s1.645-.944 3.09-1.055V2.724c0-.4.337-.724.753-.724"/><path fill="currentColor" d="M22 14v-2c0-.839-.004-2.335-.017-3H2.01c-.013.665-.01 2.161-.01 3v2c0 3.771 0 5.657 1.172 6.828S6.228 22 10 22h4c3.77 0 5.656 0 6.828-1.172S22 17.772 22 14" opacity="0.5"/><path fill="currentColor" d="M18 17a1 1 0 1 1-2 0a1 1 0 0 1 2 0m0-4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m-5 4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m0-4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m-5 4a1 1 0 1 1-2 0a1 1 0 0 1 2 0m0-4a1 1 0 1 1-2 0a1 1 0 0 1 2 0"/></svg>
                        </x-button>
                        <x-a wire:navigate href="{{ route('admin.participant.absenteeism') }}" color="blue" size="xs">Absensi
                            <svg class="rotate-45 inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/></svg>
                        </x-a>
                    </div>
                </x-slot>
                <x-table thead="#, Nama, Phone, Kelas, level, Order ID, Status, Sertifikat" :action="true">
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
                                <td class="px-6 py-4 text-wrap">
                                    {{ $participant->program->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $participant->level == 1 ? 'Level Dasar (Basic) ' : ($participant->level == 2 ? 'Level Menengah (Intermediate)' : 'Level Mahir (Advanced)') }}
                                </td>
                                <td class="px-6 py-4 text-nowrap">
                                    #{{ $participant->order }}
                                </td>
                                <td class="px-6 py-4 {{ $participant->status == 'proceed' ? 'text-blue-500' : ($participant->status == 'complete' ? 'text-green-500' : ($participant->status == 'incomplete' ? 'text-red-500' : 'text-yellow-500')) }}">
                                    {{ $participant->status == 'proceed' ? 'Proses Kursus' : ($participant->status == 'complete' ? 'Selesai Kursus' : ($participant->status == 'incomplete' ? 'Gugur' : 'Proses Registrasi')) }}
                                </td>
                                <td class="px-6 py-4 text-nowrap">
                                    <x-a size="xs" color="blue" :disabled="$participant->certificate == null" href="{{ $participant->certificate != null ? asset('storage/' . $participant->certificate) : '#' }}" target="{{  $participant->certificate != null ? '_blank' : '' }}">
                                        Lihat
                                        <svg class="rotate-45 inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/></svg>
                                    </x-a>
                                </td>
                                <td class="px-6 py-4 text-nowrap">
                                    <x-button :disabled="$participant->status == 'incomplete' || $participant->status == 'cancel' || $participant->status == 'registration'" size="xs" color="blue-outline" wire:click="$dispatch('open-modal', { id: 'upload-certificate-modal', participant_id: {{ $participant->id }}, status: {{ $participant->certificate != null ? 1 : 0 }} })">
                                        Upload
                                        <svg class=" inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/></svg>
                                    </x-button>
                                    <x-button size="xs" color="red-outline" wire:click="$dispatch('open-modal-loading', { participant_id: {{ $participant->id }} })" :disabled="$participant->certificate != null || $participant->status == 'incomplete' || $participant->status == 'cancel'">
                                        Gugurkan
                                        <svg class=" inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                        </svg>
                                    </x-button>
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4 text-center" colspan="9">
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
