<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Activate admin access via hidden key (Ctrl+Shift+Q from login page)
     */
    public function activate(Request $request)
    {
        // Check if request is from login page (via referer check or direct access)
        $referer = $request->headers->get('referer');
        $isFromLoginPage = $referer && (str_contains($referer, '/login') || str_contains($referer, route('login')));
        
        // Only allow activation if not logged in (must be from login page)
        if (!Auth::check() && ($isFromLoginPage || $request->expectsJson())) {
            // Set session untuk enable admin access
            session(['admin_access_enabled' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Admin access activated. You can access admin panel after login.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Activation only allowed from login page'
        ], 403);
    }

    /**
     * Display admin panel
     */
    public function index()
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            // Jika belum login, redirect ke admin login
            return redirect()->route('admin.login')->withErrors([
                'login_error' => 'Anda harus login sebagai admin untuk mengakses admin panel'
            ]);
        }

        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang bisa mengakses halaman ini.');
        }

        // Get statistics
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalRegularUsers = User::where('role', 'user')->count();
        $latestUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Ambil semua users
        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin', compact('users', 'totalUsers', 'totalAdmins', 'totalRegularUsers', 'latestUsers'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        // Pastikan user sudah login dan adalah admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang bisa membuat user baru.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:user,admin',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'email.max' => 'Email maksimal 255 karakter',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role harus user atau admin',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.index')
                ->withErrors($validator)
                ->withInput();
        }

        // Create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);

        // Log activity
        \Log::info('New user created by admin', [
            'created_by' => Auth::id(),
            'new_user_id' => $user->id,
            'new_user_email' => $user->email
        ]);

        return redirect()->route('admin.index')
            ->with('success', 'User berhasil dibuat! User baru dapat login dengan email dan password yang telah dibuat.');
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        // Pastikan user sudah login dan adalah admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang bisa menghapus user.');
        }

        // Jangan izinkan menghapus diri sendiri
        if ($id == Auth::id()) {
            return redirect()->route('admin.index')
                ->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri']);
        }

        $user = User::findOrFail($id);
        $userEmail = $user->email;
        $user->delete();

        // Log activity
        \Log::info('User deleted by admin', [
            'deleted_by' => Auth::id(),
            'deleted_user_email' => $userEmail
        ]);

        return redirect()->route('admin.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
