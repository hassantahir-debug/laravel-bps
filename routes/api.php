<?php

use App\Http\Controllers\API\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('visits', VisitController::class);
