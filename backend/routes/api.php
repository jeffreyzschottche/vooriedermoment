<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SongRequestController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\SampleSelectionController;
use App\Http\Controllers\Api\V1\LyricsController;
use App\Http\Controllers\Api\V1\OrderExportController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Aanvragen voor nummers (intake -> concept-lyrics -> gestubde checkout)
    Route::post('/song-requests', [SongRequestController::class, 'store']);
    Route::post('/song-requests/{songRequest}/checkout', [SongRequestController::class, 'checkout']);

    // Sample selection (public, token-based)
    Route::get('/select/{token}', [SampleSelectionController::class, 'getSamples']);
    Route::post('/select/{token}', [SampleSelectionController::class, 'chooseSample']);

    // Order-export voor de lokale Suno-macro (key-beveiligd via X-Export-Key)
    Route::prefix('orders')->middleware('export.key')->group(function () {
        Route::get('/export', [OrderExportController::class, 'index']);
        Route::post('/export/ack', [OrderExportController::class, 'ack']);
    });

    // Lyrics generator (public)
    Route::get('/lyrics/categories', [LyricsController::class, 'categories']);
    Route::get('/lyrics/songform', [LyricsController::class, 'songform']);
    Route::get('/lyrics/preview/{category}', [LyricsController::class, 'preview']);
    Route::post('/lyrics/generate', [LyricsController::class, 'generate']);
    Route::post('/lyrics/general', [LyricsController::class, 'generateGeneral'])
        ->middleware('throttle:5,1');

    // Email verification
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->name('verification.verify');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/email/resend', [AuthController::class, 'resendVerification']);
    });

    // Admin routes (protected with simple token for now)
    // TODO: Add proper admin authentication
    Route::prefix('admin')->middleware('admin.token')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/requests', [AdminController::class, 'allRequests']);
        Route::get('/requests/pending', [AdminController::class, 'pendingRequests']);
        Route::get('/requests/{songRequest}', [AdminController::class, 'showRequest']);
        Route::post('/requests/{songRequest}/samples', [AdminController::class, 'uploadSamples']);
        Route::post('/requests/{songRequest}/sample-urls', [AdminController::class, 'addSampleUrls']);
        Route::post('/requests/{songRequest}/choose', [AdminController::class, 'markSampleChosen']);
        Route::post('/requests/{songRequest}/release', [AdminController::class, 'markReleased']);
    });
});
