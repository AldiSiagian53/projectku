<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - Eco Power Monitoring</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- FONT MONTSERRAT --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    {{-- REMIX ICON --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    {{-- TAILWIND --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    {{-- SIDEBAR COMPONENT --}}
    <x-sidebar />

    {{-- CONTENT --}}
    <main class="md:ml-64 w-full p-4 md:p-8 transition-all duration-300">
        <h1 class="text-3xl font-bold mb-2">Settings</h1>
        <p class="text-gray-600 mb-6">Kelola informasi akun dan pengaturan sistem</p>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
            <i class="ri-checkbox-circle-line text-green-500 text-xl mr-3"></i>
            <span class="text-green-800">{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- USER INFORMATION CARD --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                        <i class="ri-user-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Informasi Pengguna</h2>
                        <p class="text-gray-500 text-sm">Data profil akun Anda</p>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-user-3-line mr-2"></i>Nama Lengkap
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                value="{{ $user->name }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50" 
                                readonly
                            >
                            <div class="absolute right-3 top-3">
                                <span class="text-gray-400 text-xs">Read-only</span>
                            </div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-mail-line mr-2"></i>Email
                        </label>
                        <div class="relative">
                            <input 
                                type="email" 
                                value="{{ $user->email }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50" 
                                readonly
                            >
                            <div class="absolute right-3 top-3">
                                <span class="text-gray-400 text-xs">Read-only</span>
                            </div>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-shield-user-line mr-2"></i>Role
                        </label>
                        <div class="px-4 py-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <span class="text-blue-700 font-semibold">Administrator</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CHANGE PASSWORD CARD --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center mr-4">
                        <i class="ri-lock-password-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Ubah Password</h2>
                        <p class="text-gray-500 text-sm">Ganti password akun Anda</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.updatePassword') }}" class="space-y-4">
                    @csrf

                    {{-- Current Password --}}
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-lock-line mr-2"></i>Password Saat Ini
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="current_password"
                                name="current_password" 
                                class="w-full px-4 py-3 border @error('current_password') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                placeholder="Masukkan password saat ini"
                                required
                            >
                            <button type="button" onclick="togglePassword('current_password')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i class="ri-eye-line" id="eye-current_password"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="ri-error-warning-line mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-lock-2-line mr-2"></i>Password Baru
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="new_password"
                                name="new_password" 
                                class="w-full px-4 py-3 border @error('new_password') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                placeholder="Minimal 8 karakter"
                                required
                                minlength="8"
                            >
                            <button type="button" onclick="togglePassword('new_password')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i class="ri-eye-line" id="eye-new_password"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="ri-error-warning-line mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-lock-2-line mr-2"></i>Konfirmasi Password Baru
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="new_password_confirmation"
                                name="new_password_confirmation" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                placeholder="Ulangi password baru"
                                required
                                minlength="8"
                            >
                            <button type="button" onclick="togglePassword('new_password_confirmation')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i class="ri-eye-line" id="eye-new_password_confirmation"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] flex items-center justify-center"
                    >
                        <i class="ri-save-line mr-2"></i>
                        Simpan Password
                    </button>
                </form>
            </div>
        </div>

        {{-- ADDITIONAL INFO CARD --}}
        <div class="mt-6 bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center mb-4">
                <i class="ri-information-line text-blue-500 text-xl mr-3"></i>
                <h3 class="text-lg font-bold text-gray-800">Informasi</h3>
            </div>
            <div class="text-gray-600 space-y-2">
                <p class="flex items-start">
                    <i class="ri-checkbox-circle-line text-green-500 mr-2 mt-1"></i>
                    <span>Password harus minimal 8 karakter</span>
                </p>
                <p class="flex items-start">
                    <i class="ri-checkbox-circle-line text-green-500 mr-2 mt-1"></i>
                    <span>Gunakan kombinasi huruf, angka, dan simbol untuk keamanan maksimal</span>
                </p>
                <p class="flex items-start">
                    <i class="ri-checkbox-circle-line text-green-500 mr-2 mt-1"></i>
                    <span>Jangan bagikan password Anda kepada siapapun</span>
                </p>
            </div>
        </div>
    </main>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById('eye-' + inputId);
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.remove('ri-eye-line');
                eyeIcon.classList.add('ri-eye-off-line');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('ri-eye-off-line');
                eyeIcon.classList.add('ri-eye-line');
            }
        }
    </script>
</body>
</html>

