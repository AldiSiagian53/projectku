<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Eco Power Monitoring</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- FONT MONTSERRAT --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    {{-- TAILWIND --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- APACHE ECHARTS (Grafana-like charts) --}}
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

    <style>
        body {
            font-family: "Montserrat", sans-serif !important;
        }
        .chart-container {
            height: 400px;
            width: 100%;
        }
        .small-chart {
            height: 300px;
        }
    </style>
</head>
<body class="bg-gray-100">
    {{-- SIDEBAR COMPONENT --}}
    <x-sidebar />

    {{-- CONTENT --}}
    <main class="md:ml-64 w-full p-4 md:p-8 transition-all duration-300">
        <h1 class="text-3xl font-bold">Eco Power Monitoring</h1>
        <p class="text-gray-600">Tenaga cerdas, masa depan hemat - Powered by Grafana-like Charts</p>

        {{-- DEBUG INFO --}}
        @if(isset($latest_data) && $latest_data)
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm">
            <p class="font-semibold text-yellow-800">🔍 Debug Info - Data Terbaru:</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2 text-yellow-700">
                <span>Temp: {{ $suhu }}°C</span>
                <span>Bat V: {{ $bat_v }}V</span>
                <span>Panel V: {{ $panel_v }}V</span>
                <span>Bat Wh: {{ $bat_wh }}Wh</span>
            </div>
            <p class="text-xs text-yellow-600 mt-2">Last Update: {{ $latest_data->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }} WIB</p>
        </div>
        @else
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">⚠️ Belum ada data sensor. Pastikan Arduino sudah mengirim data ke Laravel API.</p>
        </div>
        @endif

        {{-- ROW 1: SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="p-6 bg-blue-500 text-white rounded-xl shadow-md">
                <h5 class="text-lg font-semibold">Daya Panel Surya (kW)</h5>
                <h2 class="text-3xl font-bold mt-2">{{ $sisa_daya_plts }}</h2>
                <p class="text-sm opacity-80">Panel Power: {{ $panel_v }}V</p>
            </div>

            <div class="p-6 bg-white rounded-xl shadow-md">
                <h5 class="text-lg font-semibold text-gray-700">Kapasitas Baterai (%)</h5>
                <h2 class="text-3xl font-bold mt-2 text-gray-900">
                    @if($sisa_daya_kendaraan === 'N/a')
                        N/a
                    @else
                        {{ $sisa_daya_kendaraan }}%
                    @endif
                </h2>
                <p class="text-sm text-gray-500">Battery: {{ $bat_v }}V | {{ $bat_wh }}Wh</p>
            </div>

            <div class="p-6 bg-white rounded-xl shadow-md">
                <h5 class="text-lg font-semibold text-gray-700">Perkiraan Waktu Pengisian</h5>
                <h2 class="text-3xl font-bold mt-2 text-gray-900">
                    @if($perkiraan_waktu === 'N/a')
                        N/a
                    @else
                        {{ $perkiraan_waktu }} jam
                    @endif
                </h2>
                <p class="text-sm text-gray-500">Charging Power: {{ $in_kwh }} kW</p>
            </div>
        </div>

        {{-- ROW 2: TIME SERIES CHARTS --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            {{-- Temperature Chart --}}
            <div class="p-6 bg-white rounded-xl shadow-md">
                <h5 class="text-lg font-semibold text-gray-700 mb-4">Temperature (°C)</h5>
                <div id="temperatureChart" class="chart-container small-chart"></div>
            </div>

            {{-- Battery Voltage Chart --}}
            <div class="p-6 bg-white rounded-xl shadow-md">
                <h5 class="text-lg font-semibold text-gray-700 mb-4">Battery Voltage (V)</h5>
                <div id="batteryVoltageChart" class="chart-container small-chart"></div>
            </div>
        </div>

        {{-- ROW 3: POWER CHARTS --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            {{-- Panel Power Chart --}}
            <div class="p-6 bg-white rounded-xl shadow-md">
                <h5 class="text-lg font-semibold text-gray-700 mb-4">Panel Power (kW)</h5>
                <div id="panelPowerChart" class="chart-container small-chart"></div>
            </div>

            {{-- Charging Power Chart --}}
            <div class="p-6 bg-white rounded-xl shadow-md">
                <h5 class="text-lg font-semibold text-gray-700 mb-4">Charging Power (kW)</h5>
                <div id="chargingPowerChart" class="chart-container small-chart"></div>
            </div>
        </div>

        {{-- ROW 4: BATTERY STATUS --}}
        <div class="mt-6">
            <div class="p-6 bg-white rounded-xl shadow-md">
                <h5 class="text-lg font-semibold text-gray-700 mb-4">Battery Status</h5>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Battery Percentage Gauge --}}
                    <div>
                        <h6 class="text-sm font-semibold text-gray-600 mb-2">Battery Level (%)</h6>
                        <div id="batteryGaugeChart" class="chart-container small-chart"></div>
                    </div>

                    {{-- Battery Watt-hour --}}
                    <div>
                        <h6 class="text-sm font-semibold text-gray-600 mb-2">Battery Watt-hour (Wh)</h6>
                        <div id="batteryWhChart" class="chart-container small-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 5: COMBINED POWER CHART --}}
        <div class="mt-6">
            <div class="p-6 bg-white rounded-xl shadow-md">
                <h5 class="text-lg font-semibold text-gray-700 mb-4">Power Overview</h5>
                <div id="powerOverviewChart" class="chart-container"></div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Fetch data dari API
            fetch('/api/grafana/timeseries?hours=24')
                .then(response => response.json())
                .then(data => {
                    console.log('Data loaded:', data);
                    
                    // Initialize semua charts
                    initTemperatureChart(data.temperature);
                    initBatteryVoltageChart(data.battery_voltage);
                    initPanelPowerChart(data.panel_power);
                    initChargingPowerChart(data.charging_power);
                    initBatteryGaugeChart(data.battery_percent);
                    initBatteryWhChart(data.battery_wh);
                    initPowerOverviewChart(data);
                })
                .catch(error => {
                    console.error('Error loading data:', error);
                });

            // Auto-refresh setiap 1 menit
            setInterval(() => {
                location.reload();
            }, 10000);
        });

        function formatTimestamp(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit',
                timeZone: 'Asia/Jakarta'
            });
        }

        function initTemperatureChart(data) {
            const chart = echarts.init(document.getElementById('temperatureChart'));
            const option = {
                tooltip: { 
                    trigger: 'axis',
                    formatter: function(params) {
                        const value = params[0].value[1];
                        return params[0].name + '<br/>' + 
                               params[0].seriesName + ': ' + 
                               parseFloat(value).toLocaleString('id-ID', { 
                                   minimumFractionDigits: 0, 
                                   maximumFractionDigits: 1 
                               }) + '°C';
                    }
                },
                xAxis: {
                    type: 'time',
                    data: data.map(d => d[1])
                },
                yAxis: { 
                    type: 'value', 
                    name: '°C',
                    axisLabel: {
                        formatter: function(value) {
                            return parseFloat(value).toLocaleString('id-ID', { 
                                minimumFractionDigits: 0, 
                                maximumFractionDigits: 1 
                            });
                        }
                    }
                },
                series: [{
                    name: 'Temperature',
                    data: data.map(d => [d[1], d[0]]),
                    type: 'line',
                    smooth: true,
                    areaStyle: { opacity: 0.3 },
                    lineStyle: { color: '#ef4444' },
                    itemStyle: { color: '#ef4444' }
                }],
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true }
            };
            chart.setOption(option);
        }

        function initBatteryVoltageChart(data) {
            const chart = echarts.init(document.getElementById('batteryVoltageChart'));
            const option = {
                tooltip: { 
                    trigger: 'axis',
                    formatter: function(params) {
                        const value = params[0].value[1];
                        return params[0].name + '<br/>' + 
                               params[0].seriesName + ': ' + 
                               parseFloat(value).toLocaleString('id-ID', { 
                                   minimumFractionDigits: 0, 
                                   maximumFractionDigits: 1 
                               }) + 'V';
                    }
                },
                xAxis: { type: 'time', data: data.map(d => d[1]) },
                yAxis: { 
                    type: 'value', 
                    name: 'V',
                    axisLabel: {
                        formatter: function(value) {
                            return parseFloat(value).toLocaleString('id-ID', { 
                                minimumFractionDigits: 0, 
                                maximumFractionDigits: 1 
                            });
                        }
                    }
                },
                series: [{
                    name: 'Battery Voltage',
                    data: data.map(d => [d[1], d[0]]),
                    type: 'line',
                    smooth: true,
                    areaStyle: { opacity: 0.3 },
                    lineStyle: { color: '#10b981' },
                    itemStyle: { color: '#10b981' }
                }],
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true }
            };
            chart.setOption(option);
        }

        function initPanelPowerChart(data) {
            const chart = echarts.init(document.getElementById('panelPowerChart'));
            const option = {
                tooltip: { 
                    trigger: 'axis',
                    formatter: function(params) {
                        const value = params[0].value[1];
                        return params[0].name + '<br/>' + 
                               params[0].seriesName + ': ' + 
                               parseFloat(value).toLocaleString('id-ID', { 
                                   minimumFractionDigits: 0, 
                                   maximumFractionDigits: 1 
                               }) + ' kW';
                    }
                },
                xAxis: { type: 'time', data: data.map(d => d[1]) },
                yAxis: { 
                    type: 'value', 
                    name: 'kW',
                    axisLabel: {
                        formatter: function(value) {
                            return parseFloat(value).toLocaleString('id-ID', { 
                                minimumFractionDigits: 0, 
                                maximumFractionDigits: 1 
                            });
                        }
                    }
                },
                series: [{
                    name: 'Panel Power',
                    data: data.map(d => [d[1], d[0]]),
                    type: 'line',
                    smooth: true,
                    areaStyle: { opacity: 0.3 },
                    lineStyle: { color: '#2563eb' },
                    itemStyle: { color: '#2563eb' }
                }],
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true }
            };
            chart.setOption(option);
        }

        function initChargingPowerChart(data) {
            const chart = echarts.init(document.getElementById('chargingPowerChart'));
            const option = {
                tooltip: { trigger: 'axis' },
                xAxis: { type: 'time', data: data.map(d => d[1]) },
                yAxis: { type: 'value', name: 'kW' },
                series: [{
                    data: data.map(d => [d[1], d[0]]),
                    type: 'line',
                    smooth: true,
                    areaStyle: { opacity: 0.3 },
                    lineStyle: { color: '#0ea5e9' },
                    itemStyle: { color: '#0ea5e9' }
                }],
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true }
            };
            chart.setOption(option);
        }

        function initBatteryGaugeChart(data) {
            const chart = echarts.init(document.getElementById('batteryGaugeChart'));
            const latestValue = data.length > 0 ? data[data.length - 1][0] : 0;
            const option = {
                series: [{
                    type: 'gauge',
                    startAngle: 180,
                    endAngle: 0,
                    min: 0,
                    max: 100,
                    splitNumber: 10,
                    itemStyle: {
                        color: latestValue >= 70 ? '#10b981' : latestValue >= 40 ? '#f59e0b' : '#ef4444'
                    },
                    data: [{ value: latestValue, name: 'Battery %' }],
                    detail: { fontSize: 30, offsetCenter: [0, '70%'] }
                }]
            };
            chart.setOption(option);
        }

        function initBatteryWhChart(data) {
            const chart = echarts.init(document.getElementById('batteryWhChart'));
            const option = {
                tooltip: { trigger: 'axis' },
                xAxis: { type: 'time', data: data.map(d => d[1]) },
                yAxis: { type: 'value', name: 'Wh' },
                series: [{
                    data: data.map(d => [d[1], d[0]]),
                    type: 'line',
                    smooth: true,
                    areaStyle: { opacity: 0.3 },
                    lineStyle: { color: '#8b5cf6' },
                    itemStyle: { color: '#8b5cf6' }
                }],
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true }
            };
            chart.setOption(option);
        }

        function initPowerOverviewChart(data) {
            const chart = echarts.init(document.getElementById('powerOverviewChart'));
            const option = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Panel Power', 'Charging Power'] },
                xAxis: { type: 'time' },
                yAxis: { type: 'value', name: 'kW' },
                series: [
                    {
                        name: 'Panel Power',
                        data: data.panel_power.map(d => [d[1], d[0]]),
                        type: 'line',
                        smooth: true,
                        lineStyle: { color: '#2563eb' },
                        itemStyle: { color: '#2563eb' }
                    },
                    {
                        name: 'Charging Power',
                        data: data.charging_power.map(d => [d[1], d[0]]),
                        type: 'line',
                        smooth: true,
                        lineStyle: { color: '#0ea5e9' },
                        itemStyle: { color: '#0ea5e9' }
                    }
                ],
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true }
            };
            chart.setOption(option);
        }

        // Responsive charts
        window.addEventListener('resize', () => {
            echarts.getInstanceByDom(document.getElementById('temperatureChart'))?.resize();
            echarts.getInstanceByDom(document.getElementById('batteryVoltageChart'))?.resize();
            echarts.getInstanceByDom(document.getElementById('panelPowerChart'))?.resize();
            echarts.getInstanceByDom(document.getElementById('chargingPowerChart'))?.resize();
            echarts.getInstanceByDom(document.getElementById('batteryGaugeChart'))?.resize();
            echarts.getInstanceByDom(document.getElementById('batteryWhChart'))?.resize();
            echarts.getInstanceByDom(document.getElementById('powerOverviewChart'))?.resize();
        });
    </script>
</body>
</html>

