<?php

use App\Http\Controllers\PhotoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/photos/storage-stats', [PhotoController::class, 'storageStats']);
Route::delete('/photos/destroy-all', [PhotoController::class, 'destroyAll']);
Route::apiResource('photos', PhotoController::class);
Route::get('/download/{token}/zip', [PhotoController::class, 'downloadArchive']);
Route::get('/download/{token}/qr', [PhotoController::class, 'qr']);
Route::get('/download/{token}/qr-image', [PhotoController::class, 'qrImage']);


Route::post('/photos/{photo}/upload-video', [PhotoController::class, 'uploadVideo']);
Route::get('/download/{token}/video', [PhotoController::class, 'downloadVideo']);
Route::get('/download/{token}/video/qr', [PhotoController::class, 'qrVideo']);
Route::get('/download/{token}/video/qr-image', [PhotoController::class, 'qrImageVideo']);
