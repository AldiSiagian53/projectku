<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report - Eco Power Monitoring</title>
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

        /* pastikan tidak ada overflow horizontal di main content */
        main { overflow-x: hidden; }
    </style>

    {{-- Stack untuk styles dari komponen --}}
    @stack('styles')
</head>
<body class="bg-gray-100">

    {{-- SIDEBAR COMPONENT --}}
    <x-sidebar />

    {{-- STARTUP ANIMATION OVERLAY --}}
    <div id="startupOverlay" class="fixed inset-0 bg-blue-900 z-50 flex items-center justify-center">
        <div class="text-center text-white">
            <div class="w-20 h-20 mx-auto mb-6 bg-white rounded-2xl flex items-center justify-center animate-bounce">
                <span class="text-3xl">📊</span>
            </div>
            <h2 class="text-3xl font-bold mb-2 animate-pulse">Eco Power Monitoring</h2>
            <p class="text-blue-200">Loading Report Data...</p>
        </div>
    </div>

    {{-- CONTENT --}}
    <main class="md:ml-64 w-full p-4 md:p-8 transition-all duration-300 opacity-0 transform translate-y-4" id="mainContent">

        {{-- HEADER --}}
        <div class="text-center mb-8 opacity-0 transform translate-y-4" id="pageHeader">
            <h1 class="text-3xl font-bold text-gray-800">Eco Power Monitoring</h1>
            <p class="text-gray-600 mt-2">Tenaga cerdas, masa depan hemat.</p>
        </div>

        {{-- TAB SWITCH --}}
        <div class="flex justify-center mb-8 opacity-0 transform translate-y-4" id="tabSwitch">
            <div class="bg-gray-100 p-1 rounded-2xl shadow-inner border border-gray-200 flex">
                {{-- Button Daily AKTIF secara default --}}
                <button id="btnDaily" class="px-8 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 active:scale-95 bg-blue-500 text-white shadow-lg relative overflow-hidden group animate-pulse">
                    <span class="relative z-10">Daily</span>
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                </button>
                {{-- Button Monthly NON-AKTIF secara default --}}
                <button id="btnMonthly" class="px-8 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 active:scale-95 bg-white text-gray-600 shadow-md border border-gray-200 relative overflow-hidden group">
                    <span class="relative z-10">Monthly</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                </button>
            </div>
        </div>

        {{-- DAILY CONTENT - DITAMPILKAN secara default --}}
        <div id="contentDaily" class="space-y-6">
            {{-- ENERGY IN CARD --}}
            <div class="p-6 bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-3 h-3 bg-blue-500 rounded-full mr-3"></span>
                    Energy In
                </h2>
                <div class="h-64"><canvas id="dailyEnergyIn"></canvas></div>
            </div>

            {{-- ENERGY OUT CARD --}}
            <div class="p-6 bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-3 h-3 bg-cyan-500 rounded-full mr-3"></span>
                    Energy Out
                </h2>
                <div class="h-64"><canvas id="dailyEnergyOut"></canvas></div>
            </div>
        </div>

        {{-- MONTHLY CONTENT - DISEMBUNYIKAN secara default --}}
        <div id="contentMonthly" class="hidden space-y-6">
            {{-- ENERGY IN CARD --}}
            <div class="p-6 bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-3 h-3 bg-blue-500 rounded-full mr-3"></span>
                    Energy In
                </h2>
                <div class="h-64"><canvas id="monthlyEnergyIn"></canvas></div>
            </div>

            {{-- ENERGY OUT CARD --}}
            <div class="p-6 bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-3 h-3 bg-cyan-500 rounded-full mr-3"></span>
                    Energy Out
                </h2>
                <div class="h-64"><canvas id="monthlyEnergyOut"></canvas></div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Startup Animation Sequence
            setTimeout(() => {
                const startupOverlay = document.getElementById('startupOverlay');
                const mainContent = document.getElementById('mainContent');
                const pageHeader = document.getElementById('pageHeader');
                const tabSwitch = document.getElementById('tabSwitch');
                const contentDaily = document.getElementById('contentDaily');
                const contentMonthly = document.getElementById('contentMonthly');

                // Fade out startup overlay
                startupOverlay.classList.add('opacity-0', 'transition-all', 'duration-500');
                
                setTimeout(() => {
                    startupOverlay.classList.add('hidden');
                    
                    // Animate main content in
                    mainContent.classList.remove('opacity-0', 'translate-y-4');
                    mainContent.classList.add('opacity-100', 'translate-y-0');
                    
                    // Stagger animations for elements
                    setTimeout(() => {
                        pageHeader.classList.remove('opacity-0', 'translate-y-4');
                        pageHeader.classList.add('opacity-100', 'translate-y-0');
                    }, 200);

                    setTimeout(() => {
                        tabSwitch.classList.remove('opacity-0', 'translate-y-4');
                        tabSwitch.classList.add('opacity-100', 'translate-y-0');
                    }, 400);

                    setTimeout(() => {
                        contentDaily.classList.remove('opacity-0', 'translate-y-4');
                        contentDaily.classList.add('opacity-100', 'translate-y-0');
                    }, 600);

                }, 500);
            }, 1500);

            // Chart Data
            const labelsDaily = {!! json_encode($labelsDaily ?? []) !!};
            const labelsMonthly = {!! json_encode($labelsMonthly ?? []) !!};
            const energyInDaily = {!! json_encode($energyInDaily ?? []) !!};
            const energyOutDaily = {!! json_encode($energyOutDaily ?? []) !!};
            const energyInMonthly = {!! json_encode($energyInMonthly ?? []) !!};
            const energyOutMonthly = {!! json_encode($energyOutMonthly ?? []) !!};

            function createLine(id, labels, data, color, title) {
            const ctx = document.getElementById(id);
            if (!ctx) {
                console.error(`Canvas element with id '${id}' not found`);
                return null;
            }
            
            // Clear existing chart if any
            if (ctx.chart) {
                ctx.chart.destroy();
            }

            try {
                const chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: title,
                            data: data,
                            borderColor: color,
                            backgroundColor: color + '20',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointBackgroundColor: color,
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
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
                                grid: {
                                    color: 'rgba(0,0,0,0.1)'
                                },
                                title: {
                                    display: true,
                                    text: 'Energy (kWh)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                ctx.chart = chart;
                return chart;
            } catch (error) {
                console.error(`Error creating chart for ${id}:`, error);
                return null;
            }
        }

        // Penggunaan:
        createLine("dailyEnergyIn", labelsDaily, energyInDaily, "#2563EB", "Energy In");
        createLine("dailyEnergyOut", labelsDaily, energyOutDaily, "#0EA5E9", "Energy Out");

            // Initialize charts after animation
            setTimeout(() => {
                createLine("dailyEnergyIn", labelsDaily, energyInDaily, "#2563EB");
                createLine("dailyEnergyOut", labelsDaily, energyOutDaily, "#0EA5E9");
                createLine("monthlyEnergyIn", labelsMonthly, energyInMonthly, "#2563EB");
                createLine("monthlyEnergyOut", labelsMonthly, energyOutMonthly, "#0EA5E9");
            }, 2000);

            // TAB SWITCH with enhanced animations
            const btnDaily = document.getElementById("btnDaily");
            const btnMonthly = document.getElementById("btnMonthly");
            const contentDaily = document.getElementById("contentDaily");
            const contentMonthly = document.getElementById("contentMonthly");
            
            function switchToDaily() {
                // Remove active state from monthly
                btnMonthly.classList.remove("bg-blue-500", "text-white", "shadow-lg", "animate-pulse");
                btnMonthly.classList.add("bg-white", "text-gray-600", "shadow-md", "border", "border-gray-200");
                
                // Add active state to daily
                btnDaily.classList.remove("bg-white", "text-gray-600", "shadow-md", "border", "border-gray-200");
                btnDaily.classList.add("bg-blue-500", "text-white", "shadow-lg", "animate-pulse");
                
                // Content transition
                contentMonthly.classList.add("opacity-0", "translate-y-4");
                setTimeout(() => {
                    contentMonthly.classList.add("hidden");
                    contentDaily.classList.remove("hidden");
                    setTimeout(() => {
                        contentDaily.classList.remove("opacity-0", "translate-y-4");
                        contentDaily.classList.add("opacity-100", "translate-y-0");
                    }, 50);
                }, 300);
            }

            function switchToMonthly() {
                // Remove active state from daily
                btnDaily.classList.remove("bg-blue-500", "text-white", "shadow-lg", "animate-pulse");
                btnDaily.classList.add("bg-white", "text-gray-600", "shadow-md", "border", "border-gray-200");
                
                // Add active state to monthly
                btnMonthly.classList.remove("bg-white", "text-gray-600", "shadow-md", "border", "border-gray-200");
                btnMonthly.classList.add("bg-blue-500", "text-white", "shadow-lg", "animate-pulse");
                
                // Content transition
                contentDaily.classList.add("opacity-0", "translate-y-4");
                setTimeout(() => {
                    contentDaily.classList.add("hidden");
                    contentMonthly.classList.remove("hidden");
                    setTimeout(() => {
                        contentMonthly.classList.remove("opacity-0", "translate-y-4");
                        contentMonthly.classList.add("opacity-100", "translate-y-0");
                    }, 50);
                }, 300);
            }

            if (btnDaily) {
                btnDaily.onclick = switchToDaily;
            }

            if (btnMonthly) {
                btnMonthly.onclick = switchToMonthly;
            }
        });
    </script>

    {{-- Stack untuk scripts dari komponen --}}
    @stack('scripts')

</body>
</html>