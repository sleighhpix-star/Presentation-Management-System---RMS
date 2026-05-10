<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

// ── Auth ───────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Protected ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [AnalyticsController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [AnalyticsController::class, 'data'])->name('dashboard.data');

    // Research CRUD
    Route::resource('research', ResearchController::class);

    // Exports (with query string passthrough for active filters)
    Route::get('/export/excel', [ResearchController::class, 'exportExcel'])->name('research.export.excel');
});