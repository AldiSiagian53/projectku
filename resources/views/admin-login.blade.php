<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Eco Power Monitoring</title>
    
    {{-- FONT MONTSERRAT --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    {{-- REMIX ICON --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    {{-- TAILWIND --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: "Montserrat", sans-serif !important;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            
            {{-- LEFT SIDE: STATISTICS & INFO --}}
            <div class="space-y-6">
                {{-- HEADER --}}
                <div class="glass-effect rounded-2xl p-6 text-white">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mr-4">
                            <i class="ri-admin-line text-3xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Admin Portal</h1>
                            <p class="text-white/80 text-sm">Eco Power Monitoring System</p>
                        </div>
                    </div>
                </div>

                {{-- STATISTICS CARDS --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Total Users --}}
                    <div class="glass-effect rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-500/30 rounded-lg flex items-center justify-center">
                                <i class="ri-user-line text-2xl"></i>
                            </div>
                            <span class="text-3xl font-bold">{{ $totalUsers }}</span>
                        </div>
                        <p class="text-white/80 text-sm">Total Users</p>
                    </div>

                    {{-- Total Admins --}}
                    <div class="glass-effect rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-500/30 rounded-lg flex items-center justify-center">
                                <i class="ri-shield-user-line text-2xl"></i>
                            </div>
                            <span class="text-3xl font-bold">{{ $totalAdmins }}</span>
                        </div>
                        <p class="text-white/80 text-sm">Admin Accounts</p>
                    </div>

                    {{-- Regular Users --}}
                    <div class="glass-effect rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-yellow-500/30 rounded-lg flex items-center justify-center">
                                <i class="ri-user-3-line text-2xl"></i>
                            </div>
                            <span class="text-3xl font-bold">{{ $totalRegularUsers }}</span>
                        </div>
                        <p class="text-white/80 text-sm">Regular Users</p>
                    </div>
                </div>

                {{-- FEATURES CARD --}}
                <div class="glass-effect rounded-xl p-6 text-white">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="ri-shield-check-line mr-2"></i>
                        Admin Features
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start space-x-3">
                            <i class="ri-user-add-line text-xl text-green-400 mt-1"></i>
                            <div>
                                <h3 class="font-semibold">Manage Users</h3>
                                <p class="text-white/70 text-sm">Create, edit, and delete user accounts</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="ri-lock-password-line text-xl text-blue-400 mt-1"></i>
                            <div>
                                <h3 class="font-semibold">Password Management</h3>
                                <p class="text-white/70 text-sm">Reset and manage user passwords</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="ri-bar-chart-box-line text-xl text-purple-400 mt-1"></i>
                            <div>
                                <h3 class="font-semibold">System Analytics</h3>
                                <p class="text-white/70 text-sm">View system statistics and reports</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="ri-settings-3-line text-xl text-orange-400 mt-1"></i>
                            <div>
                                <h3 class="font-semibold">System Settings</h3>
                                <p class="text-white/70 text-sm">Configure system preferences</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECURITY NOTICE --}}
                <div class="glass-effect rounded-xl p-6 border-l-4 border-yellow-400">
                    <div class="flex items-start">
                        <i class="ri-shield-warning-line text-yellow-400 text-2xl mr-3 mt-1"></i>
                        <div class="text-white">
                            <h3 class="font-semibold mb-2">Security Notice</h3>
                            <p class="text-white/80 text-sm">
                                This is a restricted area. Only authorized administrators are allowed to access this portal. 
                                All login attempts are logged for security purposes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: LOGIN FORM --}}
            <div>
                <div class="bg-white rounded-2xl shadow-2xl p-8 sticky top-6">
                    {{-- LOGIN FORM HEADER --}}
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-700 rounded-full mb-4">
                            <i class="ri-shield-user-line text-white text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Admin Login</h2>
                        <p class="text-gray-600 text-sm">Masuk sebagai administrator</p>
                    </div>

                    {{-- ERROR MESSAGE --}}
                    @if ($errors->has('login_error'))
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                            <div class="flex items-center">
                                <i class="ri-error-warning-line text-red-500 text-xl mr-2"></i>
                                <p class="text-red-700 text-sm font-medium">{{ $errors->first('login_error') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- SUCCESS MESSAGE --}}
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                            <div class="flex items-center">
                                <i class="ri-checkbox-circle-line text-green-500 text-xl mr-2"></i>
                                <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- LOGIN FORM --}}
                    <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                        @csrf

                        {{-- EMAIL FIELD --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="ri-mail-line mr-2"></i>Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ri-mail-line text-gray-400"></i>
                                </div>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required 
                                    autofocus
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition duration-200 @error('email') border-red-500 @enderror"
                                    placeholder="admin@example.com"
                                >
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- PASSWORD FIELD --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="ri-lock-2-line mr-2"></i>Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ri-lock-2-line text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition duration-200 @error('password') border-red-500 @enderror"
                                    placeholder="Masukkan password"
                                >
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- REMEMBER ME --}}
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                name="remember" 
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            >
                            <label for="remember" class="ml-2 text-sm text-gray-600">
                                Ingat saya
                            </label>
                        </div>

                        {{-- SUBMIT BUTTON --}}
                        <button 
                            type="submit" 
                            class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold py-3 px-4 rounded-lg shadow-lg transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 flex items-center justify-center"
                        >
                            <i class="ri-login-box-line mr-2"></i>
                            Masuk sebagai Admin
                        </button>
                    </form>

                    {{-- BACK TO REGULAR LOGIN --}}
                    <div class="mt-6 text-center">
                        <a 
                            href="{{ route('login') }}" 
                            class="text-sm text-gray-600 hover:text-purple-600 transition-colors flex items-center justify-center"
                        >
                            <i class="ri-arrow-left-line mr-2"></i>
                            Kembali ke Login Biasa
                        </a>
                    </div>

                    {{-- FOOTER --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-xs text-gray-500 text-center">
                            © 2025 Eco Power Monitoring<br>
                            Admin Portal - Restricted Access
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>

