<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});
Route::get('/dashboard/admin', function () {
    return view('admin.dashboard');
});
