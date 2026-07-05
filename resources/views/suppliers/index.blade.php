<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Supplier - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    @php
        $userRole = Auth::user()->roles->first()->name ?? 'admin';
        $bgColor = match($userRole) {
            'admin' => 'bg-blue-600',
            'pengadaan' => 'bg-green-600',
            default => 'bg-gray-600'
        };
        $dashboardRoute = match($userRole) {
            'admin' => route('admin.dashboard'),
            default => route('pengadaan.dashboard')
        };
    @endphp

    <nav class="{{ $bgColor }} text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ $dashboardRoute }}" class="text-xl font-bold">PERUMDAM - Manajemen Supplier</a>
                        <p class="text-xs opacity-75">Kelola Data Supplier / Vendor</p>
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

    <div class="container mx-auto px-6 py-8">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">Manajemen Supplier / Vendor</h1>
                <p class="text-gray-500 text-sm mt-1">Data vendor yang terdaftar melalui sistem atau verifikasi dari user</p>
            </div>
            <!-- ==================== TOMBOL TAMBAH SUPPLIER DIHAPUS ==================== -->
            <!-- <a href="{{ route('suppliers.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                <i class="fas fa-plus mr-2"></i>Tambah Supplier
            </a> -->
        </div>

        <!-- Statistik Ringkas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Menunggu Verifikasi</p>
                        <p class="text-2xl font-bold">{{ $suppliers->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Terverifikasi</p>
                        <p class="text-2xl font-bold">{{ $suppliers->where('status', 'verified')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-full p-3">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Ditolak</p>
                        <p class="text-2xl font-bold">{{ $suppliers->where('status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Supplier -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Daftar</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </td>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suppliers as $index => $supplier)
                        <tr class="hover:bg-gray-50 
                            @if($supplier->status == 'pending') bg-yellow-50 @endif
                            @if($supplier->status == 'rejected') bg-red-50 @endif">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $suppliers->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">
                                {{ $supplier->nama_supplier }}
                                @if($supplier->status == 'pending')
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-yellow-200 text-yellow-800">
                                        Baru
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">{{ $supplier->telepon ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $supplier->email ?? '-' }}</div>
                                <div class="text-xs text-gray-400">PIC: {{ $supplier->pic ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($supplier->status == 'pending')
                                    <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
                                    </span>
                                @elseif($supplier->status == 'verified')
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                                    </span>
                                @elseif($supplier->status == 'rejected')
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                        {{ ucfirst($supplier->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $supplier->registered_at ? $supplier->registered_at->format('d/m/Y') : $supplier->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <!-- Tombol Detail (untuk verifikasi) -->
                                <a href="{{ route('suppliers.show', $supplier->id) }}" class="text-blue-600 hover:text-blue-900 mx-1" title="Detail / Verifikasi">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Tombol Edit -->
                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-green-600 hover:text-green-900 mx-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Tombol Hapus (kecuali untuk pending yang belum diproses) -->
                                @if($supplier->status == 'pending' || $supplier->status == 'rejected')
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus supplier {{ $supplier->nama_supplier }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 mx-1" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-truck text-4xl mb-2"></i>
                                <p>Belum ada data supplier / vendor</p>
                                <p class="text-sm mt-1">Vendor akan muncul setelah user mendaftar melalui form pendaftaran vendor</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $suppliers->links() }}
            </div>
        </div>

        <!-- Informasi -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                <div>
                    <p class="font-semibold text-blue-700">Informasi</p>
                    <p class="text-blue-600 text-sm">
                        Data supplier/vendor hanya dapat ditambahkan melalui pendaftaran vendor oleh user biasa. 
                        Admin hanya dapat melakukan verifikasi, edit, dan hapus data.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>