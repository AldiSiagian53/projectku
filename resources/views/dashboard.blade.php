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

    {{-- CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    <style>
        body {
            font-family: "Montserrat", sans-serif !important;
        }

        /* pastikan canvas mengikuti ukuran parent */
        canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
        }

        /* wrapper khusus agar chart punya tinggi yang konsisten */
        .chart-box { height: 16rem; }   /* set h-64 (16rem) equivalent */
        .chart-box-sm { height: 10rem; } /* smaller box for gauge if needed */

        /* pastikan tidak ada overflow horizontal di main content */
        main { overflow-x: hidden; }
    </style>

    {{-- Stack untuk styles dari komponen --}}
    @stack('styles')
</head>
<body class="bg-gray-100">

    {{-- SIDEBAR COMPONENT --}}
    <x-sidebar />

    {{-- CONTENT --}}
    <main class="md:ml-64 w-full p-4 md:p-8 transition-all duration-300" id="mainContent">

        {{-- TITLE --}}
        <h1 class="text-3xl font-bold">Eco Power Monitoring</h1>
        <p class="text-gray-600">Tenaga cerdas, masa depan hemat.</p>

        {{-- ROW 1: TOP SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

            <div class="p-6 bg-blue-500 text-white rounded-xl shadow-md transition-transform duration-300 hover:scale-105">
                <h5 class="text-lg font-semibold">Sisa Daya PLTS (kW)</h5>
                <h2 class="text-3xl font-bold mt-2">{{ $sisa_daya_plts }}</h2>
                <p class="text-sm opacity-80">Sisa daya saat ini</p>
            </div>

            <div class="p-6 bg-white rounded-xl shadow-md transition-transform duration-300 hover:scale-105">
                <h5 class="text-lg font-semibold text-gray-700">Sisa Daya Kendaraan (%)</h5>
                <h2 class="text-3xl font-bold mt-2 text-gray-900">{{ $sisa_daya_kendaraan }}%</h2>
                <p class="text-sm text-gray-500">Presentase baterai saat ini</p>
            </div>

            <div class="p-6 bg-white rounded-xl shadow-md transition-transform duration-300 hover:scale-105">
                <h5 class="text-lg font-semibold text-gray-700">Perkiraan Waktu Pengisian (Jam)</h5>
                <h2 class="text-3xl font-bold mt-2 text-gray-900">{{ $perkiraan_waktu }} jam</h2>
                <p class="text-sm text-gray-500">Berdasarkan daya 7 kW</p>
            </div>

        </div>

        {{-- ROW 2: Energy In & Energy Out --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8 w-full max-w-full">

            {{-- Energy In --}}
            <div class="p-6 bg-white rounded-xl shadow-md transition-transform duration-300 hover:scale-[1.02]">
                <h5 class="text-lg font-semibold text-gray-700 mb-4">Energy In</h5>
                <div class="chart-box w-full overflow-hidden rounded-md bg-white">
                    <canvas id="energyInChart"></canvas>
                </div>
            </div>

            {{-- Energy Out --}}
            <div class="p-6 bg-white rounded-xl shadow-md transition-transform duration-300 hover:scale-[1.02]">
                <h5 class="text-lg font-semibold text-gray-700 mb-4">Energy Out</h5>
                <div class="chart-box w-full overflow-hidden rounded-md bg-white">
                    <canvas id="energyOutChart"></canvas>
                </div>
            </div>

        </div>

        {{-- ROW 3: COMBINED ENERGY FEATURE --}}
        <div class="mt-8 w-full max-w-full">
            <div class="p-6 bg-white rounded-xl shadow-md w-full transition-transform duration-300 hover:scale-[1.01]">

                <h5 class="text-lg font-semibold text-gray-700 mb-4">Energy Status</h5>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- GAUGE (KIRI) --}}
                    <div class="flex flex-col items-center justify-center col-span-1">
                        <div class="w-40 h-32">
                            <canvas id="statusGauge"></canvas>
                        </div>

                        <h2 class="text-3xl font-bold mt-3">{{ $status_sisa_daya }}%</h2>
                        <p class="text-gray-500">Suhu {{ $suhu }}°C</p>
                    </div>

                    {{-- ENERGY TOTAL CHART (KANAN, LEBAR 2 KOLOM) --}}
                    <div class="col-span-2">
                        <div class="chart-box">
                            <canvas id="energyTotalChart"></canvas>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ROW 4: Status Energy (paling bawah) --}}
        <div class="mt-8">
            <div class="p-6 bg-white rounded-xl shadow-md transition-transform duration-300 hover:scale-[1.01]">
                <h5 class="text-lg font-semibold text-gray-700 mb-4">Status Energy (Total)</h5>
                <div class="h-48 w-full">
                    <canvas id="statusTotalChart"></canvas>
                </div>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            /* ------------------------------------
             * Data dari Controller
             * ------------------------------------ */
            const labels = {!! json_encode($labels ?? ['Sen','Sel','Rab','Kam','Jum','Sab','Min']) !!};
            const energyInData = {!! json_encode($energy_in ?? [2.5, 3.7, 4.1, 3.8, 4.5, 3.2, 2.7]) !!};
            const energyOutData = {!! json_encode($energy_out ?? [2.3, 3.5, 4.3, 3.9, 4.7, 3.5, 2.8]) !!};
            const gaugeValue = {{ $status_sisa_daya ?? 92 }};
            const inKwh = {{ $in_kwh ?? 3.5 }};
            const outKwh = {{ $out_kwh ?? 6.2 }};

            /* ------------------------------------
             * Fungsi Utility Chart
             * ------------------------------------ */
            function createLineChart(selector, dataSet, color, bgColor) {
                const el = document.getElementById(selector);
                if (!el) return;

                new Chart(el, {
                    type: "line",
                    data: {
                        labels,
                        datasets: [{
                            label: dataSet.label,
                            data: dataSet.data,
                            borderColor: color,
                            backgroundColor: bgColor,
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            /* ------------------------------------
             * Energy In Chart
             * ------------------------------------ */
            createLineChart(
                "energyInChart",
                { label: "Energy In (kWh)", data: energyInData },
                "#2563eb",
                "rgba(37, 99, 235, 0.15)"
            );

            /* ------------------------------------
             * Energy Out Chart
             * ------------------------------------ */
            createLineChart(
                "energyOutChart", 
                { label: "Energy Out (kWh)", data: energyOutData },
                "#0ea5e9",
                "rgba(14, 165, 233, 0.15)"
            );

            /* ------------------------------------
             * Gauge Chart (Half Donut) - FIXED
             * ------------------------------------ */
            (function createGauge() {
                const el = document.getElementById("statusGauge");
                if (!el) return;

                // Clear any existing chart
                if (el.chart) {
                    el.chart.destroy();
                }

                const gaugeChart = new Chart(el, {
                    type: "doughnut",
                    data: {
                        labels: ["Sisa Baterai", "Remaining"],
                        datasets: [{
                            data: [gaugeValue, 100 - gaugeValue],
                            backgroundColor: [
                                getGaugeColor(gaugeValue), // Dynamic color based on value
                                "#f3f4f6" // Light gray for remaining
                            ],
                            borderWidth: 0,
                            cutout: "70%"
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        rotation: -90, // -90deg for top half
                        circumference: 180, // 180deg for half circle
                        plugins: {
                            legend: { 
                                display: false 
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.parsed}%`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true
                        }
                    }
                });

                // Store chart reference
                el.chart = gaugeChart;
            })();

            /* ------------------------------------
             * Energy Total Chart (Menggantikan Energy Comparison)
             * ------------------------------------ */
            (function createEnergyTotalChart() {
                const ctx = document.getElementById("energyTotalChart");
                if (!ctx) return;

                // Clear any existing chart
                if (ctx.chart) {
                    ctx.chart.destroy();
                }

                const totalChart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: "Total Energy (kWh)",
                                data: energyInData.map((inVal, index) => inVal + (energyOutData[index] || 0)),
                                borderColor: "#10b981",
                                backgroundColor: "rgba(16, 185, 129, 0.1)",
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3
                            },
                            {
                                label: "Energy In (kWh)",
                                data: energyInData,
                                borderColor: "#2563eb",
                                backgroundColor: "rgba(37, 99, 235, 0.1)",
                                tension: 0.4,
                                fill: false,
                                borderWidth: 2,
                                borderDash: [5, 5]
                            },
                            {
                                label: "Energy Out (kWh)", 
                                data: energyOutData,
                                borderColor: "#0ea5e9",
                                backgroundColor: "rgba(14, 165, 233, 0.1)",
                                tension: 0.4,
                                fill: false,
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'kWh'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Hari'
                                }
                            }
                        }
                    }
                });

                // Store chart reference
                ctx.chart = totalChart;
            })();

            /* ------------------------------------
             * Status Total Chart (Bottom Chart) - FIXED
             * ------------------------------------ */
            (function createStatusTotalChart() {
                const ctx = document.getElementById("statusTotalChart");
                if (!ctx) return;

                // Clear any existing chart
                if (ctx.chart) {
                    ctx.chart.destroy();
                }

                const statusChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: ["Energy In", "Energy Out", "Net Energy"],
                        datasets: [{
                            label: "kWh",
                            data: [inKwh, outKwh, inKwh - outKwh],
                            backgroundColor: [
                                "#2563eb", // Energy In - blue
                                "#0ea5e9", // Energy Out - light blue  
                                inKwh - outKwh >= 0 ? "#10b981" : "#ef4444" // Net - green if positive, red if negative
                            ],
                            borderRadius: 8,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.parsed.y} kWh`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'kWh'
                                }
                            }
                        }
                    }
                });

                // Store chart reference
                ctx.chart = statusChart;
            })();

            /* ------------------------------------
             * Helper function untuk gauge color
             * ------------------------------------ */
            function getGaugeColor(value) {
                if (value >= 70) return "#10b981"; // Green
                if (value >= 40) return "#f59e0b"; // Yellow
                if (value >= 20) return "#f97316"; // Orange
                return "#ef4444"; // Red
            }

        }); // END DOMContentLoaded
    </script>

    {{-- Stack untuk scripts dari komponen --}}
    @stack('scripts')

</body>
</html>