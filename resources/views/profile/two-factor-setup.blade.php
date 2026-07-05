<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Two Factor Authentication - PERUMDAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-8 max-w-3xl">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-shield-alt text-blue-600 text-3xl mr-3"></i>
                <h1 class="text-2xl font-bold">Two Factor Authentication (2FA)</h1>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(Auth::user()->hasTwoFactorEnabled())
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <p class="text-green-700 font-bold">✅ Two Factor Authentication AKTIF</p>
                    <p class="text-sm text-green-600">Akun Anda dilindungi dengan lapisan keamanan tambahan.</p>
                    <form action="{{ route('two-factor.disable') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="return confirm('Yakin ingin menonaktifkan 2FA?')">
                            <i class="fas fa-times mr-2"></i>Nonaktifkan 2FA
                        </button>
                    </form>
                </div>
            @else
                <p class="text-gray-600 mb-4">
                    Two Factor Authentication menambahkan lapisan keamanan ekstra pada akun Anda. 
                    Anda akan diminta memasukkan kode 6 digit dari aplikasi authenticator setelah login.
                </p>

                <!-- QR Code -->
                <div class="bg-gray-50 p-4 rounded-lg mb-4 text-center">
                    <h2 class="font-bold mb-2">1. Scan QR Code dengan Aplikasi Authenticator</h2>
                    <p class="text-sm text-gray-500 mb-4">
                        Gunakan aplikasi seperti <strong>Google Authenticator</strong>, <strong>Microsoft Authenticator</strong>, atau <strong>Authy</strong>
                    </p>
                    <div class="flex justify-center">
                        {!! QrCode::size(200)->generate($qrCodeUrl) !!}
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Atau masukkan kode manual: <strong>{{ Auth::user()->two_factor_secret }}</strong></p>
                </div>

                <!-- Recovery Codes -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <h2 class="font-bold text-yellow-800 mb-2">2. Simpan Kode Pemulihan</h2>
                    <p class="text-sm text-yellow-700 mb-2">
                        Simpan kode pemulihan di tempat yang aman. Gunakan jika kehilangan akses ke aplikasi authenticator.
                    </p>
                    <div class="grid grid-cols-2 gap-2 font-mono text-sm bg-white p-3 rounded border">
                        @foreach($recoveryCodes as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>
                    <p class="text-xs text-yellow-600 mt-2">
                        <i class="fas fa-info-circle mr-1"></i> Setiap kode hanya bisa digunakan satu kali.
                    </p>
                </div>

                <!-- Verifikasi -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h2 class="font-bold text-blue-800 mb-2">3. Verifikasi Setup</h2>
                    <p class="text-sm text-blue-700 mb-3">
                        Masukkan kode 6 digit dari aplikasi authenticator untuk mengaktifkan 2FA.
                    </p>
                    <form action="{{ route('two-factor.confirm') }}" method="POST" class="flex items-center gap-4">
                        @csrf
                        <input type="text" name="code" placeholder="Masukkan kode 6 digit" 
                               class="px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 w-48 text-center text-2xl font-bold"
                               maxlength="6" pattern="[0-9]{6}" required>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                            <i class="fas fa-check mr-2"></i>Verifikasi & Aktifkan
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-6 flex justify-between">
                <a href="{{ route('profile.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Profil
                </a>
                @if(Auth::user()->hasTwoFactorEnabled())
                    <form action="{{ route('two-factor.regenerate') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-sm">
                            <i class="fas fa-sync mr-1"></i>Generate Ulang Kode Pemulihan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>