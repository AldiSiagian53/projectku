<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AlertController extends Controller
{
    /**
     * Generate daftar alert dari data SensorData (24 jam terakhir)
     */
    protected static function generateAlertsFromSensor(): array
    {
        $receiverStatus = Cache::get('receiver_status', 'active');

        if ($receiverStatus === 'inactive') {
            return [];
        }

        $since = Carbon::now()->subHours(24);
        $sensorRows = SensorData::where('created_at', '>=', $since)
            ->orderBy('created_at', 'desc')
            ->get();

        $alerts = [];
        $idCounter = 1;

        foreach ($sensorRows as $row) {
                $time = $row->created_at->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB';

            // Suhu tinggi
            if ($row->temperature !== null) {
                if ($row->temperature >= 65) {
                    $alerts[] = [
                            'id' => $idCounter++,
                            'type' => 'danger',
                            'title' => 'Suhu Baterai Melebihi Batas Aman',
                            'description' => 'Temperatur mencapai ' . number_format($row->temperature, 1) . '°C (batas aman 65°C).',
                            'time' => $time,
                            'icon' => '🌡️',
                            'status' => 'active',
                            'severity' => 'high',
                    ];
                } elseif ($row->temperature >= 50) {
                    $alerts[] = [
                            'id' => $idCounter++,
                            'type' => 'warning',
                            'title' => 'Suhu Baterai Meningkat',
                            'description' => 'Temperatur saat ini ' . number_format($row->temperature, 1) . '°C, mendekati batas aman 65°C.',
                            'time' => $time,
                            'icon' => '🌡️',
                            'status' => 'active',
                            'severity' => 'medium',
                    ];
                }
            }

            // Persentase baterai rendah / hampir habis
            if ($row->bat_percent !== null) {
                if ($row->bat_percent <= 15) {
                    $alerts[] = [
                            'id' => $idCounter++,
                            'type' => 'danger',
                            'title' => 'Baterai Kendaraan Hampir Habis',
                            'description' => 'Level baterai turun ke ' . round($row->bat_percent) . '%. Segera lakukan pengisian.',
                            'time' => $time,
                            'icon' => '🔋',
                            'status' => 'active',
                            'severity' => 'high',
                    ];
                } elseif ($row->bat_percent <= 30) {
                    $alerts[] = [
                            'id' => $idCounter++,
                            'type' => 'warning',
                            'title' => 'Baterai Kendaraan Rendah',
                            'description' => 'Level baterai saat ini ' . round($row->bat_percent) . '%.',
                            'time' => $time,
                            'icon' => '🔋',
                            'status' => 'active',
                            'severity' => 'medium',
                    ];
                }
            }

            // Daya panel sangat rendah di siang hari (indikasi masalah panel)
            if ($row->panel_power !== null && $row->panel_power < 0.5) {
                $hour = (int)$row->created_at->setTimezone('Asia/Jakarta')->format('H');
                if ($hour >= 9 && $hour <= 15) {
                    $alerts[] = [
                            'id' => $idCounter++,
                            'type' => 'warning',
                            'title' => 'Produksi Panel Surya Rendah',
                            'description' => 'Daya panel hanya ' . number_format($row->panel_power, 2) . ' kW pada jam siang. Periksa kondisi panel.',
                            'time' => $time,
                            'icon' => '☀️',
                            'status' => 'active',
                            'severity' => 'medium',
                    ];
                }
            }

            // Overcharge / charging tidak wajar
            if ($row->charging_power !== null && $row->bat_percent !== null) {
                if ($row->bat_percent >= 98 && $row->charging_power > 0.2) {
                    $alerts[] = [
                            'id' => $idCounter++,
                            'type' => 'danger',
                            'title' => 'Potensi Overcharge Baterai',
                            'description' => 'Baterai sudah ' . round($row->bat_percent) . '% tetapi masih menerima daya ' . number_format($row->charging_power, 2) . ' kW.',
                            'time' => $time,
                            'icon' => '⚡',
                            'status' => 'active',
                            'severity' => 'high',
                    ];
                }
            }

            // Tegangan baterai terlalu rendah
            if ($row->bat_v !== null && $row->bat_v < 10.5) {
                $alerts[] = [
                        'id' => $idCounter++,
                        'type' => 'danger',
                        'title' => 'Tegangan Baterai Terlalu Rendah',
                        'description' => 'Tegangan baterai hanya ' . number_format($row->bat_v, 2) . ' V.',
                        'time' => $time,
                        'icon' => '📉',
                        'status' => 'active',
                        'severity' => 'high',
                ];
            }
        }

        // Hapus duplikat berdasarkan title + description agar tidak terlalu banyak
        $alerts = collect($alerts)
            ->unique(function ($a) {
                return $a['title'] . '|' . $a['description'];
            })
            ->values()
            ->all();

        return $alerts;
    }

    /**
     * Hitung ringkasan alert untuk sidebar (dipakai di AppServiceProvider)
     */
    public static function getSidebarAlertCounts(): array
    {
        $alerts = self::generateAlertsFromSensor();

        return [
            'total' => count($alerts),
            'active' => collect($alerts)->where('status', 'active')->count(),
            'critical' => collect($alerts)->where('severity', 'high')->count(),
        ];
    }

    public function index()
    {
        $alerts = self::generateAlertsFromSensor();

        // Statistics untuk Daily View (sinkron dengan daftar alert di atas)
        $stats = [
            'total_alerts' => count($alerts),
            'active_alerts' => collect($alerts)->where('status', 'active')->count(),
            'resolved_alerts' => collect($alerts)->where('status', 'resolved')->count(),
            'critical_alerts' => collect($alerts)->where('severity', 'high')->count(),
            'high_severity' => collect($alerts)->where('severity', 'high')->count(),
            'medium_severity' => collect($alerts)->where('severity', 'medium')->count(),
            'low_severity' => collect($alerts)->where('severity', 'low')->count(),
        ];

        // Monthly chart data: sementara masih dummy, tapi konsisten struktur-nya
        $monthlyAlertData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'active_alerts' => [6, 9, 12, 10, 7, 5, 4, 6, 7, 10, 13, 9],
            'resolved_alerts' => [5, 8, 10, 8, 6, 4, 3, 5, 6, 8, 11, 7],
            'critical_alerts' => [1, 2, 3, 2, 1, 1, 0, 1, 2, 3, 4, 2],
        ];

        $monthlyStats = [
            'total_monthly_alerts' => array_sum($monthlyAlertData['active_alerts']) + array_sum($monthlyAlertData['resolved_alerts']),
            'avg_active_alerts' => round(array_sum($monthlyAlertData['active_alerts']) / count($monthlyAlertData['active_alerts']), 1),
            'avg_resolved_alerts' => round(array_sum($monthlyAlertData['resolved_alerts']) / count($monthlyAlertData['resolved_alerts']), 1),
            'total_critical_alerts' => array_sum($monthlyAlertData['critical_alerts']),
        ];

        return view('alerts', compact('alerts', 'stats', 'monthlyAlertData', 'monthlyStats'));
    }
}