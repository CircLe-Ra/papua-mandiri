<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\PaymentController;

Route::view('/', 'welcome');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->group(function () {
    //admin & pimpinan
    Route::group(['middleware' => ['role:admin|pimpinan']], function () {
        Volt::route('admin/dashboard', 'pages.admin.dashboard')->name('admin.dashboard');
        Volt::route('report/absent', 'pages.leader.absenteeism')->name('report.absenteeism');
        Route::get('/report/absent/{period}/{time}/{program}/{level}/print', [ReportController::class, 'absent'])->name('report.absenteeism.print');
    });
    //admin
    Route::group(['middleware' => ['role:admin']], function () {
        Volt::route('master-data/programs', 'pages.admin.master-data.program')->name('admin.master-data.program');
        Volt::route('master-data/religions', 'pages.admin.master-data.religion')->name('admin.master-data.religion');
        Volt::route('master-data/users', 'pages.admin.master-data.user')->name('admin.master-data.user');
        Volt::route('master-data/roles', 'pages.admin.master-data.role')->name('admin.master-data.role');
        Volt::route('receptions', 'pages.admin.reception.reception')->name('admin.reception');
        Volt::route('receptions/{period}/add-program', 'pages.admin.reception.opening')->name('admin.reception.opening');
        Volt::route('participant', 'pages.admin.participant.participant')->name('admin.participants');
        Volt::route('participant/receptions/{id}/detail', 'pages.admin.participant.participant')->name('admin.participant.details');
        Volt::route('participant/absenteeism', 'pages.admin.participant.absenteeism')->name('admin.participant.absenteeism');
        Volt::route('participant/absenteeism/reception/{reception}/program/{program}', 'pages.admin.participant.absenteeism.participant')->name('admin.participant.absenteeism.detail');
    });
    //peserta
    Route::group(['middleware' => ['role:peserta']], function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Volt::route('registration', 'pages.participant.registration')->name('participant.registration');
        Volt::route('information', 'pages.information')->name('information');
        Route::post('registration/success', [PaymentController::class, '__invoke'])->name('participant.registration.success');
        Route::get('registration/report/{id}/print', [ReportController::class, 'registration'])->name('participant.registration.report.print');
        Volt::route('certificate/download', 'pages.participant.certificate')->name('participant.certificate.download');
    });

});

require __DIR__.'/auth.php';
