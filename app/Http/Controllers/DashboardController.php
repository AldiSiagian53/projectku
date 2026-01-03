<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Validasi apakah data sensor valid (tidak error)
     * Data dianggap error jika semua nilai adalah null atau 0
     */
    private function isValidData($data)
    {
        if (!$data) {
            return false;
        }

        // Cek apakah ada setidaknya satu nilai yang valid (bukan null dan bukan 0)
        $hasValidValue = false;
        
        $fields = ['temperature', 'bat_v', 'panel_v', 'panel_power', 'charging_power', 'bat_percent', 'bat_wh'];
        
        foreach ($fields as $field) {
            $value = $data->$field ?? null;
            if ($value !== null && $value !== 0 && $value !== '') {
                $hasValidValue = true;
                break;
            }
        }

        return $hasValidValue;
    }

    /**
     * Format nilai untuk display - return "N/a" jika null/0/tidak valid
     * Format angka lebih sederhana dengan menghilangkan trailing zeros
     */
    private function formatValue($value, $format = 'number', $decimals = 2)
    {
        if ($value === null || $value === '' || $value === 0) {
            return 'N/a';
        }

        if ($format === 'number') {
            // Format dengan jumlah desimal yang ditentukan, lalu hapus trailing zeros
            $formatted = number_format((float)$value, $decimals, ',', '.');
            // Hapus trailing zeros dan koma jika tidak ada desimal
            $formatted = rtrim(rtrim($formatted, '0'), ',');
            return $formatted;
        } elseif ($format === 'integer') {
            return (int)$value;
        }

        return $value;
    }

    public function index()
    {
        // Cek status receiver terlebih dahulu
        $receiverStatus = Cache::get('receiver_status', 'active'); // Default aktif jika belum diset
        
        // Jika receiver nonaktif, tampilkan "N/a" karena tidak ada data baru yang masuk
        if ($receiverStatus === 'inactive') {
            return $this->getDefaultData(true); // true = tampilkan N/a
        }
        
        // Ambil data terbaru
        $latestData = SensorData::latest()->first();

        // Cek apakah data valid
        $isValid = $this->isValidData($latestData);

        // Jika tidak ada data atau data tidak valid, tampilkan "N/a"
        if (!$latestData || !$isValid) {
            return $this->getDefaultData(true); // true = tampilkan N/a
        }

        // Mapping data sensor ke dashboard dengan validasi
        $sisa_daya_plts = $latestData->panel_power ?? null;
        $sisa_daya_kendaraan = $latestData->bat_percent ?? null;
        $suhu = $latestData->temperature ?? null;
        $status_sisa_daya = $latestData->bat_percent ?? null;
        
        // Hitung perkiraan waktu pengisian (asumsi kapasitas baterai 50kWh dan daya 7kW)
        $kapasitas_baterai = 50; // kWh
        $daya_charging = $latestData->charging_power ?? null;
        $sisa_kapasitas = $sisa_daya_kendaraan !== null ? ($kapasitas_baterai * (100 - $sisa_daya_kendaraan)) / 100 : null;
        $perkiraan_waktu = ($daya_charging !== null && $daya_charging > 0 && $sisa_kapasitas !== null) 
            ? round($sisa_kapasitas / $daya_charging, 2) 
            : null;

        // Data untuk chart (7 hari terakhir)
        $chartData = $this->getChartData();

        // Data summary (raw values untuk JavaScript)
        $in_kwh_raw = $latestData->charging_power ?? null;
        $out_kwh_raw = $latestData->panel_power ?? null;
        
        // Formatted untuk display - tampilkan "N/a" jika null
        // Daya menggunakan 1 desimal untuk lebih sederhana
        $in_kwh = $this->formatValue($in_kwh_raw, 'number', 1);
        $out_kwh = $this->formatValue($out_kwh_raw, 'number', 1);

        $data = [
            // Summary cards - tampilkan "N/a" jika null
            // Daya panel: 1 desimal, Battery %: integer, Waktu: 1 desimal
            'sisa_daya_plts' => $this->formatValue($sisa_daya_plts, 'number', 1),
            'sisa_daya_kendaraan' => $sisa_daya_kendaraan !== null ? round($sisa_daya_kendaraan) : 'N/a',
            'perkiraan_waktu' => $this->formatValue($perkiraan_waktu, 'number', 1),
            
            // Chart data
            'energy_in' => $chartData['energy_in'],
            'energy_out' => $chartData['energy_out'],
            'labels' => $chartData['labels'],
            
            // Status data - tampilkan "N/a" jika null
            'suhu' => $this->formatValue($suhu, 'number', 1),
            'status_sisa_daya' => $status_sisa_daya !== null ? round($status_sisa_daya) : 'N/a',
            'in_kwh' => $in_kwh, // Formatted string untuk display
            'out_kwh' => $out_kwh, // Formatted string untuk display
            'in_kwh_raw' => $in_kwh_raw ?? 0, // Raw number untuk JavaScript (default 0 untuk chart)
            'out_kwh_raw' => $out_kwh_raw ?? 0, // Raw number untuk JavaScript (default 0 untuk chart)
            
            // Data tambahan untuk debugging - tampilkan "N/a" jika null
            // Voltage: 1 desimal, Battery Wh: 1 desimal
            'latest_data' => $latestData,
            'bat_v' => $this->formatValue($latestData->bat_v ?? null, 'number', 1),
            'panel_v' => $this->formatValue($latestData->panel_v ?? null, 'number', 1),
            'bat_wh' => $this->formatValue($latestData->bat_wh ?? null, 'number', 1),
        ];

        return view('dashboard', $data);
    }

    /**
     * Get system status data (static method untuk digunakan di view composer/sidebar)
     */
    public static function getSystemStatus()
    {
        // Cek status receiver terlebih dahulu
        $receiverStatus = Cache::get('receiver_status', 'active');
        
        // Jika receiver nonaktif, tampilkan "N/a"
        if ($receiverStatus === 'inactive') {
            return [
                'power_output' => 'N/a',
                'battery' => 'N/a',
                'efficiency' => 'N/a',
            ];
        }
        
        $latestData = SensorData::latest()->first();

        if (!$latestData) {
            return [
                'power_output' => 'N/a',
                'battery' => 'N/a',
                'efficiency' => 'N/a',
            ];
        }

        // Cek apakah data valid (ada setidaknya satu nilai yang bukan null/0)
        $hasValidValue = false;
        $fields = ['temperature', 'bat_v', 'panel_v', 'panel_power', 'charging_power', 'bat_percent', 'bat_wh'];
        foreach ($fields as $field) {
            $value = $latestData->$field ?? null;
            if ($value !== null && $value !== 0 && $value !== '') {
                $hasValidValue = true;
                break;
            }
        }

        if (!$hasValidValue) {
            return [
                'power_output' => 'N/a',
                'battery' => 'N/a',
                'efficiency' => 'N/a',
            ];
        }

        // Power Output = Panel Power (kW)
        $powerOutput = $latestData->panel_power ?? null;
        
        // Battery = Battery Percentage (%)
        $battery = $latestData->bat_percent ?? null;
        
        // Efficiency = (charging_power / panel_power) * 100 jika panel_power > 0
        $panelPower = $latestData->panel_power ?? null;
        $chargingPower = $latestData->charging_power ?? null;
        $efficiency = ($panelPower !== null && $panelPower > 0 && $chargingPower !== null) 
            ? round(($chargingPower / $panelPower) * 100, 1) 
            : null;
        
        // Limit efficiency max 100%
        if ($efficiency !== null) {
            $efficiency = min($efficiency, 100);
        }

        return [
            'power_output' => $powerOutput !== null ? number_format($powerOutput, 2) : 'N/a',
            'battery' => $battery !== null ? round($battery) : 'N/a',
            'efficiency' => $efficiency !== null ? round($efficiency) : 'N/a',
        ];
    }

    /**
     * Ambil data chart untuk 7 hari terakhir
     */
    private function getChartData()
    {
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();
        
        // Group by date dan ambil rata-rata per hari
        $dailyData = SensorData::where('created_at', '>=', $sevenDaysAgo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(charging_power) as avg_energy_in'),
                DB::raw('AVG(panel_power) as avg_energy_out')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Jika tidak ada data, return default
        if ($dailyData->isEmpty()) {
            return [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'energy_in' => [0, 0, 0, 0, 0, 0, 0],
                'energy_out' => [0, 0, 0, 0, 0, 0, 0],
            ];
        }

        // Generate labels untuk 7 hari terakhir
        $labels = [];
        $energyIn = [];
        $energyOut = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('D'); // Format: Mon, Tue, etc.
            
            $dayData = $dailyData->firstWhere('date', $dateStr);
            $energyIn[] = $dayData ? round($dayData->avg_energy_in, 2) : 0;
            $energyOut[] = $dayData ? round($dayData->avg_energy_out, 2) : 0;
        }

        return [
            'labels' => $labels,
            'energy_in' => $energyIn,
            'energy_out' => $energyOut,
        ];
    }

    /**
     * Dashboard dengan Grafana-like charts (Apache ECharts)
     */
    public function grafana()
    {
        // Ambil data terbaru
        $latestData = SensorData::latest()->first();

        // Jika tidak ada data, gunakan data default
        if (!$latestData) {
            return $this->getDefaultDataGrafana();
        }

        // Mapping data sensor ke dashboard
        $sisa_daya_plts = $latestData->panel_power ?? 0;
        $sisa_daya_kendaraan = $latestData->bat_percent ?? 0;
        $suhu = $latestData->temperature ?? 0;
        $status_sisa_daya = $latestData->bat_percent ?? 0;
        
        // Hitung perkiraan waktu pengisian
        $kapasitas_baterai = 50;
        $daya_charging = $latestData->charging_power ?? 1;
        $sisa_kapasitas = ($kapasitas_baterai * (100 - $sisa_daya_kendaraan)) / 100;
        $perkiraan_waktu = $daya_charging > 0 ? round($sisa_kapasitas / $daya_charging, 2) : 0;

        // Data summary
        $in_kwh_raw = $latestData->charging_power ?? 0;
        $out_kwh_raw = $latestData->panel_power ?? 0;
        $in_kwh = number_format($in_kwh_raw, 2);
        $out_kwh = number_format($out_kwh_raw, 2);

        $data = [
            'sisa_daya_plts' => number_format($sisa_daya_plts, 2),
            'sisa_daya_kendaraan' => number_format($sisa_daya_kendaraan, 2),
            'perkiraan_waktu' => number_format($perkiraan_waktu, 2),
            'suhu' => number_format($suhu, 1),
            'status_sisa_daya' => round($status_sisa_daya),
            'in_kwh' => $in_kwh,
            'out_kwh' => $out_kwh,
            'latest_data' => $latestData,
            'bat_v' => $latestData->bat_v ?? 0,
            'panel_v' => $latestData->panel_v ?? 0,
            'bat_wh' => $latestData->bat_wh ?? 0,
        ];

        return view('dashboard-grafana', $data);
    }

    /**
     * Return data default jika tidak ada data di database (untuk Grafana view)
     */
    private function getDefaultDataGrafana()
    {
        return view('dashboard-grafana', [
            'sisa_daya_plts' => '0.00',
            'sisa_daya_kendaraan' => '0.00',
            'perkiraan_waktu' => '0.00',
            'suhu' => '0.0',
            'status_sisa_daya' => 0,
            'in_kwh' => '0.00',
            'out_kwh' => '0.00',
            'latest_data' => null,
            'bat_v' => 0,
            'panel_v' => 0,
            'bat_wh' => 0,
        ]);
    }

    /**
     * Return data default jika tidak ada data di database
     * @param bool $showNa Jika true, tampilkan "N/a" untuk semua nilai
     */
    private function getDefaultData($showNa = false)
    {
        $defaultValue = $showNa ? 'N/a' : '0.00';
        $defaultValueFloat = $showNa ? 'N/a' : '0.0';
        $defaultValueInt = $showNa ? 'N/a' : 0;
        
        return view('dashboard', [
            'sisa_daya_plts' => $defaultValue,
            'sisa_daya_kendaraan' => $defaultValue,
            'perkiraan_waktu' => $defaultValue,
            'energy_in' => [0, 0, 0, 0, 0, 0, 0],
            'energy_out' => [0, 0, 0, 0, 0, 0, 0],
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'suhu' => $defaultValueFloat,
            'status_sisa_daya' => $defaultValueInt,
            'in_kwh' => $defaultValue,
            'out_kwh' => $defaultValue,
            'in_kwh_raw' => 0,
            'out_kwh_raw' => 0,
            'latest_data' => null,
            'bat_v' => $defaultValue,
            'panel_v' => $defaultValue,
            'bat_wh' => $defaultValueFloat,
        ]);
    }
}