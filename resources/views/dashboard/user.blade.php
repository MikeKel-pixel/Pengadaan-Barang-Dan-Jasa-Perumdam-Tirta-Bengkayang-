<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - PERUMDAM Tirta Bengkayang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <nav class="bg-blue-600 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ route('user.dashboard') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-blue-200">Portal Informasi Pengadaan</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-blue-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-green-500 px-3 py-1 rounded text-sm">User</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 pt-24 pb-8">
        <!-- Selamat Datang -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white mb-8">
            <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="text-blue-100">Anda login sebagai User Biasa. Silakan pilih menu di bawah ini.</p>
        </div>

        <!-- Menu Utama -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- 1. Informasi Pengadaan -->
            <a href="{{ route('user.procurements') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center">
                <i class="fas fa-file-alt text-blue-500 text-4xl mb-3"></i>
                <h3 class="text-xl font-bold text-gray-700">Informasi Pengadaan</h3>
                <p class="text-gray-500 text-sm mt-2">Lihat daftar pengadaan yang telah selesai</p>
            </a>

            <!-- 2. Daftar sebagai Vendor -->
            <a href="{{ route('user.register-vendor') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center border-2 border-green-400">
                <i class="fas fa-user-plus text-green-500 text-4xl mb-3"></i>
                <h3 class="text-xl font-bold text-gray-700">Daftar sebagai Vendor</h3>
                <p class="text-gray-500 text-sm mt-2">Daftarkan perusahaan Anda sebagai vendor resmi</p>
                <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                    <i class="fas fa-check-circle mr-1"></i> Klik untuk mendaftar
                </span>
            </a>

            <!-- 3. Perkembangan Pengadaan -->
            <a href="{{ route('user.track-progress') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center">
                <i class="fas fa-chart-line text-purple-500 text-4xl mb-3"></i>
                <h3 class="text-xl font-bold text-gray-700">Perkembangan Pengadaan</h3>
                <p class="text-gray-500 text-sm mt-2">Pantau status pengadaan yang sedang berjalan</p>
            </a>
        </div>

        <!-- Info Card -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-8">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-yellow-500 text-xl mr-3"></i>
                <div>
                    <p class="font-semibold text-yellow-700">Ingin menjadi vendor?</p>
                    <p class="text-yellow-600 text-sm">Klik menu <strong>"Daftar sebagai Vendor"</strong> di atas untuk mendaftarkan perusahaan Anda. Setelah mendaftar, data Anda akan diverifikasi oleh admin.</p>
                </div>
            </div>
        </div>

        <!-- Pengadaan Terbaru yang Selesai -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h2 class="text-lg font-bold">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    Pengadaan Terbaru yang Selesai
                </h2>
                <a href="{{ route('user.procurements') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="divide-y">
                @forelse($completedProcurements as $proc)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $proc->kode_pengajuan }}</p>
                            <p class="text-sm text-gray-500">{{ $proc->created_at->format('d F Y') }}</p>
                            <p class="text-sm text-gray-600">Total: Rp {{ number_format($proc->total_estimasi, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <a href="{{ route('user.procurement-detail', $proc->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>Belum ada pengadaan yang selesai</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>