<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CctvController;
use App\Http\Controllers\DummyAccountController;
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
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // User management routes
        Route::resource('users', UserController::class);

        // Dummy Account management routes
        Route::resource('dummy-accounts', DummyAccountController::class);

        // Suspected Account management routes
        Route::resource('suspected-accounts', SuspectedAccountController::class);

        // CCTV management routes
        Route::resource('cctvs', CctvController::class);

        // Instagram Scraper routes
        Route::get('admin/scraper', [\App\Http\Controllers\AdminScraperController2::class, 'showForm'])->name('admin.scraper.form');
        Route::post('admin/scraper', [\App\Http\Controllers\AdminScraperController2::class, 'submit'])->name('admin.scraper.submit');
        Route::get('admin/scraper/results/{id}', [\App\Http\Controllers\AdminScraperController2::class, 'showResults'])->name('admin.scraper.results');
        Route::get('admin/scraper-results', [\App\Http\Controllers\AdminScraperController2::class, 'index'])->name('admin.scraper.results.list');

        Route::get('admin/analyze/{id}/{text}', [\App\Http\Controllers\SocialDetectionResultController::class, 'analyze'])->name('admin.scraper.analyze');

        // Social Detection Results route
        Route::get('admin/social-detection-results', [\App\Http\Controllers\SocialDetectionResultController::class, 'index'])->name('admin.social_detection_results.index');

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
