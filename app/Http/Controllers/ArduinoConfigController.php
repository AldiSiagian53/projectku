<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArduinoConfigController extends Controller
{
    /**
     * Tampilkan form konfigurasi receiver (WiFi, IP Laravel, Blynk).
     */
    public function edit()
    {
        // Baca nilai saat ini dari file Dummy.ino (opsional, untuk prefill)
        $inoPath = base_path('projectku/parshing_laravel/Dummy/Dummy.ino');
        $content = file_exists($inoPath) ? file_get_contents($inoPath) : '';

        // Default values
        $defaults = [
            'wifi_ssid'          => '',
            'wifi_pass'          => '',
            'laravel_ip'         => '192.168.1.102',
            'laravel_port'       => '8000',
            'blynk_template_id'  => '',
            'blynk_template_name'=> '',
            'blynk_auth_token'   => '',
        ];

        // Jika masih berupa file hasil replace, kita tidak parsing lagi secara detail.
        // User akan isi ulang lewat form.

        return view('receiver-config', $defaults);
    }

    /**
     * Simpan konfigurasi dan tulis ulang file Dummy.ino
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'wifi_ssid'           => 'required|string',
            'wifi_pass'           => 'required|string',
            'laravel_ip'          => 'required|string',
            'laravel_port'        => 'required|string',
            'blynk_template_id'   => 'nullable|string',
            'blynk_template_name' => 'nullable|string',
            'blynk_auth_token'    => 'nullable|string',
        ]);

        // Baca template (Dummy.ino yang sudah kita ubah jadi placeholder)
        $templatePath = base_path('projectku/parshing_laravel/Dummy/Dummy.ino');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'File template Dummy.ino tidak ditemukan.');
        }

        $template = file_get_contents($templatePath);

        // Replace placeholder
        $search = [
            '{{WIFI_SSID}}',
            '{{WIFI_PASS}}',
            '{{LARAVEL_IP}}',
            '{{LARAVEL_PORT}}',
            '{{BLYNK_TEMPLATE_ID}}',
            '{{BLYNK_TEMPLATE_NAME}}',
            '{{BLYNK_AUTH_TOKEN}}',
        ];

        $replace = [
            $data['wifi_ssid'],
            $data['wifi_pass'],
            $data['laravel_ip'],
            $data['laravel_port'],
            $data['blynk_template_id'] ?? '',
            $data['blynk_template_name'] ?? '',
            $data['blynk_auth_token'] ?? '',
        ];

        $output = str_replace($search, $replace, $template);

        // Tulis ulang file Dummy.ino dengan konfigurasi baru
        file_put_contents($templatePath, $output);

        return back()->with('success', 'Konfigurasi receiver berhasil diperbarui dan disimpan ke Dummy.ino.');
    }
}


