<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perumdam Tirta Bengkayang - Sistem Pengadaan Barang/Jasa</title>
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
                    <a href="{{ url('/services') }}" class="hover:text-blue-200 transition">Layanan</a>
                    <a href="{{ url('/contact') }}" class="hover:text-blue-200 transition">Kontak</a>
                </div>
                <div class="flex space-x-3">
                    @auth
                        <!-- Jika sudah login, tampilkan menu dashboard dan logout -->
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
                        <!-- Jika belum login, tampilkan tombol login dan register -->
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

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white pt-32 pb-20">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Sistem Pengadaan Barang/Jasa</h1>
                    <p class="text-xl mb-6 text-blue-100">Perusahaan Umum Daerah Air Minum (PERUMDAM) Tirta Bengkayang</p>
                    <p class="text-lg mb-8">Sistem informasi pengadaan barang/jasa berbasis web untuk mendukung transparansi dan efisiensi proses pengadaan.</p>
                    <div class="flex space-x-4">
                        @guest
                            <a href="{{ route('register') }}" class="bg-green-500 hover:bg-green-600 px-6 py-3 rounded-lg font-semibold transition">
                                <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                            </a>
                        @endguest
                        <a href="#layanan" class="bg-transparent border border-white hover:bg-white hover:text-blue-700 px-6 py-3 rounded-lg font-semibold transition">
                            <i class="fas fa-info-circle mr-2"></i>Pelajari Lebih
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo Resmi PERUMDAM Tirta Bengkayang" class="rounded-lg shadow-xl max-h-64">
                </div>
            </div>
        </div>
    </section>

    <!-- Section Statistik -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-lg shadow-md">
                    <i class="fas fa-file-alt text-blue-600 text-4xl mb-4"></i>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalProcurements) }}</h3>
                    <p class="text-gray-600">Pengadaan Selesai</p>
                </div>
                <div class="text-center p-6 rounded-lg shadow-md">
                    <i class="fas fa-truck text-blue-600 text-4xl mb-4"></i>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalSuppliers) }}</h3>
                    <p class="text-gray-600">Supplier Terdaftar</p>
                </div>
                <div class="text-center p-6 rounded-lg shadow-md">
                    <i class="fas fa-tags text-blue-600 text-4xl mb-4"></i>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalCategories) }}</h3>
                    <p class="text-gray-600">Kategori Barang</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Layanan -->
    <section id="layanan" class="py-16 bg-gray-100">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Layanan Kami</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Sistem pengadaan barang/jasa yang transparan, efisien, dan akuntabel</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Pengajuan Online</h3>
                    <p class="text-gray-600">Ajukan kebutuhan barang/jasa secara online dengan mudah dan cepat</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Persetujuan Digital</h3>
                    <p class="text-gray-600">Proses persetujuan oleh pimpinan secara digital dan transparan</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-gavel text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Penawaran Vendor</h3>
                    <p class="text-gray-600">Vendor dapat memberikan penawaran harga secara online</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo" class="h-8 w-auto">
                        <h3 class="text-xl font-bold">PERUMDAM Tirta Bengkayang</h3>
                    </div>
                    <p class="text-gray-400">Jl. Raya Pontianak, Eks. Kantor BPBD Bengkayang, No. 95, RT/RW : 020/11</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white">Beranda</a></li>
                        <li><a href="{{ url('/about') }}" class="text-gray-400 hover:text-white">Tentang Kami</a></li>
                        <li><a href="{{ url('/services') }}" class="text-gray-400 hover:text-white">Layanan</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-gray-400 hover:text-white">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Jam Operasional</h3>
                    <p class="text-gray-400">Senin - Jumat: 08:00 - 16:00</p>
                    <p class="text-gray-400">Sabtu - Minggu: Tutup</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} PERUMDAM Tirta Bengkayang. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>