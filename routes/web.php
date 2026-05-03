<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ThresholdController;
use App\Http\Controllers\KasController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/api/chart-data', [DashboardController::class, 'chartData'])->middleware(['auth'])->name('api.chart-data');
Route::get('/api/latest-data', [DashboardController::class, 'latestData'])->middleware(['auth'])->name('api.latest-data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Monitoring
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/export', [MonitoringController::class, 'export'])->name('monitoring.export');

    // Controlling
    Route::get('/controlling', [ThresholdController::class, 'index'])->name('controlling.index');
    Route::put('/controlling/{kolam_id}', [ThresholdController::class, 'update'])->name('controlling.update');

    // Management Kas
    Route::get('/kas', [KasController::class, 'index'])->name('kas.index');
    Route::post('/kas/pemasukan', [KasController::class, 'storePemasukan'])->name('kas.storePemasukan');
    Route::post('/kas/pengeluaran', [KasController::class, 'storePengeluaran'])->name('kas.storePengeluaran');
    Route::get('/kas/export', [KasController::class, 'export'])->name('kas.export');
});

require __DIR__.'/auth.php';
