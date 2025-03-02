<?php

use function Livewire\Volt\{state, layout, computed, on, usesPagination, updated, mount, boot};
use App\Models\Program;
use Masmerise\Toaster\Toaster;
use App\Models\Participant;
use App\Models\StepTemp;
use App\Models\PersonalData;
use App\Models\Regency;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\Province;
use App\Models\Reception;
use App\Models\Religion;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Snap;

layout('layouts.app');
usesPagination();
state(['personal_data_id', 'participant_id', 'ktp_number', 'full_name', 'gender', 'birth_date', 'birth_place', 'address', 'rt', 'rw', 'sub_district_id', 'district_id', 'regency_id', 'province_id', 'marital_status', 'occupation', 'religion_id', 'nationality', 'blood_type', 'program_id', 'level', 'start_course', 'complete_course', 'snapToken', 'phone']);
state(['cities' => [], 'districts' => [], 'sub_districts' => [], 'step' => null, 'reception' => null, 'participant' => [], 'program' => null]);
boot(function () {
    Config::$serverKey = config('services.midtrans.serverKey');
    Config::$isProduction = config('services.midtrans.isProduction');
    Config::$isSanitized = config()['services.midtrans.isSanitized'];
    Config::$is3ds = config(['services.midtrans.is3ds']);
});
mount(function () {
    $this->reception = Reception::where('status', 'active')->first();
    if ($this->reception == null) {
        return $this->redirect(route('information'), navigate: true);
    }
    $this->start_course = $this->reception->start_course ?? '0000-00-00';
    $this->complete_course = $this->reception->complete_course ?? '0000-00-00';
    $this->participant = Participant::where('user_id', auth()->user()->id)->where('reception_id', $this->reception->id)->first();
    $this->program_id = $this->participant->program_id ?? null;
    $this->level = $this->participant->level ?? null;
    $this->snapToken = $this->participant->snap_token ?? null;
    if ($this->participant != null) {
        $this->program = Program::where('id', $this->participant->program_id)->first();
    }
    $this->step = StepTemp::where('user_id', auth()->user()->id)->where('reception_id', $this->reception->id)->first()->step ?? 1;
    $data = PersonalData::where('user_id', auth()->user()->id)->first();
    $this->personal_data_id = $data->id ?? '';
    $this->ktp_number = $data->ktp_number ?? '';
    $this->full_name = $data->full_name ?? '';
    $this->gender = $data->gender ?? '';
    $this->birth_date = $data->birth_date ?? '';
    $this->birth_place = $data->birth_place ?? '';
    $this->address = $data->address ?? '';
    $this->rt = $data->rt ?? '';
    $this->rw = $data->rw ?? '';
    $this->sub_district_id = $data->sub_district_id ?? '';
    $this->district_id = $data->district_id ?? '';
    $this->regency_id = $data->regency_id ?? '';
    $this->province_id = $data->province_id ?? '';
    $this->marital_status = $data->marital_status ?? '';
    $this->occupation = $data->occupation ?? '';
    $this->religion_id = $data->religion_id ?? '';
    $this->nationality = $data->nationality ?? '';
    $this->blood_type = $data->blood_type ?? '';
    $this->phone = $data->phone ?? '';
    $this->province_id ? $this->cities = Regency::where('province_id', $this->province_id)->get() : $this->cities = [];
    $this->regency_id ? $this->districts = District::where('regency_id', $this->regency_id)->get() : $this->districts = [];
    $this->district_id ? $this->sub_districts = SubDistrict::where('district_id', $this->district_id)->get() : $this->sub_districts = [];
});

$provinces = computed(function () {
    return Province::all();
});

$religions = computed(function () {
    return Religion::all();
});

$programs = computed(function () {
    return Program::whereHas('openings', function ($query) {
        $query->where('reception_id', $this->reception->id);
    })->get();
});

on(['refresh' => function () {
},'getPersonalData' => function () {
        $data = PersonalData::where('user_id', auth()->user()->id)->first();
        $this->personal_data_id = $data->id;
        $this->ktp_number = $data->ktp_number;
        $this->full_name = $data->full_name;
        $this->gender = $data->gender;
        $this->birth_date = $data->birth_date;
        $this->birth_place = $data->birth_place;
        $this->address = $data->address;
        $this->rt = $data->rt;
        $this->rw = $data->rw;
        $this->sub_district_id = $data->sub_district_id;
        $this->district_id = $data->district_id;
        $this->regency_id = $data->regency_id;
        $this->province_id = $data->province_id;
        $this->marital_status = $data->marital_status;
        $this->occupation = $data->occupation;
        $this->religion_id = $data->religion_id;
        $this->nationality = $data->nationality;
        $this->blood_type = $data->blood_type;
    },
    'getDataParticipant' => function ($id) {
        $this->participant = Participant::find($id);
        $this->program = Program::find($this->participant->program_id);
        $this->program_id = $this->participant->program_id;
        $this->level = $this->participant->level;
    },
    'print' => function () {
        if ($this->step < 5) {
            $this->step++;
            StepTemp::updateOrCreate(['user_id' => auth()->user()->id, 'reception_id' => $this->reception->id], ['step' => $this->step]);
        }
        $this->dispatch('print-registration');
    }
]);

