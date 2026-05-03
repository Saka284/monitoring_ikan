<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\MonitoringController;
use App\Http\Controllers\Api\ControllingController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/monitoring', [MonitoringController::class, 'store'])->middleware('throttle:60,1');
    Route::get('/controlling/{kolam_id}', [ControllingController::class, 'show']);
});
