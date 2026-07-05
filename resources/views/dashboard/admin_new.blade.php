<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-blue-200">Sistem Pengadaan Barang/Jasa</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-blue-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-green-500 px-3 py-1 rounded text-sm">{{ Auth::user()->roles->first()->name ?? 'Admin' }}</span>
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
        <h1 class="text-2xl font-bold mb-6">Dashboard Administrator</h1>
        
        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Pengajuan</p>
                        <p class="text-2xl font-bold">{{ number_format($totalProcurements) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Selesai</p>
                        <p class="text-2xl font-bold">{{ number_format($completedCount) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Menunggu</p>
                        <p class="text-2xl font-bold">{{ number_format($submittedCount) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total User</p>
                        <p class="text-2xl font-bold">{{ number_format($totalUsers) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-full p-3">
                        <i class="fas fa-box text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Barang</p>
                        <p class="text-2xl font-bold">{{ number_format($totalItems) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
                <div class="flex items-center">
                    <div class="bg-indigo-100 rounded-full p-3">
                        <i class="fas fa-truck text-indigo-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Supplier</p>
                        <p class="text-2xl font-bold">{{ number_format($totalSuppliers) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold mb-4">Pengajuan per Bulan ({{ date('Y') }})</h2>
                <canvas id="procurementChart" height="250"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold mb-4">Nilai Pengajuan per Bulan (Rp)</h2>
                <canvas id="valueChart" height="250"></canvas>
            </div>
        </div>

        <!-- Menu Admin -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Menu Administrator</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="{{ route('categories.index') }}" class="bg-blue-100 p-4 rounded-lg text-center hover:bg-blue-200 transition">
                    <i class="fas fa-tags text-blue-600 text-2xl mb-2"></i>
                    <p class="text-blue-600 font-medium">Kategori</p>
                </a>
                <a href="{{ route('items.index') }}" class="bg-yellow-100 p-4 rounded-lg text-center hover:bg-yellow-200 transition">
                    <i class="fas fa-box text-yellow-600 text-2xl mb-2"></i>
                    <p class="text-yellow-600 font-medium">Barang</p>
                </a>
                <a href="{{ route('suppliers.index') }}" class="bg-purple-100 p-4 rounded-lg text-center hover:bg-purple-200 transition">
                    <i class="fas fa-truck text-purple-600 text-2xl mb-2"></i>
                    <p class="text-purple-600 font-medium">Supplier</p>
                </a>
                <a href="{{ route('procurements.index') }}" class="bg-green-100 p-4 rounded-lg text-center hover:bg-green-200 transition">
                    <i class="fas fa-file-alt text-green-600 text-2xl mb-2"></i>
                    <p class="text-green-600 font-medium">Pengajuan</p>
                </a>
                <a href="{{ route('vendor-quotes.index') }}" class="bg-indigo-100 p-4 rounded-lg text-center hover:bg-indigo-200 transition">
                    <i class="fas fa-gavel text-indigo-600 text-2xl mb-2"></i>
                    <p class="text-indigo-600 font-medium">Penawaran</p>
                </a>
                <a href="{{ route('reports.procurements') }}" class="bg-red-100 p-4 rounded-lg text-center hover:bg-red-200 transition">
                    <i class="fas fa-chart-line text-red-600 text-2xl mb-2"></i>
                    <p class="text-red-600 font-medium">Laporan</p>
                </a>
            </div>
        </div>
    </div>

    <script>
        const ctx1 = document.getElementById('procurementChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: {!! json_encode($procurementCounts) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        const ctx2 = document.getElementById('valueChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: 'Nilai Pengajuan (Rp)',
                    data: {!! json_encode($procurementValues) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); } } } }
            }
        });
    </script>
</body>
</html>