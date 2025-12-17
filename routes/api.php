<?php

use App\Http\Controllers\SolarController;
use App\Http\Controllers\GrafanaController;
use Illuminate\Support\Facades\Route;

// Route untuk menerima data dari Arduino/ESP32
Route::post('/sensor', [SolarController::class, 'store']);

// Route untuk testing dari browser (GET) - opsional, bisa dihapus di production
Route::get('/sensor/test', function () {
    return response()->json([
        'message' => 'API endpoint aktif!',
        'endpoint' => '/api/sensor',
        'method' => 'POST',
        'format' => [
            'data' => 'CSV string dengan 7 nilai: temperature,batV,panelV,panelW,chargingW,batPct,batWh'
        ],
        'example' => [
            'data' => '25.5,12.3,18.7,50.2,45.1,85.0,1200.5'
        ]
    ]);
});

// Grafana API endpoints (Simple JSON Datasource)
Route::post('/grafana/query', [GrafanaController::class, 'query']);
Route::post('/grafana/search', [GrafanaController::class, 'search']);
Route::post('/grafana/annotations', [GrafanaController::class, 'annotations']);
Route::post('/grafana/tag-keys', [GrafanaController::class, 'tagKeys']);
Route::post('/grafana/tag-values', [GrafanaController::class, 'tagValues']);

// API untuk time-series data (alternative)
Route::get('/grafana/timeseries', [GrafanaController::class, 'getTimeSeriesApi']);