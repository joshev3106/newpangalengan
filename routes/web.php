<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\StuntingController;

// Publik bisa lihat daftar
Route::resource('stunting', StuntingController::class)->only(['index']);
Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');

Route::middleware('guest')->group( function () {
    // Auth Routes
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')   // optional: batasi 5x/menit
        ->name('login.store');
});

// Aksi CRUD butuh login (ganti 'auth' sesuai kebutuhanmu)
Route::middleware('auth')->group(function () {
    Route::resource('stunting', StuntingController::class)->except(['index', 'show']);
    Route::post('/wilayah/profile', [WilayahController::class, 'upsert'])->name('wilayah.upsert');

    // Logout
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});

Route::get('/', [DataController::class, 'home'])->name('home');
Route::get('/analisis-hotspot', [DataController::class, 'analisisHotspot'])->name('analisis-hotspot');
Route::get('/data-stunting', fn () => redirect()->route('stunting.index'))->name('data-stunting');
Route::get('/data-stunting', fn () => redirect()->route('wilayah.index'))->name('data-stunting');
Route::get('/peta', [DataController::class, 'peta'])->name('peta');
Route::get('/laporan', [DataController::class, 'laporan'])->name('laporan');
