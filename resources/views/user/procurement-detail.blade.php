<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengadaan - PERUMDAM</title>
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
                        <p class="text-xs text-blue-200">Detail Pengadaan</p>
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
            <h1 class="text-2xl font-bold">Detail Pengadaan</h1>
            <a href="{{ route('user.procurements') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">{{ $procurement->kode_pengajuan }}</h2>
                <p class="text-blue-100">Pengadaan selesai pada: {{ $procurement->updated_at->format('d F Y') }}</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-gray-500 text-sm">Tanggal Pengajuan</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($procurement->tanggal_pengajuan)->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Diajukan oleh</p>
                        <p class="font-medium">{{ $procurement->user->name }}</p>
                    </div>
                </div>

                <h3 class="font-bold text-lg mb-3">Daftar Barang</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Barang</th>
                                <th class="px-4 py-2 text-center">Jumlah</th>
                                <th class="px-4 py-2 text-left">Satuan</th>
                                <th class="px-4 py-2 text-right">Harga</th>
                                <th class="px-4 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($procurement->details as $detail)
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
                                <td colspan="4" class="px-4 py-2 text-right font-bold">Total:</td>
                                <td class="px-4 py-2 text-right font-bold text-blue-600">
                                    Rp {{ number_format($procurement->total_estimasi, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($procurement->vendorQuotes->where('status_terpilih', true)->first())
                @php $selectedVendor = $procurement->vendorQuotes->where('status_terpilih', true)->first(); @endphp
                <div class="bg-green-50 border rounded-lg p-4 mt-4">
                    <h3 class="font-bold text-green-700 mb-2">
                        <i class="fas fa-trophy mr-2"></i>Vendor Pemenang
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Nama Vendor</p>
                            <p class="font-medium">{{ $selectedVendor->supplier->nama_supplier }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Nilai Kontrak</p>
                            <p class="font-medium text-green-700">Rp {{ number_format($selectedVendor->total_penawaran, 0, ',', '.') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-600 text-sm">Alamat Vendor</p>
                            <p class="text-sm">{{ $selectedVendor->supplier->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($procurement->keterangan)
                <div class="bg-gray-50 rounded p-4 mt-4">
                    <p class="text-gray-600 text-sm">Keterangan</p>
                    <p>{{ $procurement->keterangan }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>