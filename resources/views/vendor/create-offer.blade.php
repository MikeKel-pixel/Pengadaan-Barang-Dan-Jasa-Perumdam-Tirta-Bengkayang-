<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Penawaran - PERUMDAM</title>
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
                        <p class="text-xs text-yellow-200">Buat Penawaran</p>
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
            <h1 class="text-2xl font-bold">Buat Penawaran</h1>
            <a href="{{ route('vendor.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <!-- Detail Pengajuan -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Detail Pengajuan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-500 text-sm">Kode Pengajuan</p>
                    <p class="font-bold text-lg">{{ $procurement->kode_pengajuan }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tanggal Pengajuan</p>
                    <p>{{ \Carbon\Carbon::parse($procurement->tanggal_pengajuan)->format('d F Y') }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-500 text-sm">Daftar Barang yang Dibutuhkan</p>
                    <div class="mt-2 border rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Barang</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Jumlah</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Satuan</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Estimasi</th>
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
                </div>
                @if($procurement->keterangan)
                <div class="md:col-span-2">
                    <p class="text-gray-500 text-sm">Keterangan Pengajuan</p>
                    <p class="text-gray-600">{{ $procurement->keterangan }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Form Penawaran -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Form Penawaran Harga</h2>
            <form action="{{ route('vendor.store-offer', $procurement->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="total_penawaran" class="block text-gray-700 font-bold mb-2">
                        Total Harga Penawaran (Rp) *
                    </label>
                    <input type="number" name="total_penawaran" id="total_penawaran" 
                           class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                           placeholder="Masukkan total harga penawaran Anda" 
                           step="1000" required>
                    <p class="text-gray-500 text-sm mt-1">Masukkan total harga yang Anda tawarkan untuk semua item di atas</p>
                    @error('total_penawaran')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="keterangan" class="block text-gray-700 font-bold mb-2">Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" rows="3" 
                              class="w-full md:w-2/3 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                              placeholder="Tambahkan keterangan jika diperlukan (misal: termasuk ongkir, garansi, dll)"></textarea>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <p class="text-yellow-700 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        Perhatian: Penawaran yang sudah dikirim tidak dapat diubah atau dihapus. 
                        Pastikan data yang Anda masukkan sudah benar.
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('vendor.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                        Batal
                    </a>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded" onclick="return confirm('Yakin ingin mengirim penawaran ini?')">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Penawaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>