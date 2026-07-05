<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengadaan - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="#" class="text-xl font-bold">PERUMDAM - Pengadaan Dashboard</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-blue-500 px-3 py-1 rounded text-sm">{{ Auth::user()->roles->first()->name ?? 'User' }}</span>
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
        <h1 class="text-2xl font-bold mb-6">Selamat Datang, {{ Auth::user()->name }}!</h1>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Menu Bagian Pengadaan</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <a href="{{ route('procurements.create') }}" class="bg-blue-100 p-4 rounded-lg text-center hover:bg-blue-200 transition">
                    <i class="fas fa-file-alt text-blue-600 text-2xl mb-2"></i>
                    <p class="text-blue-600 font-medium">Buat Pengajuan</p>
                </a>
                <a href="{{ route('procurements.index') }}" class="bg-green-100 p-4 rounded-lg text-center hover:bg-green-200 transition">
                    <i class="fas fa-list text-green-600 text-2xl mb-2"></i>
                    <p class="text-green-600 font-medium">Daftar Pengajuan</p>
                </a>
                <a href="{{ route('vendor-quotes.index') }}" class="bg-purple-100 p-4 rounded-lg text-center hover:bg-purple-200 transition">
                    <i class="fas fa-gavel text-purple-600 text-2xl mb-2"></i>
                    <p class="text-purple-600 font-medium">Penawaran Vendor</p>
                </a>
                <a href="{{ route('categories.index') }}" class="bg-yellow-100 p-4 rounded-lg text-center hover:bg-yellow-200 transition">
                    <i class="fas fa-tags text-yellow-600 text-2xl mb-2"></i>
                    <p class="text-yellow-600 font-medium">Kategori</p>
                </a>
                <a href="{{ route('items.index') }}" class="bg-red-100 p-4 rounded-lg text-center hover:bg-red-200 transition">
                    <i class="fas fa-box text-red-600 text-2xl mb-2"></i>
                    <p class="text-red-600 font-medium">Barang</p>
                </a>
                <a href="{{ route('suppliers.index') }}" class="bg-indigo-100 p-4 rounded-lg text-center hover:bg-indigo-200 transition">
                    <i class="fas fa-truck text-indigo-600 text-2xl mb-2"></i>
                    <p class="text-indigo-600 font-medium">Supplier</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>