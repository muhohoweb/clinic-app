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

//Route::get('/patients', function () {
//
//})->middleware(['auth', 'verified'])->name('patients.index');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/patients', [PatientsController::class, 'index'])->name('patients.index');
    Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/balances', [BalanceController::class, 'index'])->name('balances.index');
});




require __DIR__.'/settings.php';
