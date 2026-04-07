<?php

use App\Http\Controllers\API\BillController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('visits', VisitController::class);
Route::apiResource('bills',BillController::class);

Route::get('payments/export', [PaymentController::class, 'export']);
Route::apiResource('payments', PaymentController::class);