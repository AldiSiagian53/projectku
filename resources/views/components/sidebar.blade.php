{{-- REMIX ICON CDN --}}
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

{{-- OVERLAY UNTUK MOBILE --}}
<div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden md:hidden backdrop-blur-sm transition-all duration-500"></div>

{{-- SIDEBAR DESKTOP --}}
<aside class="w-64 bg-gradient-to-b from-blue-800 via-blue-700 to-blue-900 text-white p-6 min-h-screen fixed left-0 top-0 z-10 md:block hidden flex flex-col shadow-2xl border-r border-blue-600">
    <!-- Konten sidebar desktop tetap sama -->
    <div class="flex-1">
        {{-- LOGO WITH ENHANCED ANIMATION --}}
        <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-white glow-text">
            MONITORING PLTS
        </h2>
            <p class="text-blue-200 text-sm mt-2 opacity-80">Eco Power System</p>
        </div>

        {{-- NAVIGATION WITH ENHANCED EFFECTS --}}
        <nav class="space-y-3">
            <a href="{{ route('dashboard') }}" class="menu-item-enhanced block py-4 px-4 rounded-xl hover:bg-white/10 transition-all duration-500 border-l-4 border-transparent hover:border-cyan-400 hover:shadow-lg hover:scale-105 group {{ request()->routeIs('dashboard') ? 'bg-white/10 border-cyan-400 shadow-lg' : '' }}">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-cyan-400/30 transition-colors duration-300 mr-3">
                        <i class="ri-dashboard-3-line text-lg"></i>
                    </div>
                    <span class="font-semibold">Dashboard</span>
                </div>
            </a>
            
            <a href="{{ route('report.index') }}" class="menu-item-enhanced block py-4 px-4 rounded-xl hover:bg-white/10 transition-all duration-500 border-l-4 border-transparent hover:border-blue-400 hover:shadow-lg hover:scale-105 group {{ request()->routeIs('report.index') ? 'bg-white/10 border-blue-400 shadow-lg' : '' }}">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-blue-400/30 transition-colors duration-300 mr-3">
                        <i class="ri-file-chart-line text-lg"></i>
                    </div>
                    <span class="font-semibold">Report</span>
                </div>
            </a>
            
            <a href="{{ route('alerts.index') }}" class="menu-item-enhanced block py-4 px-4 rounded-xl hover:bg-white/10 transition-all duration-500 border-l-4 border-transparent hover:border-orange-400 hover:shadow-lg hover:scale-105 group {{ request()->routeIs('alerts.index') ? 'bg-white/10 border-orange-400 shadow-lg' : '' }}">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-orange-400/30 transition-colors duration-300 mr-3">
                        <i class="ri-notification-line text-lg"></i>
                    </div>
                    <span class="font-semibold">Alerts</span>
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">6</span>
                </div>
            </a>

            <a href="{{ route('settings.index') }}" class="menu-item-enhanced block py-4 px-4 rounded-xl hover:bg-white/10 transition-all duration-500 border-l-4 border-transparent hover:border-purple-400 hover:shadow-lg hover:scale-105 group {{ request()->routeIs('settings.*') ? 'bg-white/10 border-purple-400 shadow-lg' : '' }}">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-purple-400/30 transition-colors duration-300 mr-3">
                        <i class="ri-settings-3-line text-lg"></i>
                    </div>
                    <span class="font-semibold">Settings</span>
                </div>
            </a>
        </nav>

        {{-- QUICK STATS --}}
        <div class="mt-8 p-4 bg-white/5 rounded-xl border border-white/10">
            <h3 class="text-blue-200 text-sm font-semibold mb-3">System Status</h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-blue-300 text-sm">Power Output</span>
                    <span class="text-cyan-400 font-bold">{{ $systemStatus['power_output'] ?? '0.00' }} kW</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-blue-300 text-sm">Battery</span>
                    <span class="text-green-400 font-bold">{{ $systemStatus['battery'] ?? '0' }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-blue-300 text-sm">Efficiency</span>
                    <span class="text-yellow-400 font-bold">{{ $systemStatus['efficiency'] ?? '0' }}%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- LOGOUT BUTTON WITH ENHANCED ANIMATION --}}
    <div class="mt-auto pt-4 border-t border-blue-600/50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="menu-item-enhanced w-full flex items-center py-4 px-4 rounded-xl hover:bg-red-500/20 transition-all duration-500 text-left border-l-4 border-transparent hover:border-red-400 hover:shadow-lg group">
                <div class="w-8 h-8 rounded-lg bg-red-500/20 flex items-center justify-center group-hover:bg-red-400/30 transition-colors duration-300 mr-3">
                    <i class="ri-logout-box-line text-lg"></i>
                </div>
                <span class="font-semibold group-hover:text-red-200 transition-colors duration-300">Logout</span>
            </button>
        </form>
    </div>
