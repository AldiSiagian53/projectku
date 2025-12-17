<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data terbaru
        $latestData = SensorData::latest()->first();

        // Jika tidak ada data, gunakan data default
        if (!$latestData) {
            return $this->getDefaultData();
        }

        // Mapping data sensor ke dashboard
        $sisa_daya_plts = $latestData->panel_power ?? 0; // Panel Power dalam kW
        $sisa_daya_kendaraan = $latestData->bat_percent ?? 0; // Battery Percentage
        $suhu = $latestData->temperature ?? 0;
        $status_sisa_daya = $latestData->bat_percent ?? 0; // Untuk gauge chart
        
        // Hitung perkiraan waktu pengisian (asumsi kapasitas baterai 50kWh dan daya 7kW)
        $kapasitas_baterai = 50; // kWh
        $daya_charging = $latestData->charging_power ?? 1; // kW
        $sisa_kapasitas = ($kapasitas_baterai * (100 - $sisa_daya_kendaraan)) / 100; // kWh
        $perkiraan_waktu = $daya_charging > 0 ? round($sisa_kapasitas / $daya_charging, 2) : 0;

        // Data untuk chart (7 hari terakhir)
        $chartData = $this->getChartData();

        // Data summary (raw values untuk JavaScript)
        $in_kwh_raw = $latestData->charging_power ?? 0; // Energy in (charging power)
        $out_kwh_raw = $latestData->panel_power ?? 0; // Energy out (panel power)
        
        // Formatted untuk display
        $in_kwh = number_format($in_kwh_raw, 2);
        $out_kwh = number_format($out_kwh_raw, 2);

        $data = [
            // Summary cards
            'sisa_daya_plts' => number_format($sisa_daya_plts, 2),
            'sisa_daya_kendaraan' => number_format($sisa_daya_kendaraan, 2),
            'perkiraan_waktu' => number_format($perkiraan_waktu, 2),
            
            // Chart data
            'energy_in' => $chartData['energy_in'],
            'energy_out' => $chartData['energy_out'],
            'labels' => $chartData['labels'],
            
            // Status data
            'suhu' => number_format($suhu, 1),
            'status_sisa_daya' => round($status_sisa_daya),
            'in_kwh' => $in_kwh, // Formatted string untuk display
            'out_kwh' => $out_kwh, // Formatted string untuk display
            'in_kwh_raw' => $in_kwh_raw, // Raw number untuk JavaScript
            'out_kwh_raw' => $out_kwh_raw, // Raw number untuk JavaScript
            
            // Data tambahan untuk debugging
            'latest_data' => $latestData,
            'bat_v' => $latestData->bat_v ?? 0,
            'panel_v' => $latestData->panel_v ?? 0,
            'bat_wh' => $latestData->bat_wh ?? 0,
        ];

        return view('dashboard', $data);
    }

    /**
     * Get system status data (static method untuk digunakan di view composer/sidebar)
     */
    public static function getSystemStatus()
    {
        $latestData = SensorData::latest()->first();

        if (!$latestData) {
            return [
                'power_output' => '0.00',
                'battery' => '0',
                'efficiency' => '0',
            ];
        }

        // Power Output = Panel Power (kW)
        $powerOutput = $latestData->panel_power ?? 0;
        
        // Battery = Battery Percentage (%)
        $battery = $latestData->bat_percent ?? 0;
        
        // Efficiency = (charging_power / panel_power) * 100 jika panel_power > 0
        $panelPower = $latestData->panel_power ?? 0;
        $chargingPower = $latestData->charging_power ?? 0;
        $efficiency = $panelPower > 0 ? round(($chargingPower / $panelPower) * 100, 1) : 0;
        
        // Limit efficiency max 100%
        $efficiency = min($efficiency, 100);

        return [
            'power_output' => number_format($powerOutput, 2),
            'battery' => round($battery),
            'efficiency' => round($efficiency),
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
     */
    private function getDefaultData()
    {
        return view('dashboard', [
            'sisa_daya_plts' => '0.00',
            'sisa_daya_kendaraan' => '0.00',
            'perkiraan_waktu' => '0.00',
            'energy_in' => [0, 0, 0, 0, 0, 0, 0],
            'energy_out' => [0, 0, 0, 0, 0, 0, 0],
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'suhu' => '0.0',
            'status_sisa_daya' => 0,
            'in_kwh' => '0.00',
            'out_kwh' => '0.00',
            'in_kwh_raw' => 0,
            'out_kwh_raw' => 0,
            'latest_data' => null,
            'bat_v' => 0,
            'panel_v' => 0,
            'bat_wh' => 0,
        ]);
    }
}