<?php

use App\Http\Controllers\API\CodeController;
use App\Http\Controllers\API\VisitController;
use Illuminate\Support\Facades\Route;

Route::apiResource('visits', VisitController::class);
Route::apiResource('procedure-codes', CodeController::class);
