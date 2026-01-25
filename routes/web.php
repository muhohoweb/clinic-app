<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return redirect('/login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Patients Routes
    Route::get('/patients', [PatientsController::class, 'index'])->name('patients.index');
    Route::post('/patients', [PatientsController::class, 'store'])->name('patients.store');
    Route::put('/patients/{patient}', [PatientsController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientsController::class, 'destroy'])->name('patients.destroy');

    // Visits Routes
    Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
    Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
    Route::put('/visits/{visit}', [VisitController::class, 'update'])->name('visits.update');
    Route::delete('/visits/{visit}', [VisitController::class, 'destroy'])->name('visits.destroy');
    Route::get('/visits/search-patient', [VisitController::class, 'searchPatient'])->name('visits.search-patient');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/balances', [BalanceController::class, 'index'])->name('balances.index');
});

require __DIR__.'/settings.php';