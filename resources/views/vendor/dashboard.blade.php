<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Vendor - PERUMDAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <nav class="bg-yellow-600 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ route('vendor.dashboard') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-yellow-200">Dashboard Vendor</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-yellow-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-blue-500 px-3 py-1 rounded text-sm">Vendor</span>
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
        <h1 class="text-2xl font-bold mb-6">Dashboard Vendor</h1>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if($supplier)
            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-gavel text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Total Penawaran</p>
                            <p class="text-2xl font-bold">{{ number_format($totalQuotes) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-trophy text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Penawaran Menang</p>
                            <p class="text-2xl font-bold">{{ number_format($totalSelected) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-full p-3">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Total Nilai Penawaran</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($totalQuoteValue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengajuan Terbuka -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-bold">
                        <i class="fas fa-folder-open text-green-600 mr-2"></i>
                        Pengajuan Terbuka (Siap Ditawari)
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Pengajuan yang sudah disetujui pimpinan dan siap menerima penawaran</p>
                </div>
                <div class="divide-y">
                    @forelse($openProcurements as $proc)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <span class="font-bold text-blue-600">{{ $proc->kode_pengajuan }}</span>
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                        {{ ucfirst($proc->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    Tanggal: {{ \Carbon\Carbon::parse($proc->tanggal_pengajuan)->format('d/m/Y') }} |
                                    Dibuat oleh: {{ $proc->user->name }}
                                </p>
                                <p class="text-sm mt-1">
                                    <span class="text-gray-600">Estimasi Perusahaan:</span>
                                    <span class="font-bold text-blue-600">Rp {{ number_format($proc->total_estimasi, 0, ',', '.') }}</span>
                                </p>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500">Daftar Barang:</p>
                                    <ul class="text-xs text-gray-600 list-disc list-inside">
                                        @foreach($proc->details->take(3) as $detail)
                                        <li>{{ $detail->item->nama_barang }} - {{ number_format($detail->jumlah) }} {{ $detail->item->satuan }}</li>
                                        @endforeach
                                        @if($proc->details->count() > 3)
                                        <li>... dan {{ $proc->details->count() - 3 }} item lainnya</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('vendor.create-offer', $proc->id) }}" 
                                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center">
                                    <i class="fas fa-gavel mr-2"></i>
                                    Buat Penawaran
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-2 text-green-500"></i>
                        <p>Tidak ada pengajuan yang terbuka saat ini</p>
                        <p class="text-sm mt-1">Silakan cek kembali nanti</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Riwayat Penawaran Terbaru -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                    <h2 class="text-lg font-bold">
                        <i class="fas fa-history text-blue-600 mr-2"></i>
                        Riwayat Penawaran Terbaru
                    </h2>
                    <a href="{{ route('vendor.history') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="divide-y">
                    @forelse($recentQuotes as $quote)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">{{ $quote->procurementRequest->kode_pengajuan }}</p>
                                <p class="text-sm text-gray-500">
                                    Penawaran: Rp {{ number_format($quote->total_penawaran, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $quote->created_at->format('d F Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                @if($quote->status_terpilih)
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Terpilih
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                        Menunggu / Tidak Terpilih
                                    </span>
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('vendor.show-offer', $quote->id) }}" class="text-blue-500 hover:text-blue-700 text-sm">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-file-alt text-4xl mb-2"></i>
                        <p>Belum ada riwayat penawaran</p>
                        <p class="text-sm mt-1">Silakan buat penawaran pertama Anda</p>
                    </div>
                    @endforelse
                </div>
            </div>
        @else
            <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded">
                <p class="text-yellow-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Akun Anda belum terhubung dengan data supplier. Silakan hubungi administrator untuk menghubungkan akun ini dengan data supplier Anda.
                </p>
            </div>
        @endif
    </div>
</body>
</html>