<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AdminLoginController extends Controller
{
    /**
     * Show admin login form
     */
    public function showAdminLoginForm()
    {
        // Check if admin access is enabled via hidden key
        if (!session('admin_access_enabled')) {
            abort(404); // Return 404 to hide the admin login
        }

        // If already logged in as admin, redirect to admin panel
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.index');
        }

        // Get statistics
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalRegularUsers = User::where('role', 'user')->count();

        return view('admin-login', compact('totalUsers', 'totalAdmins', 'totalRegularUsers'));
    }

    /**
     * Handle admin login request
     */
    public function adminLogin(Request $request)
    {
        // Check if admin access is enabled via hidden key
        if (!session('admin_access_enabled')) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and is admin
        if (!$user) {
            return redirect()->route('admin.login')
                ->withErrors(['login_error' => 'Email atau password salah'])
                ->withInput();
        }

        // Check if user is admin
        if ($user->role !== 'admin') {
            return redirect()->route('admin.login')
                ->withErrors(['login_error' => 'Akun ini bukan admin. Hanya admin yang bisa login di sini.'])
                ->withInput();
        }

        // Verify password
        if (!Hash::check($credentials['password'], $user->password)) {
            return redirect()->route('admin.login')
                ->withErrors(['login_error' => 'Email atau password salah'])
                ->withInput();
        }

        // Login user
        Auth::login($user, $request->has('remember'));

        // Clear admin access session (no longer needed after login)
        session()->forget('admin_access_enabled');

        return redirect()->route('admin.index')
            ->with('success', 'Selamat datang, ' . $user->name . '!');
    }
}
