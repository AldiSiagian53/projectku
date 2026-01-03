<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SolarController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Cek status receiver - jika nonaktif, tolak data
            $receiverStatus = Cache::get('receiver_status', 'active'); // Default aktif jika belum diset
            
            if ($receiverStatus === 'inactive') {
                Log::info('Data ditolak karena receiver nonaktif', $request->all());
                
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'Receiver sedang nonaktif. Aktifkan receiver terlebih dahulu melalui /api/receiver/start'
                ], 403);
            }

            // Ambil data dari request
            // Format bisa berupa JSON atau CSV string
            $data = $request->all();

            Log::info('Raw data diterima', $data);

            // Jika data dalam format CSV (dari Python bridge atau serial monitor)
            if (isset($data['data']) && is_string($data['data'])) {
                $csvData = $data['data'];
                $parsedData = $this->parseCsvData($csvData);
            } 
            // Jika data dalam format JSON langsung
            elseif (isset($data['temperature']) || isset($data['bat_v']) || isset($data['batV'])) {
                $parsedData = [
                    'temperature' => $data['temperature'] ?? null,
                    'bat_v' => $data['bat_v'] ?? $data['batV'] ?? null,
                    'panel_v' => $data['panel_v'] ?? $data['panelV'] ?? null,
                    'panel_power' => $data['panel_power'] ?? $data['panelW'] ?? null,
                    'charging_power' => $data['charging_power'] ?? $data['chargingW'] ?? null,
                    'bat_percent' => $data['bat_percent'] ?? $data['batPct'] ?? null,
                    'bat_wh' => $data['bat_wh'] ?? $data['batWh'] ?? null,
                ];
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format data tidak dikenali'
                ], 400);
            }

            // Simpan ke database
            $sensorData = SensorData::create($parsedData);

            Log::info('Data berhasil disimpan', ['id' => $sensorData->id]);

            return response()->json([
                'status' => 'ok',
                'message' => 'Data berhasil diterima dan disimpan',
                'data' => $sensorData
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error menyimpan data sensor', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse data CSV dari Arduino
     * Format: temperature,batV,panelV,panelW,chargingW,batPct,batWh
     */
    private function parseCsvData(string $csvString): array
    {
        // Bersihkan whitespace
        $csvString = trim($csvString);
        
        // Split by comma
        $values = explode(',', $csvString);

        if (count($values) !== 7) {
            throw new \Exception('Format CSV tidak valid. Harus ada 7 nilai yang dipisahkan koma.');
        }

        return [
            'temperature' => !empty($values[0]) ? (float) trim($values[0]) : null,
            'bat_v' => !empty($values[1]) ? (float) trim($values[1]) : null,
            'panel_v' => !empty($values[2]) ? (float) trim($values[2]) : null,
            'panel_power' => !empty($values[3]) ? (float) trim($values[3]) : null,
            'charging_power' => !empty($values[4]) ? (float) trim($values[4]) : null,
            'bat_percent' => !empty($values[5]) ? (float) trim($values[5]) : null,
            'bat_wh' => !empty($values[6]) ? (float) trim($values[6]) : null,
        ];
    }
}
