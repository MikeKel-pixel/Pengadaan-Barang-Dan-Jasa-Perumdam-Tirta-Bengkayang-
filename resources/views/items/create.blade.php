<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">PERUMDAM - Tambah Barang</a>
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

    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Tambah Barang Baru</h1>
            <a href="{{ route('items.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('items.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 font-bold mb-2">Kategori *</label>
                    <select name="category_id" id="category_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="nama_barang" class="block text-gray-700 font-bold mb-2">Nama Barang *</label>
                    <input type="text" name="nama_barang" id="nama_barang" 
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                           value="{{ old('nama_barang') }}" required>
                    @error('nama_barang')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="satuan" class="block text-gray-700 font-bold mb-2">Satuan *</label>
                        <select name="satuan" id="satuan" 
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            <option value="">Pilih Satuan</option>
                            <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                            <option value="unit" {{ old('satuan') == 'unit' ? 'selected' : '' }}>Unit</option>
                            <option value="meter" {{ old('satuan') == 'meter' ? 'selected' : '' }}>Meter</option>
                            <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>Kg</option>
                            <option value="liter" {{ old('satuan') == 'liter' ? 'selected' : '' }}>Liter</option>
                            <option value="buah" {{ old('satuan') == 'buah' ? 'selected' : '' }}>Buah</option>
                        </select>
                        @error('satuan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="harga_estimasi_default" class="block text-gray-700 font-bold mb-2">Harga Estimasi Default (Rp)</label>
                        <input type="number" name="harga_estimasi_default" id="harga_estimasi_default" 
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                               value="{{ old('harga_estimasi_default') }}" step="1000">
                        @error('harga_estimasi_default')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="spesifikasi" class="block text-gray-700 font-bold mb-2">Spesifikasi</label>
                    <textarea name="spesifikasi" id="spesifikasi" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">{{ old('spesifikasi') }}</textarea>
                    @error('spesifikasi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>