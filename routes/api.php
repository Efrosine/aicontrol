<?php

use App\Http\Controllers\CctvWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

Route::post('/upload', function (Request $request) {
    Log::info('Upload endpoint called', ['request' => $request->all()]);
    return response()->json(['message' => 'Upload logged']);
});