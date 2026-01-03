<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alerts - Eco Power Monitoring</title>
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

        /* Animation keyframes */
        @keyframes alertPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-pulse {
            animation: alertPulse 2s ease-in-out infinite;
        }

        .slide-in-top {
            animation: slideInFromTop 0.6s ease-out;
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        .highlight-alert {
            animation: highlightPulse 2s ease-in-out;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.3);
            border-color: #ef4444 !important;
        }

        @keyframes highlightPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.3); }
            50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0.1); }
        }

        .stats-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        /* Mobile-specific fixes */
        @media (max-width: 768px) {
            .mobile-alert-item {
                padding: 12px !important;
                margin-bottom: 8px !important;
            }
            
            .mobile-alert-content {
                flex-direction: column !important;
                align-items: flex-start !important;
            }
            
            .mobile-alert-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                width: 100% !important;
            }
            
            .mobile-alert-badges {
                flex-wrap: wrap !important;
                gap: 6px !important;
                margin-top: 8px !important;
                width: 100% !important;
            }
            
            .mobile-alert-badge {
                font-size: 10px !important;
                padding: 4px 8px !important;
                margin-right: 4px !important;
                margin-bottom: 4px !important;
            }
            
            .mobile-alert-time {
                margin-top: 4px !important;
                font-size: 12px !important;
            }
            
            .mobile-alert-icon {
                margin-bottom: 8px !important;
            }
            
            /* Fix untuk stats cards di mobile */
            .mobile-stats-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 12px !important;
            }
            
            .mobile-stats-card {
                padding: 16px !important;
                min-height: auto !important;
            }
            
            .mobile-stats-number {
                font-size: 20px !important;
            }
            
            .mobile-stats-text {
                font-size: 12px !important;
            }
            
            /* Fix untuk header di mobile */
            .mobile-header {
                padding: 16px !important;
            }
            
            .mobile-tab-buttons {
                flex-wrap: wrap !important;
                gap: 8px !important;
                margin-top: 12px !important;
            }
            
            .mobile-tab-button {
                flex: 1 !important;
                min-width: 120px !important;
                text-align: center !important;
            }
        }

        /* Additional mobile optimizations */
        @media (max-width: 480px) {
            .mobile-alert-item {
                padding: 10px !important;
            }
            
            .mobile-stats-grid {
                grid-template-columns: 1fr !important;
            }
            
            .mobile-alert-badges {
                justify-content: flex-start !important;
            }
            
            .mobile-alert-badge {
                flex: 0 0 auto !important;
            }
        }
    </style>

    {{-- Stack untuk styles dari komponen --}}
    @stack('styles')
