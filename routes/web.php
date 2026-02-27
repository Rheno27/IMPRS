<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InputIndikatorController;
use App\Http\Controllers\Superadmin\SDashboardController;
use App\Http\Controllers\Superadmin\DetailIndikatorController;
use App\Http\Controllers\Superadmin\IndikatorRuanganController;
use App\Http\Controllers\Superadmin\SkmController;
use App\Http\Controllers\Guest\SurveyController;
use App\Http\Controllers\Superadmin\IndikatorMutuController;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
Route::get('/update-password-sekarang', function () {
    // Ambil semua user
    $users = User::all();
    $count = 0;

    foreach ($users as $user) {
        // Cek jika password belum di-hash (panjangnya < 60 karakter)
        if (strlen($user->password) < 60) {
            // Update langsung ke database
            User::where('id_user', $user->id_user)->update([
                'password' => Hash::make($user->password)
            ]);
            $count++;
        }
    }
    return "BERHASIL! {$count} user sudah di-aman-kan (Hashing). Silakan coba login.";
});

Route::get('/cek-error', function () {
    return view('errors.error');
});

// --- PUBLIC ---
Route::get('/', function () {
    return redirect()->route('login'); });
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// --- SKM (GUEST / TANPA LOGIN) ---
Route::prefix('SKM')->name('guest.')->group(function () {
    Route::get('/dashboard', function () {
        return view('guest.dashboard'); })->name('dashboard');
    Route::get('/survei-1', [SurveyController::class, 'create'])->name('survei-1');
    Route::post('/survei-1', [SurveyController::class, 'store'])->name('survei-1.store');
    Route::get('/survei-done', function () {
        return view('guest.skm_done'); })->name('survei-done');
});

// --- HALAMAN ADMIN RUANGAN ---
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/input_indikator', [InputIndikatorController::class, 'create'])->name('input_indikator');
        Route::post('/input_indikator', [InputIndikatorController::class, 'store'])->name('input_indikator.store');
        Route::get('/download-rekap', [DashboardController::class, 'downloadRekap'])->name('download_rekap');
    });
});

// --- HALAMAN SUPERADMIN ---
Route::middleware(['auth', 'role:superadmin'])->group(function () {

    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SDashboardController::class, 'index'])->name('dashboard');

        // Fitur Ruangan
        Route::get('/ruangan/{ruangan}/detail', [DetailIndikatorController::class, 'show'])->name('ruangan.detail');
        Route::get('/ruangan/{ruangan}/edit-indikator', [IndikatorRuanganController::class, 'edit'])->name('ruangan.edit_indikator');
        Route::post('/ruangan/update-indikator', [IndikatorRuanganController::class, 'update'])->name('ruangan.update_indikator');
        Route::post('/ruangan/add-indikator', [IndikatorRuanganController::class, 'store'])->name('ruangan.add_indikator');
        Route::post('/ruangan/deactivate-indikator', [IndikatorRuanganController::class, 'deactivate'])->name('ruangan.deactivate_indikator');

        // Master Indikator
        Route::get('/indikator-mutu/create', [IndikatorMutuController::class, 'create'])->name('indikator_mutu.create');
        Route::post('/indikator-mutu', [IndikatorMutuController::class, 'store'])->name('indikator_mutu.store');
        Route::delete('/indikator-mutu/{id}', [IndikatorMutuController::class, 'destroy'])->name('indikator_mutu.destroy');
        Route::put('/indikator-mutu/{id}', [IndikatorMutuController::class, 'update'])->name('indikator_mutu.update');

        // SKM Management
        Route::prefix('skm')->name('skm.')->group(function () {
            Route::get('/rekap', [SkmController::class, 'index'])->name('rekap');
            Route::get('/hasil', [SkmController::class, 'hasil'])->name('hasil');
            Route::get('/edit2', [SkmController::class, 'editPertanyaan'])->name('edit2');
            Route::put('/update-pertanyaan', [SkmController::class, 'updatePertanyaan'])->name('update_pertanyaan');
            Route::delete('/pertanyaan/{id}', [SkmController::class, 'destroyPertanyaan'])->name('destroy_pertanyaan');
            Route::get('/download', [SkmController::class, 'downloadRekap'])->name('download');
        });

        // Downloads
        Route::get('/download-rekap-indikator', [SDashboardController::class, 'downloadRekapIndikator'])->name('download_rekap_indikator');
        Route::get('/download-rekap', [DetailIndikatorController::class, 'downloadRekap'])->name('download_rekap');
    });

});