updated(['province_id' => function () {
    $this->cities = Regency::where('province_id', $this->province_id)->get();
    $this->districts = [];
    $this->sub_districts = [];
}, 'regency_id' => function () {
    $this->districts = District::where('regency_id', $this->regency_id)->get();
    $this->sub_districts = [];
}, 'district_id' => function () {
    $this->sub_districts = SubDistrict::where('district_id', $this->district_id)->get();
}]);

$nextStep = function () {
    switch ($this->step) {
        case 1:
            $validate = $this->validate([
                'ktp_number' => 'required_if:step,1',
                'full_name' => 'required_if:step,1',
                'gender' => 'required_if:step,1',
                'birth_date' => 'required_if:step,1',
                'birth_place' => 'required_if:step,1',
                'address' => 'required_if:step,1',
                'rt' => 'required_if:step,1',
                'rw' => 'required_if:step,1',
                'sub_district_id' => 'required_if:step,1',
                'district_id' => 'required_if:step,1',
                'regency_id' => 'required_if:step,1',
                'province_id' => 'required_if:step,1',
                'marital_status' => 'required_if:step,1',
                'occupation' => 'required_if:step,1',
                'religion_id' => 'required_if:step,1',
                'nationality' => 'required_if:step,1',
                'blood_type' => 'nullable',
            ]);
            $validate['user_id'] = auth()->user()->id;
            try {
                $data = PersonalData::updateOrCreate(['user_id' => auth()->user()->id], $validate);
                $this->dispatch('getPersonalData');
                if ($this->step < 5) {
                    $this->step++;
                    StepTemp::updateOrCreate(['user_id' => auth()->user()->id, 'reception_id' => $this->reception->id], ['step' => $this->step]);

                }
            } catch (\Exception $e) {
                Toaster::error($e->getMessage());
            }
            break;
        case 2:
            $validate = $this->validate([
                'program_id' => 'required_if:step,2',
                'level' => 'required_if:step,2',
            ]);
            $validate['user_id'] = auth()->user()->id;
            $validate['reception_id'] = $this->reception->id;
            try {
                $data = Participant::updateOrCreate(['user_id' => auth()->user()->id, 'reception_id' => $this->reception->id], $validate);
                $this->dispatch('getDataParticipant', $data->id);
                if ($this->step < 5) {
                    $this->step++;
                    StepTemp::updateOrCreate(['user_id' => auth()->user()->id, 'reception_id' => $this->reception->id], ['step' => $this->step]);
                }
            } catch (\Exception $e) {
                Toaster::error($e->getMessage());
            }
            break;
        case 3:
            if ($this->step < 5) {
                $this->step++;
                StepTemp::updateOrCreate(['user_id' => auth()->user()->id, 'reception_id' => $this->reception->id], ['step' => $this->step]);
            }
            break;
    }

};

$prevStep = function () {
    if ($this->step > 1) {
        $this->step--;
        StepTemp::updateOrCreate(['user_id' => auth()->user()->id, 'reception_id' => $this->reception->id], ['step' => $this->step]);
    }
    if ($this->step == 1) {
        $this->dispatch('getPersonalData');
    }
};

$pay = function () {
    $validator = \Illuminate\Support\Facades\Validator::make(['phone' => $this->phone], [
        'phone' => ['required', 'numeric', 'regex:/^\d{10,15}$/']
    ]);
    if ($validator->fails()) {
        Toaster::error($validator->errors()->first('phone'));
        return;
    }

    try {
        PersonalData::updateOrCreate(['id' => $this->personal_data_id], [
            'phone' => $this->phone
        ]);
        Participant::updateOrCreate(['id' => $this->participant->id], [
            'order' => 'ORDER' . '-' . $this->reception->id . auth()->user()->id . Carbon::now()->format('dmy')
        ]);
    } catch (\Exception $e) {
        Toaster::error($e->getMessage());
    }

    $payload = [
        'transaction_details' => [
            'order_id' => 'ORDER' . '-' . $this->reception->id . auth()->user()->id . Carbon::now()->format('dmy') . '-' . rand(1000,9999),
            'gross_amount' => (int)$this->program->amount,
        ],
        'customer_details' => [
            'name' => $this->full_name,
            'email' => Auth::user()->email,
            'phone' => $this->phone
        ],
        'item_details' => [
            [
                'id' => $this->program_id,
                'price' => (int)$this->program->amount,
                'quantity' => 1,
                'name' => $this->program->name
            ]
        ],
    ];
    if ($this->participant != null) {
        if ($this->participant->snap_token == null) {
            $snapToken = Snap::getSnapToken($payload);
            $this->participant->snap_token = $snapToken;
            $this->participant->save();
        } else {
            $snapToken = $this->participant->snap_token;
        }
    $this->dispatch('snap-token', snap: $snapToken, id: $this->participant->id);
    }
}


