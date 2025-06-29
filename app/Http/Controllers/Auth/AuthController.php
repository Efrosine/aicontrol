<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Log login activity
            ActivityService::logUserActivity(
                action: 'login',
                subject: 'session',
                status: 'success',
                user: Auth::user()
            );
            
            // Redirect based on user role
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('dashboard');
            } else {
                return redirect()->intended('home');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout activity before logging out
        if ($user) {
            ActivityService::logUserActivity(
                action: 'logout',
                subject: 'session',
                status: 'info',
                user: $user
            );
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