</aside>

{{-- MOBILE MENU BUTTON --}}
<button id="mobileMenuBtn" class="md:hidden fixed top-6 left-6 z-50 bg-gradient-to-br from-cyan-500 to-blue-600 text-white p-4 rounded-2xl shadow-2xl transition-all duration-500 hover:scale-110 hover:shadow-cyan-500/25 hover:from-cyan-600 hover:to-blue-700">
    <div class="relative">
        <i class="ri-menu-line text-2xl"></i>
    </div>
</button>

{{-- MOBILE SIDEBAR DENGAN BACKGROUND SAMA SEPERTI DESKTOP --}}
<aside id="mobileSidebar" class="w-80 bg-gradient-to-b from-blue-800 via-blue-700 to-blue-900 text-white p-6 min-h-screen fixed left-0 top-0 z-40 transform -translate-x-full flex flex-col shadow-2xl border-r border-blue-600 transition-transform duration-500">
    <div class="flex-1">
        {{-- MOBILE HEADER --}}
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="ri-flashlight-line text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-cyan-400 to-blue-300 bg-clip-text text-transparent">
                        PLTS
                    </h2>
                    <p class="text-blue-200 text-sm">Eco Power</p>
                </div>
            </div>
            <button id="closeMobileMenu" class="text-white hover:text-cyan-300 transition-all duration-500 p-2 rounded-lg hover:bg-white/10">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        {{-- MOBILE NAVIGATION --}}
        <nav class="space-y-4">
            <a href="{{ route('dashboard') }}" class="block py-4 px-4 rounded-2xl hover:bg-white/10 transition-all duration-500 border-l-4 border-transparent hover:border-cyan-400 hover:shadow-lg transform hover:translate-x-2 group {{ request()->routeIs('dashboard') ? 'bg-white/10 border-cyan-400 shadow-lg' : '' }}">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center group-hover:bg-cyan-400/30 transition-colors duration-300 mr-4">
                        <i class="ri-dashboard-3-line text-xl"></i>
                    </div>
                    <span class="font-semibold text-lg">Dashboard</span>
                </div>
            </a>
            
            <a href="{{ route('report.index') }}" class="block py-4 px-4 rounded-2xl hover:bg-white/10 transition-all duration-500 border-l-4 border-transparent hover:border-blue-400 hover:shadow-lg transform hover:translate-x-2 group {{ request()->routeIs('report.index') ? 'bg-white/10 border-blue-400 shadow-lg' : '' }}">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center group-hover:bg-blue-400/30 transition-colors duration-300 mr-4">
                        <i class="ri-file-chart-line text-xl"></i>
                    </div>
                    <span class="font-semibold text-lg">Report</span>
                </div>
            </a>
            
            <a href="{{ route('alerts.index') }}" class="block py-4 px-4 rounded-2xl hover:bg-white/10 transition-all duration-500 border-l-4 border-transparent hover:border-orange-400 hover:shadow-lg transform hover:translate-x-2 group {{ request()->routeIs('alerts.index') ? 'bg-white/10 border-orange-400 shadow-lg' : '' }}">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center group-hover:bg-orange-400/30 transition-colors duration-300 mr-4">
                        <i class="ri-notification-line text-xl"></i>
                    </div>
                    <span class="font-semibold text-lg">Alerts</span>
                    <span class="ml-auto bg-red-500 text-white text-sm px-2 py-1 rounded-full animate-pulse">3</span>
                </div>
            </a>

            <a href="{{ route('settings.index') }}" class="block py-4 px-4 rounded-2xl hover:bg-white/10 transition-all duration-500 border-l-4 border-transparent hover:border-purple-400 hover:shadow-lg transform hover:translate-x-2 group {{ request()->routeIs('settings.*') ? 'bg-white/10 border-purple-400 shadow-lg' : '' }}">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center group-hover:bg-purple-400/30 transition-colors duration-300 mr-4">
                        <i class="ri-settings-3-line text-xl"></i>
                    </div>
                    <span class="font-semibold text-lg">Settings</span>
                </div>
            </a>
        </nav>

        {{-- MOBILE QUICK STATS --}}
        <div class="mt-8 p-4 bg-white/5 rounded-2xl border border-white/10">
            <h3 class="text-blue-200 text-sm font-semibold mb-3">Live Status</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-white/5 rounded-xl">
                    <div class="text-cyan-400 font-bold text-lg">{{ $systemStatus['power_output'] ?? '0.00' }} kW</div>
                    <div class="text-blue-300 text-xs">Power</div>
                </div>
                <div class="text-center p-3 bg-white/5 rounded-xl">
                    <div class="text-green-400 font-bold text-lg">{{ $systemStatus['battery'] ?? '0' }}%</div>
                    <div class="text-blue-300 text-xs">Battery</div>
                </div>
            </div>
            <div class="mt-3 text-center p-3 bg-white/5 rounded-xl">
                <div class="text-yellow-400 font-bold text-lg">{{ $systemStatus['efficiency'] ?? '0' }}%</div>
                <div class="text-blue-300 text-xs">Efficiency</div>
            </div>
        </div>
    </div>

    {{-- MOBILE LOGOUT --}}
    <div class="mt-auto pt-4 border-t border-white/20">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center py-4 px-4 rounded-2xl hover:bg-red-500/20 transition-all duration-500 text-left border-l-4 border-transparent hover:border-red-400 transform hover:translate-x-2 group">
                <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center group-hover:bg-red-400/30 transition-colors duration-300 mr-4">
                    <i class="ri-logout-box-line text-xl"></i>
                </div>
                <span class="font-semibold text-lg group-hover:text-red-200">Logout</span>
            </button>
        </form>
    </div>
