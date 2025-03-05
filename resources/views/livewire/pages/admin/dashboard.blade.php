<?php

use function Livewire\Volt\{state, layout, mount, computed, updated};
use App\Models\Participant;
use App\Models\Program;
use App\Models\Reception;
use App\Models\Opening;
use App\Models\Absent;

layout('layouts.app');
state(['participantCount', 'programCount', 'participantCompleteCount', 'periodActive']);
state(['reception' => [],'periods' => [], 'reception_get' => true, 'periode_temp' => '']);
state(['period' => '','time_id','level' => 1])->url(keep: false);

mount(function () {
    $this->participantCount = Participant::count();
    $this->programCount = Program::count();
    $this->participantCompleteCount = Participant::where('status', 'complete')->count();
    $this->periodActive = Reception::where('status', 'active')->first();
    if ($this->periodActive !== null) {
        $this->period = $this->periodActive->period;
        $this->periods = Reception::whereYear('start_course', $this->period)->get();
        $this->time_id = $this->periodActive->id;
        $this->reception = Reception::find($this->time_id);

    }
});


$receptions = computed(function () {
    return Reception::select('period')->groupBy('period')->get();
});

$participants = computed(function () {
    return Participant::with('user.personal_data', 'program')
        ->where('reception_id', $this->reception->id ?? 0)
        ->latest()->get();
});

updated(['period' => function ($period) {
    if ($this->periode_temp !== $period) {
        $this->periods = Reception::whereYear('start_course', $period)->get();
        $this->periode_temp = $period;
        $this->reception_get = false;
        $this->time_id = '';
    } elseif ($this->reception_get) {
        $this->periods = Reception::whereYear('start_course', $period)->get();
        $this->periode_temp = $period;
        $this->reception_get = false;
        $this->time_id = '';
    }
}]);

$find = function () {
    $validator = \Illuminate\Support\Facades\Validator::make([
        'period' => $this->period,
        'time_id' => $this->time_id,
    ], [
        'period' => 'required',
        'time_id' => 'required',
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            Toaster::error($error);
        }
        return;
    }

    $this->reception = Reception::where('id', $this->time_id)->first();
    $this->participants = Participant::with('user.personal_data', 'program')
        ->where('reception_id', $this->reception->id)
        ->latest()->get();
};

$programActive = computed(function () {
    return Opening::with('program')->where('reception_id', $this->reception->id ?? 0)->get();
});


?>

