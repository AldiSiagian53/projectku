<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        // Ambil data user yang sedang login
        $user = Auth::user();

        // Jika tidak ada user yang login, redirect ke login
        if (!$user) {
            return redirect()->route('login');
        }

        return view('settings', compact('user'));
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        // Pastikan user sudah login
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors([
                'auth_error' => 'Anda harus login untuk mengubah password'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->route('settings.index')
                ->withErrors($validator)
                ->withInput();
        }

        // Validasi current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai'
            ])->withInput();
        }

        // Update password di database
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Log activity (optional)
        \Log::info('User password changed', [
            'user_id' => $user->id,
            'user_email' => $user->email
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Password berhasil diubah! Silakan gunakan password baru saat login berikutnya.');
    }
}
