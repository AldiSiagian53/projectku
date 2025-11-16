<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek username (name) atau email dan password
        $user = \App\Models\User::where(function($query) use ($credentials) {
            $query->where('name', $credentials['username'])
                  ->orWhere('email', $credentials['username']);
        })->first();

        // Debug: Log untuk troubleshooting (hapus di production)
        if (!$user) {
            \Log::info('Login attempt failed: User not found', ['username' => $credentials['username']]);
        } elseif (!Hash::check($credentials['password'], $user->password)) {
            \Log::info('Login attempt failed: Password mismatch', ['username' => $credentials['username']]);
        }

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login_error' => 'Maaf dashboard ini hanya bisa di pakai oleh yang berkepentingan',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}

