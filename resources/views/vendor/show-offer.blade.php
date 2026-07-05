<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penawaran - PERUMDAM</title>
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
                        <p class="text-xs text-yellow-200">Detail Penawaran</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-yellow-200">
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
            <h1 class="text-2xl font-bold">Detail Penawaran</h1>
            <a href="{{ route('vendor.history') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Riwayat
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Informasi Penawaran -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold mb-4 border-b pb-2">Informasi Penawaran</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-500 text-sm">Kode Pengajuan</p>
                        <p class="font-medium">{{ $quote->procurementRequest->kode_pengajuan }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Tanggal Penawaran</p>
                        <p>{{ $quote->created_at->format('d F Y H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Penawaran</p>
                        <p class="font-bold text-2xl text-green-600">Rp {{ number_format($quote->total_penawaran, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Status</p>
                        @if($quote->status_terpilih)
                            <span class="px-2 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> TERPILIH sebagai vendor pemenang
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-sm bg-gray-100 text-gray-600">
                                BELUM TERPILIH
                            </span>
                        @endif
                    </div>
                    @if($quote->keterangan)
                    <div>
                        <p class="text-gray-500 text-sm">Keterangan</p>
                        <p class="text-gray-600">{{ $quote->keterangan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informasi Supplier -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-bold mb-4 border-b pb-2">Informasi Perusahaan</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-500 text-sm">Nama Supplier</p>
                        <p class="font-medium">{{ $quote->supplier->nama_supplier }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Alamat</p>
                        <p>{{ $quote->supplier->alamat ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Telepon</p>
                        <p>{{ $quote->supplier->telepon ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Email</p>
                        <p>{{ $quote->supplier->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Person In Charge (PIC)</p>
                        <p>{{ $quote->supplier->pic ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Pengajuan -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Detail Pengajuan</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Barang</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Jumlah</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Satuan</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Harga Estimasi</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($quote->procurementRequest->details as $detail)
                        <tr>
                            <td class="px-4 py-2">{{ $detail->item->nama_barang }}</td>
                            <td class="px-4 py-2 text-center">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $detail->item->satuan }}</td>
                            <td class="px-4 py-2 text-right">Rp {{ number_format($detail->harga_estimasi, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right font-bold">Total Estimasi Perusahaan:</td>
                            <td class="px-4 py-2 text-right font-bold">
                                Rp {{ number_format($quote->procurementRequest->total_estimasi, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Catatan jika terpilih -->
        @if($quote->status_terpilih)
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mt-6 rounded">
            <div class="flex items-center">
                <i class="fas fa-trophy text-green-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-bold text-green-800">Selamat! Anda terpilih sebagai vendor pemenang</p>
                    <p class="text-green-700 text-sm">Silakan menunggu informasi lebih lanjut dari bagian pengadaan untuk proses kontrak.</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-gray-50 border-l-4 border-gray-400 p-4 mt-6 rounded">
            <div class="flex items-center">
                <i class="fas fa-clock text-gray-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-bold text-gray-700">Menunggu Seleksi</p>
                    <p class="text-gray-600 text-sm">Penawaran Anda sedang dalam proses seleksi oleh bagian pengadaan.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</body>
</html>