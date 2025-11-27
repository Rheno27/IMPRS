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


// --- PUBLIC ---
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// --- SKM (GUEST / TANPA LOGIN) ---
Route::prefix('SKM')->name('guest.')->group(function () {
    Route::get('/dashboard', function () {
        return view('guest.dashboard'); })->name('dashboard');
    Route::get('/survei-1', [App\Http\Controllers\Guest\SurveyController::class, 'create'])->name('survei-1');
    Route::post('/survei-1', [App\Http\Controllers\Guest\SurveyController::class, 'store'])->name('survei-1.store');
    Route::get('/survei-done', function () {
        return view('guest.skm_done'); })->name('survei-done');
});

// --- HALAMAN ADMIN RUANGAN ---
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/input_indikator', [App\Http\Controllers\Admin\InputIndikatorController::class, 'create'])->name('input_indikator');
        Route::post('/input_indikator', [App\Http\Controllers\Admin\InputIndikatorController::class, 'store'])->name('input_indikator.store');
        Route::get('/download-rekap', [App\Http\Controllers\Admin\DashboardController::class, 'downloadRekap'])->name('download_rekap');
    });
});

// --- HALAMAN SUPERADMIN ---
Route::middleware(['auth', 'role:superadmin'])->group(function () {

    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Superadmin\SDashboardController::class, 'index'])->name('dashboard');

        // Fitur Ruangan
        Route::get('/ruangan/{ruangan}/detail', [App\Http\Controllers\Superadmin\DetailIndikatorController::class, 'show'])->name('ruangan.detail');
        Route::get('/ruangan/{ruangan}/edit-indikator', [App\Http\Controllers\Superadmin\IndikatorRuanganController::class, 'edit'])->name('ruangan.edit_indikator');
        Route::post('/ruangan/update-indikator', [App\Http\Controllers\Superadmin\IndikatorRuanganController::class, 'update'])->name('ruangan.update_indikator');
        Route::post('/ruangan/add-indikator', [App\Http\Controllers\Superadmin\IndikatorRuanganController::class, 'store'])->name('ruangan.add_indikator');
        Route::post('/ruangan/deactivate-indikator', [App\Http\Controllers\Superadmin\IndikatorRuanganController::class, 'deactivate'])->name('ruangan.deactivate_indikator');

        // Master Indikator
        Route::get('/indikator-mutu/create', [App\Http\Controllers\Superadmin\IndikatorMutuController::class, 'create'])->name('indikator_mutu.create');
        Route::post('/indikator-mutu', [App\Http\Controllers\Superadmin\IndikatorMutuController::class, 'store'])->name('indikator_mutu.store');
        Route::delete('/indikator-mutu/{id}', [App\Http\Controllers\Superadmin\IndikatorMutuController::class, 'destroy'])->name('indikator_mutu.destroy');
        Route::put('/indikator-mutu/{id}', [App\Http\Controllers\Superadmin\IndikatorMutuController::class, 'update'])->name('indikator_mutu.update');

        // SKM Management
        Route::prefix('skm')->name('skm.')->group(function () {
            Route::get('/rekap', [App\Http\Controllers\Superadmin\SkmController::class, 'index'])->name('rekap');
            Route::get('/hasil', [App\Http\Controllers\Superadmin\SkmController::class, 'hasil'])->name('hasil');
            Route::get('/edit2', [App\Http\Controllers\Superadmin\SkmController::class, 'editPertanyaan'])->name('edit2');
            Route::put('/update-pertanyaan', [App\Http\Controllers\Superadmin\SkmController::class, 'updatePertanyaan'])->name('update_pertanyaan');
            Route::delete('/pertanyaan/{id}', [App\Http\Controllers\Superadmin\SkmController::class, 'destroyPertanyaan'])->name('destroy_pertanyaan');
            Route::get('/download', [App\Http\Controllers\Superadmin\SkmController::class, 'downloadRekap'])->name('download');
        });

        // Downloads
        Route::get('/download-rekap-indikator', [App\Http\Controllers\Superadmin\SDashboardController::class, 'downloadRekapIndikator'])->name('download_rekap_indikator');
        Route::get('/download-rekap', [App\Http\Controllers\Superadmin\DetailIndikatorController::class, 'downloadRekap'])->name('download_rekap');
    });

});
