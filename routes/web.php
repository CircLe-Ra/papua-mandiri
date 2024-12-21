<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\PaymentController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->group(function () {
    //admin
    Volt::route('master-data/programs', 'pages.admin.master-data.program')->name('admin.master-data.program');
    Volt::route('master-data/religions', 'pages.admin.master-data.religion')->name('admin.master-data.religion');
    Volt::route('receptions', 'pages.admin.reception.reception')->name('admin.reception');
    Volt::route('receptions/{period}/add-program', 'pages.admin.reception.opening')->name('admin.reception.opening');
    Volt::route('participants/receptions', 'pages.admin.participant.reception')->name('admin.participants');
    Volt::route('participant/receptions/{id}/detail', 'pages.admin.participant.participant')->name('admin.participant.details');
    Volt::route('participant/absenteeism', 'pages.admin.participant.absenteeism')->name('admin.participant.absenteeism');
    Volt::route('participant/absenteeism/{id}/detail', 'pages.admin.participant.absenteeism.program')->name('admin.participant.absenteeism.detail');

    //peserta
    Volt::route('registration', 'pages.participant.registration')->name('participant.registration');
    Route::post('registration/success', [PaymentController::class, '__invoke'])->name('participant.registration.success');
    Route::get('registration/report/{id}/print', [ReportController::class, 'registration'])->name('participant.registration.report.print');
});


require __DIR__.'/auth.php';
