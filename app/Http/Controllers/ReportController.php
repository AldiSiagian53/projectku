<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Dummy Data Daily (Harian) - dalam kWh
        // Simulasi data 7 hari dengan variasi cuaca
        $labelsDaily = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        
        // Energy In - Produksi PLTS (tergantung cuaca)
        $energyInDaily = [
            15.8,  // Sen - Cerah
            12.3,  // Sel - Berawan
            18.2,  // Rab - Sangat cerah
            9.5,   // Kam - Hujan
            16.7,  // Jum - Cerah
            14.1,  // Sab - Berawan
            11.2   // Min - Mendung
        ];
        
        // Energy Out - Konsumsi daya (weekend biasanya lebih tinggi)
        $energyOutDaily = [
            10.5,  // Sen
            11.2,  // Sel  
            10.8,  // Rab
            12.1,  // Kam - AC extra
            9.8,   // Jum
            14.3,  // Sab - Weekend, penggunaan lebih
            13.7   // Min - Weekend
        ];

        // Dummy Data Monthly (Bulanan) - dalam kWh
        $labelsMonthly = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        // Energy In bulanan - Simulasi produksi PLTS sepanjang tahun
        $energyInMonthly = [
            420,   // Jan - Musim hujan, produksi rendah
            480,   // Feb
            550,   // Mar - Mulai musim kemarau
            620,   // Apr
            680,   // Mei - Puncak produksi
            650,   // Jun
            630,   // Jul
            610,   // Agu
            580,   // Sep
            520,   // Okt - Mulai musim hujan
            460,   // Nov
            430    // Des
        ];
        
        // Energy Out bulanan - Simulasi konsumsi sepanjang tahun
        $energyOutMonthly = [
            380,   // Jan - AC sedikit
            360,   // Feb
            350,   // Mar
            340,   // Apr
            370,   // Mei - AC mulai dinyalakan
            420,   // Jun - AC intensif
            480,   // Jul - Puncak konsumsi AC
            460,   // Agu
            430,   // Sep
            390,   // Okt
            370,   // Nov
            385    // Des
        ];

        return view('report', compact(
            'labelsDaily', 'energyInDaily', 'energyOutDaily',
            'labelsMonthly', 'energyInMonthly', 'energyOutMonthly'
        ));
    }
}