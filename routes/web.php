<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InputIndikatorController;
use App\Http\Controllers\Superadmin\DetailIndikatorController;
use App\Http\Controllers\Superadmin\IndikatorRuanganController;
use App\Http\Controllers\Superadmin\SDashboardController;


Route::get('/', function () {
    return view('login');
});

// Admin Routes
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::get('/admin/input_indikator', [InputIndikatorController::class, 'create'])->name('admin.input_indikator');
Route::post('/admin/input_indikator', [InputIndikatorController::class, 'store'])->name('admin.input_indikator.store');

// Superadmin Routes
Route::get('/superadmin/dashboard', [SDashboardController::class, 'index'])->name('superadmin.dashboard');
Route::get('/superadmin/ruangan/{ruangan}/detail', [DetailIndikatorController::class, 'show'])->name('superadmin.ruangan.detail');
Route::get('/superadmin/ruangan/{ruangan}/edit-indikator', [IndikatorRuanganController::class, 'edit'])->name('superadmin.ruangan.edit_indikator');
Route::post('/superadmin/ruangan/update-indikator', [IndikatorRuanganController::class, 'update'])->name('superadmin.ruangan.update_indikator');
Route::post('/superadmin/ruangan/add-indikator', [IndikatorRuanganController::class, 'store'])->name('superadmin.ruangan.add_indikator');

Route::get('/superadmin/edit_survei', function () {
    return view('superadmin.edit_survei');
})->name('superadmin.data_user');

// User Routes
Route::get('/SKM/dashboard', function () {
    return view('guest.dashboard');
})->name('guest.dashboard');
Route::get('/SKM/data_responden', function () {
    return view('guest.skm1');
})->name('guest.data_responden');
Route::get('/SKM/survei-1', function () {
    return view('guest.skm2');
})->name('guest.survei-1');
Route::get('/SKM/survei-2', function () {
    return view('guest.skm3');
})->name('guest.survei-2');
Route::get('/SKM/survei-done', function () {
    return view('guest.skm_done');
})->name('guest.survei-done');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');



