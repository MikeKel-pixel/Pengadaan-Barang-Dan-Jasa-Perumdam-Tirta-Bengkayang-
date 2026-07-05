<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - PERUMDAM Tirta Bengkayang</title>
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

    <!-- Navbar -->
    <nav class="{{ $bgColor }} text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="{{ $dashboardRoute }}" class="text-xl font-bold">PERUMDAM - Edit Profil</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-blue-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded text-sm">{{ ucfirst($userRole) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 pt-24 pb-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Profil</h1>
            <a href="{{ route('profile.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Profil
            </a>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-gray-700 font-bold mb-2">Nama Lengkap *</label>
                        <input type="text" name="name" id="name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                               value="{{ old('name', $user->name) }}" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-gray-700 font-bold mb-2">Email *</label>
                        <input type="email" name="email" id="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                               value="{{ old('email', $user->email) }}" required>
                        <p class="text-xs text-gray-500 mt-1">Email akan digunakan untuk login</p>
                    </div>
                </div>

                <!-- Field tambahan untuk vendor -->
                @if($userRole == 'vendor' && $supplier)
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-lg font-bold mb-4 text-blue-700">Data Supplier</h3>
                    <div class="bg-blue-50 p-4 rounded-lg mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            Data supplier akan digunakan untuk proses penawaran. Pastikan data sudah benar.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="telepon" class="block text-gray-700 font-bold mb-2">Telepon</label>
                            <input type="text" name="telepon" id="telepon" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                   value="{{ old('telepon', $supplier->telepon) }}">
                        </div>
                        <div>
                            <label for="pic" class="block text-gray-700 font-bold mb-2">Person In Charge (PIC)</label>
                            <input type="text" name="pic" id="pic" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                   value="{{ old('pic', $supplier->pic) }}">
                        </div>
                        <div class="md:col-span-2">
                            <label for="alamat" class="block text-gray-700 font-bold mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" id="alamat" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                      placeholder="Jl. ...">{{ old('alamat', $supplier->alamat) }}</textarea>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('profile.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6 rounded">
            <p class="text-yellow-700 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Catatan:</strong> Perubahan email akan mempengaruhi data login Anda. Pastikan email yang Anda masukkan aktif dan dapat diakses.
            </p>
        </div>
    </div>
</body>
</html>