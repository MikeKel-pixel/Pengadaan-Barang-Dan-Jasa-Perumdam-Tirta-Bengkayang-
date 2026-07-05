<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Vendor - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="#" class="text-xl font-bold">PERUMDAM - Laporan Vendor</a>
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
            <h1 class="text-2xl font-bold">Laporan Vendor/Supplier</h1>
            <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('reports.vendors') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm mb-1">Cari Supplier</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama supplier..." class="w-full px-3 py-2 border border-gray-300 rounded">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-search mr-1"></i>Cari
                    </button>
                    <a href="{{ route('reports.vendors') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-redo mr-1"></i>Reset
                    </a>
                    <a href="{{ route('reports.vendors.export', request()->all()) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-download mr-1"></i>Export CSV
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <p class="text-gray-500 text-sm">Total Penawaran</p>
                <p class="text-2xl font-bold">{{ number_format($totalQuotes) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <p class="text-gray-500 text-sm">Penawaran Terpilih</p>
                <p class="text-2xl font-bold">{{ number_format($totalSelected) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <p class="text-gray-500 text-sm">Total Nilai Penawaran</p>
                <p class="text-2xl font-bold">Rp {{ number_format($totalQuoteValue, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Top Vendors Chart -->
        @if($topVendors->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-bold mb-4">Top 5 Vendor Terbaik</h2>
            <canvas id="topVendorsChart" height="200"></canvas>
        </div>
        @endif

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Penawaran</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terpilih</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Nilai</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suppliers as $index => $supplier)
                    @php
                        $totalQuotesVendor = $supplier->vendorQuotes->count();
                        $totalSelectedVendor = $supplier->vendorQuotes->where('status_terpilih', true)->count();
                        $totalValueVendor = $supplier->vendorQuotes->sum('total_penawaran');
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $suppliers->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium">{{ $supplier->nama_supplier }}</td>
                        <td class="px-4 py-3">
                            <div class="text-sm">{{ $supplier->telepon ?: '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $supplier->email ?: '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">{{ number_format($totalQuotesVendor) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs {{ $totalSelectedVendor > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ number_format($totalSelectedVendor) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($totalValueVendor, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="showDetail({{ json_encode($supplier) }}, {{ json_encode($supplier->vendorQuotes) }})" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-database text-4xl mb-2"></i>
                            <p>Tidak ada data supplier</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $suppliers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold" id="modalTitle">Detail Supplier</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modalContent"></div>
            <div class="mt-4 text-right">
                <button onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function showDetail(supplier, quotes) {
            document.getElementById('modalTitle').innerText = 'Detail Supplier: ' + supplier.nama_supplier;
            
            let content = `
                <div class="mb-4 p-3 bg-gray-50 rounded">
                    <p><strong>Alamat:</strong> ${supplier.alamat || '-'}</p>
                    <p><strong>Telepon:</strong> ${supplier.telepon || '-'}</p>
                    <p><strong>Email:</strong> ${supplier.email || '-'}</p>
                    <p><strong>PIC:</strong> ${supplier.pic || '-'}</p>
                </div>
                <h4 class="font-bold mb-2">Riwayat Penawaran:</h4>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-2 py-1 text-left">Kode Pengajuan</th>
                            <th class="px-2 py-1 text-right">Penawaran</th>
                            <th class="px-2 py-1 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            if (quotes.length > 0) {
                quotes.forEach(quote => {
                    content += `
                        <tr class="border-b">
                            <td class="px-2 py-1">${quote.procurement_request?.kode_pengajuan || '-'}</td>
                            <td class="px-2 py-1 text-right">Rp ${new Intl.NumberFormat('id-ID').format(quote.total_penawaran)}</td>
                            <td class="px-2 py-1 text-center">
                                ${quote.status_terpilih ? 
                                    '<span class="text-green-600">✓ Terpilih</span>' : 
                                    '<span class="text-gray-400">-</span>'}
                            </td>
                        </tr>
                    `;
                });
            } else {
                content += '<tr><td colspan="3" class="px-2 py-4 text-center text-gray-500">Belum ada penawaran</td></tr>';
            }
            
            content += `</tbody></table>`;
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('detailModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
        
        @if($topVendors->count() > 0)
        const ctx = document.getElementById('topVendorsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($topVendors->pluck('supplier.nama_supplier')->toArray()) !!},
                datasets: [
                    {
                        label: 'Total Penawaran',
                        data: {!! json_encode($topVendors->pluck('total_quotes')->toArray()) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Terpilih',
                        data: {!! json_encode($topVendors->pluck('selected_count')->toArray()) !!},
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
        @endif
    </script>
</body>
</html>