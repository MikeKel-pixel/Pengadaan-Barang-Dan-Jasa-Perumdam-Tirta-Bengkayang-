<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Pengajuan - Perumdam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <nav class="bg-purple-600 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ route('pimpinan.dashboard') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-purple-200">Persetujuan Pengajuan</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-purple-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <span class="bg-blue-500 px-3 py-1 rounded text-sm">Pimpinan</span>
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

    <div class="container mx-auto px-6 pt-24 pb-8">
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

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Pengajuan Menunggu Persetujuan</h1>
            <a href="{{ route('approvals.history') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-history mr-2"></i>Riwayat Persetujuan
            </a>
        </div>

        @if($procurements->count() > 0)
            <div class="grid grid-cols-1 gap-6">
                @foreach($procurements as $proc)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-lg">{{ $proc->kode_pengajuan }}</h3>
                            <p class="text-sm text-gray-500">
                                Dibuat oleh: {{ $proc->user->name }} | 
                                {{ \Carbon\Carbon::parse($proc->tanggal_pengajuan)->format('d F Y') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>Menunggu Persetujuan
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <p class="text-gray-600 font-medium">Total Estimasi:</p>
                            <p class="text-2xl font-bold text-blue-600">
                                Rp {{ number_format($proc->total_estimasi, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="mb-4">
                            <p class="text-gray-600 font-medium">Detail Barang:</p>
                            <ul class="list-disc list-inside text-gray-600">
                                @foreach($proc->details->take(3) as $detail)
                                <li>{{ $detail->item->nama_barang }} - {{ number_format($detail->jumlah) }} {{ $detail->item->satuan }}</li>
                                @endforeach
                                @if($proc->details->count() > 3)
                                <li>... dan {{ $proc->details->count() - 3 }} item lainnya</li>
                                @endif
                            </ul>
                        </div>
                        <div class="flex justify-end">
                            <a href="{{ route('approvals.show', $proc->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                <i class="fas fa-eye mr-2"></i>Proses Persetujuan
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $procurements->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak Ada Pengajuan Menunggu Persetujuan</h3>
                <p class="text-gray-500">Semua pengajuan sudah diproses.</p>
            </div>
        @endif
    </div>
</body>
</html>