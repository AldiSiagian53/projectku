<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Eco Power Monitoring</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- FONT MONTSERRAT --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    {{-- REMIX ICON --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    {{-- TAILWIND --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    {{-- CONTENT --}}
    <main class="w-full min-h-screen p-4 md:p-8 transition-all duration-300">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Admin Panel</h1>
                    <p class="text-gray-600">Kelola akun pengguna sistem</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Logged in as</p>
                    <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    <span class="inline-block mt-1 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        <i class="ri-shield-user-line mr-1"></i>Admin
                    </span>
                </div>
            </div>
        </div>

        {{-- STATISTICS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm mb-1">Total Users</p>
                        <p class="text-3xl font-bold">{{ $totalUsers }}</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="ri-user-line text-3xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm mb-1">Admin Accounts</p>
                        <p class="text-3xl font-bold">{{ $totalAdmins }}</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="ri-shield-user-line text-3xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm mb-1">Regular Users</p>
                        <p class="text-3xl font-bold">{{ $totalRegularUsers }}</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="ri-user-3-line text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
            <i class="ri-checkbox-circle-line text-green-500 text-xl mr-3"></i>
            <span class="text-green-800">{{ session('success') }}</span>
        </div>
        @endif

        {{-- ERROR MESSAGE --}}
        @if(session('error') || $errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center mb-2">
                <i class="ri-error-warning-line text-red-500 text-xl mr-3"></i>
                <span class="text-red-800 font-semibold">Terjadi kesalahan</span>
            </div>
            @if(session('error'))
                <p class="text-red-700 ml-8">{{ session('error') }}</p>
            @endif
            @if($errors->any())
                <ul class="list-disc list-inside text-red-700 ml-8 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- CREATE USER CARD --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                        <i class="ri-user-add-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Buat Akun Baru</h2>
                        <p class="text-gray-500 text-sm">Tambahkan user baru ke sistem</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.store') }}" class="space-y-4">
                    @csrf
                    
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-user-3-line mr-2"></i>Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            id="name"
                            name="name" 
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 border @error('name') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            placeholder="Masukkan nama lengkap"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-mail-line mr-2"></i>Email
                        </label>
                        <input 
                            type="email" 
                            id="email"
                            name="email" 
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border @error('email') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            placeholder="contoh@email.com"
                            required
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-lock-2-line mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password"
                                name="password" 
                                class="w-full px-4 py-3 border @error('password') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                placeholder="Minimal 8 karakter"
                                required
                            >
                            <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i class="ri-eye-line" id="eye-password"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-lock-2-line mr-2"></i>Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password_confirmation"
                                name="password_confirmation" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                placeholder="Ulangi password"
                                required
                            >
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i class="ri-eye-line" id="eye-password_confirmation"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-shield-user-line mr-2"></i>Role
                        </label>
                        <select 
                            id="role"
                            name="role" 
                            class="w-full px-4 py-3 border @error('role') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center"
                    >
                        <i class="ri-user-add-line mr-2"></i>
                        Buat Akun
                    </button>
                </form>

                {{-- Info --}}
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-800">
                        <i class="ri-information-line mr-2"></i>
                        User yang dibuat dapat langsung login menggunakan email dan password yang telah dibuat.
                    </p>
                </div>
            </div>

            {{-- USERS LIST CARD --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                        <i class="ri-user-list-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Daftar Pengguna</h2>
                        <p class="text-gray-500 text-sm">Total: {{ $users->count() }} user</p>
                    </div>
                </div>

                <div class="space-y-3 max-h-[600px] overflow-y-auto">
                    @forelse($users as $user)
                        <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors {{ $user->id === auth()->id() ? 'bg-blue-50 border-blue-300' : '' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <i class="ri-user-3-line text-blue-500 mr-2"></i>
                                        <h3 class="font-semibold text-gray-800">{{ $user->name }}</h3>
                                        @if($user->id === auth()->id())
                                            <span class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded-full">Anda</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <i class="ri-mail-line mr-2"></i>
                                        {{ $user->email }}
                                    </p>
                                    <div class="flex items-center mt-2 space-x-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            <i class="ri-{{ $user->role === 'admin' ? 'shield-user' : 'user-3' }}-line mr-1"></i>
                                            {{ ucfirst($user->role) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <i class="ri-time-line mr-1"></i>
                                            {{ $user->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.destroy', $user->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Hapus user"
                                            >
                                                <i class="ri-delete-bin-line text-lg"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-sm">Tidak dapat dihapus</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="ri-user-line text-4xl mb-2"></i>
                            <p>Belum ada user</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        </div>
    </main>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById('eye-' + inputId);
            
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.remove('ri-eye-line');
                eye.classList.add('ri-eye-off-line');
            } else {
                input.type = 'password';
                eye.classList.remove('ri-eye-off-line');
                eye.classList.add('ri-eye-line');
            }
        }
    </script>

    {{-- Stack untuk scripts dari komponen --}}
    @stack('scripts')

</body>
</html>

