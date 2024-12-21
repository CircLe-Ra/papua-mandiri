<?php

use function Livewire\Volt\{state, layout, computed, usesPagination};
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

?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ],
            [
                'text' => 'Peserta Kursus'
            ]
        ]">
    </x-breadcrumbs>

    <div class="grid-cols-1 lg:grid-cols-3 grid gap-2 ">
        <div class="col-span-3 ">
            <x-card class="mt-2 w-full ">
                <x-slot name="header">
                    Peserta Kurusus
                    <p class="text-sm text-gray-600 dark:text-gray-300">Klik pada area manapun didalam kotak untuk melihat peserta kursus.</p>
                </x-slot>
                <div x-data x-init="
                    window.addEventListener('livewire:navigated', () => {
                        $nextTick(() => {
                            $el.querySelector('div#open-participant-button').click();
                        });
                    }, { once: true });
                ">
                    <div @click="$dispatch('open-modal', { id : 'open-participant' })" id="open-participant-button"
                        class="relative mt-2 border-2 border-dashed border-gray-400 dark:border-gray-600 rounded-lg pb-[34%] w-full">
                        <div class="absolute top-0 left-0 right-0 bottom-0 flex justify-center items-center">
                            <div class="text-center text-white font-bold">
                                Klik disini.
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
    <x-modal id="open-participant" />
</div>
