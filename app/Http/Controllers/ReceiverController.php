<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReceiverController extends Controller
{
    /**
     * Tampilkan halaman kontrol receiver
     */
    public function index()
    {
        $receiverStatus = Cache::get('receiver_status', 'active'); // Default aktif jika belum diset
        
        return view('receiver', [
            'receiver_status' => $receiverStatus
        ]);
    }

    /**
     * Mulai menerima data dari transmitter
     */
    public function start(Request $request)
    {
        try {
            // Set status receiver menjadi aktif
            Cache::put('receiver_status', 'active', now()->addDays(30)); // Status aktif selama 30 hari
            
            Log::info('Receiver diaktifkan', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Receiver telah diaktifkan. Sistem sekarang akan menerima data dari transmitter.',
                'receiver_status' => 'active'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error mengaktifkan receiver', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengaktifkan receiver: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Berhenti menerima data dari transmitter
     */
    public function stop(Request $request)
    {
        try {
            // Set status receiver menjadi nonaktif
            Cache::put('receiver_status', 'inactive', now()->addDays(30)); // Status nonaktif selama 30 hari
            
            Log::info('Receiver dinonaktifkan', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Receiver telah dinonaktifkan. Sistem tidak akan menerima data dari transmitter.',
                'receiver_status' => 'inactive'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error menonaktifkan receiver', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menonaktifkan receiver: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cek status receiver saat ini
     */
    public function status(Request $request)
    {
        try {
            $receiverStatus = Cache::get('receiver_status', 'active'); // Default aktif jika belum diset
            
            return response()->json([
                'status' => 'success',
                'receiver_status' => $receiverStatus,
                'message' => $receiverStatus === 'active' 
                    ? 'Receiver sedang aktif dan menerima data' 
                    : 'Receiver sedang nonaktif dan tidak menerima data'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error mendapatkan status receiver', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendapatkan status receiver: ' . $e->getMessage()
            ], 500);
        }
    }
}

