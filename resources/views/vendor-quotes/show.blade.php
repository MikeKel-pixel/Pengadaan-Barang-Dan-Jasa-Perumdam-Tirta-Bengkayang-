<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Penawaran Vendor - {{ $procurement->kode_pengajuan }} - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="{{ route('pengadaan.dashboard') }}" class="text-xl font-bold">PERUMDAM - Penawaran Vendor</a>
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
            <h1 class="text-2xl font-bold">Penawaran Vendor - {{ $procurement->kode_pengajuan }}</h1>
            <a href="{{ route('vendor-quotes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-500 text-sm">Kode Pengajuan</p>
                    <p class="font-bold">{{ $procurement->kode_pengajuan }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Status</p>
                    @php
                        $statusColors = [
                            'disetujui' => 'green',
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
                    <p class="text-gray-500 text-sm">Total Estimasi</p>
                    <p class="font-bold text-blue-600">Rp {{ number_format($procurement->total_estimasi, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Detail Barang Ringkas -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="font-bold text-lg">Detail Barang</h2>
            </div>
            <div class="p-6">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Barang</th>
                            <th class="text-left py-2">Jumlah</th>
                            <th class="text-left py-2">Satuan</th>
                            <th class="text-right py-2">Harga Estimasi</th>
                            <th class="text-right py-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($procurement->details as $detail)
                        <tr class="border-b">
                            <td class="py-2">{{ $detail->item->nama_barang }}</td>
                            <td class="py-2">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                            <td class="py-2">{{ $detail->item->satuan }}</td>
                            <td class="py-2 text-right">Rp {{ number_format($detail->harga_estimasi, 0, ',', '.') }}</td>
                            <td class="py-2 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="py-2 text-right font-bold">Total:</td>
                            <td class="py-2 text-right font-bold text-blue-600">Rp {{ number_format($procurement->total_estimasi, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Daftar Penawaran Vendor -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h2 class="font-bold text-lg">Daftar Penawaran Vendor</h2>
                @if($procurement->status == 'disetujui')
                <button onclick="toggleForm()" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                    <i class="fas fa-plus mr-1"></i>Tambah Penawaran
                </button>
                @endif
            </div>
            
            <div class="p-6">
                @if($procurement->vendorQuotes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Penawaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($procurement->vendorQuotes->sortBy('total_penawaran') as $index => $quote)
                                <tr class="{{ $quote->status_terpilih ? 'bg-green-50' : '' }}">
                                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-medium">{{ $quote->supplier->nama_supplier }}</td>
                                    <td class="px-6 py-4 text-right font-bold">Rp {{ number_format($quote->total_penawaran, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">{{ $quote->keterangan ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($quote->status_terpilih)
                                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle"></i> Terpilih
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($procurement->status == 'disetujui' && !$quote->status_terpilih)
                                            <form action="{{ route('vendor-quotes.select', [$procurement->id, $quote->id]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-900 mr-2" onclick="return confirm('Pilih vendor ini sebagai vendor terbaik?')">
                                                    <i class="fas fa-check-circle"></i> Pilih
                                                </button>
                                            </form>
                                            <form action="{{ route('vendor-quotes.destroy', [$procurement->id, $quote->id]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Hapus penawaran ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @elseif($quote->status_terpilih && $procurement->status == 'diproses')
                                            <span class="text-green-600">Vendor Terpilih</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Belum ada penawaran vendor. Silakan tambah penawaran.</p>
                @endif
            </div>
        </div>

        <!-- Form Tambah Penawaran (Hidden by default) -->
        <div id="addQuoteForm" class="bg-white rounded-lg shadow p-6 mb-6 hidden">
            <h2 class="font-bold text-lg mb-4">Tambah Penawaran Vendor</h2>
            <form action="{{ route('vendor-quotes.store', $procurement->id) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="supplier_id" class="block text-gray-700 font-bold mb-2">Supplier *</label>
                        <select name="supplier_id" id="supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="total_penawaran" class="block text-gray-700 font-bold mb-2">Total Penawaran (Rp) *</label>
                        <input type="number" name="total_penawaran" id="total_penawaran" 
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                               placeholder="Masukkan total penawaran" step="1000" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="keterangan" class="block text-gray-700 font-bold mb-2">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="2" 
                              class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                              placeholder="Tambahkan keterangan jika diperlukan"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="toggleForm()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-save mr-2"></i>Simpan Penawaran
                    </button>
                </div>
            </form>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-end space-x-3">
            @if($procurement->status == 'diproses')
                <form action="{{ route('vendor-quotes.complete', $procurement->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded" onclick="return confirm('Yakin pengadaan ini sudah selesai?')">
                        <i class="fas fa-check-double mr-2"></i>Selesaikan Pengadaan
                    </button>
                </form>
            @endif
            
            @if($procurement->status == 'disetujui' && $procurement->vendorQuotes->count() > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <p class="text-yellow-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Silakan pilih vendor terbaik dari daftar penawaran di atas.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('addQuoteForm');
            form.classList.toggle('hidden');
        }
    </script>
</body>
</html>