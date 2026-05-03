<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\MonitoringController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/monitoring', [MonitoringController::class, 'store'])->middleware('throttle:60,1');

use App\Http\Controllers\Api\ControllingController;
Route::get('/controlling/{kolam_id}', [ControllingController::class, 'show']);
