<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pimpinan - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-purple-600 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ route('pimpinan.dashboard') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-purple-200">Sistem Pengadaan Barang/Jasa</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-purple-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-blue-500 px-3 py-1 rounded text-sm">{{ Auth::user()->roles->first()->name ?? 'Pimpinan' }}</span>
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
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Menunggu Persetujuan</p>
                        <p class="text-2xl font-bold">{{ number_format($waitingApproval) }}</p>
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
                        <p class="text-2xl font-bold">{{ number_format($totalApproved) }}</p>
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
                        <p class="text-2xl font-bold">{{ number_format($totalRejected) }}</p>
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
                        <p class="text-2xl font-bold">{{ number_format($totalCompleted) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow p-6 text-white mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-blue-100 text-sm">Total Nilai Pengadaan Selesai</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalProcurementValue, 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-chart-line text-4xl text-blue-200"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-bold mb-4">Statistik Persetujuan per Bulan ({{ date('Y') }})</h2>
            <canvas id="approvalChart" height="200"></canvas>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Menu Pimpinan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('approvals.index') }}" class="bg-blue-100 p-4 rounded-lg text-center hover:bg-blue-200 transition">
                    <i class="fas fa-check-circle text-blue-600 text-2xl mb-2"></i>
                    <p class="text-blue-600 font-medium">Persetujuan Pengajuan</p>
                    <p class="text-sm text-gray-500">Proses pengajuan yang menunggu persetujuan</p>
                </a>
                <a href="{{ route('approvals.history') }}" class="bg-green-100 p-4 rounded-lg text-center hover:bg-green-200 transition">
                    <i class="fas fa-history text-green-600 text-2xl mb-2"></i>
                    <p class="text-green-600 font-medium">Riwayat Persetujuan</p>
                    <p class="text-sm text-gray-500">Lihat riwayat pengajuan yang sudah diproses</p>
                </a>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('approvalChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    { label: 'Disetujui', data: {!! json_encode($approvedCounts) !!}, backgroundColor: 'rgba(34, 197, 94, 0.5)', borderColor: 'rgba(34, 197, 94, 1)', borderWidth: 1 },
                    { label: 'Ditolak', data: {!! json_encode($rejectedCounts) !!}, backgroundColor: 'rgba(239, 68, 68, 0.5)', borderColor: 'rgba(239, 68, 68, 1)', borderWidth: 1 }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    </script>
</body>
</html>