</aside>

{{-- STYLES UNTUK SIDEBAR MOBILE --}}
@push('styles')
<style>
    /* Animasi Bounce untuk Sidebar Mobile */
    @keyframes slideInBounce {
        0% {
            transform: translateX(-100%);
        }
        60% {
            transform: translateX(5%);
        }
        80% {
            transform: translateX(-2%);
        }
        100% {
            transform: translateX(0);
        }
    }

    @keyframes slideOutBounce {
        0% {
            transform: translateX(0);
        }
        20% {
            transform: translateX(-5%);
        }
        40% {
            transform: translateX(2%);
        }
        100% {
            transform: translateX(-100%);
        }
    }

    .sidebar-open {
        animation: slideInBounce 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
    }

    .sidebar-close {
        animation: slideOutBounce 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
    }

    /* Efek blur untuk konten saat sidebar terbuka */
    .content-blur {
        filter: blur(4px);
        transition: filter 0.3s ease;
    }

    /* Smooth transitions untuk overlay */
    .overlay-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }

    .overlay-fade-out {
        animation: fadeOut 0.3s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
</style>
@endpush

{{-- JAVASCRIPT UNTUK SIDEBAR MOBILE --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mainContent = document.querySelector('main'); // Asumsi main content ada di tag <main>
        
        let isSidebarOpen = false;

        function openMobileSidebar() {
            if (isSidebarOpen) return;
            
            isSidebarOpen = true;
            
            // Animasi sidebar masuk dengan bounce
            mobileSidebar.classList.remove('sidebar-close', '-translate-x-full');
            mobileSidebar.classList.add('sidebar-open');
            
            // Tampilkan overlay dengan animasi
            mobileOverlay.classList.remove('hidden', 'overlay-fade-out');
            mobileOverlay.classList.add('overlay-fade-in');
            
            // Tambahkan efek blur ke konten utama
            if (mainContent) {
                mainContent.classList.add('content-blur');
            }
            
            // Nonaktifkan scroll body
            document.body.style.overflow = 'hidden';
        }
        
        function closeMobileSidebar() {
            if (!isSidebarOpen) return;
            
            isSidebarOpen = false;
            
            // Animasi sidebar keluar dengan bounce
            mobileSidebar.classList.remove('sidebar-open');
            mobileSidebar.classList.add('sidebar-close');
            
            // Sembunyikan overlay dengan animasi
            mobileOverlay.classList.remove('overlay-fade-in');
            mobileOverlay.classList.add('overlay-fade-out');
            
            // Hapus efek blur dari konten utama
            if (mainContent) {
                mainContent.classList.remove('content-blur');
            }
            
            // Aktifkan scroll body
            document.body.style.overflow = '';
            
            // Reset sidebar setelah animasi selesai
            setTimeout(() => {
                if (!isSidebarOpen) {
                    mobileSidebar.classList.add('-translate-x-full');
                    mobileSidebar.classList.remove('sidebar-close');
                    mobileOverlay.classList.add('hidden');
                    mobileOverlay.classList.remove('overlay-fade-out');
                }
            }, 500);
        }
        
        // Event listeners
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', openMobileSidebar);
        }
        
        if (closeMobileMenu) {
            closeMobileMenu.addEventListener('click', closeMobileSidebar);
        }
        
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', closeMobileSidebar);
        }
        
        // Close sidebar saat resize ke desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                closeMobileSidebar();
            }
        });

        // Close sidebar dengan ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && isSidebarOpen) {
                closeMobileSidebar();
            }
        });

        // Swipe to close untuk mobile
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
@endpush