?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],
            [
                'text' => 'Pendaftaran Peserta'
            ],
        ]">
    </x-breadcrumbs>
    @if($this->reception)
        @if($this->start_course > now())
            <div class="grid-cols-1 lg:grid-cols-3 grid gap-2 ">
                <div class="w-full col-span-3 lg:col-span-1">
                    <x-card class="mt-2">
                        <ol class="relative text-gray-500 border-s border-gray-200 dark:border-gray-700 dark:text-gray-400">
                            <li class="mb-10 ms-6">
                                @if($this->step <= 1)
                                     <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-200 rounded-full -start-4 ring-4 ring-blue-300 dark:ring-gray-900 dark:bg-gray-700">
                                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-gray-400" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                                            <path
                                                d="M18 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2ZM6.5 3a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3.014 13.021l.157-.625A3.427 3.427 0 0 1 6.5 9.571a3.426 3.426 0 0 1 3.322 2.805l.159.622-6.967.023ZM16 12h-3a1 1 0 0 1 0-2h3a1 1 0 0 1 0 2Zm0-3h-3a1 1 0 1 1 0-2h3a1 1 0 1 1 0 2Zm0-3h-3a1 1 0 1 1 0-2h3a1 1 0 1 1 0 2Z"/>
                                        </svg>
                                    </span>
                                @else
                                    <span class="absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -start-4 ring-4 ring-green-300 dark:ring-gray-900 dark:bg-green-900">
                                        <svg class="w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                        </svg>
                                    </span>
                                @endif
                                <h3 class="font-medium leading-tight">Data Diri</h3>
                                <p class="text-sm">Lengkai data pribadi sesuai KTP</p>
                            </li>
                            <li class="mb-10 ms-6">
                                @if($this->step <= 2)
                                    <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-200 rounded-full -start-4 ring-4 ring-blue-300 dark:ring-gray-900 dark:bg-gray-700">
                                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M1.637 1.637C.732 1.637 0 2.369 0 3.273v17.454c0 .904.732 1.636 1.637 1.636h20.726c.905 0 1.637-.732 1.637-1.636V3.273c0-.904-.732-1.636-1.637-1.636zm.545 2.181h19.636v16.364h-2.726v-1.09h-4.91v1.09h-12zM12 8.182a1.636 1.636 0 1 0 0 3.273a1.636 1.636 0 1 0 0-3.273m-4.363 1.91c-.678 0-1.229.55-1.229 1.226a1.228 1.228 0 0 0 2.455 0c0-.677-.549-1.226-1.226-1.226m8.726 0a1.227 1.227 0 1 0 0 2.453a1.227 1.227 0 0 0 0-2.453M12 12.545c-1.179 0-2.413.401-3.148 1.006a4.1 4.1 0 0 0-1.215-.188c-1.314 0-2.729.695-2.729 1.559v.896h14.184v-.896c0-.864-1.415-1.559-2.729-1.559c-.41 0-.83.068-1.215.188c-.735-.605-1.969-1.006-3.148-1.006"/></svg>
                                    </span>
                                @else
                                    <span
                                        class="absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -start-4 ring-4 ring-green-300 dark:ring-gray-900 dark:bg-green-900">
                                        <svg class="w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                        </svg>
                                    </span>
                                @endif
                                <h3 class="font-medium leading-tight">Kelas Belajar</h3>
                                <p class="text-sm">Pilih kelas belajar yang diinginkan</p>
                            </li>
                            <li class="mb-10 ms-6">
                                @if($this->step <= 3)
                                    <span
                                        class="absolute flex items-center justify-center w-8 h-8 bg-blue-200 rounded-full -start-4 ring-4 ring-blue-300 dark:ring-gray-900 dark:bg-gray-700">
                                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill="currentColor" d="M4.75 4A2.75 2.75 0 0 0 2 6.75V8h16V6.75A2.75 2.75 0 0 0 15.25 4zM18 9H2v4.25A2.75 2.75 0 0 0 4.75 16h10.5A2.75 2.75 0 0 0 18 13.25zm-4.5 4h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1"/></svg>
                                    </span>
                                @else
                                    <span
                                        class="absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -start-4 ring-4 ring-green-300 dark:ring-gray-900 dark:bg-green-900">
                                        <svg class="w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                        </svg>
                                    </span>
                                @endif
                                <h3 class="font-medium leading-tight">Pembayaran</h3>
                                <p class="text-sm">Lakukan pembayaran</p>
                            </li>
                            <li class="ms-6">
                                @if($this->step <= 4)
                                    <span
                                        class="absolute flex items-center justify-center w-8 h-8 bg-blue-200 rounded-full -start-4 ring-4 ring-blue-300 dark:ring-gray-900 dark:bg-gray-700">
                                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-gray-400" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z"/>
                                        </svg>
                                    </span>
                                @else
                                    <span
                                        class="absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -start-4 ring-4 ring-green-300 dark:ring-gray-900 dark:bg-green-900">
                                        <svg class="w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                                        </svg>
                                    </span>
                                @endif
                                <h3 class="font-medium leading-tight">Selesai</h3>
                                <p class="text-sm">Cetak bukti pendaftaran</p>
                            </li>
                        </ol>
                    </x-card>
                </div>
                <div class="col-span-2">
                    <x-card class="mt-2 w-full ">
                            <form>
                                <x-slot name="header" class=" -mt-3">
                                    @if ($step == 1)
                                        <div class="w-full">
                                            <h5 class="text-xl font-medium text-gray-900 dark:text-white">Data Diri</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Lengkapi Data Diri sesuai dengan KTP anda.</p>
                                        </div>
                                        <div class="flex items-center space-x-2 justify-end w-full mb-2">
                                            <button type="submit" class="" wire:click="nextStep">
                                                <p class="text-sm text-gray-600 dark:text-gray-300 inline">Kelas
                                                    Belajar</p>
                                                <svg class="rotate-180 inline -mt-1 dark:text-gray-300 text-gray-600" xmlns="http://www.w3.org/2000/svg"
                                                     width="24" height="24" viewBox="0 0 24 24">
                                                    <path fill="currentColor" d="m7.825 13l4.9 4.9q.3.3.288.7t-.313.7q-.3.275-.7.288t-.7-.288l-6.6-6.6q-.15-.15-.213-.325T4.426 12t.063-.375t.212-.325l6.6-6.6q.275-.275.688-.275t.712.275q.3.3.3.713t-.3.712L7.825 11H19q.425 0 .713.288T20 12t-.288.713T19 13z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @elseif ($step == 2)
                                        <div class="w-full">
                                            <div class="flex items-center space-x-2 justify-between w-full mb-2">
                                                <button type="submit" class="dark:text-gray-300 text-gray-600" wire:click="prevStep">
                                                    <svg class=" inline -mt-1" xmlns="http://www.w3.org/2000/svg" width="24"
                                                         height="24" viewBox="0 0 24 24">
                                                        <path fill="currentColor"
                                                              d="m7.825 13l4.9 4.9q.3.3.288.7t-.313.7q-.3.275-.7.288t-.7-.288l-6.6-6.6q-.15-.15-.213-.325T4.426 12t.063-.375t.212-.325l6.6-6.6q.275-.275.688-.275t.712.275q.3.3.3.713t-.3.712L7.825 11H19q.425 0 .713.288T20 12t-.288.713T19 13z"/>
                                                    </svg>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 inline">Data Diri</p>
                                                </button>
                                                <button type="submit" class="dark:text-gray-300 text-gray-600" wire:click="nextStep">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 inline">
                                                        Pembayaran</p>
                                                    <svg class="rotate-180 inline -mt-1" xmlns="http://www.w3.org/2000/svg"
                                                         width="24" height="24" viewBox="0 0 24 24">
                                                        <path fill="currentColor"
                                                              d="m7.825 13l4.9 4.9q.3.3.288.7t-.313.7q-.3.275-.7.288t-.7-.288l-6.6-6.6q-.15-.15-.213-.325T4.426 12t.063-.375t.212-.325l6.6-6.6q.275-.275.688-.275t.712.275q.3.3.3.713t-.3.712L7.825 11H19q.425 0 .713.288T20 12t-.288.713T19 13z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="">
                                                <h5 class="text-xl font-medium text-gray-900 dark:text-white">Kelas Belajar</h5>
                                                <p class="text-sm text-gray-600 dark:text-gray-300">Silahkah pilih kelas belajar
                                                    yang anda inginkan.</p>
                                            </div>
                                        </div>
                                    @elseif ($step == 3)
                                        <div class="w-full">
                                            <div class="flex items-center space-x-2 justify-between w-full mb-2">
                                                <button type="submit" class="dark:text-gray-300 text-gray-600" wire:click="prevStep">
                                                    <svg class=" inline -mt-1" xmlns="http://www.w3.org/2000/svg" width="24"
                                                         height="24" viewBox="0 0 24 24">
                                                        <path fill="currentColor"
                                                              d="m7.825 13l4.9 4.9q.3.3.288.7t-.313.7q-.3.275-.7.288t-.7-.288l-6.6-6.6q-.15-.15-.213-.325T4.426 12t.063-.375t.212-.325l6.6-6.6q.275-.275.688-.275t.712.275q.3.3.3.713t-.3.712L7.825 11H19q.425 0 .713.288T20 12t-.288.713T19 13z"/>
                                                    </svg>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 inline">Kelas
                                                        Belajar</p>
                                                </button>
                                                <button type="submit"
                                                        class="{{ ($this->participant->payment ?? 'unpaid') == 'unpaid' ? 'opacity-50 cursor-not-allowed dark:text-gray-300 text-gray-600' : 'dark:text-gray-300 text-gray-600' }}"
                                                        wire:click="nextStep"@disabled(!$this->participant || $this->participant->payment == 'unpaid')>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 inline">
                                                        Selesai</p>
                                                    <svg class="rotate-180 inline -mt-1" xmlns="http://www.w3.org/2000/svg"
                                                         width="24" height="24" viewBox="0 0 24 24">
                                                        <path fill="currentColor"
                                                              d="m7.825 13l4.9 4.9q.3.3.288.7t-.313.7q-.3.275-.7.288t-.7-.288l-6.6-6.6q-.15-.15-.213-.325T4.426 12t.063-.375t.212-.325l6.6-6.6q.275-.275.688-.275t.712.275q.3.3.3.713t-.3.712L7.825 11H19q.425 0 .713.288T20 12t-.288.713T19 13z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div>

                                                <h5 class="text-xl font-medium text-gray-900 dark:text-white">Pembayaran</h5>
                                                <p class="text-sm text-gray-600 dark:text-gray-300">Silahkan pilih metode
                                                    pembayaran.</p>
                                            </div>
                                        </div>
                                    @elseif ($step == 4 || $step == 5)
                                        <div>
                                            <h5 class="text-xl font-medium text-gray-900 dark:text-white">Pendaftaran Selesai</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Selamat anda berhasil melakukan pendaftaran.</p>
                                        </div>
                                        <div class="flex items-center space-x-2 justify-end w-1/2 mb-2">
                                            <x-button color="light" wire:click="dispatch('print')" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-xs px-4 py-2  dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                                <svg class="inline -mt-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M18 7H6V3h12zm0 5.5q.425 0 .713-.288T19 11.5t-.288-.712T18 10.5t-.712.288T17 11.5t.288.713t.712.287M16 19v-4H8v4zm2 2H6v-4H2v-6q0-1.275.875-2.137T5 8h14q1.275 0 2.138.863T22 11v6h-4z"/></svg>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 inline">Cetak Bukti</p>
                                            </x-button>
                                        </div>
                                    @endif
                                </x-slot>
                                <div x-data="{ step: $wire.entangle('step').live }" class="mb-3">
                                    <div x-show="step == 1"
                                         @if($this->step > 1) x-cloak @endif
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-90"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-300"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-90">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2 mb-2">
                                            <x-form.input label="Nomor KTP" id="ktp_number" name="ktp_number" required
                                                          maxlength="16" wire:model="ktp_number"/>
                                            <x-form.input label="Nama Lengkap" id="full_name" name="full_name" required
                                                          wire:model="full_name"/>
                                            <x-form.input-select label="Jenis Kelamin" id="gender" name="gender" size="md"
                                                                 mainClass="sm:col-span-2 xl:col-span-1" required
                                                                 wire:model="gender">
                                                <option value="">Pilih?</option>
                                                <option value="M">Laki-laki</option>
                                                <option value="F">Perempuan</option>
                                            </x-form.input-select>
                                        </div>
                                        <div class="justify-between flex space-x-2 mb-2">
                                            <x-form.input label="Tanggal Lahir" id="birth_date" name="birth_date"
                                                          type="date" required main-class="w-full" wire:model="birth_date"/>
                                            <x-form.input label="Tempat Lahir" id="birth_place" name="birth_place" required
                                                          main-class="w-full" wire:model="birth_place"/>
                                        </div>
                                        <div class="mb-2">
                                            <x-form.input label="Alamat" id="address" name="address" required
                                                          main-class="w-full" wire:model="address"/>
                                        </div>
                                        <div class=" flex justify-between space-x-2 mb-2">
                                            <x-form.input label="RT" id="rt" name="rt" required main-class="w-full"
                                                          wire:model="rt"/>
                                            <x-form.input label="RW" id="rw" name="rw" required main-class="w-full"
                                                          wire:model="rw"/>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-2 mb-2">
                                            <x-form.input-select label="Provinsi" id="province_id" name="province_id"
                                                                 get-data="server" :data="$this->provinces" required
                                                                 wire:model.live="province_id"/>
                                            <x-form.input-select label="Kota" id="regency_id" name="regency_id" get-data="server"
                                                                 required wire:model.live="regency_id"
                                                                 :data="$this->cities"/>
                                            <x-form.input-select label="Kecamatan" id="district_id" name="district_id" required
                                                                 wire:model.live="district_id" get-data="server"
                                                                 :data="$this->districts"/>
                                            <x-form.input-select label="Kelurahan" id="sub_district_id" name="sub_district_id"
                                                                 required wire:model.live="sub_district_id"
                                                                 get-data="server" :data="$this->sub_districts"/>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2 mb-2">
                                            <x-form.input label="Status Perkawinan" id="marital_status"
                                                          name="marital_status" required wire:model="marital_status"/>
                                            <x-form.input label="Pekerjaan" id="occupation" name="occupation" required
                                                          wire:model="occupation"/>
                                            <x-form.input-select label="Agama" id="religion_id" name="religion_id"
                                                                 mainClass="sm:col-span-2 xl:col-span-1" get-data="server"
                                                                 :data="$this->religions" required
                                                                 wire:model.live="religion_id"/>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 ">
                                            <x-form.input label="Kewarganegaraan" id="nationality" name="nationality"
                                                          required main-class="w-full" wire:model="nationality"/>
                                            <x-form.input-select label="Golongan Darah" id="blood_type" name="blood_type"
                                                                 size="md" main-class="w-full" wire:model="blood_type">
                                                <option value="">Pilih?</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="AB">AB</option>
                                                <option value="O">O</option>
                                            </x-form.input-select>
                                        </div>
                                    </div>
                                    <div x-show="step == 2"
                                         @if($this->step < 1 || $this->step > 2) x-cloak @endif
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-90"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-300"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-90">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <x-form.input label="Mulai Kursus" type="date" id="start_course"
                                                          name="start_course" wire:model="start_course" :disabled="true"/>
                                            <x-form.input label="Selesai Kursus" type="date" id="complete_course"
                                                          name="complete_course" required wire:model="complete_course"
                                                          :disabled="true"/>
                                        </div>
                                        <div class="justify-between flex space-x-2 mt-2">
                                            <x-form.input-select label="Program Belajar" id="program_id" name="program_id"
                                                                 type="date" :required="$this->step === 2"
                                                                 main-class="w-full" wire:model="program_id"
                                                                 get-data="server" :data="$this->programs"/>
                                            <x-form.input-select label="Level Pembelajaran" id="level" name="level"
                                                                 :required="$this->step === 2" main-class="w-full"
                                                                 wire:model="level">
                                                <option value="" selected>Pilih?</option>
                                                <option value="1">Level Dasar (Basic)</option>
                                                <option value="2">Level Menengah (Intermediate)</option>
                                                <option value="3">Level Mahir (Advanced)</option>
                                            </x-form.input-select>
                                        </div>
                                    </div>
                                    <div x-show="step == 3"
                                         @if($this->step < 2 || $this->step > 3) x-cloak @endif
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-90"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-300"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-90"
                                    >
                                        <div class="justify-between flex">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 ">
                                                    #{{ 'ORDER' . '-' . $this->reception->id . auth()->user()->id . \Carbon\Carbon::now()->format('dmy') }}</p>
                                                <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white ">
                                                    {{ $this->program->name ?? '' }}
                                                </h1>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 flex justify-end">Total
                                                    Pembayaran</p>
                                                <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-white flex justify-end">
                                                    RP. {{ number_format($this->program->amount ?? 0, 2, ',', '.') }}
                                                </h1>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-2">
                                            <div
                                                class=" bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-2 rounded-lg">
                                                <p class="text-sm text-gray-600 dark:text-gray-300 font-bold flex justify-center mt-2">
                                                    Data Personal</p>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">Nama</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $this->full_name }}</p>
                                                </div>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">Email</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ auth()->user()->email }}</p>
                                                </div>
                                                <div
                                                    class="border-t-2 border-gray-300 dark:border-gray-700 mt-4 mb-2"></div>
                                                <div class="flex justify-between ">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Nomor
                                                        Handphone</p>
                                                    <input type="number" id="phone" name="phone" wire:model="phone"
                                                           class="text-gray-900 border border-gray-300 rounded-lg bg-gray-50focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 p-2 text-xs">
                                                </div>

                                            </div>
                                            <div
                                                class=" bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-2 rounded-lg">
                                                <p class="text-sm text-gray-600 dark:text-gray-300 font-bold flex justify-center mt-2">
                                                    Detail Pembayaran</p>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">Kelas/Program</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $this->program->name ?? '' }}</p>
                                                </div>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">Level
                                                        Pembelajaran</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $this->level == 1 ? 'Level Dasar (Basic) ' : ($this->level == 2 ? 'Level Menengah (Intermediate)' : 'Level Mahir (Advanced)') }}</p>
                                                </div>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">Periode</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($this->start_course)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                                        - {{ \Carbon\Carbon::parse($this->complete_course)->locale('id')->isoFormat('DD MMMM YYYY') }}</p>
                                                </div>
                                            </div>
                                            @if(!$this->participant || $this->participant->payment == 'unpaid' || $this->participant->payment === null)
                                                <button wire:click="pay" wire:loading.attr="disabled"  type="button"
                                                        class="xl:col-span-2 w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition duration-200 items-center">
                                                    <div class="relative w-full" wire:loading wire:target="pay">
                                                        <span class="bg-blue-200 text-sm font-medium text-blue-800 text-center -mt-1 p-1 leading-none rounded-full px-2 dark:bg-blue-900 dark:text-blue-200 absolute -translate-y-1/2 -translate-x-1/2 top-2/4 left-1/2 animate-pulse" >Loading...</span>
                                                    </div>
                                                    <div wire:loading.attr="hidden" wire:target="pay">
                                                        Bayar Sekarang
                                                    </div>
                                                </button>
                                            @endif
                                        </div>

                                    </div>
                                    <div x-show="step == 4 || step == 5"
                                         @if($this->step < 3 || $this->step > 5) x-cloak @endif
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-90"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-300"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-90"
                                         id="print-area"
                                    >
                                        <div class="hidden print:block">
                                            <div class="flex justify-center">
                                                <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white print:text-gray-900">Bukti Pembayaran</h1>
                                            </div>
                                        </div>

                                        <div class="justify-between flex">
                                            <div>
                                                <p class="text-sm text-gray-950 dark:text-gray-300 ">Program Belajar</p>
                                                <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white ">
                                                    {{ $this->program->name ?? '' }}
                                                </h1>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-950 dark:text-gray-300 flex justify-end ">Periode Belajar</p>
                                                <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white print:text-gray-900">
                                                    {{ Carbon::parse($this->start_course)->locale('id')->isoFormat('DD MMMM YYYY') }} s/d {{ Carbon::parse($this->complete_course)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                                </h1>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-2">
                                            <div
                                                class="print:mb-5 bg-white dark:bg-gray-800 border border-gray-500 dark:border-gray-700 p-2 rounded-lg">
                                                <p class="text-sm text-gray-950 dark:text-gray-300 font-bold flex justify-center mt-2">
                                                    Data Personal</p>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="hidden print:block">
                                                    <div class="flex justify-between">
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">Nomor KTP</p>
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->ktp_number }}</p>
                                                    </div>
                                                </div>
                                                <div class="hidden print:block border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-950 dark:text-gray-300">Nama</p>
                                                    <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->full_name }}</p>
                                                </div>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-950 dark:text-gray-300">Email</p>
                                                    <p class="text-sm text-gray-950 dark:text-gray-300">{{ auth()->user()->email }}</p>
                                                </div>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 mt-4 mb-2"></div>
                                                <div class="flex justify-between ">
                                                    <p class="text-sm text-gray-950 dark:text-gray-300 mt-2">Nomor Handphone</p>
                                                    <p class="text-sm text-gray-950 dark:text-gray-300 mt-2">{{ $this->phone }}</p>
                                                </div>
                                                <div class="hidden print:block">
                                                    <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                    <div class="flex justify-between">
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">Jenis Kelamin</p>
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->gender == 'M' ? 'Laki-Laki' : 'Perempuan' }}</p>
                                                    </div>
                                                    <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                    <div class="flex justify-between">
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">Tempat Tanggal Lahir</p>
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->birth_place }}, {{ Carbon::parse($this->birth_date)->locale('id')->isoFormat('DD MMMM YYYY') }}</p>
                                                    </div>
                                                    <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                    <div class="flex justify-between">
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">Alamat</p>
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->address }}</p>
                                                    </div>
                                                    <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                    <div class="flex justify-between">
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">Pekerjaan</p>
                                                        <p class="text-sm text-gray-950 dark:text-gray-300">{{ $this->occupation }}</p>
                                                    </div>
                                                </div>

                                            </div>
                                            <div
                                                class=" bg-white dark:bg-gray-800 border border-gray-500 dark:border-gray-700 p-2 rounded-lg">
                                                <p class="text-sm text-gray-950 dark:text-gray-300 font-bold flex justify-center mt-2">
                                                    Status Pembayaran</p>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-950 dark:text-gray-300">Order ID</p>
                                                    <p class="text-sm text-gray-950 dark:text-gray-300">#{{ $this->participant->order ?? '' }}</p>
                                                </div>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-950 dark:text-gray-300">Pembayaran</p>
                                                    <p class="text-sm text-green-500 dark:text-gray-300  px-3">{{ ($this->participant->payment ?? 'unpaid') == 'unpaid' ? 'Belum Lunas' : 'Lunas' }}</p>
                                                </div>
                                                <div class="border-t-2 border-gray-300 dark:border-gray-700 my-4"></div>
                                                <div class="flex justify-between">
                                                    <p class="text-sm text-gray-950 dark:text-gray-300">Total Bayar</p>
                                                    <p class="text-sm text-gray-950 dark:text-gray-300">Rp.{{ number_format($this->participant->amount ?? 0, 2, ',', '.') }}</p>
                                                </div>

                                            </div>
                                            <p class="text-sm text-gray-950 dark:text-gray-300 text-justify flex justify-center mt-2 xl:col-span-2">
                                                {{ $this->full_name }}, Selamat anda telah terdaftar di kelas belajar dengan program {{ $this->program->name ?? '' }}. Silahkan cetak bukti pembayaran anda, dan lampirkan bukti pembayaran tersebut ke panitia untuk mengkonfirmasi keikutsertaan anda.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                    </x-card>
                </div>
            </div>
        @elseif($this->start_course <= now() && $this->complete_course > now())
            <div
                class="relative mt-2 border-2 border-dashed border-gray-400 dark:border-gray-600 rounded-lg pb-[43.10%] w-full">
                <div class="absolute top-0 left-0 right-0 bottom-0 flex justify-center items-center">
                    <div class="text-center font-bold">
                        <svg class="w-full" xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                             viewBox="0 0 36 36">
                            <path fill="#ffe8b6"
                                  d="M21 18c0-2.001 3.246-3.369 5-6c2-3 2-10 2-10H8s0 7 2 10c1.754 2.631 5 3.999 5 6s-3.246 3.369-5 6c-2 3-2 10-2 10h20s0-7-2-10c-1.754-2.631-5-3.999-5-6"/>
                            <path fill="#ffac33"
                                  d="M18 2h-8s0 4 1 7c1.304 3.912 6 4.999 6 9s0 13 1 13s1-9 1-13s4.697-5.088 6-9c1-3 1-7 1-7z"/>
                            <path fill="#3b88c3"
                                  d="M30 34a2 2 0 0 1-2 2H8a2 2 0 0 1 0-4h20a2 2 0 0 1 2 2m0-32a2 2 0 0 1-2 2H8a2 2 0 0 1 0-4h20a2 2 0 0 1 2 2"/>
                        </svg>
                        <p class="mt-2 text-gray-950 dark:text-white">Kursus sedang berlangsung saat ini.</p>
                    </div>
                </div>
            </div>
        @else
            <div
                class="relative mt-2 border-2 border-dashed border-gray-400 dark:border-gray-600 rounded-lg pb-[43.10%] w-full">
                <div class="absolute top-0 left-0 right-0 bottom-0 flex justify-center items-center">
                    <div class="text-center font-bold">
                        <svg class="w-full" xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                             viewBox="0 0 36 36">
                            <path fill="#ffe8b6"
                                  d="M21 18c0-2.001 3.246-3.369 5-6c2-3 2-10 2-10H8s0 7 2 10c1.754 2.631 5 3.999 5 6s-3.246 3.369-5 6c-2 3-2 10-2 10h20s0-7-2-10c-1.754-2.631-5-3.999-5-6"/>
                            <path fill="#ffac33"
                                  d="M18 2h-8s0 4 1 7c1.304 3.912 6 4.999 6 9s0 13 1 13s1-9 1-13s4.697-5.088 6-9c1-3 1-7 1-7z"/>
                            <path fill="#3b88c3"
                                  d="M30 34a2 2 0 0 1-2 2H8a2 2 0 0 1 0-4h20a2 2 0 0 1 2 2m0-32a2 2 0 0 1-2 2H8a2 2 0 0 1 0-4h20a2 2 0 0 1 2 2"/>
                        </svg>
                        <p class="mt-2 text-gray-950 dark:text-white ">Kursus sudah selesai. Silahkan menunggu kursus batch berikutnya.</p>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div
            class="relative mt-2 border-2 border-dashed border-gray-400 dark:border-gray-600 rounded-lg pb-[43.10%] w-full">
            <div class="absolute top-0 left-0 right-0 bottom-0 flex justify-center items-center">
                <div class="text-center font-bold">
                    <svg class="w-full" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36">
                        <path fill="#ffe8b6"
                              d="M21 18c0-2.001 3.246-3.369 5-6c2-3 2-10 2-10H8s0 7 2 10c1.754 2.631 5 3.999 5 6s-3.246 3.369-5 6c-2 3-2 10-2 10h20s0-7-2-10c-1.754-2.631-5-3.999-5-6"/>
                        <path fill="#ffac33"
                              d="M18 2h-8s0 4 1 7c1.304 3.912 6 4.999 6 9s0 13 1 13s1-9 1-13s4.697-5.088 6-9c1-3 1-7 1-7z"/>
                        <path fill="#3b88c3"
                              d="M30 34a2 2 0 0 1-2 2H8a2 2 0 0 1 0-4h20a2 2 0 0 1 2 2m0-32a2 2 0 0 1-2 2H8a2 2 0 0 1 0-4h20a2 2 0 0 1 2 2"/>
                    </svg>
                    <p class="mt-2 text-gray-950 dark:text-white">Saat ini belum ada kursus yang dibuka.</p>
                </div>
            </div>
        </div>
    @endif

    @pushonce('scripts')
        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
        <script type="text/javascript">
            document.addEventListener('livewire:navigated', () => {
                Livewire.on('snap-token', (data) => {
                    window.snap.pay(data.snap, {
                        onSuccess: function (result) {
                            fetch('/registration/success', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id: data.id,
                                    amount: result.gross_amount
                                })
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        return Promise.reject('Server error');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    Livewire.navigate(@js(route('participant.registration')))
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                        },
                        onPending: function (result) {
                            console.log(result);
                        },
                        onError: function (result) {
                            console.log(result);
                        },
                        onClose: function () {
                            alert('you closed the popup without finishing the payment');
                        }
                    });
                });
                Livewire.on('print-registration', () => {
                    let printContents = document.getElementById('print-area').innerHTML;
                    document.body.innerHTML = printContents;
                    window.print();
                    window.location.reload();
                });
            }, { once: true });
        </script>
    @endpushonce


</div>
