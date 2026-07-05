<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Supplier - Perumdam</title>
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
                        <a href="{{ $dashboardRoute }}" class="text-xl font-bold">PERUMDAM - Detail Supplier</a>
                        <p class="text-xs opacity-75">Verifikasi Vendor</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:opacity-75">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded text-sm">{{ ucfirst($userRole) }}</span>
                    <a href="{{ route('suppliers.index') }}" class="hover:underline text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
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
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Detail Supplier</h1>
            <div class="space-x-2">
                <a href="{{ route('suppliers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
                </a>
                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-edit mr-2"></i>Edit Supplier
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Supplier -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">Informasi Perusahaan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Nama Perusahaan</p>
                            <p class="font-medium text-lg">{{ $supplier->nama_supplier }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Status</p>
                            @if($supplier->status == 'pending')
                                <span class="px-2 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
                                </span>
                            @elseif($supplier->status == 'verified')
                                <span class="px-2 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                                </span>
                            @elseif($supplier->status == 'rejected')
                                <span class="px-2 py-1 rounded-full text-sm bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Ditolak
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Person In Charge (PIC)</p>
                            <p>{{ $supplier->pic ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Email</p>
                            <p>{{ $supplier->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Telepon</p>
                            <p>{{ $supplier->telepon ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">NPWP</p>
                            <p>{{ $supplier->npwp ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Bidang Usaha</p>
                            <p>{{ $supplier->bidang_usaha ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-500 text-sm">Alamat</p>
                            <p>{{ $supplier->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pendaftaran -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">Informasi Pendaftaran</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Tanggal Mendaftar</p>
                            <p>{{ $supplier->registered_at ? $supplier->registered_at->format('d F Y H:i') : $supplier->created_at->format('d F Y H:i') }}</p>
                        </div>
                        @if($supplier->verified_at)
                        <div>
                            <p class="text-gray-500 text-sm">Tanggal Diverifikasi</p>
                            <p>{{ $supplier->verified_at->format('d F Y H:i') }}</p>
                        </div>
                        @endif
                        @if($supplier->verifier)
                        <div>
                            <p class="text-gray-500 text-sm">Diverifikasi oleh</p>
                            <p>{{ $supplier->verifier->name }}</p>
                        </div>
                        @endif
                        @if($supplier->rejection_reason)
                        <div class="md:col-span-2">
                            <p class="text-gray-500 text-sm">Alasan Penolakan</p>
                            <div class="bg-red-50 p-3 rounded">
                                <p class="text-red-700">{{ $supplier->rejection_reason }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel Aksi Verifikasi (Hanya untuk Admin) -->
            @if(Auth::user()->hasRole('admin'))
            <div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">Aksi Verifikasi</h2>
                    
                    @if($supplier->status == 'pending')
                        <div class="bg-yellow-50 rounded-lg p-4 mb-4 text-center">
                            <i class="fas fa-clock text-yellow-500 text-3xl mb-2"></i>
                            <p class="font-bold text-yellow-700">Menunggu Verifikasi</p>
                            <p class="text-sm text-yellow-600">Vendor ini mendaftar melalui form pendaftaran user</p>
                        </div>

                        <div class="space-y-3">
                            <!-- Tombol Setujui -->
                            <form action="{{ route('suppliers.verify', $supplier->id) }}" method="POST" onsubmit="return confirmVerification()">
                                @csrf
                                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                    <i class="fas fa-check mr-2"></i>Setujui & Verifikasi
                                </button>
                            </form>

                            <!-- Tombol Tolak (buka modal) -->
                            <button onclick="openRejectModal()" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                <i class="fas fa-times mr-2"></i>Tolak Pendaftaran
                            </button>
                        </div>
                    @elseif($supplier->status == 'verified')
                        <div class="bg-green-50 rounded-lg p-4 mb-4 text-center">
                            <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                            <p class="font-bold text-green-700">Terverifikasi</p>
                            <p class="text-sm text-green-600">Vendor ini sudah diverifikasi</p>
                            @if($supplier->verified_at)
                                <p class="text-xs text-green-500 mt-2">{{ $supplier->verified_at->format('d F Y H:i') }}</p>
                            @endif
                        </div>
                    @elseif($supplier->status == 'rejected')
                        <div class="bg-red-50 rounded-lg p-4 mb-4 text-center">
                            <i class="fas fa-times-circle text-red-500 text-3xl mb-2"></i>
                            <p class="font-bold text-red-700">Ditolak</p>
                            <p class="text-sm text-red-600">Pendaftaran vendor ini ditolak</p>
                        </div>
                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data vendor ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                                <i class="fas fa-trash mr-2"></i>Hapus Data
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Info User Terkait -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">Info User Terkait</h2>
                    @php
                        $relatedUser = \App\Models\User::where('email', $supplier->email)->first();
                    @endphp
                    @if($relatedUser)
                        <div class="space-y-2">
                            <div>
                                <p class="text-gray-500 text-sm">Nama User</p>
                                <p class="font-medium">{{ $relatedUser->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Role Saat Ini</p>
                                <p>
                                    @if($relatedUser->hasRole('vendor'))
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Vendor</span>
                                    @elseif($relatedUser->hasRole('user'))
                                        <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">User Biasa</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">{{ $relatedUser->roles->first()->name ?? 'User' }}</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Status Akun</p>
                                <p>{{ $relatedUser->email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Tidak ditemukan user terkait dengan email ini</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Tolak -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-red-600">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Tolak Pendaftaran
                </h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="{{ route('suppliers.reject', $supplier->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-gray-700 font-bold mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500"
                              placeholder="Berikan alasan mengapa pendaftaran ini ditolak..."></textarea>
                    <p class="text-gray-500 text-xs mt-1">Alasan akan dikirimkan ke user</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-times mr-2"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
        function confirmVerification() {
            return confirm('Yakin ingin menyetujui dan memverifikasi vendor ini? User akan diubah menjadi vendor.');
        }
        // Tutup modal jika klik di luar area modal
        window.onclick = function(event) {
            const modal = document.getElementById('rejectModal');
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</body>
</html>