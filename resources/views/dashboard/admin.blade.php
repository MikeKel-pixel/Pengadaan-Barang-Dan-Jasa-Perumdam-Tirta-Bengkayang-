<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {!! $chart->script() !!}
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="#" class="text-xl font-bold">PERUMDAM - Admin Dashboard</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.edit') }}" class="hover:text-gray-200">
                        <i class="fas fa-user-circle"></i> Profil
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
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

    <div class="container mx-auto px-6 py-8">
        <h1 class="text-2xl font-bold mb-6">Dashboard Administrator</h1>
        
        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-blue-500 rounded-full p-3">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Users</p>
                        <p class="text-2xl font-bold">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-green-500 rounded-full p-3">
                        <i class="fas fa-box text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Barang</p>
                        <p class="text-2xl font-bold">{{ $totalItems }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-500 rounded-full p-3">
                        <i class="fas fa-truck text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Supplier</p>
                        <p class="text-2xl font-bold">{{ $totalSuppliers }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-purple-500 rounded-full p-3">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Pengajuan</p>
                        <p class="text-2xl font-bold">{{ $totalProcurements }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Pengajuan Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-lg mb-4">Status Pengajuan</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Draft</span>
                        <span class="font-bold">{{ $totalDraft }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diajukan</span>
                        <span class="font-bold text-yellow-600">{{ $totalSubmitted }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Disetujui</span>
                        <span class="font-bold text-green-600">{{ $totalApproved }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ditolak</span>
                        <span class="font-bold text-red-600">{{ $totalRejected }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diproses</span>
                        <span class="font-bold text-blue-600">{{ $totalProcessed }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Selesai</span>
                        <span class="font-bold text-purple-600">{{ $totalCompleted }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between">
                        <span class="font-bold">Total Nilai Pengadaan Selesai:</span>
                        <span class="font-bold text-blue-600">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
                <h3 class="font-bold text-lg mb-4">Statistik Kategori</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Total Kategori</p>
                        <p class="text-2xl font-bold">{{ $totalCategories }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Role</p>
                        <p class="text-2xl font-bold">{{ $totalRoles }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="font-bold text-lg mb-4">Trend Pengajuan</h3>
            <div style="height: 400px;">
                {!! $chart->container() !!}
            </div>
        </div>

        <!-- Menu Admin -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Menu Administrator</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('users.index') }}" class="bg-blue-100 p-4 rounded-lg text-center hover:bg-blue-200 transition">
                    <i class="fas fa-users text-blue-600 text-2xl mb-2"></i>
                    <p class="text-blue-600 font-medium">Manajemen User</p>
                </a>
                <a href="{{ route('categories.index') }}" class="bg-green-100 p-4 rounded-lg text-center hover:bg-green-200 transition">
                    <i class="fas fa-tags text-green-600 text-2xl mb-2"></i>
                    <p class="text-green-600 font-medium">Kategori</p>
                </a>
                <a href="{{ route('items.index') }}" class="bg-yellow-100 p-4 rounded-lg text-center hover:bg-yellow-200 transition">
                    <i class="fas fa-box text-yellow-600 text-2xl mb-2"></i>
                    <p class="text-yellow-600 font-medium">Barang</p>
                </a>
                <a href="{{ route('suppliers.index') }}" class="bg-purple-100 p-4 rounded-lg text-center hover:bg-purple-200 transition">
                    <i class="fas fa-truck text-purple-600 text-2xl mb-2"></i>
                    <p class="text-purple-600 font-medium">Supplier</p>
                </a>
                <a href="{{ route('reports.index') }}" class="bg-red-100 p-4 rounded-lg text-center hover:bg-red-200 transition">
                    <i class="fas fa-chart-line text-red-600 text-2xl mb-2"></i>
                    <p class="text-red-600 font-medium">Laporan</p>
                </a>
                <a href="{{ route('procurements.index') }}" class="bg-indigo-100 p-4 rounded-lg text-center hover:bg-indigo-200 transition">
                    <i class="fas fa-file-alt text-indigo-600 text-2xl mb-2"></i>
                    <p class="text-indigo-600 font-medium">Semua Pengajuan</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>