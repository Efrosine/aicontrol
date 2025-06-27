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

    // Redirect authenticated users to their proper dashboard
    Route::get('/', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard');
        }
        return redirect()->route('home');
    });
});
