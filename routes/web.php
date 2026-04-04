<?php

use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/download/{token}', [PhotoController::class, 'downloadPortal']);
Route::get('/download/{token}/item/{type}/{photoResult?}', [PhotoController::class, 'downloadItem']);