</head>
<body class="bg-gray-50">

    {{-- SIDEBAR COMPONENT --}}
    <x-sidebar />

    {{-- STARTUP ANIMATION OVERLAY --}}
    <div id="startupOverlay" class="fixed inset-0 bg-gradient-to-br from-blue-900 to-purple-900 z-50 flex items-center justify-center">
        <div class="text-center text-white">
            <div class="w-24 h-24 mx-auto mb-6 bg-white bg-opacity-20 rounded-3xl flex items-center justify-center animate-bounce backdrop-blur-sm">
                <span class="text-4xl">🔔</span>
            </div>
            <h2 class="text-4xl font-bold mb-4 animate-pulse">Eco Power Monitoring</h2>
            <p class="text-blue-200 text-lg">Loading Alert System...</p>
            <div class="mt-6 w-48 h-1 bg-blue-300 rounded-full mx-auto overflow-hidden">
                <div class="h-full bg-white animate-pulse" style="animation-duration: 2s;"></div>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <main class="md:ml-64 w-full p-4 md:p-8 transition-all duration-300" id="mainContent">

        {{-- HEADER --}}
        <div class="text-center mb-8 opacity-0 transform -translate-y-4" id="pageHeader">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Eco Power Monitoring</h1>
            <p class="text-gray-600">Tenaga cerdas, masa depan hemat.</p>
        </div>

        {{-- DASHBOARD HEADER --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 opacity-0 transform -translate-y-4 mobile-header" id="dashboardHeader">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Dashboard Report</h2>
                    <p class="text-gray-600 mt-1">Monitoring & Alert System</p>
                </div>
                <div class="flex space-x-4 mt-4 md:mt-0 mobile-tab-buttons">
                    <button id="btnDaily" class="px-6 py-2 bg-blue-500 text-white rounded-xl font-semibold transition-all duration-300 hover:bg-blue-600 hover:scale-105 active:scale-95 shadow-lg mobile-tab-button">
                        Daily
                    </button>
                    <button id="btnMonthly" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-xl font-semibold transition-all duration-300 hover:bg-gray-200 hover:scale-105 active:scale-95 mobile-tab-button">
                        Monthly
                    </button>
                </div>
            </div>
        </div>

        {{-- STATISTICS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mobile-stats-grid" id="statsCards">
            {{-- Total Alerts Card --}}
            <div class="stats-card bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-2xl shadow-lg opacity-0 transform -translate-y-4 cursor-pointer mobile-stats-card" 
                 onclick="scrollToAlerts()" 
                 id="totalAlertsCard">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm mobile-stats-text">Total Alerts</p>
                        <p class="text-3xl font-bold mt-2 mobile-stats-number" id="totalAlertsCount">{{ $stats['total_alerts'] }}</p>
                        <p class="text-blue-100 text-xs mt-1 mobile-stats-text" id="totalAlertsSubtitle">Current Alerts</p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">📊</span>
                    </div>
                </div>
                <div class="mt-3 text-blue-100 text-sm flex items-center mobile-stats-text">
                    <span id="totalAlertsAction">Click to view all alerts</span>
                    <i class="ml-2">↓</i>
                </div>
            </div>

            {{-- Active Alerts Card --}}
            <div class="stats-card bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-2xl shadow-lg opacity-0 transform -translate-y-4 cursor-pointer mobile-stats-card" 
                 onclick="scrollToActiveAlerts()" 
                 id="activeAlertsCard">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm mobile-stats-text">Active Alerts</p>
                        <p class="text-3xl font-bold mt-2 mobile-stats-number" id="activeAlertsCount">{{ $stats['active_alerts'] }}</p>
                        <p class="text-orange-100 text-xs mt-1 mobile-stats-text" id="activeAlertsSubtitle">Need Attention</p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">⚠️</span>
                    </div>
                </div>
                <div class="mt-3 text-orange-100 text-sm flex items-center mobile-stats-text">
                    <span id="activeAlertsAction">Click to view active</span>
                    <i class="ml-2">↓</i>
                </div>
            </div>

            {{-- Resolved Alerts Card --}}
            <div class="stats-card bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-2xl shadow-lg opacity-0 transform -translate-y-4 cursor-pointer mobile-stats-card" 
                 onclick="scrollToResolvedAlerts()" 
                 id="resolvedAlertsCard">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm mobile-stats-text">Resolved</p>
                        <p class="text-3xl font-bold mt-2 mobile-stats-number" id="resolvedAlertsCount">{{ $stats['resolved_alerts'] }}</p>
                        <p class="text-green-100 text-xs mt-1 mobile-stats-text" id="resolvedAlertsSubtitle">Completed</p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">✅</span>
                    </div>
                </div>
                <div class="mt-3 text-green-100 text-sm flex items-center mobile-stats-text">
                    <span id="resolvedAlertsAction">Click to view resolved</span>
                    <i class="ml-2">↓</i>
                </div>
            </div>

            {{-- Critical Alerts Card --}}
            <div class="stats-card bg-gradient-to-br from-red-500 to-red-600 text-white p-6 rounded-2xl shadow-lg opacity-0 transform -translate-y-4 cursor-pointer alert-pulse mobile-stats-card" 
                 onclick="scrollToCriticalAlerts()" 
                 id="criticalAlertsCard">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm mobile-stats-text">Critical</p>
                        <p class="text-3xl font-bold mt-2 mobile-stats-number" id="criticalAlertsCount">{{ $stats['critical_alerts'] }}</p>
                        <p class="text-red-100 text-xs mt-1 mobile-stats-text" id="criticalAlertsSubtitle">Urgent Action</p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">🚨</span>
                    </div>
                </div>
                <div class="mt-3 text-red-100 text-sm flex items-center mobile-stats-text">
                    <span id="criticalAlertsAction">Click to view critical</span>
                    <i class="ml-2">↓</i>
                </div>
            </div>
        </div>

        {{-- DAILY CONTENT --}}
        <div id="contentDaily" class="space-y-6">
            {{-- ALERTS SECTION --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 opacity-0 transform -translate-y-4" id="alertsSection">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 mobile-alert-header">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Peringatan</h2>
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mobile-alert-badges">
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span>Critical</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                            <span>Active</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span>Resolved</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4" id="alertsList">
                    @forelse($alerts as $alert)
                    @php
                        // Tentukan warna berdasarkan status dan type
                        $bgColor = 'bg-gray-50 border-gray-200';
                        $iconBg = 'bg-gray-100';
                        
                        if ($alert['status'] == 'resolved') {
                            $bgColor = 'bg-green-50 border-green-200';
                            $iconBg = 'bg-green-100';
                        } elseif ($alert['type'] == 'danger') {
                            $bgColor = 'bg-red-50 border-red-200';
                            $iconBg = 'bg-red-100';
                        } elseif ($alert['type'] == 'warning') {
                            $bgColor = 'bg-orange-50 border-orange-200';
                            $iconBg = 'bg-orange-100';
                        } elseif ($alert['status'] == 'active') {
                            $bgColor = 'bg-orange-50 border-orange-200';
                            $iconBg = 'bg-orange-100';
                        }
                    @endphp
                    <div class="border rounded-xl p-4 transition-all duration-300 hover:shadow-md hover:scale-[1.02] {{ $bgColor }} opacity-0 transform -translate-y-4 alert-item mobile-alert-item"
                         data-alert-id="{{ $alert['id'] }}"
                         data-alert-status="{{ $alert['status'] }}"
                         data-alert-type="{{ $alert['type'] }}"
                         data-alert-severity="{{ $alert['severity'] }}">
                        <div class="flex flex-col md:flex-row md:items-start space-y-3 md:space-y-0 md:space-x-4 mobile-alert-content">
                            <div class="w-10 h-10 rounded-full {{ $iconBg }} flex items-center justify-center flex-shrink-0 mobile-alert-icon">
                                <span class="text-lg">{{ $alert['icon'] }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between mobile-alert-header">
                                    <h3 class="font-semibold text-gray-800 text-base md:text-lg break-words">{{ $alert['title'] }}</h3>
                                    <span class="text-sm text-gray-500 mt-1 md:mt-0 mobile-alert-time">{{ $alert['time'] }}</span>
                                </div>
                                <p class="text-gray-600 mt-2 text-sm md:text-base break-words">{{ $alert['description'] }}</p>
                                <div class="flex flex-wrap items-center mt-3 gap-2 mobile-alert-badges">
                                    <span class="px-3 py-1 text-xs rounded-full mobile-alert-badge
                                        {{ $alert['status'] == 'active' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }} font-medium">
                                        {{ $alert['status'] == 'active' ? 'ACTIVE' : 'RESOLVED' }}
                                    </span>
                                    <span class="px-3 py-1 text-xs rounded-full mobile-alert-badge
                                        {{ $alert['type'] == 'danger' ? 'bg-red-100 text-red-800' : 
                                           ($alert['type'] == 'warning' ? 'bg-orange-100 text-orange-800' : 
                                           ($alert['type'] == 'info' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }} font-medium">
                                        {{ strtoupper($alert['type']) }}
                                    </span>
                                    <span class="px-3 py-1 text-xs rounded-full mobile-alert-badge
                                        {{ $alert['severity'] == 'high' ? 'bg-red-100 text-red-800' : 
                                           ($alert['severity'] == 'medium' ? 'bg-orange-100 text-orange-800' : 
                                           ($alert['severity'] == 'low' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }} font-medium">
                                        {{ strtoupper($alert['severity']) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <span class="text-3xl">🔔</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak Ada Peringatan</h3>
                        <p class="text-gray-600">Semua sistem berjalan dengan baik.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- MONTHLY CONTENT --}}
        <div id="contentMonthly" class="hidden space-y-6">
            {{-- MONTHLY CHART SECTION --}}
            <div class="bg-white rounded-2xl shadow-lg p-6" id="monthlyChartSection">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Monthly Alert Trends</h2>
                <div class="h-96">
                    <canvas id="monthlyAlertsChart"></canvas>
                </div>
            </div>

            {{-- MONTHLY STATS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mobile-stats-grid">
                @php
                    // Hitung bulan dengan alert tertinggi
                    $maxActiveIndex = array_search(max($monthlyAlertData['active_alerts']), $monthlyAlertData['active_alerts']);
                    $peakMonth = $monthlyAlertData['labels'][$maxActiveIndex];
                    $peakAlerts = max($monthlyAlertData['active_alerts']);
                    
                    // Hitung bulan dengan alert terendah
                    $minActiveIndex = array_search(min($monthlyAlertData['active_alerts']), $monthlyAlertData['active_alerts']);
                    $bestMonth = $monthlyAlertData['labels'][$minActiveIndex];
                    $bestAlerts = min($monthlyAlertData['active_alerts']);
                    
                    // Hitung rata-rata resolution time (dummy calculation)
                    $totalResolved = array_sum($monthlyAlertData['resolved_alerts']);
                    $avgResolutionDays = $totalResolved > 0 ? round(($totalResolved / count($monthlyAlertData['resolved_alerts'])) * 0.3, 1) : 0;
                @endphp
                
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-2xl shadow-lg mobile-stats-card">
                    <div class="text-center">
                        <p class="text-purple-100 text-sm mobile-stats-text">Peak Alerts Month</p>
                        <p class="text-2xl font-bold mt-2 mobile-stats-number">{{ $peakMonth }}</p>
                        <p class="text-purple-100 text-sm mt-1 mobile-stats-text">{{ $peakAlerts }} Active Alerts</p>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white p-6 rounded-2xl shadow-lg mobile-stats-card">
                    <div class="text-center">
                        <p class="text-indigo-100 text-sm mobile-stats-text">Best Month</p>
                        <p class="text-2xl font-bold mt-2 mobile-stats-number">{{ $bestMonth }}</p>
                        <p class="text-indigo-100 text-sm mt-1 mobile-stats-text">{{ $bestAlerts }} Active Alerts</p>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-pink-500 to-pink-600 text-white p-6 rounded-2xl shadow-lg mobile-stats-card">
                    <div class="text-center">
                        <p class="text-pink-100 text-sm mobile-stats-text">Avg Resolution Time</p>
                        <p class="text-2xl font-bold mt-2 mobile-stats-number">{{ $avgResolutionDays }} Days</p>
                        <p class="text-pink-100 text-sm mt-1 mobile-stats-text">Per Alert</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Alert Data dari Controller
            const monthlyAlertData = {!! json_encode($monthlyAlertData) !!};

            // Tab Switching Functionality - FIXED
            const btnDaily = document.getElementById('btnDaily');
            const btnMonthly = document.getElementById('btnMonthly');
            const contentDaily = document.getElementById('contentDaily');
            const contentMonthly = document.getElementById('contentMonthly');

            let monthlyChartInitialized = false;

            // Function to update stats cards based on view mode
            function updateStatsCards(isMonthly) {
                if (isMonthly) {
                    // Update untuk Monthly View - Nonaktifkan onclick dan ubah cursor
                    const totalCard = document.getElementById('totalAlertsCard');
                    const activeCard = document.getElementById('activeAlertsCard');
                    const resolvedCard = document.getElementById('resolvedAlertsCard');
                    const criticalCard = document.getElementById('criticalAlertsCard');
                    
                    // Hapus onclick dan ubah cursor
                    if (totalCard) {
                        totalCard.removeAttribute('onclick');
                        totalCard.style.cursor = 'default';
                    }
                    if (activeCard) {
                        activeCard.removeAttribute('onclick');
                        activeCard.style.cursor = 'default';
                    }
                    if (resolvedCard) {
                        resolvedCard.removeAttribute('onclick');
                        resolvedCard.style.cursor = 'default';
                    }
                    if (criticalCard) {
                        criticalCard.removeAttribute('onclick');
                        criticalCard.style.cursor = 'default';
                    }
                    
                    // Update text
                    document.getElementById('totalAlertsCount').textContent = {{ $monthlyStats['total_monthly_alerts'] }};
                    document.getElementById('totalAlertsSubtitle').textContent = 'Year Total';
                    //document.getElementById('totalAlertsAction').textContent = 'View monthly trends';
                    
                    document.getElementById('activeAlertsCount').textContent = {{ $monthlyStats['avg_active_alerts'] }};
                    document.getElementById('activeAlertsSubtitle').textContent = 'Monthly Average';
                    //document.getElementById('activeAlertsAction').textContent = 'View active trends';
                    
                    document.getElementById('resolvedAlertsCount').textContent = {{ $monthlyStats['avg_resolved_alerts'] }};
                    document.getElementById('resolvedAlertsSubtitle').textContent = 'Monthly Average';
                    //document.getElementById('resolvedAlertsAction').textContent = 'View resolved trends';
                    
                    document.getElementById('criticalAlertsCount').textContent = {{ $monthlyStats['total_critical_alerts'] }};
                    document.getElementById('criticalAlertsSubtitle').textContent = 'Year Total';
                    //document.getElementById('criticalAlertsAction').textContent = 'View critical trends';
                } else {
                    // Update untuk Daily View - Aktifkan kembali onclick
                    const totalCard = document.getElementById('totalAlertsCard');
                    const activeCard = document.getElementById('activeAlertsCard');
                    const resolvedCard = document.getElementById('resolvedAlertsCard');
                    const criticalCard = document.getElementById('criticalAlertsCard');
                    
                    // Tambahkan kembali onclick dan cursor pointer
                    if (totalCard) {
                        totalCard.setAttribute('onclick', 'scrollToAlerts()');
                        totalCard.style.cursor = 'pointer';
                    }
                    if (activeCard) {
                        activeCard.setAttribute('onclick', 'scrollToActiveAlerts()');
                        activeCard.style.cursor = 'pointer';
                    }
                    if (resolvedCard) {
                        resolvedCard.setAttribute('onclick', 'scrollToResolvedAlerts()');
                        resolvedCard.style.cursor = 'pointer';
                    }
                    if (criticalCard) {
                        criticalCard.setAttribute('onclick', 'scrollToCriticalAlerts()');
                        criticalCard.style.cursor = 'pointer';
                    }
                    // Update untuk Daily View
                    document.getElementById('totalAlertsCount').textContent = {{ $stats['total_alerts'] }};
                    document.getElementById('totalAlertsSubtitle').textContent = 'Current Alerts';
                    document.getElementById('totalAlertsAction').textContent = 'Click to view all alerts';
                    
                    document.getElementById('activeAlertsCount').textContent = {{ $stats['active_alerts'] }};
                    document.getElementById('activeAlertsSubtitle').textContent = 'Need Attention';
                    document.getElementById('activeAlertsAction').textContent = 'Click to view active';
                    
                    document.getElementById('resolvedAlertsCount').textContent = {{ $stats['resolved_alerts'] }};
                    document.getElementById('resolvedAlertsSubtitle').textContent = 'Completed';
                    document.getElementById('resolvedAlertsAction').textContent = 'Click to view resolved';
                    
                    document.getElementById('criticalAlertsCount').textContent = {{ $stats['critical_alerts'] }};
                    document.getElementById('criticalAlertsSubtitle').textContent = 'Urgent Action';
                    document.getElementById('criticalAlertsAction').textContent = 'Click to view critical';
                }
            }

            // Update Tab Switching Functions
            function switchToDaily() {
                btnDaily.classList.add('bg-blue-500', 'text-white', 'shadow-lg');
                btnDaily.classList.remove('bg-gray-100', 'text-gray-600');
                btnMonthly.classList.remove('bg-blue-500', 'text-white', 'shadow-lg');
                btnMonthly.classList.add('bg-gray-100', 'text-gray-600');
                
                contentDaily.classList.remove('hidden');
                contentMonthly.classList.add('hidden');
                
                // Update stats cards untuk Daily view
                updateStatsCards(false);
            }

            function switchToMonthly() {
                btnMonthly.classList.add('bg-blue-500', 'text-white', 'shadow-lg');
                btnMonthly.classList.remove('bg-gray-100', 'text-gray-600');
                btnDaily.classList.remove('bg-blue-500', 'text-white', 'shadow-lg');
                btnDaily.classList.add('bg-gray-100', 'text-gray-600');
                
                contentDaily.classList.add('hidden');
                contentMonthly.classList.remove('hidden');
                
                // Update stats cards untuk Monthly view
                updateStatsCards(true);
                
                // Initialize monthly chart jika belum ada
                if (!monthlyChartInitialized) {
                    initMonthlyChart();
                    monthlyChartInitialized = true;
                }
            }

            // Fungsi scroll ke semua alerts
            function scrollToAlerts() {
                // Cek jika sedang di Monthly view, jangan lakukan scroll
                if (contentMonthly && !contentMonthly.classList.contains('hidden')) {
                    return;
                }
                
                // Pastikan kita di Daily view
                if (contentDaily && contentDaily.classList.contains('hidden')) {
                    switchToDaily();
                    // Tunggu animasi selesai sebelum scroll
                    setTimeout(() => {
                        performScrollToAlerts();
                    }, 500);
                } else {
                    performScrollToAlerts();
                }
            }

            function performScrollToAlerts() {
                const alertsSection = document.getElementById('alertsSection');
                if (!alertsSection) {
                    console.warn('Alerts section not found');
                    return;
                }

                // Pastikan element visible dan tidak dalam animasi
                alertsSection.classList.remove('opacity-0', '-translate-y-4', 'hidden');
                alertsSection.classList.add('opacity-100', 'translate-y-0');
                
                // Tunggu sebentar untuk memastikan element sudah ter-render
                setTimeout(() => {
                    // Hitung offset untuk header (jika ada fixed header atau sidebar)
                    const offset = 120; // Offset dalam pixels untuk sidebar dan spacing
                    const elementPosition = alertsSection.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;

                    // Scroll dengan smooth behavior
                    window.scrollTo({
                        top: Math.max(0, offsetPosition), // Pastikan tidak negatif
                        behavior: 'smooth'
                    });

                    // Highlight effect setelah scroll dimulai
                    setTimeout(() => {
                        alertsSection.classList.add('highlight-alert');
                        // Tambahkan sedikit animasi bounce
                        alertsSection.style.transition = 'all 0.3s ease';
                        alertsSection.style.transform = 'scale(1.02)';
                        
                        setTimeout(() => {
                            alertsSection.style.transform = 'scale(1)';
                        }, 300);
                        
                        setTimeout(() => {
                            alertsSection.classList.remove('highlight-alert');
                        }, 2000);
                    }, 300);
                }, 100);
            }

            // Fungsi scroll ke active alerts
            function scrollToActiveAlerts() {
                // Cek jika sedang di Monthly view, jangan lakukan scroll
                if (contentMonthly && !contentMonthly.classList.contains('hidden')) {
                    return;
                }
                
                // Pastikan kita di Daily view
                if (contentDaily && contentDaily.classList.contains('hidden')) {
                    switchToDaily();
                    setTimeout(() => {
                        performScrollToActiveAlerts();
                    }, 500);
                } else {
                    performScrollToActiveAlerts();
                }
            }

            function performScrollToActiveAlerts() {
                const activeAlerts = document.querySelectorAll('.alert-item[data-alert-status="active"]');
                if (activeAlerts.length > 0) {
                    const firstActiveAlert = activeAlerts[0];
                    // Pastikan alert visible
                    firstActiveAlert.classList.remove('opacity-0', '-translate-y-4', 'hidden');
                    firstActiveAlert.classList.add('opacity-100', 'translate-y-0');
                    
                    setTimeout(() => {
                        const offset = 120;
                        const elementPosition = firstActiveAlert.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - offset;

                        window.scrollTo({
                            top: Math.max(0, offsetPosition),
                            behavior: 'smooth'
                        });

                        setTimeout(() => {
                            highlightAlerts(activeAlerts);
                        }, 300);
                    }, 100);
                } else {
                    scrollToAlerts();
                }
            }

            // Fungsi scroll ke resolved alerts
            function scrollToResolvedAlerts() {
                // Cek jika sedang di Monthly view, jangan lakukan scroll
                if (contentMonthly && !contentMonthly.classList.contains('hidden')) {
                    return;
                }
                
                // Pastikan kita di Daily view
                if (contentDaily && contentDaily.classList.contains('hidden')) {
                    switchToDaily();
                    setTimeout(() => {
                        performScrollToResolvedAlerts();
                    }, 500);
                } else {
                    performScrollToResolvedAlerts();
                }
            }

            function performScrollToResolvedAlerts() {
                const resolvedAlerts = document.querySelectorAll('.alert-item[data-alert-status="resolved"]');
                if (resolvedAlerts.length > 0) {
                    const firstResolvedAlert = resolvedAlerts[0];
                    // Pastikan alert visible
                    firstResolvedAlert.classList.remove('opacity-0', '-translate-y-4', 'hidden');
                    firstResolvedAlert.classList.add('opacity-100', 'translate-y-0');
                    
                    setTimeout(() => {
                        const offset = 120;
                        const elementPosition = firstResolvedAlert.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - offset;

                        window.scrollTo({
                            top: Math.max(0, offsetPosition),
                            behavior: 'smooth'
                        });

                        setTimeout(() => {
                            highlightAlerts(resolvedAlerts);
                        }, 300);
                    }, 100);
                } else {
                    scrollToAlerts();
                }
            }

            // Fungsi scroll ke critical alerts
            function scrollToCriticalAlerts() {
                // Cek jika sedang di Monthly view, jangan lakukan scroll
                if (contentMonthly && !contentMonthly.classList.contains('hidden')) {
                    return;
                }
                
                // Pastikan kita di Daily view
                if (contentDaily && contentDaily.classList.contains('hidden')) {
                    switchToDaily();
                    setTimeout(() => {
                        performScrollToCriticalAlerts();
                    }, 500);
                } else {
                    performScrollToCriticalAlerts();
                }
            }

            function performScrollToCriticalAlerts() {
                const criticalAlerts = document.querySelectorAll('.alert-item[data-alert-type="danger"]');
                if (criticalAlerts.length > 0) {
                    const firstCriticalAlert = criticalAlerts[0];
                    // Pastikan alert visible
                    firstCriticalAlert.classList.remove('opacity-0', '-translate-y-4', 'hidden');
                    firstCriticalAlert.classList.add('opacity-100', 'translate-y-0');
                    
                    setTimeout(() => {
                        const offset = 120;
                        const elementPosition = firstCriticalAlert.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - offset;

                        window.scrollTo({
                            top: Math.max(0, offsetPosition),
                            behavior: 'smooth'
                        });

                        setTimeout(() => {
                            highlightAlerts(criticalAlerts);
                        }, 300);
                    }, 100);
                } else {
                    scrollToAlerts();
                }
            }

            function highlightAlerts(alerts) {
                alerts.forEach((alert, index) => {
                    // Pastikan alert visible
                    alert.classList.remove('opacity-0', '-translate-y-4');
                    alert.classList.add('opacity-100', 'translate-y-0');
                    
                    // Tambahkan highlight dengan delay untuk efek wave
                    setTimeout(() => {
                        alert.classList.add('highlight-alert');
                        alert.style.transition = 'all 0.3s ease';
                        alert.style.transform = 'scale(1.02)';
                        
                        setTimeout(() => {
                            alert.style.transform = 'scale(1)';
                        }, 300);
                    }, index * 100);
                });
                
                // Hapus highlight setelah beberapa detik
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.classList.remove('highlight-alert');
                    });
                }, 2500);
            }

            function initMonthlyChart() {
                const ctx = document.getElementById('monthlyAlertsChart');
                if (!ctx) {
                    console.error('Monthly chart canvas not found');
                    return;
                }

                try {
                    // Destroy existing chart jika ada
                    if (ctx.chart) {
                        ctx.chart.destroy();
                    }

                    ctx.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: monthlyAlertData.labels,
                            datasets: [
                                {
                                    label: 'Active Alerts',
                                    data: monthlyAlertData.active_alerts,
                                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                    borderColor: 'rgb(239, 68, 68)',
                                    borderWidth: 2
                                },
                                {
                                    label: 'Resolved Alerts',
                                    data: monthlyAlertData.resolved_alerts,
                                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                    borderColor: 'rgb(34, 197, 94)',
                                    borderWidth: 2
                                },
                                {
                                    label: 'Critical Alerts',
                                    data: monthlyAlertData.critical_alerts,
                                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                                    borderColor: 'rgb(99, 102, 241)',
                                    borderWidth: 2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Monthly Alert Distribution'
                                },
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Number of Alerts'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Months'
                                    }
                                }
                            }
                        }
                    });
                    
                    console.log('Monthly chart initialized successfully');
                } catch (error) {
                    console.error('Error initializing monthly chart:', error);
                }
            }

            // Startup Animation Sequence
            setTimeout(() => {
                const startupOverlay = document.getElementById('startupOverlay');
                const mainContent = document.getElementById('mainContent');
                
                // Fade out startup overlay
                startupOverlay.classList.add('opacity-0', 'transition-all', 'duration-700');
                
                setTimeout(() => {
                    startupOverlay.classList.add('hidden');
                    
                    // Animate main content elements with staggered delay
                    const elements = [
                        { id: 'pageHeader', delay: 100 },
                        { id: 'dashboardHeader', delay: 200 },
                        { id: 'statsCards', delay: 300 },
                        { id: 'alertsSection', delay: 400 }
                    ];

                    elements.forEach((element, index) => {
                        setTimeout(() => {
                            const el = document.getElementById(element.id);
                            if (el) {
                                el.classList.remove('opacity-0', '-translate-y-4');
                                el.classList.add('opacity-100', 'translate-y-0');
                                
                                // Animate individual alert cards
                                if (element.id === 'alertsSection') {
                                    const alertCards = document.querySelectorAll('#alertsList > div');
                                    alertCards.forEach((card, cardIndex) => {
                                        setTimeout(() => {
                                            card.classList.remove('opacity-0', '-translate-y-4');
                                            card.classList.add('opacity-100', 'translate-y-0');
                                        }, cardIndex * 150);
                                    });
                                }

                                // Animate individual stat cards
                                if (element.id === 'statsCards') {
                                    const statCards = document.querySelectorAll('#statsCards > div');
                                    statCards.forEach((card, cardIndex) => {
                                        setTimeout(() => {
                                            card.classList.remove('opacity-0', '-translate-y-4');
                                            card.classList.add('opacity-100', 'translate-y-0');
                                        }, cardIndex * 100);
                                    });
                                }
                            }
                        }, element.delay);
                    });

                }, 700);
            }, 1500);

            // Expose scroll functions to global scope untuk onclick handler
            window.scrollToAlerts = scrollToAlerts;
            window.scrollToActiveAlerts = scrollToActiveAlerts;
            window.scrollToResolvedAlerts = scrollToResolvedAlerts;
            window.scrollToCriticalAlerts = scrollToCriticalAlerts;

            // Event Listeners
            if (btnDaily) btnDaily.onclick = switchToDaily;
            if (btnMonthly) btnMonthly.onclick = switchToMonthly;

            // Add hover effects
            const alertCards = document.querySelectorAll('.alert-item');
            alertCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Stats cards hover effects
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Auto-refresh simulation
            setInterval(() => {
                const criticalCard = document.querySelector('.alert-pulse');
                if (criticalCard) {
                    criticalCard.style.animation = 'none';
                    setTimeout(() => {
                        criticalCard.style.animation = 'alertPulse 2s ease-in-out infinite';
                    }, 10);
                }
            }, 4000);

            // Debug info
            console.log('Alert system initialized');
            console.log('Total alerts:', {{ $stats['total_alerts'] }});
            console.log('Monthly data:', monthlyAlertData);
        });
    </script>

    {{-- Stack untuk scripts dari komponen --}}
    @stack('scripts')

</body>
</html>