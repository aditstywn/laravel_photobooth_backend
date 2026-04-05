<?php

use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

Route::controller(TokenController::class)->group(function () {
    Route::get('/login', 'showLoginPage')->name('login');
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/token-dashboard', 'dashboard')->name('token-dashboard');
});

Route::get('/download/{token}', [PhotoController::class, 'downloadPortal']);
Route::get('/download/{token}/item/{type}/{photoResult?}', [PhotoController::class, 'downloadItem']);
