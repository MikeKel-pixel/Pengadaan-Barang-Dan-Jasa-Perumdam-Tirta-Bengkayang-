<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengadaan - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-green-600 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ route('pengadaan.dashboard') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-green-200">Sistem Pengadaan Barang/Jasa</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-green-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-blue-500 px-3 py-1 rounded text-sm">{{ Auth::user()->roles->first()->name ?? 'Pengadaan' }}</span>
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
        <h1 class="text-2xl font-bold mb-6">Selamat Datang, {{ Auth::user()->name }}!</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500">
                <div class="flex items-center">
                    <div class="bg-gray-100 rounded-full p-3">
                        <i class="fas fa-pencil-alt text-gray-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Draft</p>
                        <p class="text-2xl font-bold">{{ number_format($totalMyDraft) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-paper-plane text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Diajukan</p>
                        <p class="text-2xl font-bold">{{ number_format($totalMySubmitted) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Disetujui</p>
                        <p class="text-2xl font-bold">{{ number_format($totalMyApproved) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-full p-3">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Ditolak</p>
                        <p class="text-2xl font-bold">{{ number_format($totalMyRejected) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-cogs text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Diproses</p>
                        <p class="text-2xl font-bold">{{ number_format($totalMyProcessed) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-check-double text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Selesai</p>
                        <p class="text-2xl font-bold">{{ number_format($totalMyCompleted) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-blue-100 text-sm">Total Nilai Pengajuan Anda</p>
                        <p class="text-3xl font-bold">Rp {{ number_format($totalMyValue, 0, ',', '.') }}</p>
                    </div>
                    <i class="fas fa-chart-line text-4xl text-blue-200"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-green-100 text-sm">Menunggu Diproses Vendor</p>
                        <p class="text-3xl font-bold">{{ number_format($waitingForVendor) }}</p>
                        <p class="text-green-100 text-sm mt-2">Pengajuan disetujui siap diproses</p>
                    </div>
                    <i class="fas fa-hourglass-half text-4xl text-green-200"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Menu Bagian Pengadaan</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="{{ route('procurements.create') }}" class="bg-blue-100 p-4 rounded-lg text-center hover:bg-blue-200 transition">
                    <i class="fas fa-file-alt text-blue-600 text-2xl mb-2"></i>
                    <p class="text-blue-600 font-medium">Buat Pengajuan</p>
                </a>
                <a href="{{ route('procurements.index') }}" class="bg-green-100 p-4 rounded-lg text-center hover:bg-green-200 transition">
                    <i class="fas fa-list text-green-600 text-2xl mb-2"></i>
                    <p class="text-green-600 font-medium">Daftar Pengajuan</p>
                </a>
                <a href="{{ route('vendor-quotes.index') }}" class="bg-purple-100 p-4 rounded-lg text-center hover:bg-purple-200 transition">
                    <i class="fas fa-gavel text-purple-600 text-2xl mb-2"></i>
                    <p class="text-purple-600 font-medium">Penawaran Vendor</p>
                </a>
                <a href="{{ route('reports.procurements') }}" class="bg-red-100 p-4 rounded-lg text-center hover:bg-red-200 transition">
                    <i class="fas fa-chart-line text-red-600 text-2xl mb-2"></i>
                    <p class="text-red-600 font-medium">Laporan</p>
                </a>
                <a href="{{ route('reports.vendors') }}" class="bg-indigo-100 p-4 rounded-lg text-center hover:bg-indigo-200 transition">
                    <i class="fas fa-chart-bar text-indigo-600 text-2xl mb-2"></i>
                    <p class="text-indigo-600 font-medium">Laporan Vendor</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>