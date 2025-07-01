<?php

use App\Http\Controllers\CctvWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// CCTV Webhook routes (accessible by external service, no CSRF protection)
Route::prefix('webhooks/cctv')->group(function () {
    Route::post('camera-status', [CctvWebhookController::class, 'cameraStatusChange']);
    Route::post('service-status', [CctvWebhookController::class, 'serviceStatusChange']);
    Route::post('detection-event', [CctvWebhookController::class, 'detectionEvent']);
});

// Public CCTV file upload endpoint (no authentication required)
Route::post('/cctv/upload', [App\Http\Controllers\CctvUploadController::class, 'upload']);