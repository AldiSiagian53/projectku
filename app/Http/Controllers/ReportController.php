<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Data Daily (7 hari terakhir) - Sinkron dengan dashboard
        $dailyData = $this->getDailyData();
        $labelsDaily = $dailyData['labels'];
        $energyInDaily = $dailyData['energy_in'];
        $energyOutDaily = $dailyData['energy_out'];

        // Data Monthly (12 bulan terakhir) - dari database
        $monthlyData = $this->getMonthlyData();
        $labelsMonthly = $monthlyData['labels'];
        $energyInMonthly = $monthlyData['energy_in'];
        $energyOutMonthly = $monthlyData['energy_out'];

        return view('report', compact(
            'labelsDaily', 'energyInDaily', 'energyOutDaily',
            'labelsMonthly', 'energyInMonthly', 'energyOutMonthly'
        ));
    }

    /**
     * Get daily data (7 hari terakhir) - sama seperti dashboard
     */
    private function getDailyData()
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

        // Generate labels dan data untuk 7 hari terakhir
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
     * Get monthly data (12 bulan terakhir)
     */
    private function getMonthlyData()
    {
        $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();
        
        // Group by month dan ambil rata-rata per bulan (SQLite compatible)
        $monthlyData = SensorData::where('created_at', '>=', $twelveMonthsAgo)
            ->select(
                DB::raw('strftime("%Y-%m", created_at) as month'),
                DB::raw('AVG(charging_power) as avg_energy_in'),
                DB::raw('AVG(panel_power) as avg_energy_out'),
                DB::raw('COUNT(*) as data_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Generate labels dan data untuk 12 bulan terakhir
        $labels = [];
        $energyIn = [];
        $energyOut = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStr = $date->format('Y-m');
            $labels[] = $date->format('M'); // Format: Jan, Feb, etc.
            
            $monthData = $monthlyData->firstWhere('month', $monthStr);
            // Untuk monthly, kalikan rata-rata dengan jumlah hari estimasi (30 hari)
            if ($monthData && $monthData->data_count > 0) {
                $energyIn[] = round($monthData->avg_energy_in * 30, 2); // Estimasi bulanan
                $energyOut[] = round($monthData->avg_energy_out * 30, 2);
            } else {
                $energyIn[] = 0;
                $energyOut[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'energy_in' => $energyIn,
            'energy_out' => $energyOut,
        ];
    }
}