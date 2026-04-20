<?php

use App\Http\Controllers\API\AccidentDetailsController;
use App\Http\Controllers\API\CodeController;
use App\Http\Controllers\API\BillController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\VisitController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\InsuranceController;
use App\Http\Middleware\LoggerMiddleware;
use Illuminate\Support\Facades\Route;

// Biller section
Route::middleware(['role:Admin,Biller'])->group(function () {
    Route::apiResource('visits', VisitController::class)->middleware(LoggerMiddleware::class);
    Route::post('bills', [BillController::class, 'store']);
    Route::put('bills/{bill}', [BillController::class, 'update']);
});

// Shared section
Route::middleware(['role:Admin,Biller,Payment Poster'])->group(function () {
    Route::get('bills', [BillController::class, 'index']);
    Route::get('bills/{bill}', [BillController::class, 'show']);
    Route::apiResource('document', DocumentController::class)->middleware(LoggerMiddleware::class);
});

// Admin delete
Route::delete('bills/{bill}', [BillController::class, 'destroy'])->middleware('role:Admin');

// Poster section
Route::middleware(['role:Admin,Payment Poster'])->group(function () {
    Route::get('payments/export', [PaymentController::class, 'export']);
    Route::apiResource('payments', PaymentController::class);
    Route::get('/dashboard/poster', [DashboardController::class, 'posterStats']);
});

// Biller stats
Route::get('/dashboard/biller', [DashboardController::class, 'billerStats'])->middleware('role:Admin,Biller');

// Admin only
Route::apiResource('procedure-codes', CodeController::class)->middleware('role:Admin,Biller');
Route::apiResource('patients', PatientController::class)->middleware('role:Admin');
Route::apiResource('insurance', InsuranceController::class)->middleware('role:Admin');

// Accident data
Route::apiResource('accidentdetails', AccidentDetailsController::class)->middleware('role:Admin,Biller');
