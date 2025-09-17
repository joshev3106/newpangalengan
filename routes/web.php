<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\HotspotController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\StuntingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StuntingChartController;

/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Stunting (publik hanya index + endpoint chart JSON)
Route::resource('stunting', StuntingController::class)->only(['index']);

// TAB CHART (punya ranking + trend tertimbang) — dipakai di stunting/index.blade.php
Route::get('/stunting/chart-data', [StuntingController::class, 'chartData'])->name('stunting.chart');

// MINI TREND (12 bulan) untuk Home — dipakai di home/index.blade.php
Route::get('/stunting/trend', [StuntingChartController::class, 'trend'])->name('stunting.trend');

// Wilayah (daftar publik)
Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');

// Hotspot (publik: tabel/peta + endpoint JSON)
Route::get('/hotspot', [HotspotController::class, 'index'])->name('hotspot.index');
Route::get('/hotspot/data', [HotspotController::class, 'data'])->name('hotspot.data');

// Peta Faskes (publik)
Route::get('/peta', [PetaController::class, 'index'])->name('peta');

Route::get('/laporan', function () {
    return view('laporan.index');
})->name('laporan');

/*
|--------------------------------------------------------------------------
| Auth (guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1') // max 5x/menit
        ->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Rute yang Memerlukan Login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // CRUD Stunting selain index & show
    Route::resource('stunting', StuntingController::class)->except(['index', 'show']);

    // Wilayah: edit & update profil (Faskes Terdekat + Pasien Dilayani)
    Route::get('/wilayah/{desa}/edit', [WilayahController::class, 'edit'])->name('wilayah.edit');
    Route::put('/wilayah/{desa}',      [WilayahController::class, 'update'])->name('wilayah.update');

    // (Opsional) Backward compat jika form lama pakai POST /wilayah/profile
    Route::post('/wilayah/profile', [WilayahController::class, 'upsert'])->name('wilayah.upsert');

    // Logout
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
