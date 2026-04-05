<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\PhotoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/token/generate', [TokenController::class, 'generate']);
    Route::get('/tokens', [TokenController::class, 'index']);
    Route::post('/token/{token}/deactivate', [TokenController::class, 'deactivate']);
});

Route::post('/token/login', [TokenController::class, 'login']);
Route::post('/token/revoke', [TokenController::class, 'revoke']);
Route::post('/token/logout-device', [TokenController::class, 'logout']);
Route::post('/token/logout-all', [TokenController::class, 'logoutAllDevices']);
Route::post('/token/check', [TokenController::class, 'checkToken']);
Route::delete('/token/{token}', [TokenController::class, 'destroy']);


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
