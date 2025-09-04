<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

Route::get('/', [DataController::class, 'home'])->name('home');
Route::get('/analisis-hotspot', [DataController::class, 'analisisHotspot'])->name('analisis-hotspot');
Route::get('/data-tunting', [DataController::class, 'dataStunting'])->name('data-stunting');
Route::get('/peta', [DataController::class, 'peta'])->name('peta');
Route::get('/data-wilayah', [DataController::class, 'dataWilayah'])->name('data-wilayah');
Route::get('/laporan', [DataController::class, 'laporan'])->name('laporan');


Route::get('/admin/login', [AdminController::class, 'loginAdmin'])->name('admin.login');
Route::post('/user/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

Route ::get('tos', function () {
    return view('tos');
});