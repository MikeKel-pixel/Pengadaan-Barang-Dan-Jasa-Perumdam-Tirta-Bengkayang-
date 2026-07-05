<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - PERUMDAM Tirta Bengkayang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

    <!-- Navbar dengan kondisi login -->
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
                    <a href="{{ url('/about') }}" class="text-blue-200">Tentang Kami</a>
                    <a href="{{ url('/services') }}" class="hover:text-blue-200 transition">Layanan</a>
                    <a href="{{ url('/contact') }}" class="hover:text-blue-200 transition">Kontak</a>
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

    <div class="container mx-auto px-6 pt-32 pb-16">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Tentang PERUMDAM Tirta Bengkayang</h1>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-blue-700 mb-4">Sejarah</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Keberadaan air untuk kebutuhan minum, masak, mandi dan lain-lain merupakan hajat hidup orang, masyarakat, dimanapun berada. Kota Bengkayang tumbuh dan berkembang sesuai perubahan dan perkembangannya yang dulunya hanya ibukota kecamatan pada tahun 1999 menjadi ibukota kabupaten, dengan adanya Undang-Undang nomor 10 tahun 1999 Tentang Pemerintah Kabupaten Bengkayang. Fasilitas untuk melayani kebutuhan publik yang belum ada, harus diadakan. Fasilitas kebutuhan publik untuk air bersih pada saat masih berada pada masa Kabupaten Sambas, ialah air bersih yang ditangani oleh PDAM Kabupaten Sambas yang berada di Bengkayang, berupa sumber air dari Riam Budi. Konsumennya terbatas, debit air juga kecil, tidak mampu mengatasi kebutuhan masyarakat Kota Bengkayang saat itu, apalagi pelayanan yang berkembang dan bertambahnya rumah-rumah, bangunan, dan fasilitas pemerintah dengan ada bertambahnya keberadaan instansi di ibukota Kabupaten Bengkayang.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Pemerintah Kabupaten Bengkayang menyikapi keadaan untuk tersedianya air bersih yang memadai dan mencukupi, melakukan langkah-langkah mempelajari cara mengatasi masalah kebutuhan air bersih tersebut. Potensi yang tersedia untuk kebutuhan air bersih ada beberapa pilihan yakni air sungai, sumur, atau air dalam tanah, air hujan, atau air dari gunung yang dapat dialirkan untuk sumber air bersih. Pilihannya adalah mempelajari sumber air dari gunung dan riam yang cukup tersedia di daerah ini.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-blue-700 mb-4">Visi</h2>
                <p class="text-gray-700 leading-relaxed">
                    “Tewujudnya Perumda Air Minum Tirta Bengkayang yang Sehat, Baik, Terpercaya, Maju dan Mampu Menghasilkan Pendapatan Asli Daerah bagi Kabupaten Bengkayang”
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-blue-700 mb-4">Misi</h2>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li>Menjadikan Perumda Air Minum Tirta Bengkayang terbaik di Kalimantan Barat;</li>
                    <li>Menjadikan Perumda Air Minum Tirta Bengkayang yang Sehat, Baik Keuangan dan Manajemen;</li>
                    <li>Mendorong agar segera terwujudnya Rencana Induk Sistem Penyediaan Air Minum (RISPAM) Kabupaten Bengkayang;</li>
                    <li>Mendorong Badan hukum PDAM Kabupaten Bengkayang menjadi Perumda Air Minum sesuai Amanat Undang-Undang;</li>
                    <li>Revitalisasi Aset Intake Madi agar bisa berfungsi kembali seperti semula 100 liter/detik;</li>
                    <li>Memperbaiki raport merah Perumda Air Minum Tirta Bengkayang yang saat ini memiliki sepuluh (10) indicator angka merah dan meningkatkan;</li>
                    <li>Membenahi manajemen Perusahaan agar menghasilkan laba sehingga mencukupi untuk Biaya Produksi, Operasional maupun biaya lain Perusahaan;</li>
                    <li>Mewujudkan Keseimbangan Perumda Air Minum Tirta Bengkayang menjadi Sosial dan Profit Oriented;</li>
                    <li>Mendorong pemerintah Kabupaten Bengkayang untuk menyerahkan beberapa Aset SPAM yang sudah dibangun selama ini untuk diserahkan pengelolaanya kepada Perumda Air Minum Tirta Bengkayang;</li>
                    <li>Memenuhi Cakupan Pelayanan Perumda Air Minum Tirta Bengkayang sampai keseluruh Masyarakat Kabupaten Bengkayang mendapatkan pelayanan dasar air;</li>
                    <li>Menekan angka kebocoran dan memberantas ilegal Conection maupun Pencurian Air;</li>
                    <li>Pembenahan manajemen perusahaan agar sehat, baik dan professional;</li>
                    <li>Mensejahterakan pemenuhan hak-hak Karyawan serta Jaminan Hari Tua/Pensiun Karyawan maupun Peningkatan SDM Karyawan;</li>
                    <li>Peningkatan Volume Air Terjual;</li>
                    <li>Pemeliharaan Aset Perumda Air Minum baik Kantor Pusat, Kantor Unit, SPAM Kecamatan, Pipa Distribusi, Pipa Transmisi, intake, IPA, Reservoir dan Aset lainnya;</li>
                    <li>Mewujudkan Pelayanan yang Prima, Cepat Tanggap dan Puas Kepada Pelanggan;</li>
                    <li>Membangun Kerjasama dengan Pemerintah dan Swasta maupun Lembaga terkait untuk memajukan Perumdam Tirta Bengkayang;</li>
                    <li>Merencanakan memperluas wilayah dan penataan serta pengembangan jaringan distribusi air bersih dengan efektif dan efisien;</li>
                    <li>Pembangunan Kantor Pusat dan Kantor Pelayanan Perumdam yang Refresentatif.</li>
                </ul>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-blue-700 mb-4">Sistem Pengadaan Barang/Jasa</h2>
                <p class="text-gray-700 leading-relaxed">
                    Sistem ini dirancang untuk memudahkan proses pengadaan barang/jasa di lingkungan 
                    PERUMDAM Tirta Bengkayang. Dengan sistem ini, seluruh proses pengadaan menjadi 
                    lebih transparan, efisien, dan akuntabel. Mulai dari pengajuan kebutuhan, 
                    persetujuan oleh pimpinan, hingga penawaran dari vendor dapat dilakukan secara online.
                </p>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-8 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; {{ date('Y') }} PERUMDAM Tirta Bengkayang. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>