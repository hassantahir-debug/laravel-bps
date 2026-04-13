<?php

use App\Http\Controllers\API\CodeController;
use App\Http\Controllers\API\BillController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\VisitController;
use App\Http\Middleware\LoggerMiddleware;
use Illuminate\Support\Facades\Route;

Route::apiResource('visits', VisitController::class)->middleware(LoggerMiddleware::class);
Route::apiResource('procedure-codes', CodeController::class);


Route::apiResource('bills', BillController::class);
Route::apiResource('document', DocumentController::class)->middleware(LoggerMiddleware::class);


Route::get('payments/export', [PaymentController::class, 'export']);
Route::apiResource('payments', PaymentController::class);


Route::get('/dashboard/poster', [DashboardController::class, 'posterStats']);
Route::get('/dashboard/biller', [DashboardController::class, 'billerStats']);