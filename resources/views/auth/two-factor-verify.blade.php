<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2FA - PERUMDAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <!-- Logo -->
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Verifikasi Keamanan</h1>
            <p class="text-gray-500 text-sm">Masukkan kode 6 digit yang telah dikirim ke email Anda</p>
            <p class="text-xs text-gray-400 mt-1">Email: <strong>{{ Auth::user()->email ?? 'user@perumdam.com' }}</strong></p>
        </div>

        <!-- Alert Messages -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 text-sm">
                <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
            </div>
        @endif

        <!-- Form Verifikasi -->
        <form action="{{ route('two-factor.verify.submit') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Kode Verifikasi (6 digit)</label>
                <input type="text" name="code" placeholder="Contoh: 123456"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-2xl font-bold tracking-widest"
                       maxlength="6" pattern="[0-9]{6}" required autofocus>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition">
                <i class="fas fa-shield-alt mr-2"></i>Verifikasi
            </button>
        </form>

        <!-- Kirim Ulang & Ganti Akun -->
        <div class="mt-4 text-center space-y-2">
            <p class="text-sm text-gray-500">
                Tidak menerima kode?
                <form action="{{ route('two-factor.resend') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium">
                        Kirim Ulang
                    </button>
                </form>
            </p>

            <p class="text-sm text-gray-400">
                <i class="fas fa-clock mr-1"></i> Kode berlaku selama 10 menit
            </p>

            <!-- ==================== TOMBOL KEMBALI KE LOGIN ==================== -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 rounded-lg transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Kembali ke Login & Ganti Akun
                    </button>
                </form>
                <p class="text-xs text-gray-400 mt-2">Keluar dan login dengan akun lain</p>
            </div>
        </div>
    </div>
</body>
</html>