<?php

use App\Http\Controllers\MpesaSettingController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    // M-Pesa Settings Routes
    Route::get('settings/mpesa', function () {
        return Inertia::render('settings/MpesaSettings');
    })->name('mpesa.settings')->middleware(['password.confirm']);

    // M-Pesa API Routes
    Route::get('mpesa-settings', [MpesaSettingController::class, 'index']);
    Route::post('mpesa-settings', [MpesaSettingController::class, 'store']);
    Route::put('mpesa-settings/{id}', [MpesaSettingController::class, 'update']);
    Route::delete('mpesa-settings/{id}', [MpesaSettingController::class, 'destroy']);
    Route::post('mpesa-settings/test-connection', [MpesaSettingController::class, 'testConnection']);
});