<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="{{ route('pengadaan.dashboard') }}" class="text-xl font-bold">PERUMDAM - Detail Pengajuan</a>
                </div>
                <div class="flex items-center space-x-4">
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
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Detail Pengajuan</h1>
            <a href="{{ route('procurements.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Info Pengajuan -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-500 text-sm">Kode Pengajuan</p>
                    <p class="font-bold text-lg">{{ $procurement->kode_pengajuan }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Status</p>
                    @php
                        $statusColors = [
                            'draft' => 'gray',
                            'diajukan' => 'yellow',
                            'disetujui' => 'green',
                            'ditolak' => 'red',
                            'diproses' => 'blue',
                            'selesai' => 'purple'
                        ];
                        $color = $statusColors[$procurement->status] ?? 'gray';
                    @endphp
                    <span class="px-2 py-1 rounded-full text-sm bg-{{ $color }}-100 text-{{ $color }}-800">
                        {{ ucfirst($procurement->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tanggal Pengajuan</p>
                    <p>{{ \Carbon\Carbon::parse($procurement->tanggal_pengajuan)->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Dibuat Oleh</p>
                    <p>{{ $procurement->user->name }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-500 text-sm">Keterangan</p>
                    <p>{{ $procurement->keterangan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Detail Barang -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="font-bold text-lg">Detail Barang</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Estimasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($procurement->details as $index => $detail)
                    <tr>
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">{{ $detail->item->nama_barang }}</td>
                        <td class="px-6 py-4">{{ $detail->item->category->nama_kategori }}</td>
                        <td class="px-6 py-4">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">{{ $detail->item->satuan }}</td>
                        <td class="px-6 py-4">Rp {{ number_format($detail->harga_estimasi, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-right font-bold">Total Keseluruhan:</td>
                        <td class="px-6 py-4 font-bold text-blue-600">Rp {{ number_format($procurement->total_estimasi, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Riwayat Approval (jika ada) -->
        @if($procurement->approvals->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="font-bold text-lg">Riwayat Persetujuan</h2>
            </div>
            <div class="p-6">
                @foreach($procurement->approvals as $approval)
                <div class="mb-4 p-4 border rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-bold">{{ $approval->user->name }}</span>
                            <span class="text-gray-500 text-sm ml-2">{{ \Carbon\Carbon::parse($approval->tanggal_approval)->format('d/m/Y H:i') }}</span>
                        </div>
                        <span class="px-2 py-1 rounded-full text-sm bg-{{ $approval->status == 'disetujui' ? 'green' : 'red' }}-100 text-{{ $approval->status == 'disetujui' ? 'green' : 'red' }}-800">
                            {{ ucfirst($approval->status) }}
                        </span>
                    </div>
                    @if($approval->catatan)
                    <p class="text-gray-600 mt-2">Catatan: {{ $approval->catatan }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Penawaran Vendor (jika ada) -->
        @if($procurement->vendorQuotes->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="font-bold text-lg">Penawaran Vendor</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Penawaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($procurement->vendorQuotes as $index => $quote)
                    <tr class="{{ $quote->status_terpilih ? 'bg-green-50' : '' }}">
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">{{ $quote->supplier->nama_supplier }}</td>
                        <td class="px-6 py-4">Rp {{ number_format($quote->total_penawaran, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if($quote->status_terpilih)
                                <span class="px-2 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                    <i class="fas fa-check"></i> Terpilih
                                </span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

<!-- ==================== TOMBOL SELESAIKAN (HANYA UNTUK STATUS DIPROSES) ==================== -->
@if($procurement->status == 'diproses' && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('pengadaan')))
<div class="mt-6 flex justify-end">
    <form action="{{ route('procurements.complete', $procurement->id) }}" method="POST" onsubmit="return confirmComplete()">
        @csrf
        <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg shadow-md transition">
            <i class="fas fa-check-double mr-2"></i>Selesaikan Pengadaan
        </button>
    </form>
</div>
@endif
    </div>
</body>
</html>