<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan - PERUMDAM Tirta Bengkayang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

    <!-- Navbar dengan kondisi login -->
    <nav class="bg-blue-700 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-12 w-auto">
                    <div>
                        <a href="{{ url('/') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-blue-200">Sistem Pengadaan Barang/Jasa</p>
                    </div>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="{{ url('/') }}" class="hover:text-blue-200 transition">Beranda</a>
                    <a href="{{ url('/about') }}" class="hover:text-blue-200 transition">Tentang Kami</a>
                    <a href="{{ url('/services') }}" class="text-blue-200">Layanan</a>
                    <a href="{{ url('/contact') }}" class="hover:text-blue-200 transition">Kontak</a>
                </div>
                <div class="flex space-x-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded transition">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-transparent border border-white px-4 py-2 rounded hover:bg-white hover:text-blue-700 transition">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded transition">
                            <i class="fas fa-user-plus mr-2"></i>Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 pt-32 pb-16">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Layanan Kami</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Berikut adalah layanan yang tersedia dalam sistem pengadaan barang/jasa</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-user text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Untuk User Biasa</h3>
                <ul class="text-gray-600 space-y-2">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Melihat informasi pengadaan</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Mendaftar sebagai vendor</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Mengikuti perkembangan pengadaan</li>
                </ul>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-clipboard-list text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Untuk Bagian Pengadaan</h3>
                <ul class="text-gray-600 space-y-2">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Membuat pengajuan pengadaan</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Mengelola data barang dan supplier</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Memilih vendor terbaik</li>
                </ul>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Untuk Pimpinan</h3>
                <ul class="text-gray-600 space-y-2">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Menyetujui pengajuan pengadaan</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Memberikan catatan revisi</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Melihat riwayat persetujuan</li>
                </ul>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-gavel text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Untuk Vendor</h3>
                <ul class="text-gray-600 space-y-2">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Melihat pengajuan yang terbuka</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Memberikan penawaran harga</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Melihat status penawaran</li>
                </ul>
            </div>
        </div>

        <div class="text-center mt-12">
            @guest
                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </a>
            @endguest
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-8 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; {{ date('Y') }} PERUMDAM Tirta Bengkayang. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>