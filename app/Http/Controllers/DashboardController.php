<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data mockup, bisa diganti dengan query DB
        $data = [
            'sisa_daya_plts' => 50.25,
            'sisa_daya_kendaraan' => 62.50,
            'perkiraan_waktu' => 4.23,
            'energy_in' => [2.5, 3.7, 4.1, 3.8, 4.5, 3.2, 2.7],
            'energy_out' => [2.3, 3.5, 4.3, 3.9, 4.7, 3.5, 2.8],
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'suhu' => 45,
            'status_sisa_daya' => 92,
            'in_kwh' => 2.5,
            'out_kwh' => 6.2,
        ];

        return view('dashboard', $data);
    }
}