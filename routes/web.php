<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CctvController;
use App\Http\Controllers\CctvSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetectionArchiveController;
use App\Http\Controllers\DummyAccountController;
use App\Http\Controllers\StorageSettingsController;
use App\Http\Controllers\SuspectedAccountController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::middleware(['admin'])->group(function () {
        // Dashboard route for admins
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/activities', [DashboardController::class, 'activities'])->name('dashboard.activities');

        // User management routes
        Route::resource('users', UserController::class);

        // Dummy Account management routes
        Route::resource('dummy-accounts', DummyAccountController::class);

        // Suspected Account management routes
        Route::resource('suspected-accounts', SuspectedAccountController::class);

        // CCTV management routes
        Route::resource('cctvs', CctvController::class);
        Route::get('cctvs/{id}/stream', [CctvController::class, 'stream'])->name('cctvs.stream');
        Route::get('cctvs/{id}/status', [CctvController::class, 'status'])->name('cctvs.status');
        Route::put('cctvs/detection-config', [CctvController::class, 'updateDetectionConfig'])->name('cctvs.detection.update');

        // CCTV Settings routes
        Route::get('settings/cctv', [CctvSettingsController::class, 'index'])->name('settings.cctv.index');
        Route::put('settings/cctv', [CctvSettingsController::class, 'update'])->name('settings.cctv.update');
        Route::post('settings/cctv/test', [CctvSettingsController::class, 'testConnection'])->name('settings.cctv.test');

        // Instagram Scraper routes
        Route::get('admin/scraper', [\App\Http\Controllers\AdminScraperController2::class, 'showForm'])->name('admin.scraper.form');
        Route::post('admin/scraper', [\App\Http\Controllers\AdminScraperController2::class, 'submit'])->name('admin.scraper.submit');
        Route::get('admin/scraper/results/{id}', [\App\Http\Controllers\AdminScraperController2::class, 'showResults'])->name('admin.scraper.results');
        Route::get('admin/scraper-results', [\App\Http\Controllers\AdminScraperController2::class, 'index'])->name('admin.scraper.results.list');

        Route::get('admin/analyze/{id}/{text}', [\App\Http\Controllers\SocialDetectionResultController::class, 'analyze'])->name('admin.scraper.analyze');

        // Social Detection Results route
        Route::get('admin/social-detection-results', [\App\Http\Controllers\SocialDetectionResultController::class, 'index'])->name('admin.social_detection_results.index');

        // Additional routes for navigation completeness
        Route::get('admin/security-alerts', function () {
            return view('admin.security-alerts');
        })->name('admin.security.alerts');

        Route::get('admin/zone-management', function () {
            return view('admin.zone-management');
        })->name('admin.security.zones');

        Route::get('admin/detection-archive', [DetectionArchiveController::class, 'index'])->name('admin.security.detection-archive');
        Route::get('admin/detection-archive/preview', [DetectionArchiveController::class, 'preview'])->name('admin.security.detection-archive.preview');
        Route::get('admin/detection-archive/download', [DetectionArchiveController::class, 'download'])->name('admin.security.detection-archive.download');

        // Storage Settings routes
        Route::get('admin/storage-settings', [StorageSettingsController::class, 'index'])->name('admin.storage.settings.index');
        Route::put('admin/storage-settings', [StorageSettingsController::class, 'updateSettings'])->name('admin.storage.settings.update');
        Route::post('admin/storage-settings/test', [StorageSettingsController::class, 'testConnection'])->name('admin.storage.settings.test');

        Route::get('admin/notifications', function () {
            return view('admin.notifications');
        })->name('admin.notifications');

        // WhatsApp Broadcast routes
        Route::prefix('broadcast')->group(function () {
            // Sender Number routes
            Route::resource('sender-numbers', App\Http\Controllers\SenderNumberController::class);

            // Broadcast Recipient routes
            Route::resource('broadcast-recipients', App\Http\Controllers\BroadcastRecipientController::class);

            // Broadcast sending
            Route::get('/send', [App\Http\Controllers\BroadcastController::class, 'showSendForm'])->name('broadcast.send');
            Route::post('/send', [App\Http\Controllers\BroadcastController::class, 'send'])->name('broadcast.send.post');
            Route::post('/get-detection-results', [App\Http\Controllers\BroadcastController::class, 'getDetectionResults'])
                ->name('broadcast.get-detection-results');
        });
    });

    // Home route for regular users
    Route::get('/home', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard');
        }
        return view('home');
    })->name('home');

    // CCTV view for regular users
    Route::get('/cctv-view', [CctvController::class, 'userView'])->name('cctv.user-view');

    // User-facing Instagram Scraper results
    Route::get('scraper-results', [\App\Http\Controllers\AdminScraperController2::class, 'userIndex'])->name('scraper.results.list');
    Route::get('scraper-results/{id}', [\App\Http\Controllers\AdminScraperController2::class, 'userShow'])->name('scraper.results');

    // Redirect authenticated users to their proper dashboard
    Route::get('/', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard');
        }
        return redirect()->route('home');
    });
});
