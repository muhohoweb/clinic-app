<?php

use App\Http\Controllers\MpesaSettingController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ScheduledReportController;

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

    // Payments Routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::post('/payments/mpesa-stk-push', [PaymentController::class, 'mpesaStkPush'])->name('payments.mpesa-stk-push');
    Route::get('/payments/search-visit', [PaymentController::class, 'searchVisit'])->name('payments.search-visit');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');




    Route::get('/scheduled-reports', [ScheduledReportController::class, 'index']);
    Route::get('/scheduled-reports/{id}', [ScheduledReportController::class, 'show']);
    Route::post('/scheduled-reports', [ScheduledReportController::class, 'store']);
    Route::put('/scheduled-reports/{id}', [ScheduledReportController::class, 'update']);
    Route::delete('/scheduled-reports/{id}', [ScheduledReportController::class, 'destroy']);
    Route::post('/scheduled-reports/{id}/toggle', [ScheduledReportController::class, 'toggleStatus']);
    Route::get('/cron/trigger', [ScheduledReportController::class, 'trigger']);




    // M-Pesa Settings Routes (existing)
    Route::get('mpesa-settings', [MpesaSettingController::class, 'index']);
    Route::post('mpesa-settings', [MpesaSettingController::class, 'store']);
    Route::put('mpesa-settings/{id}', [MpesaSettingController::class, 'update']);
    Route::delete('mpesa-settings/{id}', [MpesaSettingController::class, 'destroy']);
    Route::post('mpesa-settings/test-connection', [MpesaSettingController::class, 'testConnection']);

    // M-Pesa Callback Route (new)
    Route::post('api/mpesa/callback', [MpesaSettingController::class, 'callback']);

    // Payment Routes (new)
    Route::post('payments/mpesa/stk-push', [PaymentController::class, 'mpesaStkPush']);
    Route::post('payments/mpesa/check-status', [PaymentController::class, 'checkMpesaStatus']);

});

require __DIR__.'/settings.php';