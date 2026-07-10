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

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo" class="h-10 w-auto">
                    <a href="{{ route('profile.index') }}" class="text-xl font-bold">PERUMDAM - 2FA Setup</a>
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

    <div class="container mx-auto px-6 py-8 max-w-3xl">
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

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-shield-alt text-blue-600 text-3xl mr-3"></i>
                <h1 class="text-2xl font-bold">Two Factor Authentication (2FA)</h1>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-blue-700">
                    Two Factor Authentication menambahkan lapisan keamanan ekstra pada akun Anda.
                    Setelah login, Anda akan diminta memasukkan kode 6 digit yang dikirim ke email Anda.
                </p>
            </div>

            @if($user->hasTwoFactorEnabled())
                <!-- Jika 2FA sudah aktif -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
                    <h2 class="text-xl font-bold text-green-700">✅ Two Factor Authentication AKTIF</h2>
                    <p class="text-green-600 mt-2">
                        Akun Anda sudah dilindungi dengan 2FA. Setiap kali login, Anda akan menerima kode verifikasi via email.
                    </p>
                    <form action="{{ route('two-factor.disable') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded" onclick="return confirm('Yakin ingin menonaktifkan 2FA?')">
                            <i class="fas fa-times mr-2"></i>Nonaktifkan 2FA
                        </button>
                    </form>
                </div>
            @else
                <!-- Jika 2FA belum aktif -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-5xl mb-4"></i>
                    <h2 class="text-xl font-bold text-yellow-700">❌ Two Factor Authentication BELUM AKTIF</h2>
                    <p class="text-yellow-600 mt-2">
                        Aktifkan 2FA untuk melindungi akun Anda dari akses yang tidak sah.
                    </p>
                    <form action="{{ route('two-factor.enable') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                            <i class="fas fa-shield-alt mr-2"></i>Aktifkan 2FA
                        </button>
                    </form>
                </div>
            @endif

            <!-- Informasi Cara Kerja -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="font-bold text-gray-700 mb-2">📌 Cara Kerja 2FA:</h3>
                <ol class="list-decimal list-inside text-gray-600 text-sm space-y-1">
                    <li>Setelah login, Anda akan diminta memasukkan kode 6 digit</li>
                    <li>Kode akan dikirim ke email Anda</li>
                    <li>Masukkan kode untuk menyelesaikan proses login</li>
                    <li>Kode berlaku selama 10 menit</li>
                </ol>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('profile.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Profil
                </a>
            </div>
        </div>
    </div>
</body>
</html>