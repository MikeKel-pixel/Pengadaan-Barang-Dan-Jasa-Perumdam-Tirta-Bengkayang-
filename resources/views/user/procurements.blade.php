<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Pengadaan - PERUMDAM</title>
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
                        <p class="text-xs text-blue-200">Informasi Pengadaan</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-blue-200">
                        <i class="fas fa-user-circle text-xl"></i>
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

    <div class="container mx-auto px-6 pt-24 pb-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Informasi Pengadaan Selesai</h1>
            <a href="{{ route('user.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($procurements as $proc)
            <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
                <div class="bg-green-500 px-4 py-2">
                    <p class="text-white font-bold">{{ $proc->kode_pengajuan }}</p>
                </div>
                <div class="p-4">
                    <p class="text-gray-500 text-sm">
                        <i class="fas fa-calendar mr-1"></i> {{ $proc->created_at->format('d F Y') }}
                    </p>
                    <p class="text-gray-700 mt-2">
                        <span class="font-semibold">Total Pengadaan:</span><br>
                        Rp {{ number_format($proc->total_estimasi, 0, ',', '.') }}
                    </p>
                    <p class="text-gray-700 mt-2">
                        <span class="font-semibold">Jumlah Item:</span> {{ $proc->details->count() }} barang
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('user.procurement-detail', $proc->id) }}" class="text-blue-600 hover:text-blue-800">
                            Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 bg-white rounded-lg shadow p-8 text-center text-gray-500">
                <i class="fas fa-inbox text-5xl mb-3"></i>
                <p>Belum ada pengadaan yang selesai</p>
            </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $procurements->links() }}
        </div>
    </div>
</body>
</html>