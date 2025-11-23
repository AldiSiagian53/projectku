<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        // Dummy data alerts (improved & realistic)
        $alerts = [
            [
                'id' => 1,
                'type' => 'warning',
                'title' => 'Daya Mendekati Batas Maksimum',
                'description' => 'Konsumsi mencapai 4.7 kWh dari batas 5 kWh',
                'time' => '18:45 WIB',
                'icon' => '⚠️',
                'status' => 'active',
                'severity' => 'medium'
            ],
            [
                'id' => 2,
                'type' => 'danger',
                'title' => 'Baterai Overcharge',
                'description' => 'Level baterai 100% selama lebih dari 60 menit',
                'time' => '19:30 WIB',
                'icon' => '🔋',
                'status' => 'active',
                'severity' => 'high'
            ],
            [
                'id' => 3,
                'type' => 'warning',
                'title' => 'Suhu Baterai Meningkat',
                'description' => 'Temperatur mencapai 47°C (maks. aman 50°C)',
                'time' => '20:15 WIB',
                'icon' => '🌡️',
                'status' => 'resolved',
                'severity' => 'medium'
            ],
            [
                'id' => 4,
                'type' => 'info',
                'title' => 'Arus Pengisian Stabil',
                'description' => 'Arus charging stabil pada 18A selama 45 menit',
                'time' => '20:40 WIB',
                'icon' => '⚡',
                'status' => 'active',
                'severity' => 'low'
            ],
            [
                'id' => 5,
                'type' => 'danger',
                'title' => 'Penurunan Tegangan Signifikan',
                'description' => 'Tegangan turun menjadi 182V (normal 220–230V)',
                'time' => '21:10 WIB',
                'icon' => '📉',
                'status' => 'active',
                'severity' => 'high'
            ],
            [
                'id' => 6,
                'type' => 'warning',
                'title' => 'Inverter Overheat',
                'description' => 'Suhu inverter 63°C (potensi shutdown otomatis)',
                'time' => '17:50 WIB',
                'icon' => '🔥',
                'status' => 'resolved',
                'severity' => 'medium'
            ]
        ];

        // Statistics untuk Daily View
        $stats = [
            'total_alerts' => count($alerts),
            'active_alerts' => collect($alerts)->where('status', 'active')->count(),
            'resolved_alerts' => collect($alerts)->where('status', 'resolved')->count(),
            'critical_alerts' => collect($alerts)->where('severity', 'high')->count(),
            'high_severity' => collect($alerts)->where('severity', 'high')->count(),
            'medium_severity' => collect($alerts)->where('severity', 'medium')->count(),
            'low_severity' => collect($alerts)->where('severity', 'low')->count()
        ];

        // Monthly chart data (more realistic trend)
        $monthlyAlertData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'active_alerts' => [6, 9, 12, 10, 7, 5, 4, 6, 7, 10, 13, 9],
            'resolved_alerts' => [5, 8, 10, 8, 6, 4, 3, 5, 6, 8, 11, 7],
            'critical_alerts' => [1, 2, 3, 2, 1, 1, 0, 1, 2, 3, 4, 2]
        ];

        // Statistics untuk Monthly View (berdasarkan data grafik)
        $monthlyStats = [
            'total_monthly_alerts' => array_sum($monthlyAlertData['active_alerts']) + array_sum($monthlyAlertData['resolved_alerts']),
            'avg_active_alerts' => round(array_sum($monthlyAlertData['active_alerts']) / count($monthlyAlertData['active_alerts']), 1),
            'avg_resolved_alerts' => round(array_sum($monthlyAlertData['resolved_alerts']) / count($monthlyAlertData['resolved_alerts']), 1),
            'total_critical_alerts' => array_sum($monthlyAlertData['critical_alerts']),
            'peak_month' => $monthlyAlertData['labels'][array_search(max($monthlyAlertData['active_alerts']), $monthlyAlertData['active_alerts'])],
            'peak_alerts' => max($monthlyAlertData['active_alerts']),
            'best_month' => $monthlyAlertData['labels'][array_search(min($monthlyAlertData['active_alerts']), $monthlyAlertData['active_alerts'])],
            'best_alerts' => min($monthlyAlertData['active_alerts'])
        ];

        return view('alerts', compact('alerts', 'stats', 'monthlyAlertData', 'monthlyStats'));
    }
}