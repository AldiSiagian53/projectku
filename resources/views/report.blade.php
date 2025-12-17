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

    {{-- APACHE ECHARTS (Grafana-like) --}}
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

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
                    Energy In (Charging Power) - 7 Hari Terakhir
                </h2>
                <div id="dailyEnergyIn" style="height: 400px;"></div>
            </div>

            {{-- ENERGY OUT CARD --}}
            <div class="p-6 bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-3 h-3 bg-cyan-500 rounded-full mr-3"></span>
                    Energy Out (Panel Power) - 7 Hari Terakhir
                </h2>
                <div id="dailyEnergyOut" style="height: 400px;"></div>
            </div>

            {{-- COMBINED CHART --}}
            <div class="p-6 bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-3 h-3 bg-purple-500 rounded-full mr-3"></span>
                    Energy Overview - 7 Hari Terakhir
                </h2>
                <div id="dailyCombined" style="height: 400px;"></div>
            </div>
        </div>

        {{-- MONTHLY CONTENT - DISEMBUNYIKAN secara default --}}
        <div id="contentMonthly" class="hidden space-y-6">
            {{-- ENERGY IN CARD --}}
            <div class="p-6 bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-3 h-3 bg-blue-500 rounded-full mr-3"></span>
                    Energy In (Charging Power) - 12 Bulan Terakhir
                </h2>
                <div id="monthlyEnergyIn" style="height: 400px;"></div>
            </div>

            {{-- ENERGY OUT CARD --}}
            <div class="p-6 bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-3 h-3 bg-cyan-500 rounded-full mr-3"></span>
                    Energy Out (Panel Power) - 12 Bulan Terakhir
                </h2>
                <div id="monthlyEnergyOut" style="height: 400px;"></div>
            </div>

            {{-- COMBINED MONTHLY CHART --}}
            <div class="p-6 bg-white rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <span class="w-3 h-3 bg-purple-500 rounded-full mr-3"></span>
                    Energy Overview - 12 Bulan Terakhir
                </h2>
                <div id="monthlyCombined" style="height: 400px;"></div>
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

            // Chart Data (Real data dari database)
            const labelsDaily = {!! json_encode($labelsDaily ?? []) !!};
            const labelsMonthly = {!! json_encode($labelsMonthly ?? []) !!};
            const energyInDaily = {!! json_encode($energyInDaily ?? []) !!};
            const energyOutDaily = {!! json_encode($energyOutDaily ?? []) !!};
            const energyInMonthly = {!! json_encode($energyInMonthly ?? []) !!};
            const energyOutMonthly = {!! json_encode($energyOutMonthly ?? []) !!};

            // Debug log
            console.log('Report Data Loaded:', {
                labelsDaily: labelsDaily,
                labelsMonthly: labelsMonthly,
                energyInDaily: energyInDaily,
                energyOutDaily: energyOutDaily,
                energyInMonthly: energyInMonthly,
                energyOutMonthly: energyOutMonthly
            });

            // Store chart instances globally
            let dailyCharts = {};
            let monthlyCharts = {};

            // Initialize ECharts after animation
            setTimeout(() => {
                // Daily Charts (always visible initially)
                dailyCharts.energyIn = initDailyEnergyInChart();
                dailyCharts.energyOut = initDailyEnergyOutChart();
                dailyCharts.combined = initDailyCombinedChart();
                
                // Monthly Charts - initialize after element is visible
                // Will be initialized when user switches to monthly tab
            }, 2000);

            // Daily Energy In Chart
            function initDailyEnergyInChart() {
                const element = document.getElementById('dailyEnergyIn');
                if (!element) {
                    console.error('Element dailyEnergyIn not found');
                    return null;
                }
                
                const chart = echarts.init(element);
                const option = {
                    tooltip: {
                        trigger: 'axis',
                        formatter: '{b}: {c} kWh'
                    },
                    xAxis: {
                        type: 'category',
                        data: labelsDaily,
                        boundaryGap: false
                    },
                    yAxis: {
                        type: 'value',
                        name: 'kWh',
                        axisLabel: {
                            formatter: '{value} kWh'
                        }
                    },
                    series: [{
                        name: 'Energy In',
                        type: 'line',
                        smooth: true,
                        data: energyInDaily,
                        areaStyle: {
                            opacity: 0.3
                        },
                        lineStyle: {
                            color: '#2563eb',
                            width: 3
                        },
                        itemStyle: {
                            color: '#2563eb'
                        }
                    }],
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    }
                };
                chart.setOption(option);
                
                // Responsive
                window.addEventListener('resize', () => chart.resize());
                
                return chart;
            }

            // Daily Energy Out Chart
            function initDailyEnergyOutChart() {
                const element = document.getElementById('dailyEnergyOut');
                if (!element) {
                    console.error('Element dailyEnergyOut not found');
                    return null;
                }
                
                const chart = echarts.init(element);
                const option = {
                    tooltip: {
                        trigger: 'axis',
                        formatter: '{b}: {c} kWh'
                    },
                    xAxis: {
                        type: 'category',
                        data: labelsDaily,
                        boundaryGap: false
                    },
                    yAxis: {
                        type: 'value',
                        name: 'kWh',
                        axisLabel: {
                            formatter: '{value} kWh'
                        }
                    },
                    series: [{
                        name: 'Energy Out',
                        type: 'line',
                        smooth: true,
                        data: energyOutDaily,
                        areaStyle: {
                            opacity: 0.3
                        },
                        lineStyle: {
                            color: '#0ea5e9',
                            width: 3
                        },
                        itemStyle: {
                            color: '#0ea5e9'
                        }
                    }],
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    }
                };
                chart.setOption(option);
                
                // Responsive
                window.addEventListener('resize', () => chart.resize());
                
                return chart;
            }

            // Daily Combined Chart
            function initDailyCombinedChart() {
                const element = document.getElementById('dailyCombined');
                if (!element) return null;
                
                const chart = echarts.init(element);
                const option = {
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['Energy In', 'Energy Out']
                    },
                    xAxis: {
                        type: 'category',
                        data: labelsDaily,
                        boundaryGap: false
                    },
                    yAxis: {
                        type: 'value',
                        name: 'kWh'
                    },
                    series: [
                        {
                            name: 'Energy In',
                            type: 'line',
                            smooth: true,
                            data: energyInDaily,
                            lineStyle: { color: '#2563eb', width: 3 },
                            itemStyle: { color: '#2563eb' },
                            areaStyle: { opacity: 0.2 }
                        },
                        {
                            name: 'Energy Out',
                            type: 'line',
                            smooth: true,
                            data: energyOutDaily,
                            lineStyle: { color: '#0ea5e9', width: 3 },
                            itemStyle: { color: '#0ea5e9' },
                            areaStyle: { opacity: 0.2 }
                        }
                    ],
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    }
                };
                chart.setOption(option);
                
                // Responsive
                window.addEventListener('resize', () => chart.resize());
                
                return chart;
            }

            // Monthly Energy In Chart
            function initMonthlyEnergyInChart() {
                const element = document.getElementById('monthlyEnergyIn');
                if (!element) {
                    console.error('Element monthlyEnergyIn not found');
                    return null;
                }
                
                console.log('Initializing monthlyEnergyIn chart', {
                    labels: labelsMonthly,
                    data: energyInMonthly
                });
                
                const chart = echarts.init(element);
                const option = {
                    tooltip: {
                        trigger: 'axis',
                        formatter: '{b}: {c} kWh'
                    },
                    xAxis: {
                        type: 'category',
                        data: labelsMonthly
                    },
                    yAxis: {
                        type: 'value',
                        name: 'kWh',
                        axisLabel: {
                            formatter: '{value} kWh'
                        }
                    },
                    series: [{
                        name: 'Energy In',
                        type: 'bar',
                        data: energyInMonthly,
                        itemStyle: {
                            color: '#2563eb'
                        }
                    }],
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    }
                };
                chart.setOption(option);
                
                // Responsive
                window.addEventListener('resize', () => chart.resize());
                
                return chart;
            }

            // Monthly Energy Out Chart
            function initMonthlyEnergyOutChart() {
                const element = document.getElementById('monthlyEnergyOut');
                if (!element) {
                    console.error('Element monthlyEnergyOut not found');
                    return null;
                }
                
                console.log('Initializing monthlyEnergyOut chart', {
                    labels: labelsMonthly,
                    data: energyOutMonthly
                });
                
                const chart = echarts.init(element);
                const option = {
                    tooltip: {
                        trigger: 'axis',
                        formatter: '{b}: {c} kWh'
                    },
                    xAxis: {
                        type: 'category',
                        data: labelsMonthly
                    },
                    yAxis: {
                        type: 'value',
                        name: 'kWh',
                        axisLabel: {
                            formatter: '{value} kWh'
                        }
                    },
                    series: [{
                        name: 'Energy Out',
                        type: 'bar',
                        data: energyOutMonthly,
                        itemStyle: {
                            color: '#0ea5e9'
                        }
                    }],
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    }
                };
                chart.setOption(option);
                
                // Responsive
                window.addEventListener('resize', () => chart.resize());
                
                return chart;
            }

            // Monthly Combined Chart
            function initMonthlyCombinedChart() {
                const element = document.getElementById('monthlyCombined');
                if (!element) {
                    console.error('Element monthlyCombined not found');
                    return null;
                }
                
                console.log('Initializing monthlyCombined chart');
                
                const chart = echarts.init(element);
                const option = {
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['Energy In', 'Energy Out']
                    },
                    xAxis: {
                        type: 'category',
                        data: labelsMonthly
                    },
                    yAxis: {
                        type: 'value',
                        name: 'kWh'
                    },
                    series: [
                        {
                            name: 'Energy In',
                            type: 'bar',
                            data: energyInMonthly,
                            itemStyle: { color: '#2563eb' }
                        },
                        {
                            name: 'Energy Out',
                            type: 'bar',
                            data: energyOutMonthly,
                            itemStyle: { color: '#0ea5e9' }
                        }
                    ],
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    }
                };
                chart.setOption(option);
                
                // Responsive
                window.addEventListener('resize', () => chart.resize());
                
                return chart;
            }

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
                        
                        // Initialize monthly charts after element becomes visible
                        // Wait a bit longer to ensure element is fully rendered
                        setTimeout(() => {
                            if (!monthlyCharts.energyIn) {
                                monthlyCharts.energyIn = initMonthlyEnergyInChart();
                            } else {
                                monthlyCharts.energyIn.resize();
                            }
                            
                            if (!monthlyCharts.energyOut) {
                                monthlyCharts.energyOut = initMonthlyEnergyOutChart();
                            } else {
                                monthlyCharts.energyOut.resize();
                            }
                            
                            if (!monthlyCharts.combined) {
                                monthlyCharts.combined = initMonthlyCombinedChart();
                            } else {
                                monthlyCharts.combined.resize();
                            }
                            
                            // Resize charts again to ensure proper rendering
                            setTimeout(() => {
                                if (monthlyCharts.energyIn) monthlyCharts.energyIn.resize();
                                if (monthlyCharts.energyOut) monthlyCharts.energyOut.resize();
                                if (monthlyCharts.combined) monthlyCharts.combined.resize();
                            }, 200);
                        }, 350);
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