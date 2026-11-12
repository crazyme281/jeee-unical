<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('admin/login', fn () => redirect()->route('login'))->name('backpack.auth.login');
});
