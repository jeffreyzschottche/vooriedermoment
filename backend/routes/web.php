<?php

use App\Http\Controllers\AdminUploadController;
use App\Http\Controllers\CustomerSampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin sample upload routes
Route::get('/admin/upload/{token}', [AdminUploadController::class, 'show'])->name('admin.upload');
Route::post('/admin/upload/{token}', [AdminUploadController::class, 'store'])->name('admin.upload.store');
Route::post('/admin/upload/{token}/send', [AdminUploadController::class, 'sendToCustomer'])->name('admin.upload.send');

// Customer sample selection routes
Route::get('/samples/{token}', [CustomerSampleController::class, 'showPassword'])->name('samples.password');
Route::post('/samples/{token}/verify', [CustomerSampleController::class, 'verifyPassword'])->name('samples.verify');
Route::get('/samples/{token}/listen', [CustomerSampleController::class, 'listen'])->name('samples.listen');
Route::post('/samples/{token}/choose', [CustomerSampleController::class, 'choose'])->name('samples.choose');
