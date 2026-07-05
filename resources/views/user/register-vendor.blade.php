<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar sebagai Vendor - PERUMDAM Tirta Bengkayang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-input:focus {
            ring: 2px solid #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ route('user.dashboard') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-blue-200">Pendaftaran Vendor</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-blue-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-green-500 px-3 py-1 rounded text-sm">{{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 pt-24 pb-8">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumb -->
            <div class="flex items-center text-sm text-gray-500 mb-4">
                <a href="{{ route('user.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <span class="text-gray-700 font-medium">Pendaftaran Vendor</span>
            </div>

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Pendaftaran Vendor</h1>
                    <p class="text-gray-500 text-sm mt-1">Isi formulir berikut untuk mendaftarkan perusahaan Anda sebagai vendor resmi</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>

            <!-- Alert Messages -->
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 px-4 py-3 rounded mb-4 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 px-4 py-3 rounded mb-4 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            <!-- Pesan untuk pendaftaran yang ditolak -->
            @if(isset($isRejected) && $isRejected)
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
                    <div class="flex">
                        <i class="fas fa-times-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-bold text-red-700">Pendaftaran Anda Sebelumnya Ditolak</p>
                            <p class="text-red-600 text-sm mt-1">Silakan perbarui data Anda sesuai alasan di bawah ini dan daftar ulang.</p>
                            @if($oldData && $oldData->rejection_reason)
                                <div class="mt-3 bg-red-100 p-3 rounded">
                                    <p class="text-red-700 text-sm font-semibold">Alasan Penolakan:</p>
                                    <p class="text-red-600 text-sm">{{ $oldData->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Info Penting -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded shadow-sm">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                    <div>
                        <p class="font-bold text-blue-700">Informasi Penting</p>
                        <ul class="text-blue-600 text-sm mt-1 space-y-1">
                            <li><i class="fas fa-check-circle text-blue-500 text-xs mr-2"></i> Data yang Anda masukkan akan diverifikasi oleh administrator</li>
                            <li><i class="fas fa-check-circle text-blue-500 text-xs mr-2"></i> Proses verifikasi membutuhkan waktu 1x24 jam</li>
                            <li><i class="fas fa-check-circle text-blue-500 text-xs mr-2"></i> Setelah diverifikasi, Anda akan dapat mengikuti tender pengadaan</li>
                            <li><i class="fas fa-check-circle text-blue-500 text-xs mr-2"></i> Email {{ Auth::user()->email }} akan digunakan sebagai email resmi vendor</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Pendaftaran -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-building mr-2"></i>Formulir Pendaftaran Vendor
                    </h2>
                    <p class="text-green-100 text-sm mt-1">Isi semua data dengan lengkap dan benar</p>
                </div>

                <form action="{{ route('user.register-vendor.store') }}" method="POST" class="p-6">
                    @csrf

                    <!-- Data Perusahaan -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">
                            <i class="fas fa-building text-green-600 mr-2"></i>Data Perusahaan
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="nama_supplier" class="block text-gray-700 font-medium mb-2">
                                    Nama Perusahaan/Vendor <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_supplier" id="nama_supplier" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('nama_supplier') border-red-500 @enderror"
                                       value="{{ old('nama_supplier', isset($oldData) ? $oldData->nama_supplier : '') }}" 
                                       placeholder="Contoh: CV. Maju Bersama" required>
                                @error('nama_supplier')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="alamat" class="block text-gray-700 font-medium mb-2">
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea name="alamat" id="alamat" rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('alamat') border-red-500 @enderror"
                                          placeholder="Jl. Contoh No. 123, RT/RW, Kelurahan, Kecamatan, Kabupaten/Kota" required>{{ old('alamat', isset($oldData) ? $oldData->alamat : '') }}</textarea>
                                @error('alamat')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Data Kontak -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">
                            <i class="fas fa-phone text-green-600 mr-2"></i>Data Kontak
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="telepon" class="block text-gray-700 font-medium mb-2">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="telepon" id="telepon" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('telepon') border-red-500 @enderror"
                                       value="{{ old('telepon', isset($oldData) ? $oldData->telepon : '') }}" 
                                       placeholder="081234567890" required>
                                @error('telepon')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email_display" class="block text-gray-700 font-medium mb-2">
                                    Email (Terdaftar)
                                </label>
                                <input type="email" id="email_display" 
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-100 text-gray-600"
                                       value="{{ Auth::user()->email }}" readonly disabled>
                                <p class="text-gray-400 text-xs mt-1">Email sesuai akun Anda dan tidak dapat diubah</p>
                            </div>
                        </div>
                    </div>

                    <!-- Data PIC -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">
                            <i class="fas fa-user-tie text-green-600 mr-2"></i>Data Penanggung Jawab (PIC)
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="pic" class="block text-gray-700 font-medium mb-2">
                                    Nama Person In Charge <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="pic" id="pic" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('pic') border-red-500 @enderror"
                                       value="{{ old('pic', isset($oldData) ? $oldData->pic : '') }}" 
                                       placeholder="Nama lengkap penanggung jawab" required>
                                @error('pic')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Data Tambahan (Opsional) -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">
                            <i class="fas fa-file-alt text-green-600 mr-2"></i>Data Tambahan (Opsional)
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="npwp" class="block text-gray-700 font-medium mb-2">NPWP</label>
                                <input type="text" name="npwp" id="npwp" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                       value="{{ old('npwp', isset($oldData) ? $oldData->npwp : '') }}" 
                                       placeholder="00.000.000.0-000.000">
                                @error('npwp')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="bidang_usaha" class="block text-gray-700 font-medium mb-2">Bidang Usaha</label>
                                <input type="text" name="bidang_usaha" id="bidang_usaha" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                       value="{{ old('bidang_usaha', isset($oldData) ? $oldData->bidang_usaha : '') }}" 
                                       placeholder="Contoh: Kontraktor, Supply Barang, Jasa Konsultansi">
                                @error('bidang_usaha')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Perjanjian -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-start">
                            <input type="checkbox" name="agreement" id="agreement" required
                                   class="mt-1 mr-3 w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <label for="agreement" class="text-gray-600 text-sm">
                                Saya menyatakan bahwa data yang saya masukkan adalah benar dan dapat dipertanggungjawabkan. 
                                Saya setuju untuk mengikuti peraturan dan ketentuan yang berlaku di PERUMDAM Tirta Bengkayang.
                                <span class="text-red-500">*</span>
                            </label>
                        </div>
                        @error('agreement')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('user.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition flex items-center">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition flex items-center shadow-md">
                            <i class="fas fa-paper-plane mr-2"></i>Daftar sebagai Vendor
                        </button>
                    </div>
                </form>
            </div>

            <!-- Informasi Tambahan -->
            <div class="mt-6 bg-white rounded-lg shadow p-4">
                <div class="flex items-center text-gray-500 text-sm">
                    <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                    <span>Data Anda aman dan tidak akan disalahgunakan. Pendaftaran ini gratis.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4 mt-8">
        <div class="container mx-auto px-6 text-center text-sm">
            <p>&copy; {{ date('Y') }} PERUMDAM Tirta Bengkayang - Sistem Pengadaan Barang/Jasa</p>
        </div>
    </footer>

    <script>
        // Validasi form sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            var agreement = document.getElementById('agreement');
            if (!agreement.checked) {
                e.preventDefault();
                alert('Harap centang pernyataan bahwa data yang dimasukkan benar.');
                return false;
            }
            return true;
        });
    </script>
</body>
</html>