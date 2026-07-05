<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Perkembangan - PERUMDAM</title>
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
                        <p class="text-xs text-blue-200">Detail Perkembangan</p>
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
            <h1 class="text-2xl font-bold">Detail Perkembangan Pengadaan</h1>
            <a href="{{ route('user.track-progress') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">{{ $procurement->kode_pengajuan }}</h2>
                <p class="text-blue-100">{{ $procurement->created_at->format('d F Y') }}</p>
            </div>

            <div class="p-6">
                <!-- Timeline Status -->
                <div class="mb-8">
                    <h3 class="font-bold text-lg mb-4">Timeline Pengadaan</h3>
                    <div class="relative">
                        <div class="flex justify-between">
                            @php
                                $statuses = [
                                    'draft' => ['name' => 'Draft', 'icon' => 'fa-file-alt', 'color' => 'gray'],
                                    'diajukan' => ['name' => 'Diajukan', 'icon' => 'fa-paper-plane', 'color' => 'yellow'],
                                    'disetujui' => ['name' => 'Disetujui', 'icon' => 'fa-check-circle', 'color' => 'green'],
                                    'diproses' => ['name' => 'Diproses', 'icon' => 'fa-cogs', 'color' => 'blue'],
                                    'selesai' => ['name' => 'Selesai', 'icon' => 'fa-flag-checkered', 'color' => 'purple'],
                                ];
                                $currentStatus = $procurement->status;
                                $statusOrder = ['draft', 'diajukan', 'disetujui', 'diproses', 'selesai'];
                                $currentIndex = array_search($currentStatus, $statusOrder);
                            @endphp

                            @foreach($statusOrder as $index => $status)
                                @php $statusInfo = $statuses[$status]; @endphp
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $index <= $currentIndex ? 'bg-' . $statusInfo['color'] . '-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                                        <i class="fas {{ $statusInfo['icon'] }}"></i>
                                    </div>
                                    <span class="text-xs mt-2 {{ $index <= $currentIndex ? 'text-' . $statusInfo['color'] . '-600 font-bold' : 'text-gray-400' }}">
                                        {{ $statusInfo['name'] }}
                                    </span>
                                </div>
                                @if(!$loop->last)
                                    <div class="flex-1 h-0.5 {{ $index < $currentIndex ? 'bg-green-500' : 'bg-gray-200' }} mt-5"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Detail Barang -->
                <h3 class="font-bold text-lg mb-3">Daftar Barang</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Barang</th>
                                <th class="px-4 py-2 text-center">Jumlah</th>
                                <th class="px-4 py-2 text-left">Satuan</th>
                                <th class="px-4 py-2 text-right">Estimasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($procurement->details as $detail)
                            <tr>
                                <td class="px-4 py-2">{{ $detail->item->nama_barang }}</td>
                                <td class="px-4 py-2 text-center">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">{{ $detail->item->satuan }}</td>
                                <td class="px-4 py-2 text-right">Rp {{ number_format($detail->harga_estimasi, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-bold">Total Estimasi:</td>
                                <td class="px-4 py-2 text-right font-bold text-blue-600">
                                    Rp {{ number_format($procurement->total_estimasi, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Riwayat Persetujuan -->
                @if($procurement->approvals->count() > 0)
                <h3 class="font-bold text-lg mb-3">Riwayat Persetujuan</h3>
                <div class="space-y-3 mb-6">
                    @foreach($procurement->approvals as $approval)
                    <div class="border rounded-lg p-3 {{ $approval->status == 'disetujui' ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-medium">{{ $approval->user->name }}</span>
                                <span class="text-sm text-gray-500 ml-2">{{ $approval->tanggal_approval->format('d F Y H:i') }}</span>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs {{ $approval->status == 'disetujui' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                {{ $approval->status == 'disetujui' ? 'DISETUJUI' : 'DITOLAK' }}
                            </span>
                        </div>
                        @if($approval->catatan)
                        <p class="text-sm text-gray-600 mt-2">Catatan: {{ $approval->catatan }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Vendor Winner -->
                @if($procurement->vendorQuotes->where('status_terpilih', true)->first())
                @php $selectedVendor = $procurement->vendorQuotes->where('status_terpilih', true)->first(); @endphp
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-bold text-green-700 mb-2">
                        <i class="fas fa-trophy mr-2"></i>Vendor Terpilih
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama Vendor</p>
                            <p class="font-medium">{{ $selectedVendor->supplier->nama_supplier }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Nilai Kontrak</p>
                            <p class="font-medium text-green-700">Rp {{ number_format($selectedVendor->total_penawaran, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>