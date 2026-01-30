<?php

use App\Http\Controllers\ScheduledReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/scheduled-reports', [ScheduledReportController::class, 'store']);
Route::get('/cron/trigger', [ScheduledReportController::class, 'trigger']);
