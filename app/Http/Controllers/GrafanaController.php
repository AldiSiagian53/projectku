<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GrafanaController extends Controller
{
    /**
     * API endpoint untuk Grafana datasource
     * Format: Grafana Simple JSON Datasource
     */
    public function query(Request $request)
    {
        $targets = $request->input('targets', []);
        $range = $request->input('range', []);
        $from = $range['from'] ?? Carbon::now()->subHours(24)->timestamp * 1000;
        $to = $range['to'] ?? Carbon::now()->timestamp * 1000;

        $fromDate = Carbon::createFromTimestampMs($from);
        $toDate = Carbon::createFromTimestampMs($to);

        $results = [];

        foreach ($targets as $target) {
            $targetName = $target['target'] ?? '';
            $refId = $target['refId'] ?? 'A';

            $data = $this->getTimeSeriesData($targetName, $fromDate, $toDate);

            $results[] = [
                'target' => $targetName,
                'datapoints' => $data,
                'refId' => $refId,
            ];
        }

        return response()->json($results);
    }

    /**
     * Get time series data berdasarkan target name dengan aggregation untuk mengurangi data points
     */
    private function getTimeSeriesData($target, $from, $to)
    {
        // Hitung durasi dalam menit
        $durationMinutes = $from->diffInMinutes($to);
        
        // Tentukan interval aggregation berdasarkan durasi
        // Semakin lama durasi, semakin besar interval untuk mengurangi jumlah data points
        $intervalMinutes = $this->calculateInterval($durationMinutes);
        
        // Tentukan field berdasarkan target
        $field = $this->getFieldName($target);
        
        if (!$field) {
            return [];
        }

        // Gunakan aggregation untuk mengurangi jumlah data points
        // Untuk fleksibilitas dan kompatibilitas dengan SQLite, kita gunakan approach manual
        return $this->getAggregatedDataByInterval($target, $from, $to, $intervalMinutes);
    }

    /**
     * Hitung interval aggregation berdasarkan durasi query
     */
    private function calculateInterval($durationMinutes)
    {
        // Maksimal 200 data points untuk performa yang baik
        $maxDataPoints = 200;
        $intervalMinutes = max(1, ceil($durationMinutes / $maxDataPoints));
        
        // Round up ke interval yang lebih "bersih" untuk readability
        if ($intervalMinutes >= 1440) { // >= 1 hari
            return 1440; // 1 hari
        } elseif ($intervalMinutes >= 60) { // >= 1 jam
            // Round ke 5, 10, 15, 30 menit atau 1 jam
            $rounded = [5, 10, 15, 30, 60];
            foreach ($rounded as $r) {
                if ($intervalMinutes <= $r) {
                    return $r;
                }
            }
            return 60; // Default 1 jam
        } else {
            // Round ke 1, 2, 5, 10, 15 menit
            $rounded = [1, 2, 5, 10, 15];
            foreach ($rounded as $r) {
                if ($intervalMinutes <= $r) {
                    return $r;
                }
            }
            return 1; // Default 1 menit
        }
    }

    /**
     * Get field name berdasarkan target
     */
    private function getFieldName($target)
    {
        switch (strtolower($target)) {
            case 'temperature':
            case 'temp':
                return 'temperature';
            case 'battery_voltage':
            case 'bat_v':
                return 'bat_v';
            case 'panel_voltage':
            case 'panel_v':
                return 'panel_v';
            case 'panel_power':
            case 'panel_w':
                return 'panel_power';
            case 'charging_power':
            case 'charging_w':
            case 'energy_in':
                return 'charging_power';
            case 'battery_percent':
            case 'bat_percent':
            case 'battery_level':
                return 'bat_percent';
            case 'battery_wh':
            case 'bat_wh':
                return 'bat_wh';
            default:
                return null;
        }
    }

    /**
     * Get aggregated data by custom interval (untuk interval < 1 jam)
     */
    private function getAggregatedDataByInterval($target, $from, $to, $intervalMinutes)
    {
        $field = $this->getFieldName($target);
        
        if (!$field) {
            return [];
        }

        $datapoints = [];
        $current = $from->copy();
        
        while ($current->lt($to)) {
            $intervalEnd = $current->copy()->addMinutes($intervalMinutes);
            if ($intervalEnd->gt($to)) {
                $intervalEnd = $to->copy();
            }
            
            // Ambil rata-rata data dalam interval ini
            $avgValue = SensorData::whereBetween('created_at', [$current, $intervalEnd])
                ->whereNotNull($field)
                ->avg($field);
            
            if ($avgValue !== null) {
                $datapoints[] = [
                    floatval($avgValue),
                    $current->timestamp * 1000
                ];
            }
            
            $current = $intervalEnd;
        }

        return $datapoints;
    }

    /**
     * Search endpoint untuk Grafana (list semua metric yang tersedia)
     */
    public function search(Request $request)
    {
        return response()->json([
            'temperature',
            'battery_voltage',
            'panel_voltage',
            'panel_power',
            'charging_power',
            'battery_percent',
            'battery_wh',
        ]);
    }

    /**
     * Annotations endpoint untuk Grafana
     */
    public function annotations(Request $request)
    {
        return response()->json([]);
    }

    /**
     * Tag keys endpoint untuk Grafana
     */
    public function tagKeys(Request $request)
    {
        return response()->json([]);
    }

    /**
     * Tag values endpoint untuk Grafana
     */
    public function tagValues(Request $request)
    {
        return response()->json([]);
    }

    /**
     * Dashboard view dengan embedded Grafana
     */
    public function dashboard()
    {
        $grafanaUrl = env('GRAFANA_URL', 'http://localhost:3000');
        $dashboardUid = env('GRAFANA_DASHBOARD_UID', 'solar-monitoring');
        
        return view('grafana.dashboard', compact('grafanaUrl', 'dashboardUid'));
    }

    /**
     * Get all sensor data untuk chart (alternative jika tidak pakai Grafana)
     * Dengan aggregation untuk mengurangi jumlah data points
     */
    public function getTimeSeriesApi(Request $request)
    {
        $hours = $request->input('hours', 24);
        $from = Carbon::now()->subHours($hours);
        $to = Carbon::now();
        
        // Hitung interval aggregation
        $durationMinutes = $from->diffInMinutes($to);
        $intervalMinutes = $this->calculateInterval($durationMinutes);

        $result = [
            'temperature' => [],
            'battery_voltage' => [],
            'panel_voltage' => [],
            'panel_power' => [],
            'charging_power' => [],
            'battery_percent' => [],
            'battery_wh' => [],
        ];

        // Gunakan aggregation untuk setiap field
        $fields = [
            'temperature' => 'temperature',
            'battery_voltage' => 'bat_v',
            'panel_voltage' => 'panel_v',
            'panel_power' => 'panel_power',
            'charging_power' => 'charging_power',
            'battery_percent' => 'bat_percent',
            'battery_wh' => 'bat_wh',
        ];

        foreach ($fields as $resultKey => $dbField) {
            $result[$resultKey] = $this->getAggregatedDataByInterval($resultKey, $from, $to, $intervalMinutes);
        }

        return response()->json($result);
    }
}



