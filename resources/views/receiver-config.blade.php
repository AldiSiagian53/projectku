<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Konfigurasi Receiver - Eco Power Monitoring</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-xl bg-slate-800/90 rounded-2xl shadow-2xl p-6 md:p-8 text-slate-50">
        <h1 class="text-2xl md:text-3xl font-bold mb-4">Konfigurasi Receiver</h1>
        <p class="text-slate-300 mb-6 text-sm">
            Atur WiFi, IP Laravel API, dan kredensial Blynk yang akan ditanam ke file <code>Dummy.ino</code>.
        </p>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/15 text-emerald-300 border border-emerald-500/40 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/15 text-red-300 border border-red-500/40 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/15 text-red-300 border border-red-500/40 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('receiver.config.update') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1">WiFi SSID</label>
                <input type="text" name="wifi_ssid" value="{{ old('wifi_ssid', $wifi_ssid ?? '') }}"
                       class="w-full rounded-lg bg-slate-900/70 border border-slate-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">WiFi Password</label>
                <input type="text" name="wifi_pass" value="{{ old('wifi_pass', $wifi_pass ?? '') }}"
                       class="w-full rounded-lg bg-slate-900/70 border border-slate-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                       required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">IP Laravel API</label>
                    <input type="text" name="laravel_ip" value="{{ old('laravel_ip', $laravel_ip ?? '192.168.1.102') }}"
                           class="w-full rounded-lg bg-slate-900/70 border border-slate-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Port Laravel API</label>
                    <input type="text" name="laravel_port" value="{{ old('laravel_port', $laravel_port ?? '8000') }}"
                           class="w-full rounded-lg bg-slate-900/70 border border-slate-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           required>
                </div>
            </div>

            <hr class="my-4 border-slate-700">

            <h2 class="text-sm font-semibold text-slate-200 mb-2">Blynk (opsional)</h2>

            <div>
                <label class="block text-sm font-semibold mb-1">BLYNK_TEMPLATE_ID</label>
                <input type="text" name="blynk_template_id" value="{{ old('blynk_template_id', $blynk_template_id ?? '') }}"
                       class="w-full rounded-lg bg-slate-900/70 border border-slate-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">BLYNK_TEMPLATE_NAME</label>
                <input type="text" name="blynk_template_name" value="{{ old('blynk_template_name', $blynk_template_name ?? '') }}"
                       class="w-full rounded-lg bg-slate-900/70 border border-slate-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">BLYNK_AUTH_TOKEN</label>
                <input type="text" name="blynk_auth_token" value="{{ old('blynk_auth_token', $blynk_auth_token ?? '') }}"
                       class="w-full rounded-lg bg-slate-900/70 border border-slate-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>

            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('receiver.index') }}"
                   class="inline-flex items-center text-xs text-slate-300 hover:text-white">
                    &larr; Kembali ke Receiver Control
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-sm font-semibold shadow-lg shadow-emerald-500/30">
                    Simpan & Tulis ke Dummy.ino
                </button>
            </div>
        </form>
    </div>
</body>
</html>