<div>
    <x-breadcrumbs :crumbs="[
            [
                'href' => route('dashboard'),
                'text' => 'Dashboard'
            ]
        ]">
        <x-slot:actions>
            <p class="my-[5px] dark:text-white">Selamat datang, {{ auth()->user()->name }}</p>
        </x-slot:actions>
    </x-breadcrumbs>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-2 mt-2">
        <x-card class="w-full">
            <div class="flex items-center text-gray-900 whitespace-nowrap dark:text-white">
                <svg class="w-20 h-20 " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128"><path fill="#3c2b24" d="M73.76 89.08H54.23v19.33c0 4.85 3.98 8.78 8.88 8.78h1.77c4.9 0 8.88-3.93 8.88-8.78zm17.57-38.67H36.67c-5.89 0-10.71 5.14-10.71 11.41c0 6.28 4.82 11.41 10.71 11.41h54.65c5.89 0 10.71-5.14 10.71-11.41c.01-6.27-4.81-11.41-10.7-11.41"/><path fill="#70534a" d="M64 11.05c-17.4 0-33.52 18.61-33.52 45.39c0 26.64 16.61 39.81 33.52 39.81s33.52-13.17 33.52-39.81c0-26.78-16.12-45.39-33.52-45.39"/><g fill="#1a1717"><ellipse cx="47.56" cy="58.79" rx="4.93" ry="5.1"/><ellipse cx="80.44" cy="58.79" rx="4.93" ry="5.1"/></g><path fill="#33251f" d="M67.86 68.04c-.11-.04-.21-.07-.32-.08h-7.07c-.11.01-.22.04-.32.08c-.64.26-.99.92-.69 1.63s1.71 2.69 4.55 2.69s4.25-1.99 4.55-2.69c.29-.71-.06-1.37-.7-1.63"/><path fill="#1a1717" d="M72.42 76.12c-3.19 1.89-13.63 1.89-16.81 0c-1.83-1.09-3.7.58-2.94 2.24c.75 1.63 6.45 5.42 11.37 5.42s10.55-3.79 11.3-5.42c.75-1.66-1.09-3.33-2.92-2.24"/><path fill="#232020" d="M64.02 5.03h-.04c-45.44.24-36.13 50.14-36.13 50.14s2.04 5.35 2.97 7.71c.13.34.63.3.71-.05c.97-4.34 4.46-19.73 6.22-24.41a6.075 6.075 0 0 1 6.79-3.83c4.46.81 11.55 1.81 19.38 1.81h.16c7.82 0 14.92-1 19.37-1.81c2.9-.53 5.76 1.08 6.79 3.83c1.75 4.66 5.22 19.96 6.2 24.36c.08.36.58.39.71.05l2.98-7.67c.02.01 9.32-49.89-36.11-50.13"/><radialGradient id="notoManStudentDarkSkinTone0" cx="64.001" cy="81.221" r="37.873" gradientTransform="matrix(1 0 0 -1.1282 0 138.298)" gradientUnits="userSpaceOnUse"><stop offset=".794" stop-color="#444140" stop-opacity="0"/><stop offset="1" stop-color="#444140"/></radialGradient><path fill="url(#notoManStudentDarkSkinTone0)" d="M100.15 55.17s9.31-49.9-36.13-50.14h-.04c-.71 0-1.4.02-2.08.05c-1.35.06-2.66.16-3.92.31h-.04c-.09.01-.17.03-.26.04c-38.25 4.81-29.84 49.74-29.84 49.74l2.98 7.68c.13.34.62.31.7-.05c.98-4.39 4.46-19.71 6.22-24.37a6.08 6.08 0 0 1 6.8-3.83c4.46.8 11.55 1.8 19.38 1.8h.16c7.82 0 14.92-1 19.37-1.81c2.9-.53 5.76 1.08 6.79 3.83c1.76 4.68 5.25 20.1 6.21 24.42c.08.36.57.39.7.05c.94-2.35 3-7.72 3-7.72"/><path fill="#1a1717" d="M40.01 50.72c2.99-4.23 9.78-4.63 13.67-1.48c.62.5 1.44 1.2 1.68 1.98c.4 1.27-.82 2.26-2.01 1.96c-.76-.19-1.47-.6-2.22-.83c-1.37-.43-2.36-.55-3.59-.55c-1.82-.01-2.99.22-4.72.92c-.71.29-1.29.75-2.1.41c-.93-.39-1.27-1.57-.71-2.41m46.06 2.4c-.29-.13-.57-.29-.86-.41c-1.78-.74-2.79-.93-4.72-.92c-1.7.01-2.71.24-4.04.69c-.81.28-1.84.98-2.74.71c-1.32-.4-1.28-1.84-.56-2.76c.86-1.08 2.04-1.9 3.29-2.44c2.9-1.26 6.44-1.08 9.17.55c.89.53 1.86 1.26 2.4 2.18c.78 1.31-.4 3.03-1.94 2.4"/><path fill="#e8ad00" d="M116.5 54.28c-1.24 0-2.25.96-2.25 2.14v9.2c0 1.18 1.01 2.14 2.25 2.14s2.25-.96 2.25-2.14v-9.2c0-1.18-1.01-2.14-2.25-2.14m-4.5 0c-1.24 0-2.25.96-2.25 2.14v9.2c0 1.18 1.01 2.14 2.25 2.14s2.25-.96 2.25-2.14v-9.2c0-1.18-1.01-2.14-2.25-2.14"/><path fill="#ffca28" d="M114.25 54.28c-1.24 0-2.25.96-2.25 2.14v11.19c0 1.18 1.01 2.14 2.25 2.14s2.25-.96 2.25-2.14V56.42c0-1.18-1.01-2.14-2.25-2.14"/><ellipse cx="114.25" cy="53.05" fill="#ffca28" rx="2.76" ry="2.63"/><path fill="#504f4f" d="M114.25 53.02c-.55 0-1-.45-1-1v-38c0-.55.45-1 1-1s1 .45 1 1v38c0 .56-.45 1-1 1"/><linearGradient id="notoManStudentDarkSkinTone1" x1="64" x2="64" y1="127.351" y2="98.71" gradientTransform="matrix(1 0 0 -1 0 128)" gradientUnits="userSpaceOnUse"><stop offset=".003" stop-color="#424242"/><stop offset=".472" stop-color="#353535"/><stop offset="1" stop-color="#212121"/></linearGradient><path fill="url(#notoManStudentDarkSkinTone1)" d="M116 12.98c-30.83-7.75-52-8-52-8s-21.17.25-52 8v.77c0 1.33.87 2.5 2.14 2.87c3.72 1.1 13.13 3.53 18.18 4.54c-.08.08-1.1 1.87-1.83 3.53c0 0 8.14 5.72 33.52 8.28c25.38-2.56 33.76-7.58 33.76-7.58c-.88-1.81-1.79-3.49-1.79-3.49c4.5-.74 14.23-4.07 17.95-5.26c1.25-.4 2.09-1.55 2.09-2.86v-.8z"/><linearGradient id="notoManStudentDarkSkinTone2" x1="64" x2="64" y1="127.184" y2="96.184" gradientTransform="matrix(1 0 0 -1 0 128)" gradientUnits="userSpaceOnUse"><stop offset=".003" stop-color="#616161"/><stop offset=".324" stop-color="#505050"/><stop offset=".955" stop-color="#242424"/><stop offset="1" stop-color="#212121"/></linearGradient><path fill="url(#notoManStudentDarkSkinTone2)" d="M64 4.98s-21.17.25-52 8c0 0 35.41 9.67 52 9.38c16.59.29 52-9.38 52-9.38c-30.83-7.75-52-8-52-8"/><linearGradient id="notoManStudentDarkSkinTone3" x1="13.893" x2="114.721" y1="109.017" y2="109.017" gradientTransform="matrix(1 0 0 -1 0 128)" gradientUnits="userSpaceOnUse"><stop offset=".001" stop-color="#bfbebe"/><stop offset=".3" stop-color="#212121" stop-opacity="0"/><stop offset=".7" stop-color="#212121" stop-opacity="0"/><stop offset="1" stop-color="#bfbebe"/></linearGradient><path fill="url(#notoManStudentDarkSkinTone3)" d="M116 12.98c-30.83-7.75-52-8-52-8s-21.17.25-52 8v.77c0 1.33.87 2.5 2.14 2.87c3.72 1.1 13.13 3.69 18.18 4.71c0 0-.96 1.56-1.83 3.53c0 0 8.14 5.55 33.52 8.12c25.38-2.56 33.76-7.58 33.76-7.58c-.88-1.81-1.79-3.49-1.79-3.49c4.5-.74 14.23-4.07 17.95-5.26c1.25-.4 2.09-1.55 2.09-2.86v-.81z" opacity="0.4"/><path fill="#212121" d="M114.5 120.99c0-14.61-21.75-21.54-40.72-23.1l-8.6 11.03c-.28.36-.72.58-1.18.58s-.9-.21-1.18-.58L54.2 97.87c-10.55.81-40.71 4.75-40.71 23.12V124h101z"/><radialGradient id="notoManStudentDarkSkinTone4" cx="64" cy="5.397" r="54.167" gradientTransform="matrix(1 0 0 -.5247 0 125.435)" gradientUnits="userSpaceOnUse"><stop offset=".598" stop-color="#212121"/><stop offset="1" stop-color="#616161"/></radialGradient><path fill="url(#notoManStudentDarkSkinTone4)" d="M114.5 120.99c0-14.61-21.75-21.54-40.72-23.1l-8.6 11.03c-.28.36-.72.58-1.18.58s-.9-.21-1.18-.58L54.2 97.87c-10.55.81-40.71 4.75-40.71 23.12V124h101z"/></svg>
                <div class="ps-3">
                    <div class="text-lg font-semibold">Peserta Kursus</div>
                    <div class="font-normal text-base text-gray-500">{{ $this->participantCount }} Peserta</div>
                </div>
            </div>
        </x-card>
        <x-card class="w-full">
            <div class="flex items-center text-gray-900 whitespace-nowrap dark:text-white">
                <svg class="w-20 h-20 " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 284"><path fill="#f9ab00" d="M256.003 247.933a35.224 35.224 0 0 1-39.376 35.161c-18.044-2.67-31.266-18.371-30.826-36.606V36.845C185.365 18.591 198.62 2.881 216.687.24a35.22 35.22 0 0 1 39.316 35.16z"/><path fill="#e37400" d="M35.101 213.193c19.386 0 35.101 15.716 35.101 35.101c0 19.386-15.715 35.101-35.101 35.101S0 267.68 0 248.295s15.715-35.102 35.101-35.102m92.358-106.387c-19.477 1.068-34.59 17.406-34.137 36.908v94.285c0 25.588 11.259 41.122 27.755 44.433a35.16 35.16 0 0 0 42.146-34.56V142.089a35.22 35.22 0 0 0-35.764-35.282"/></svg>
                <div class="ps-3">
                    <div class="text-lg font-semibold">Program Kursus</div>
                    <div class="font-normal text-base text-gray-500">{{ $this->programCount }} Program</div>
                </div>
            </div>
        </x-card>
        <x-card class="w-full">
            <div class="flex items-center text-gray-900 whitespace-nowrap dark:text-white">
                <svg class="w-20 h-20 " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><g fill="none"><g filter="url(#f620idf)"><path fill="url(#f620id0)" d="M4.716 14.013h22.328v1.652a7.5 7.5 0 0 1-7.5 7.5h-7.328a7.5 7.5 0 0 1-7.5-7.5z"/></g><g filter="url(#f620idg)"><path fill="url(#f620id1)" d="m15.289 20.399l-12.69-6.482a1.1 1.1 0 0 1 .003-1.96L15.29 5.512a1.3 1.3 0 0 1 1.179 0l12.69 6.46a1.1 1.1 0 0 1 0 1.96L16.47 20.399a1.3 1.3 0 0 1-1.182 0"/></g><g filter="url(#f620idh)"><path stroke="url(#f620id2)" stroke-linecap="round" stroke-width="0.7" d="m16.419 19.263l12.468-6.594"/></g><g filter="url(#f620idi)"><path fill="url(#f620id3)" d="m23.137 17.325l-1.407.717v-.5c0-.667-.087-1.168-.945-1.581l-5.573-2.914a.734.734 0 0 1-.309-.975a.7.7 0 0 1 .951-.316l5.574 2.913a3.23 3.23 0 0 1 1.71 2.656"/></g><g filter="url(#f620idj)"><path fill="url(#f620id4)" d="m23.646 16.742l-1.407.717v-.5c0-.667-.087-1.168-.945-1.581l-5.574-2.914a.734.734 0 0 1-.308-.975a.7.7 0 0 1 .95-.317l5.574 2.914a3.23 3.23 0 0 1 1.71 2.656"/></g><g filter="url(#f620idk)"><path fill="url(#f620id5)" fill-rule="evenodd" d="M15.414 11.168a.725.725 0 0 1 .975-.316l5.714 2.913a3.23 3.23 0 0 1 1.76 2.873v5.844a.725.725 0 0 1-1.45 0v-5.844c0-.667-.375-1.278-.97-1.58l-5.713-2.915a.725.725 0 0 1-.316-.975" clip-rule="evenodd"/></g><path fill="url(#f620id9)" fill-rule="evenodd" d="M15.414 11.168a.725.725 0 0 1 .975-.316l5.714 2.913a3.23 3.23 0 0 1 1.76 2.873v5.844a.725.725 0 0 1-1.45 0v-5.844c0-.667-.375-1.278-.97-1.58l-5.713-2.915a.725.725 0 0 1-.316-.975" clip-rule="evenodd"/><path fill="url(#f620id6)" fill-rule="evenodd" d="M15.414 11.168a.725.725 0 0 1 .975-.316l5.714 2.913a3.23 3.23 0 0 1 1.76 2.873v5.844a.725.725 0 0 1-1.45 0v-5.844c0-.667-.375-1.278-.97-1.58l-5.713-2.915a.725.725 0 0 1-.316-.975" clip-rule="evenodd"/><path fill="url(#f620id7)" fill-rule="evenodd" d="M15.414 11.168a.725.725 0 0 1 .975-.316l5.714 2.913a3.23 3.23 0 0 1 1.76 2.873v5.844a.725.725 0 0 1-1.45 0v-5.844c0-.667-.375-1.278-.97-1.58l-5.713-2.915a.725.725 0 0 1-.316-.975" clip-rule="evenodd"/><path fill="url(#f620ida)" d="M22.419 22.474c-.649.266-.95.823-.95 1.43a1.668 1.668 0 0 0 3.336 0c0-.607-.355-1.149-.941-1.43z"/><path fill="url(#f620idb)" d="M22.419 22.474c-.649.266-.95.823-.95 1.43a1.668 1.668 0 0 0 3.336 0c0-.607-.355-1.149-.941-1.43z"/><path fill="url(#f620idc)" d="M22.419 22.474c-.649.266-.95.823-.95 1.43a1.668 1.668 0 0 0 3.336 0c0-.607-.355-1.149-.941-1.43z"/><path fill="#ff9d4e" d="m21.226 27.647l.638-2.65h2.527l.652 2.627a1.994 1.994 0 0 1-1.905 2.577a1.992 1.992 0 0 1-1.912-2.554"/><path fill="url(#f620id8)" d="m21.226 27.647l.638-2.65h2.527l.652 2.627a1.994 1.994 0 0 1-1.905 2.577a1.992 1.992 0 0 1-1.912-2.554"/><path fill="url(#f620idd)" d="m21.226 27.647l.638-2.65h2.527l.652 2.627a1.994 1.994 0 0 1-1.905 2.577a1.992 1.992 0 0 1-1.912-2.554"/><path fill="url(#f620ide)" d="m21.226 27.647l.638-2.65h2.527l.652 2.627a1.994 1.994 0 0 1-1.905 2.577a1.992 1.992 0 0 1-1.912-2.554"/><defs><linearGradient id="f620id0" x1="25.95" x2="5.95" y1="18.589" y2="18.589" gradientUnits="userSpaceOnUse"><stop stop-color="#68518b"/><stop stop-color="#68518b"/><stop offset=".444" stop-color="#503678"/><stop offset=".909" stop-color="#35254e"/></linearGradient><linearGradient id="f620id1" x1="28.192" x2="7.262" y1="12.956" y2="12.956" gradientUnits="userSpaceOnUse"><stop stop-color="#655676"/><stop offset="1" stop-color="#2d2635"/></linearGradient><linearGradient id="f620id2" x1="17.762" x2="29.95" y1="19.263" y2="12.263" gradientUnits="userSpaceOnUse"><stop stop-color="#6e5f85"/><stop offset="1" stop-color="#9a8da7"/></linearGradient><linearGradient id="f620id3" x1="17.443" x2="23.857" y1="13.21" y2="18.458" gradientUnits="userSpaceOnUse"><stop stop-color="#3c2d42"/><stop offset="1" stop-color="#3d2c43" stop-opacity="0"/></linearGradient><linearGradient id="f620id4" x1="17.035" x2="22.079" y1="12.685" y2="17.146" gradientUnits="userSpaceOnUse"><stop stop-color="#2c1a20"/><stop offset="1" stop-color="#4c2350"/></linearGradient><linearGradient id="f620id5" x1="17.262" x2="23.387" y1="11.263" y2="23.207" gradientUnits="userSpaceOnUse"><stop stop-color="#d3a543"/><stop offset="1" stop-color="#ffba62"/></linearGradient><linearGradient id="f620id6" x1="24.273" x2="23.076" y1="18.215" y2="18.304" gradientUnits="userSpaceOnUse"><stop stop-color="#ffd574"/><stop offset="1" stop-color="#ffd574" stop-opacity="0"/></linearGradient><linearGradient id="f620id7" x1="22.089" x2="21.625" y1="13.321" y2="14.204" gradientUnits="userSpaceOnUse"><stop stop-color="#ffd574"/><stop offset="1" stop-color="#ffd574" stop-opacity="0"/></linearGradient><linearGradient id="f620id8" x1="21.35" x2="22.866" y1="27.599" y2="27.846" gradientUnits="userSpaceOnUse"><stop stop-color="#d17047"/><stop offset="1" stop-color="#d17047" stop-opacity="0"/></linearGradient><radialGradient id="f620id9" cx="0" cy="0" r="1" gradientTransform="matrix(3.47162 1.76575 -1.10075 2.16418 15.684 10.463)" gradientUnits="userSpaceOnUse"><stop stop-color="#d0a745"/><stop offset="1" stop-color="#d0a745" stop-opacity="0"/></radialGradient><radialGradient id="f620ida" cx="0" cy="0" r="1" gradientTransform="rotate(137.337 7.51 16.412)scale(2.07561 2.23528)" gradientUnits="userSpaceOnUse"><stop stop-color="#ffcc6c"/><stop offset="1" stop-color="#f49041"/></radialGradient><radialGradient id="f620idb" cx="0" cy="0" r="1" gradientTransform="matrix(-3.44037 -1.42864 1.14909 -2.76717 24.44 24.29)" gradientUnits="userSpaceOnUse"><stop offset=".6" stop-color="#b25d41" stop-opacity="0"/><stop offset=".911" stop-color="#b25d41"/></radialGradient><radialGradient id="f620idc" cx="0" cy="0" r="1" gradientTransform="matrix(2.24498 -.34987 .17007 1.0913 21.845 25.076)" gradientUnits="userSpaceOnUse"><stop stop-color="#fd805e"/><stop offset="1" stop-color="#fd805e" stop-opacity="0"/></radialGradient><radialGradient id="f620idd" cx="0" cy="0" r="1" gradientTransform="matrix(0 4.37335 -1.34116 0 24.09 24.727)" gradientUnits="userSpaceOnUse"><stop offset=".567" stop-color="#ffbc6e"/><stop offset="1" stop-color="#ffbc6e" stop-opacity="0"/></radialGradient><radialGradient id="f620ide" cx="0" cy="0" r="1" gradientTransform="matrix(.58311 -2.60156 3.00404 .67332 22.4 30.2)" gradientUnits="userSpaceOnUse"><stop offset=".165" stop-color="#ff697e"/><stop offset="1" stop-color="#ff697e" stop-opacity="0"/></radialGradient><filter id="f620idf" width="22.578" height="9.402" x="4.466" y="13.763" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feColorMatrix in="SourceAlpha" result="hardAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dx="-.25" dy="-.25"/><feGaussianBlur stdDeviation=".25"/><feComposite in2="hardAlpha" k2="-1" k3="1" operator="arithmetic"/><feColorMatrix values="0 0 0 0 0.470588 0 0 0 0 0.372549 0 0 0 0 0.658824 0 0 0 1 0"/><feBlend in2="shape" result="effect1_innerShadow_18_17524"/></filter><filter id="f620idg" width="28.759" height="15.92" x="1" y="4.621" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feColorMatrix in="SourceAlpha" result="hardAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="-1.25"/><feGaussianBlur stdDeviation=".375"/><feComposite in2="hardAlpha" k2="-1" k3="1" operator="arithmetic"/><feColorMatrix values="0 0 0 0 0.133333 0 0 0 0 0.0980392 0 0 0 0 0.176471 0 0 0 1 0"/><feBlend in2="shape" result="effect1_innerShadow_18_17524"/><feColorMatrix in="SourceAlpha" result="hardAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dx="-1" dy="-.5"/><feGaussianBlur stdDeviation=".5"/><feComposite in2="hardAlpha" k2="-1" k3="1" operator="arithmetic"/><feColorMatrix values="0 0 0 0 0.372549 0 0 0 0 0.254902 0 0 0 0 0.501961 0 0 0 1 0"/><feBlend in2="effect1_innerShadow_18_17524" result="effect2_innerShadow_18_17524"/></filter><filter id="f620idh" width="14.169" height="8.294" x="15.569" y="11.819" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feGaussianBlur result="effect1_foregroundBlur_18_17524" stdDeviation=".25"/></filter><filter id="f620idi" width="9.312" height="7.366" x="14.326" y="11.176" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feGaussianBlur result="effect1_foregroundBlur_18_17524" stdDeviation=".25"/></filter><filter id="f620idj" width="9.112" height="7.166" x="14.934" y="10.693" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feGaussianBlur result="effect1_foregroundBlur_18_17524" stdDeviation=".2"/></filter><filter id="f620idk" width="8.928" height="12.585" x="15.334" y="10.773" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feColorMatrix in="SourceAlpha" result="hardAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dx=".4" dy=".15"/><feGaussianBlur stdDeviation=".325"/><feComposite in2="hardAlpha" k2="-1" k3="1" operator="arithmetic"/><feColorMatrix values="0 0 0 0 0.8 0 0 0 0 0.458824 0 0 0 0 0.227451 0 0 0 1 0"/><feBlend in2="shape" result="effect1_innerShadow_18_17524"/></filter></defs></g></svg>
                <div class="ps-3">
                    <div class="text-lg font-semibold">Peserta Lulus</div>
                    <div class="font-normal text-base text-gray-500">{{ $this->participantCompleteCount }} Peserta</div>
                </div>
            </div>
        </x-card>
        <x-card class="w-full">
            <div class="flex items-center text-gray-900 whitespace-nowrap dark:text-white">

                <svg class="w-20 h-20 " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path fill="#cfd8dc" d="M5 38V14h38v24c0 2.2-1.8 4-4 4H9c-2.2 0-4-1.8-4-4"/><path fill="#f44336" d="M43 10v6H5v-6c0-2.2 1.8-4 4-4h30c2.2 0 4 1.8 4 4"/><g fill="#b71c1c"><circle cx="33" cy="10" r="3"/><circle cx="15" cy="10" r="3"/></g><path fill="#b0bec5" d="M33 3c-1.1 0-2 .9-2 2v5c0 1.1.9 2 2 2s2-.9 2-2V5c0-1.1-.9-2-2-2M15 3c-1.1 0-2 .9-2 2v5c0 1.1.9 2 2 2s2-.9 2-2V5c0-1.1-.9-2-2-2"/><path fill="#90a4ae" d="M13 20h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm-18 6h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm-18 6h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4z"/></svg>
                <div class="ps-3">
                    <div class="text-lg font-semibold">Periode Aktif</div>
                    @if($this->periodActive == null)
                        <div class="font-normal text-base text-gray-500">Tidak ada periode yang diaktifkan</div>
                    @else
                    <div class="font-normal text-base text-gray-500">Mulai : {{ $this->periodActive->start_course }}</div>
                    <div class="font-normal text-base text-gray-500">Selesai : {{ $this->periodActive->complete_course }}</div>
                    @endif
                </div>
            </div>
        </x-card>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 mt-2">
        <x-card class="w-full col-span-2">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-2">
                <div class="text-gray-900 dark:text-white">
                    <h1 class="text-lg font-semibold">Peserta Kursus</h1>
                    <p class="font-normal text-sm text-gray-500">Daftar peserta kursus berdasarkan periode aktif</p>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-2">
                    <x-form.input-select display_name="period" value="period" id="period" name="period" wire:model.live="period" size="xs" get-data="server" :data="$this->receptions" display_name_first="Periode?" :alert="false" />
                    <x-form.input-select display_name="start_course,complete_course" id="time_id" name="time_id" wire:model.live="time_id" size="xs" get-data="server" :data="$this->periods" display_name_first="Waktu Kursus?" :selected_first="true" :alert="false" />
                    <x-button wire:click="find" color="blue" size="xs" wire:loading.attr="disabled" class="col-span-2" wire:loading.class="cursor-not-allowed">Tampilkan
                        <svg class="rotate-180 inline -mt-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="m5 12l7-7l7 7m-7 7V5"/>
                        </svg>
                    </x-button>
                </div>
            </div>
            <x-table thead="#,Nama,Phone,Alamat,Kelas" :action="false">
                @if(count($this->participants) > 0)
                    @foreach($this->participants as $key => $participant)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4">
                                {{ $loop->iteration }}
                            </td>
                            <th scope="row"
                                class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                <img class="w-10 h-10 rounded-full"
                                     src="{{ $participant->user->profile_photo_path ? asset('storage/' . $participant->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . $participant->user->personal_data->full_name }}"
                                     alt="Image Name">
                                <div class="ps-3">
                                    <div
                                        class="text-base font-semibold">{{ $participant->user->personal_data->full_name }}</div>
                                    <div class="font-normal text-gray-500">{{ $participant->user->email }}</div>
                                </div>
                            </th>
                            <td class="px-6 py-4 text-nowrap">
                              {{ $participant->program->name }}
                            </td>
                            <th scope="row" class="px-6 py-4 items-center text-gray-900 dark:text-white text-nowrap">
                                <div class="">
                                    <div class="text-base font-semibold">{{ $participant->user->personal_data->address }}</div>
                                    <div class="font-normal text-gray-500">
                                        RT {{ $participant->user->personal_data->rt }} :
                                        RW {{ $participant->user->personal_data->rw }}</div>
                                </div>
                            </th>
                            <td class="px-6 py-4 ">
                                {{ $participant->user->personal_data->phone }}
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
        </x-card>
        <x-card class="w-full">
            <div class="text-gray-900 dark:text-white">
                <h1 class="text-lg font-semibold">Program Kursus</h1>
                <p class="font-normal text-sm text-gray-500">Daftar program kursus berdasarkan periode aktif</p>
            </div>
            <x-table thead="#,Kelas,Biaya Kelas" :action="false">
                @if(count($this->programActive) > 0)
                    @foreach($this->programActive as $key => $program)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 text-nowrap">
                                {{ $program->program->name }}
                            </td>
                            <td class="px-6 py-4 ">
                                Rp.{{ number_format($program->program->amount, 0, ',', '.') }}
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
        </x-card>
    </div>
</div>
