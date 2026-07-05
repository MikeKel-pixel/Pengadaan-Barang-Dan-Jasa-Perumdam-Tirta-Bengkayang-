<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Pengajuan - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .item-row {
            background: #f9fafb;
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <a href="{{ route('pengadaan.dashboard') }}" class="text-xl font-bold">PERUMDAM - Edit Pengajuan</a>
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
            <h1 class="text-2xl font-bold">Edit Pengajuan</h1>
            <a href="{{ route('procurements.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <p class="text-yellow-700">
                <i class="fas fa-info-circle mr-2"></i>
                Pengajuan dengan status <strong>Draft</strong> dapat diedit. Setelah diajukan, tidak dapat diedit lagi.
            </p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('procurements.update', $procurement->id) }}" method="POST" id="procurementForm">
                @csrf
                @method('PUT')
                
                <div class="mb-6">
                    <label for="tanggal_pengajuan" class="block text-gray-700 font-bold mb-2">Tanggal Pengajuan *</label>
                    <input type="date" name="tanggal_pengajuan" id="tanggal_pengajuan" 
                           class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                           value="{{ old('tanggal_pengajuan', $procurement->tanggal_pengajuan->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-gray-700 font-bold">Detail Barang *</label>
                        <button type="button" id="addItemBtn" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-plus mr-1"></i>Tambah Barang
                        </button>
                    </div>
                    
                    <div id="itemsContainer">
                        @foreach($procurement->details as $index => $detail)
                        <div class="item-row" data-index="{{ $index }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-gray-600 text-sm mb-1">Pilih Barang *</label>
                                    <select name="item_id[]" class="item-select w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" 
                                                    data-satuan="{{ $item->satuan }}"
                                                    data-harga="{{ $item->harga_estimasi_default ?? 0 }}"
                                                    {{ $detail->item_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->category->nama_kategori }} - {{ $item->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-sm mb-1">Jumlah *</label>
                                    <input type="number" name="jumlah[]" class="jumlah-input w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" 
                                           placeholder="Jumlah" min="1" value="{{ $detail->jumlah }}" required>
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-sm mb-1">Harga Estimasi (Rp) *</label>
                                    <input type="number" name="harga_estimasi[]" class="harga-input w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" 
                                           placeholder="Harga" step="1000" value="{{ $detail->harga_estimasi }}" required>
                                </div>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-sm text-gray-500">Subtotal: Rp <span class="subtotal-display">{{ number_format($detail->subtotal, 0, ',', '') }}</span></span>
                                <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 ml-3 text-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <label for="keterangan" class="block text-gray-700 font-bold mb-2">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">{{ old('keterangan', $procurement->keterangan) }}</textarea>
                </div>

                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-lg">Total Estimasi Keseluruhan:</span>
                        <span class="font-bold text-2xl text-blue-600">Rp <span id="grandTotal">{{ number_format($procurement->total_estimasi, 0, ',', '') }}</span></span>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                        <i class="fas fa-save mr-2"></i>Update Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk update subtotal
        function updateSubtotal(row) {
            const jumlah = row.querySelector('.jumlah-input').value || 0;
            const harga = row.querySelector('.harga-input').value || 0;
            const subtotal = jumlah * harga;
            row.querySelector('.subtotal-display').innerText = subtotal;
            updateGrandTotal();
        }

        // Fungsi untuk update grand total
        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-display').forEach(display => {
                total += parseInt(display.innerText) || 0;
            });
            document.getElementById('grandTotal').innerText = total.toLocaleString('id-ID');
        }

        // Fungsi untuk set harga default saat memilih barang
        function setHargaDefault(select, row) {
            const selectedOption = select.options[select.selectedIndex];
            const hargaDefault = selectedOption.getAttribute('data-harga');
            const satuan = selectedOption.getAttribute('data-satuan');
            
            if (hargaDefault && hargaDefault > 0 && !row.querySelector('.harga-input').value) {
                const hargaInput = row.querySelector('.harga-input');
                hargaInput.value = hargaDefault;
                updateSubtotal(row);
            }
            
            updateSubtotal(row);
        }

        // Event listener untuk semua row yang ada
        function attachRowEvents(row) {
            const select = row.querySelector('.item-select');
            select.addEventListener('change', function() {
                setHargaDefault(this, row);
            });
            
            const jumlahInput = row.querySelector('.jumlah-input');
            jumlahInput.addEventListener('input', function() {
                updateSubtotal(row);
            });
            
            const hargaInput = row.querySelector('.harga-input');
            hargaInput.addEventListener('input', function() {
                updateSubtotal(row);
            });
            
            const removeBtn = row.querySelector('.remove-item-btn');
            removeBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                    updateGrandTotal();
                } else {
                    alert('Minimal harus ada 1 item');
                }
            });
        }

        // Attach events ke row yang sudah ada
        document.querySelectorAll('.item-row').forEach(row => {
            attachRowEvents(row);
        });

        // Tambah item baru
        document.getElementById('addItemBtn').addEventListener('click', function() {
            const container = document.getElementById('itemsContainer');
            const firstRow = document.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);
            
            // Reset nilai
            newRow.querySelectorAll('input, select').forEach(input => {
                if (input.type === 'number') {
                    if (input.classList.contains('jumlah-input')) {
                        input.value = '1';
                    } else if (input.classList.contains('harga-input')) {
                        input.value = '';
                    }
                } else if (input.tagName === 'SELECT') {
                    input.value = '';
                }
            });
            
            newRow.querySelector('.subtotal-display').innerText = '0';
            
            attachRowEvents(newRow);
            container.appendChild(newRow);
            updateGrandTotal();
        });

        // Update grand total awal
        updateGrandTotal();
    </script>
</body>
</html>