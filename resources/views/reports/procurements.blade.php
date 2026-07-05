<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajuan - PERUMDAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    @php
        $userRole = Auth::user()->roles->first()->name ?? 'admin';
        $bgColor = match($userRole) {
            'admin' => 'bg-blue-600',
            'pengadaan' => 'bg-green-600',
            'pimpinan' => 'bg-purple-600',
            default => 'bg-gray-600'
        };
        $dashboardRoute = match($userRole) {
            'admin' => route('admin.dashboard'),
            'pengadaan' => route('pengadaan.dashboard'),
            'pimpinan' => route('pimpinan.dashboard'),
            default => route('dashboard')
        };
    @endphp

    <!-- Navbar -->
    <nav class="{{ $bgColor }} text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ $dashboardRoute }}" class="text-xl font-bold">PERUMDAM - Laporan Pengajuan</a>
                        <p class="text-xs opacity-75">Sistem Pengadaan Barang/Jasa</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:opacity-75">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded text-sm">{{ ucfirst($userRole) }}</span>
                    <a href="{{ $dashboardRoute }}" class="hover:underline text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Dashboard
                    </a>
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
            <h1 class="text-2xl font-bold">Laporan Pengajuan Pengadaan</h1>
            <a href="{{ $dashboardRoute }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
            </a>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('reports.procurements') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                    <a href="{{ route('reports.procurements') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-redo mr-1"></i>Reset
                    </a>
                    <a href="{{ route('reports.procurements.export', request()->all()) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-download mr-1"></i>Export CSV
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
            <div class="bg-white rounded-lg shadow p-3 text-center">
                <p class="text-gray-500 text-xs">Total</p>
                <p class="text-xl font-bold">{{ number_format($summary['total']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 text-center">
                <p class="text-gray-500 text-xs">Draft</p>
                <p class="text-xl font-bold text-gray-600">{{ number_format($summary['draft']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 text-center">
                <p class="text-gray-500 text-xs">Diajukan</p>
                <p class="text-xl font-bold text-yellow-600">{{ number_format($summary['submitted']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 text-center">
                <p class="text-gray-500 text-xs">Disetujui</p>
                <p class="text-xl font-bold text-green-600">{{ number_format($summary['approved']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 text-center">
                <p class="text-gray-500 text-xs">Ditolak</p>
                <p class="text-xl font-bold text-red-600">{{ number_format($summary['rejected']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 text-center">
                <p class="text-gray-500 text-xs">Diproses</p>
                <p class="text-xl font-bold text-blue-600">{{ number_format($summary['processed']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 text-center">
                <p class="text-gray-500 text-xs">Selesai</p>
                <p class="text-xl font-bold text-purple-600">{{ number_format($summary['completed']) }}</p>
            </div>
        </div>

        <!-- Total Value Card -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-4 mb-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-blue-100 text-sm">Total Nilai Estimasi Pengajuan</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($summary['total_value'], 0, ',', '.') }}</p>
                </div>
                <i class="fas fa-chart-line text-3xl text-blue-200"></i>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembuat</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Estimasi</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Penawaran</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($procurements as $index => $proc)
                        @php
                            // Ambil total penawaran dari vendor yang terpilih
                            $selectedQuote = $proc->vendorQuotes->where('status_terpilih', true)->first();
                            $totalPenawaran = $selectedQuote ? $selectedQuote->total_penawaran : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $procurements->firstItem() + $index }}</td>
                            <td class="px-4 py-3 font-medium">{{ $proc->kode_pengajuan }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($proc->tanggal_pengajuan)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $proc->user->name }}</td>
                            <td class="px-4 py-3 text-right">Rp {{ number_format($proc->total_estimasi, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($totalPenawaran > 0)
                                    <span class="font-bold text-green-600">Rp {{ number_format($totalPenawaran, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $colors = [
                                        'draft' => 'gray',
                                        'diajukan' => 'yellow',
                                        'disetujui' => 'green',
                                        'ditolak' => 'red',
                                        'diproses' => 'blue',
                                        'selesai' => 'purple'
                                    ];
                                    $color = $colors[$proc->status] ?? 'gray';
                                    $labels = [
                                        'draft' => 'Draft',
                                        'diajukan' => 'Diajukan',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        'diproses' => 'Diproses',
                                        'selesai' => 'Selesai'
                                    ];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ $labels[$proc->status] ?? ucfirst($proc->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('procurements.show', $proc->id) }}" class="text-blue-600 hover:text-blue-800" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-database text-4xl mb-2"></i>
                                <p>Tidak ada data</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $procurements->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</body>
</html>