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

        /* z-index untuk sidebar dan overlay */
        #mobileSidebar { z-index: 40; }
        #mobileOverlay { z-index: 30; }
        #mobileMenuBtn { z-index: 50; }

        /* Animasi Sidebar Transparan dengan Glass Effect */
        .sidebar-glass {
            background: rgba(30, 58, 138, 0.85) !important; /* blue-900 dengan transparansi */
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Animasi Slide dengan Bounce */
        @keyframes slideInBounce {
            0% {
                transform: translateX(-100%);
                opacity: 0;
            }
            70% {
                transform: translateX(10px);
                opacity: 0.8;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutBounce {
            0% {
                transform: translateX(0);
                opacity: 1;
            }
            30% {
                transform: translateX(-10px);
                opacity: 0.8;
            }
            100% {
                transform: translateX(-100%);
                opacity: 0;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(37, 99, 235, 0);
            }
        }

        /* Class untuk animasi */
        .sidebar-open {
            animation: slideInBounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        .sidebar-close {
            animation: slideOutBounce 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        .overlay-open {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .overlay-close {
            animation: fadeOut 0.3s ease-out forwards;
        }

        .menu-btn-pulse {
            animation: pulseGlow 2s infinite;
        }

        /* Hover effects untuk menu items */
        .menu-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }

        .menu-item:hover::before {
            left: 100%;
        }

        .menu-item:hover {
            transform: translateX(8px);
            background: rgba(255, 255, 255, 0.1) !important;
        }

        /* Animasi untuk logo/text */
        @keyframes textGlow {
            0%, 100% {
                text-shadow: 0 0 5px rgba(255,255,255,0.5);
            }
            50% {
                text-shadow: 0 0 15px rgba(255,255,255,0.8), 0 0 20px rgba(255,255,255,0.6);
            }
        }

        .logo-glow {
            animation: textGlow 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-100">

    {{-- OVERLAY UNTUK MOBILE --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-30 z-30 hidden md:hidden"></div>

    {{-- SIDEBAR DESKTOP --}}
    <aside class="w-64 bg-blue-900 text-white p-6 min-h-screen fixed left-0 top-0 z-10 md:block hidden flex flex-col">
        <div class="flex-1">
            <h2 class="text-2xl font-bold mb-8 logo-glow">MONITORING PLTS</h2>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="menu-item block py-3 px-4 rounded-lg hover:bg-blue-700 transition-all duration-300 border-l-4 border-transparent hover:border-white">
                    📊 Dashboard
                </a>
                <a href="#" class="menu-item block py-3 px-4 rounded-lg hover:bg-blue-700 transition-all duration-300 border-l-4 border-transparent hover:border-white">
                    📈 Report
                </a>
                <a href="#" class="menu-item block py-3 px-4 rounded-lg hover:bg-blue-700 transition-all duration-300 border-l-4 border-transparent hover:border-white">
                    🔔 Alerts
                </a>
            </nav>
        </div>

        {{-- LOGOUT BUTTON --}}
        <div class="mt-auto pt-4 border-t border-blue-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item w-full flex items-center py-3 px-4 rounded-lg hover:bg-blue-700 transition-all duration-300 text-left border-l-4 border-transparent hover:border-red-400">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                 Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MOBILE MENU BUTTON --}}
    <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-40 bg-blue-600 text-white p-3 rounded-xl shadow-2xl menu-btn-pulse transition-all duration-300 hover:scale-110 hover:bg-blue-700">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    {{-- MOBILE SIDEBAR --}}
    <aside id="mobileSidebar" class="sidebar-glass w-64 text-white p-6 min-h-screen fixed left-0 top-0 z-40 transform -translate-x-full flex flex-col">
        <div class="flex-1">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold logo-glow">MONITORING PLTS</h2>
                <button id="closeMobileMenu" class="text-white hover:text-gray-300 transition-transform duration-300 hover:scale-125 hover:rotate-90">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <nav class="space-y-3">
                <a href="{{ route('dashboard') }}" class="menu-item block py-3 px-4 rounded-lg hover:bg-white hover:bg-opacity-20 transition-all duration-300 border-l-4 border-transparent hover:border-white transform hover:translate-x-2">
                    📊 Dashboard
                </a>
                <a href="#" class="menu-item block py-3 px-4 rounded-lg hover:bg-white hover:bg-opacity-20 transition-all duration-300 border-l-4 border-transparent hover:border-white transform hover:translate-x-2">
                    📈 Report
                </a>
                <a href="#" class="menu-item block py-3 px-4 rounded-lg hover:bg-white hover:bg-opacity-20 transition-all duration-300 border-l-4 border-transparent hover:border-white transform hover:translate-x-2">
                    🔔 Alerts
                </a>
            </nav>
        </div>

        {{-- LOGOUT BUTTON MOBILE --}}
        <div class="mt-auto pt-4 border-t border-white border-opacity-20">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item w-full flex items-center py-3 px-4 rounded-lg hover:bg-white hover:bg-opacity-20 transition-all duration-300 text-left border-l-4 border-transparent hover:border-red-400 transform hover:translate-x-2">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

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

        /* ------------------------------------
         * MOBILE SIDEBAR TOGGLE FUNCTIONALITY DENGAN ANIMASI
         * ------------------------------------ */
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const closeMobileMenu = document.getElementById('closeMobileMenu');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const mainContent = document.getElementById('mainContent');
            
            let isSidebarOpen = false;

            // Fungsi untuk buka sidebar dengan animasi
            function openMobileSidebar() {
                if (isSidebarOpen) return;
                
                isSidebarOpen = true;
                
                // Hapus class close dan tambah class open
                mobileSidebar.classList.remove('sidebar-close');
                mobileSidebar.classList.add('sidebar-open');
                mobileSidebar.classList.remove('-translate-x-full');
                
                // Show overlay dengan animasi
                if (mobileOverlay) {
                    mobileOverlay.classList.remove('hidden');
                    mobileOverlay.classList.add('overlay-open');
                }
                
                // Animasi content blur dan scale
                if (mainContent) {
                    mainContent.style.transform = 'scale(0.98)';
                    mainContent.style.filter = 'blur(2px)';
                    mainContent.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                }
                
                document.body.style.overflow = 'hidden';
                
                // Hentikan pulse animation pada button
                mobileMenuBtn.classList.remove('menu-btn-pulse');
            }
            
            // Fungsi untuk tutup sidebar dengan animasi
            function closeMobileSidebar() {
                if (!isSidebarOpen) return;
                
                isSidebarOpen = false;
                
                // Hapus class open dan tambah class close
                mobileSidebar.classList.remove('sidebar-open');
                mobileSidebar.classList.add('sidebar-close');
                
                // Hide overlay dengan animasi
                if (mobileOverlay) {
                    mobileOverlay.classList.remove('overlay-open');
                    mobileOverlay.classList.add('overlay-close');
                }
                
                // Kembalikan content ke normal
                if (mainContent) {
                    mainContent.style.transform = 'scale(1)';
                    mainContent.style.filter = 'blur(0)';
                }
                
                document.body.style.overflow = '';
                
                // Set timeout untuk reset transform setelah animasi selesai
                setTimeout(() => {
                    if (!isSidebarOpen) {
                        mobileSidebar.classList.add('-translate-x-full');
                        mobileSidebar.classList.remove('sidebar-close');
                        if (mobileOverlay) {
                            mobileOverlay.classList.add('hidden');
                            mobileOverlay.classList.remove('overlay-close');
                        }
                    }
                }, 500); // Match dengan duration animasi
                
                // Mulai kembali pulse animation setelah delay
                setTimeout(() => {
                    mobileMenuBtn.classList.add('menu-btn-pulse');
                }, 1000);
            }
            
            // Event listeners
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', openMobileSidebar);
            }
            
            if (closeMobileMenu) {
                closeMobileMenu.addEventListener('click', closeMobileSidebar);
            }
            
            // Close sidebar ketika klik di overlay
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', closeMobileSidebar);
            }
            
            // Close sidebar ketika resize window ke desktop size
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeMobileSidebar();
                }
            });

            // Close sidebar ketika tekan ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && isSidebarOpen) {
                    closeMobileSidebar();
                }
            });

            // Swipe to close (untuk mobile)
            let touchStartX = 0;
            let touchEndX = 0;

            document.addEventListener('touchstart', function(event) {
                touchStartX = event.changedTouches[0].screenX;
            });

            document.addEventListener('touchend', function(event) {
                touchEndX = event.changedTouches[0].screenX;
                if (isSidebarOpen && touchStartX - touchEndX > 50) {
                    closeMobileSidebar();
                }
            });
        });
    </script>

</body>
</html>