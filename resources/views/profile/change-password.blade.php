<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - PERUMDAM Tirta Bengkayang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    @php
        $userRole = Auth::user()->roles->first()->name ?? 'user';
        $bgColor = match($userRole) {
            'admin' => 'bg-blue-600',
            'pengadaan' => 'bg-green-600',
            'pimpinan' => 'bg-purple-600',
            'vendor' => 'bg-yellow-600',
            default => 'bg-gray-600'
        };
        $dashboardRoute = match($userRole) {
            'admin' => route('admin.dashboard'),
            'pengadaan' => route('pengadaan.dashboard'),
            'pimpinan' => route('pimpinan.dashboard'),
            'vendor' => route('vendor.dashboard'),
            default => route('user.dashboard')
        };
    @endphp

    <!-- Navbar -->
    <nav class="{{ $bgColor }} text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="{{ $dashboardRoute }}" class="text-xl font-bold">PERUMDAM - Ganti Password</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-blue-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded text-sm">{{ ucfirst($userRole) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 pt-24 pb-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Ganti Password</h1>
            <a href="{{ route('profile.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Profil
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6 max-w-lg mx-auto">
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Untuk keamanan akun Anda, gunakan password yang kuat dan jangan berikan kepada siapapun.
                </p>
            </div>

            <form action="{{ route('profile.change-password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="current_password" class="block text-gray-700 font-bold mb-2">Password Saat Ini *</label>
                    <input type="password" name="current_password" id="current_password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" 
                           placeholder="Masukkan password Anda saat ini" required>
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="new_password" class="block text-gray-700 font-bold mb-2">Password Baru *</label>
                    <input type="password" name="new_password" id="new_password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" 
                           placeholder="Minimal 6 karakter" required>
                    <p class="text-gray-500 text-xs mt-1">Minimal 6 karakter, gunakan kombinasi huruf dan angka</p>
                    @error('new_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="new_password_confirmation" class="block text-gray-700 font-bold mb-2">Konfirmasi Password Baru *</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" 
                           placeholder="Ketik ulang password baru" required>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded font-semibold">
                        <i class="fas fa-save mr-2"></i>Ganti Password
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-gray-100 border border-gray-300 p-4 mt-6 max-w-lg mx-auto rounded-lg">
            <h4 class="font-bold text-gray-700 mb-2">Tips Password Aman:</h4>
            <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                <li>Gunakan minimal 8 karakter</li>
                <li>Kombinasikan huruf besar, huruf kecil, angka, dan simbol</li>
                <li>Jangan gunakan informasi pribadi seperti tanggal lahir</li>
                <li>Jangan gunakan password yang sama untuk akun lain</li>
                <li>Ganti password secara berkala (minimal 3 bulan sekali)</li>
            </ul>
        </div>
    </div>
</body>
</html>