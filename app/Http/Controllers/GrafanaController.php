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
     * Get time series data berdasarkan target name
     */
    private function getTimeSeriesData($target, $from, $to)
    {
        $data = SensorData::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $datapoints = [];

        switch (strtolower($target)) {
            case 'temperature':
            case 'temp':
                foreach ($data as $row) {
                    $datapoints[] = [
                        floatval($row->temperature ?? 0),
                        strtotime($row->created_at) * 1000
                    ];
                }
                break;

            case 'battery_voltage':
            case 'bat_v':
                foreach ($data as $row) {
                    $datapoints[] = [
                        floatval($row->bat_v ?? 0),
                        strtotime($row->created_at) * 1000
                    ];
                }
                break;

            case 'panel_voltage':
            case 'panel_v':
                foreach ($data as $row) {
                    $datapoints[] = [
                        floatval($row->panel_v ?? 0),
                        strtotime($row->created_at) * 1000
                    ];
                }
                break;

            case 'panel_power':
            case 'panel_w':
                foreach ($data as $row) {
                    $datapoints[] = [
                        floatval($row->panel_power ?? 0),
                        strtotime($row->created_at) * 1000
                    ];
                }
                break;

            case 'charging_power':
            case 'charging_w':
            case 'energy_in':
                foreach ($data as $row) {
                    $datapoints[] = [
                        floatval($row->charging_power ?? 0),
                        strtotime($row->created_at) * 1000
                    ];
                }
                break;

            case 'battery_percent':
            case 'bat_percent':
            case 'battery_level':
                foreach ($data as $row) {
                    $datapoints[] = [
                        floatval($row->bat_percent ?? 0),
                        strtotime($row->created_at) * 1000
                    ];
                }
                break;

            case 'battery_wh':
            case 'bat_wh':
                foreach ($data as $row) {
                    $datapoints[] = [
                        floatval($row->bat_wh ?? 0),
                        strtotime($row->created_at) * 1000
                    ];
                }
                break;

            default:
                // Return empty jika target tidak dikenal
                break;
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
     */
    public function getTimeSeriesApi(Request $request)
    {
        $hours = $request->input('hours', 24);
        $from = Carbon::now()->subHours($hours);
        
        $data = SensorData::where('created_at', '>=', $from)
            ->orderBy('created_at')
            ->get();

        $result = [
            'temperature' => [],
            'battery_voltage' => [],
            'panel_voltage' => [],
            'panel_power' => [],
            'charging_power' => [],
            'battery_percent' => [],
            'battery_wh' => [],
        ];

        foreach ($data as $row) {
            $timestamp = strtotime($row->created_at) * 1000;
            
            $result['temperature'][] = [$row->temperature ?? 0, $timestamp];
            $result['battery_voltage'][] = [$row->bat_v ?? 0, $timestamp];
            $result['panel_voltage'][] = [$row->panel_v ?? 0, $timestamp];
            $result['panel_power'][] = [$row->panel_power ?? 0, $timestamp];
            $result['charging_power'][] = [$row->charging_power ?? 0, $timestamp];
            $result['battery_percent'][] = [$row->bat_percent ?? 0, $timestamp];
            $result['battery_wh'][] = [$row->bat_wh ?? 0, $timestamp];
        }

        return response()->json($result);
    }
}


