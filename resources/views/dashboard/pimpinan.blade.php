<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pimpinan - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-purple-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="#" class="text-xl font-bold">PERUMDAM - Pimpinan Dashboard</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-blue-500 px-3 py-1 rounded text-sm">{{ Auth::user()->roles->first()->name ?? 'User' }}</span>
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
        <h1 class="text-2xl font-bold mb-6">Selamat Datang, {{ Auth::user()->name }}!</h1>
        
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
</body>
</html>