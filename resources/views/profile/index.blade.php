<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - PERUMDAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    @php
        $userRole = Auth::user()->roles->first()->name ?? 'user';
        $bgColor = match($userRole) {
            'admin' => 'bg-blue-600',
            'pengadaan' => 'bg-green-600',
            'pimpinan' => 'bg-purple-600',
            'vendor' => 'bg-yellow-600',
            default => 'bg-gray-600'
        };
        $dashboardRoute = match($userRole) {
            'admin' => route('admin.dashboard'),
            'pengadaan' => route('pengadaan.dashboard'),
            'pimpinan' => route('pimpinan.dashboard'),
            'vendor' => route('vendor.dashboard'),
            default => route('user.dashboard')
        };
    @endphp

    <nav class="{{ $bgColor }} text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ $dashboardRoute }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs opacity-75">Profil Saya</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:opacity-75">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded text-sm">{{ ucfirst($userRole) }}</span>
                    <a href="{{ $dashboardRoute }}" class="hover:underline text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali ke Dashboard
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="mb-4">
                        <img src="{{ Auth::user()->photo_url }}" alt="Foto Profil" 
                             class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-blue-500">
                    </div>
                    <h3 class="font-bold text-lg">{{ Auth::user()->name }}</h3>
                    <p class="text-gray-500 text-sm">{{ ucfirst($userRole) }}</p>
                    <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                    
                    <div class="mt-4 pt-4 border-t">
                        <form action="{{ route('profile.upload-photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                            @csrf
                            <label class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded cursor-pointer inline-block">
                                <i class="fas fa-camera mr-2"></i>Ganti Foto
                                <input type="file" name="photo" accept="image/*" class="hidden" onchange="document.getElementById('photoForm').submit()">
                            </label>
                        </form>
                        
                        @if(Auth::user()->photo)
                        <form action="{{ route('profile.delete-photo') }}" method="POST" class="inline-block mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Yakin ingin menghapus foto profil?')">
                                <i class="fas fa-trash mr-1"></i>Hapus Foto
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Informasi Profil</h2>
                        <a href="{{ route('profile.edit') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            <i class="fas fa-edit mr-2"></i>Edit Profil
                        </a>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="border-b pb-3">
                            <p class="text-gray-500 text-sm">Nama Lengkap</p>
                            <p class="font-medium">{{ Auth::user()->name }}</p>
                        </div>
                        <div class="border-b pb-3">
                            <p class="text-gray-500 text-sm">Email</p>
                            <p class="font-medium">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="border-b pb-3">
                            <p class="text-gray-500 text-sm">Role / Jabatan</p>
                            <p class="font-medium">{{ ucfirst($userRole) }}</p>
                        </div>
                        <div class="border-b pb-3">
                            <p class="text-gray-500 text-sm">Terdaftar Sejak</p>
                            <p class="font-medium">{{ Auth::user()->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- ==================== CARD KEAMANAN AKUN (DENGAN 2FA) ==================== -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">
                            <i class="fas fa-shield-alt text-blue-600 mr-2"></i>Keamanan Akun
                        </h2>
                        <div class="space-x-2 flex flex-wrap gap-2">
                            <a href="{{ route('profile.change-password') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm">
                                <i class="fas fa-key mr-2"></i>Ganti Password
                            </a>
                            <a href="{{ route('two-factor.setup') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                <i class="fas fa-shield-alt mr-2"></i>
                                @if(Auth::user()->hasTwoFactorEnabled())
                                    Kelola 2FA
                                @else
                                    Aktifkan 2FA
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if(Auth::user()->hasTwoFactorEnabled())
                            <span class="text-green-600 font-bold">
                                <i class="fas fa-check-circle mr-1"></i> Two Factor Authentication AKTIF
                            </span>
                            <span class="text-gray-500 text-xs">Akun Anda dilindungi dengan lapisan keamanan tambahan.</span>
                        @else
                            <span class="text-gray-400">
                                <i class="fas fa-circle mr-1" style="font-size: 8px;"></i> Two Factor Authentication BELUM AKTIF
                            </span>
                            <span class="text-gray-500 text-xs">Aktifkan untuk meningkatkan keamanan akun.</span>
                        @endif
                    </div>
                    @if(Auth::user()->hasTwoFactorEnabled())
                        <div class="mt-3 text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i> Anda akan diminta kode verifikasi saat login.
                        </div>
                    @endif
                </div>

                @if($userRole == 'vendor' && isset($supplier) && $supplier)
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-xl font-bold mb-4">Data Supplier</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Nama Supplier</p>
                            <p class="font-medium">{{ $supplier->nama_supplier }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Telepon</p>
                            <p class="font-medium">{{ $supplier->telepon ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-500 text-sm">Alamat</p>
                            <p class="font-medium">{{ $supplier->alamat ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Person In Charge (PIC)</p>
                            <p class="font-medium">{{ $supplier->pic ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Status</p>
                            <p class="font-medium text-green-600">Terdaftar</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>