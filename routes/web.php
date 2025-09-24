<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

// Admin Routes
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/input_indikator', function () {
    return view('admin.input_indikator');
})->name('admin.input_indikator');



// User Routes
Route::get('/SKM/dashboard', function () {
    return view('guest.dashboard');
})->name('guest.dashboard');
Route::get('/SKM/data_responden', function () {
    return view('guest.skm1'); })->name('guest.data_responden');
Route::get('/SKM/survei-1', function () {
    return view('guest.skm2'); })->name('guest.survei-1');
Route::get('/SKM/survei-2', function () {
    return view('guest.skm3'); })->name('guest.survei-2');
Route::get('/SKM/survei-done', function () {
    return view('guest.skm_done'); })->name('guest.survei-done');



