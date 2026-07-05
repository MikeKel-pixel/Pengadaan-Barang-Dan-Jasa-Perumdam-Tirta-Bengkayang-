<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - PERUMDAM Tirta Bengkayang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS & JS (OpenStreetMap - Gratis, tanpa API key) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 12px;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="bg-blue-700 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-12 w-auto">
                    <div>
                        <a href="{{ url('/') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-blue-200">Sistem Pengadaan Barang/Jasa</p>
                    </div>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="{{ url('/') }}" class="hover:text-blue-200 transition">Beranda</a>
                    <a href="{{ url('/about') }}" class="hover:text-blue-200 transition">Tentang Kami</a>
                    <a href="{{ url('/services') }}" class="hover:text-blue-200 transition">Layanan</a>
                    <a href="{{ url('/contact') }}" class="text-blue-200">Kontak</a>
                </div>
                <div class="flex space-x-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded transition">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-transparent border border-white px-4 py-2 rounded hover:bg-white hover:text-blue-700 transition">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded transition">
                            <i class="fas fa-user-plus mr-2"></i>Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 pt-32 pb-16">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Hubungi Kami</h1>
            <p class="text-gray-600">Informasi alamat kantor dan tautan resmi layanan PERUMDAM Tirta Bengkayang.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
            <!-- Kolom 1: Alamat & Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-blue-700 mb-4 border-b pb-2">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>Alamat Kantor
                </h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <i class="fas fa-building text-blue-600 text-xl mr-4 mt-1 w-6"></i>
                        <div>
                            <p class="font-semibold text-gray-800">Kantor Pusat</p>
                            <p class="text-gray-600">Jl. Raya Pontianak, Eks. Kantor BPBD Bengkayang, No. 95</p>
                            <p class="text-gray-600">RT/RW: 020/11, Bengkayang, Kalimantan Barat</p>
                            <p class="text-gray-600">Kode Pos: 79211</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-clock text-blue-600 text-xl mr-4 mt-1 w-6"></i>
                        <div>
                            <p class="font-semibold text-gray-800">Jam Operasional</p>
                            <p class="text-gray-600">Senin - Jumat: 08.00 - 16.00 WIB</p>
                            <p class="text-gray-600">Sabtu - Minggu: Tutup</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-link text-blue-600 text-xl mr-4 mt-1 w-6"></i>
                        <div>
                            <p class="font-semibold text-gray-800">Tautan Resmi</p>
                            <p class="text-gray-600 mb-2">Semua tautan layanan tersedia dalam satu halaman:</p>
                            <a href="https://linktr.ee/Perumdam_Tirta_Bengkayang" target="_blank" rel="noopener noreferrer" class="text-green-600 hover:text-green-800 font-bold break-all">
                                <i class="fas fa-external-link-alt mr-1"></i> https://linktr.ee/Perumdam_Tirta_Bengkayang
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: Peta Lokasi (OpenStreetMap - Gratis) -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-blue-700 mb-4 border-b pb-2">
                    <i class="fas fa-map text-blue-600 mr-2"></i>Lokasi Kantor Pusat
                </h2>
                <div id="map"></div>
                <div class="mt-4 text-center">
                    <a href="https://www.openstreetmap.org/directions?engine=graphhopper&route=-0.5039%2C109.7946" 
                       target="_blank" 
                       class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-directions mr-2"></i>Petunjuk Arah (OpenStreetMap)
                    </a>
                    <a href="https://www.openstreetmap.org/?mlat=-0.5039&mlon=109.7946&zoom=17" 
                       target="_blank" 
                       class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition ml-2">
                        <i class="fas fa-external-link-alt mr-2"></i>Buka di OpenStreetMap
                    </a>
                </div>
            </div>
        </div>

        <!-- Tautan Layanan & Informasi (Linktree) -->
        <div class="max-w-3xl mx-auto mt-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-blue-700 mb-4 border-b pb-2">
                    <i class="fas fa-link mr-2"></i>Tautan Layanan & Informasi
                </h2>
                <div class="aspect-video w-full overflow-hidden rounded-lg border border-gray-200">
                    <iframe 
                        src="https://linktr.ee/Perumdam_Tirta_Bengkayang" 
                        class="w-full h-full min-h-[400px]" 
                        frameborder="0" 
                        title="Linktree Perumdam Tirta Bengkayang">
                    </iframe>
                </div>
                <p class="text-sm text-gray-500 mt-4 text-center">
                    Kunjungi <a href="https://linktr.ee/Perumdam_Tirta_Bengkayang" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">Linktree resmi Perumdam Tirta Bengkayang</a> untuk mengakses:
                </p>
                <div class="flex flex-wrap justify-center gap-2 mt-3">
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">✅ Kontak PPID</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">✅ Cek Tagihan</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">✅ Website Resmi</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">✅ WhatsApp Bisnis</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">✅ Kuisioner Kepuasan</span>
                </div>
            </div>
        </div>
        
        <div class="text-center text-gray-500 text-sm mt-8">
            <p>&copy; {{ date('Y') }} PERUMDAM Tirta Bengkayang. All rights reserved.</p>
            <p class="text-xs mt-1">Peta menggunakan <a href="https://www.openstreetmap.org/copyright" target="_blank" class="text-blue-500">OpenStreetMap</a> | Bebas biaya</p>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; {{ date('Y') }} PERUMDAM Tirta Bengkayang - Sistem Pengadaan Barang/Jasa</p>
        </div>
    </footer>

    <!-- Leaflet Map JavaScript -->
    <script>
        // Koordinat kantor PERUMDAM Tirta Bengkayang
        const perumdamLocation = [-0.5039, 109.7946]; // [Latitude, Longitude]
        
        // Inisialisasi peta
        const map = L.map('map').setView(perumdamLocation, 17);
        
        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20,
            minZoom: 1
        }).addTo(map);
        
        // Custom marker icon
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: '<i class="fas fa-map-marker-alt" style="font-size: 40px; color: #ef4444; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></i>',
            iconSize: [40, 40],
            popupAnchor: [0, -20]
        });
        
        // Tambahkan marker
        const marker = L.marker(perumdamLocation, { icon: customIcon }).addTo(map);
        
        // Tambahkan popup
        marker.bindPopup(`
            <div style="min-width: 200px; font-family: Arial, sans-serif;">
                <h3 style="font-weight: bold; margin-bottom: 8px; color: #1e40af;">
                    <i class="fas fa-water"></i> PERUMDAM Tirta Bengkayang
                </h3>
                <hr style="margin: 5px 0; border-color: #e5e7eb;">
                <p style="margin: 5px 0; font-size: 12px;">
                    <strong>Kantor Pusat</strong><br>
                    Jl. Raya Pontianak, Eks. Kantor BPBD Bengkayang, No. 95<br>
                    RT/RW: 020/11, Bengkayang<br>
                    Kalimantan Barat
                </p>
                <hr style="margin: 5px 0; border-color: #e5e7eb;">
                <p style="margin: 5px 0; font-size: 11px; color: #4b5563;">
                    <i class="fas fa-clock"></i> Senin - Jumat: 08:00 - 16:00
                </p>
                <p style="margin: 5px 0; font-size: 11px; color: #4b5563;">
                    <i class="fas fa-link"></i> linktr.ee/Perumdam_Tirta_Bengkayang
                </p>
            </div>
        `).openPopup();
        
        // Tambahkan kontrol zoom
        map.zoomControl.setPosition('topright');
    </script>
</body>
</html>