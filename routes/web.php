<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InputIndikatorController;
use App\Http\Controllers\Superadmin\DetailIndikatorController;
use App\Http\Controllers\Superadmin\IndikatorRuanganController;
use App\Http\Controllers\Superadmin\SDashboardController;
use App\Http\Controllers\Superadmin\SkmController;
use App\Http\Controllers\Guest\SurveyController;
use App\Http\Controllers\Superadmin\IndikatorMutuController;


Route::get('/', function () {
    return view('login');
});

// Admin Routes
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/input_indikator', [InputIndikatorController::class, 'create'])->name('admin.input_indikator');
Route::post('/admin/input_indikator', [InputIndikatorController::class, 'store'])->name('admin.input_indikator.store');
Route::get('/admin/download-rekap', [DashboardController::class, 'downloadRekap'])->name('admin.download_rekap');

// Superadmin Routes
Route::get('/superadmin/dashboard', [SDashboardController::class, 'index'])->name('superadmin.dashboard');
Route::get('/superadmin/ruangan/{ruangan}/detail', [DetailIndikatorController::class, 'show'])->name('superadmin.ruangan.detail');
Route::get('/superadmin/ruangan/{ruangan}/edit-indikator', [IndikatorRuanganController::class, 'edit'])->name('superadmin.ruangan.edit_indikator');
Route::post('/superadmin/ruangan/update-indikator', [IndikatorRuanganController::class, 'update'])->name('superadmin.ruangan.update_indikator');
Route::post('/superadmin/ruangan/add-indikator', [IndikatorRuanganController::class, 'store'])->name('superadmin.ruangan.add_indikator');
Route::post('/superadmin/ruangan/deactivate-indikator', [IndikatorRuanganController::class, 'deactivate'])->name('superadmin.ruangan.deactivate_indikator');
Route::get('/superadmin/indikator-mutu/create', [IndikatorMutuController::class, 'create'])->name('superadmin.indikator_mutu.create');
Route::post('/superadmin/indikator-mutu', [IndikatorMutuController::class, 'store'])->name('superadmin.indikator_mutu.store');
Route::delete('/superadmin/indikator-mutu/{id}', [IndikatorMutuController::class, 'destroy'])->name('superadmin.indikator_mutu.destroy');
Route::put('/superadmin/indikator-mutu/{id}', [IndikatorMutuController::class, 'update'])->name('superadmin.indikator_mutu.update');

Route::get('/superadmin/skm/rekap', [SkmController::class, 'index'])->name('superadmin.skm_rekap');
Route::get('/superadmin/skm/hasil', [SkmController::class, 'hasil'])->name('superadmin.skm_hasil');
Route::get('/superadmin/skm/edit2', [SkmController::class, 'editPertanyaan'])->name('superadmin.skm_edit2');
Route::put('/superadmin/skm/update-pertanyaan', [SkmController::class, 'updatePertanyaan'])->name('superadmin.skm.update_pertanyaan');
Route::delete('/superadmin/skm/pertanyaan/{id}', [SkmController::class, 'destroyPertanyaan'])->name('superadmin.skm.destroy_pertanyaan');

Route::get('/superadmin/download-rekap-indikator', [SDashboardController::class, 'downloadRekapIndikator'])->name('superadmin.download_rekap_indikator');
Route::get('/superadmin/download-rekap', [DetailIndikatorController::class, 'downloadRekap'])->name('superadmin.download_rekap');
Route::get('/superadmin/skm/download', [SkmController::class, 'downloadRekap'])->name('superadmin.skm.download');

// User Routes
Route::get('/SKM/dashboard', function () {
    return view('guest.dashboard');
})->name('guest.dashboard');
Route::get('/SKM/survei-1', [SurveyController::class, 'create'])->name('guest.survei-1');
Route::post('/SKM/survei-1', [SurveyController::class, 'store'])->name('guest.survei-1.store');
Route::get('/SKM/survei-done', function () {
    return view('guest.skm_done');
})->name('guest.survei-done');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');



