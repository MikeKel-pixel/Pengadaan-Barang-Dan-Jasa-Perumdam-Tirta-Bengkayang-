<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan - Persetujuan - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <nav class="bg-purple-600 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ route('pimpinan.dashboard') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-purple-200">Persetujuan Pengajuan</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-purple-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-blue-500 px-3 py-1 rounded text-sm">Pimpinan</span>
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
            <h1 class="text-2xl font-bold">Detail Pengajuan</h1>
            <a href="{{ route('approvals.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
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
                    <p class="text-gray-500 text-sm">Status Saat Ini</p>
                    <span class="px-2 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1"></i> Menunggu Persetujuan
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
                    <p class="text-gray-600">{{ $procurement->keterangan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Detail Barang -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="font-bold text-lg">Detail Barang yang Diajukan</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Estimasi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($procurement->details as $index => $detail)
                        <tr>
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $detail->item->nama_barang }}</td>
                            <td class="px-6 py-4">{{ $detail->item->category->nama_kategori }}</td>
                            <td class="px-6 py-4 text-center">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $detail->item->satuan }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($detail->harga_estimasi, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-right font-bold">Total Keseluruhan:</td>
                            <td class="px-6 py-4 text-right font-bold text-blue-600">
                                Rp {{ number_format($procurement->total_estimasi, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Form Persetujuan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-bold text-lg mb-4">Proses Persetujuan</h2>
            
            <!-- Form SETUJUI -->
            <div class="mb-8">
                <h3 class="font-semibold text-green-600 mb-3">
                    <i class="fas fa-check-circle mr-2"></i>Setujui Pengajuan
                </h3>
                <form action="{{ route('approvals.approve', $procurement->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="catatan_approve" class="block text-gray-600 text-sm mb-1">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan_approve" rows="2" 
                                  class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-green-500"
                                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">
                        <i class="fas fa-check mr-2"></i>Setujui Pengajuan
                    </button>
                </form>
            </div>

            <hr class="my-6">

            <!-- Form TOLAK (DIPERBAIKI) -->
            <div>
                <h3 class="font-semibold text-red-600 mb-3">
                    <i class="fas fa-times-circle mr-2"></i>Tolak Pengajuan
                </h3>
                <form action="{{ route('approvals.reject', $procurement->id) }}" method="POST" id="rejectForm">
                    @csrf
                    <div class="mb-3">
                        <label for="catatan_reject" class="block text-gray-600 text-sm mb-1">
                            Catatan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="catatan" id="catatan_reject" rows="3" 
                                  class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500"
                                  placeholder="Berikan alasan penolakan (minimal 5 karakter)..."></textarea>
                        <p class="text-gray-400 text-xs mt-1">Catatan penolakan WAJIB diisi sebagai alasan mengapa pengajuan ditolak</p>
                    </div>
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded" id="rejectBtn">
                        <i class="fas fa-times mr-2"></i>Tolak Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Validasi client-side untuk form tolak
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            var catatan = document.getElementById('catatan_reject').value.trim();
            if (catatan === '') {
                e.preventDefault();
                alert('Catatan penolakan harus diisi!');
                return false;
            }
            if (catatan.length < 5) {
                e.preventDefault();
                alert('Catatan penolakan minimal 5 karakter!');
                return false;
            }
            if (confirm('Yakin ingin MENOLAK pengajuan ini?\n\nAlasan: ' + catatan)) {
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>