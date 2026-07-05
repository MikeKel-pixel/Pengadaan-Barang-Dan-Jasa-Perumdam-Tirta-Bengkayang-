<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Pengajuan - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    @php
        $userRole = Auth::user()->roles->first()->name ?? 'pengadaan';
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
                        <a href="{{ $dashboardRoute }}" class="text-xl font-bold">PERUMDAM - Daftar Pengajuan</a>
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

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Daftar Pengajuan Pengadaan</h1>
            <a href="{{ route('procurements.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                <i class="fas fa-plus mr-2"></i>Buat Pengajuan Baru
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembuat</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Estimasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($procurements as $index => $proc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $procurements->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $proc->kode_pengajuan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($proc->tanggal_pengajuan)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $proc->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">Rp {{ number_format($proc->total_estimasi, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $statusColors = [
                                    'draft' => 'gray',
                                    'diajukan' => 'yellow',
                                    'disetujui' => 'green',
                                    'ditolak' => 'red',
                                    'diproses' => 'blue',
                                    'selesai' => 'purple'
                                ];
                                $color = $statusColors[$proc->status] ?? 'gray';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs bg-{{ $color }}-100 text-{{ $color }}-800">
                                {{ ucfirst($proc->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <!-- Tombol Detail -->
                            <a href="{{ route('procurements.show', $proc->id) }}" class="text-blue-600 hover:text-blue-900 mx-1" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <!-- Tombol Edit (hanya untuk status draft) -->
                            @if($proc->status == 'draft')
                                <a href="{{ route('procurements.edit', $proc->id) }}" class="text-green-600 hover:text-green-900 mx-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            
                            <!-- Tombol Ajukan (hanya untuk status draft) -->
                            @if($proc->status == 'draft')
                                <form action="{{ route('procurements.submit', $proc->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900 mx-1" title="Ajukan" onclick="return confirm('Yakin ingin mengajukan pengajuan ini?')">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Tombol Hapus (hanya untuk status draft) - PERHATIKAN INI -->
                            @if($proc->status == 'draft')
                                <form action="{{ route('procurements.destroy', $proc->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengajuan {{ $proc->kode_pengajuan }}? Data yang dihapus tidak dapat dikembalikan.')">
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
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Belum ada data pengajuan</p>
                            <a href="{{ route('procurements.create') }}" class="mt-2 inline-block text-blue-600">Buat pengajuan pertama</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $procurements->links() }}
            </div>
        </div>
    </div>

    <script>
        // Tambahan script untuk memastikan confirm berfungsi
        document.querySelectorAll('form[action*="/procurements/"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (this.method.toUpperCase() === 'POST') {
                    // Cek apakah ini form delete (ada method DELETE)
                    if (this.querySelector('input[name="_method"]') && 
                        this.querySelector('input[name="_method"]').value === 'DELETE') {
                        if (!confirm('Yakin ingin menghapus pengajuan ini? Data yang dihapus tidak dapat dikembalikan.')) {
                            e.preventDefault();
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>