<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penawaran - PERUMDAM</title>
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
                        <p class="text-xs text-yellow-200">Riwayat Penawaran</p>
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
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Riwayat Penawaran</h1>
            <a href="{{ route('vendor.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if($supplier)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-bold">
                        <i class="fas fa-list text-blue-600 mr-2"></i>
                        Semua Penawaran Anda
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Menampilkan semua penawaran yang pernah Anda kirimkan</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Pengajuan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nilai Penawaran</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($quotes as $index => $quote)
                            <tr class="hover:bg-gray-50 {{ $quote->status_terpilih ? 'bg-green-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $quotes->firstItem() + $index }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    {{ $quote->procurementRequest->kode_pengajuan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $quote->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-bold">
                                    Rp {{ number_format($quote->total_penawaran, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($quote->status_terpilih)
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> TERPILIH
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                            BELUM TERPILIH
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('vendor.show-offer', $quote->id) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Belum ada riwayat penawaran</p>
                                    <a href="{{ route('vendor.dashboard') }}" class="mt-2 inline-block text-blue-600">Lihat pengajuan terbuka</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4">
                    {{ $quotes->links() }}
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