<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perkembangan Pengadaan - PERUMDAM</title>
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
                        <p class="text-xs text-blue-200">Perkembangan Pengadaan</p>
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
            <h1 class="text-2xl font-bold">Perkembangan Pengadaan</h1>
            <a href="{{ route('user.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @forelse($ongoingProcurements as $proc)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg">{{ $proc->kode_pengajuan }}</h3>
                        @php
                            $statusConfig = [
                                'diajukan' => ['label' => 'Menunggu Persetujuan Pimpinan', 'step' => 1, 'color' => 'yellow'],
                                'disetujui' => ['label' => 'Menunggu Penawaran Vendor', 'step' => 2, 'color' => 'blue'],
                                'diproses' => ['label' => 'Sedang Diproses', 'step' => 3, 'color' => 'purple'],
                            ];
                            $config = $statusConfig[$proc->status] ?? ['label' => ucfirst($proc->status), 'step' => 0, 'color' => 'gray'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800">
                            {{ $config['label'] }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-xs text-gray-500">Pengajuan</span>
                            <span class="text-xs text-gray-500">Disetujui</span>
                            <span class="text-xs text-gray-500">Diproses</span>
                            <span class="text-xs text-gray-500">Selesai</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($config['step'] / 4) * 100 }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Tanggal Pengajuan</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($proc->tanggal_pengajuan)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Estimasi</p>
                            <p class="font-bold text-blue-600">Rp {{ number_format($proc->total_estimasi, 0, ',', '.') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-500 text-sm">Jumlah Item</p>
                            <p>{{ $proc->details->count() }} barang</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('user.track-detail', $proc->id) }}" class="text-blue-600 hover:text-blue-800">
                            Lihat Detail Perkembangan <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                <i class="fas fa-check-circle text-5xl mb-3 text-green-500"></i>
                <p>Tidak ada pengadaan yang sedang berjalan</p>
            </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $ongoingProcurements->links() }}
        </div>
    </div>
</body>
</html>