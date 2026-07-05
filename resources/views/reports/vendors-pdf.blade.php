<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Vendor PERUMDAM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 15px;
        }
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            color: #4b5563;
            margin-bottom: 3px;
        }
        .info-box {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 10px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 3px 5px;
        }
        .info-box td:first-child {
            font-weight: bold;
            width: 140px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data th {
            background-color: #1e40af;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }
        table.data td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: center;
            font-size: 9px;
        }
        table.data tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 100%;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="logo">
        <div class="title">PERUMDAM TIRTA BENGKAYANG</div>
        <div class="subtitle">Laporan Data Vendor / Supplier</div>
        <div class="subtitle">Jl. Raya Pontianak, Eks. Kantor BPBD Bengkayang, No. 95, Bengkayang</div>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>Pencarian</td>
                <td>: {{ $filterInfo['search'] }}</td>
                <td>Total Supplier</td>
                <td>: {{ number_format($filterInfo['total_suppliers']) }}</td>
            </tr>
            <tr>
                <td>Total Penawaran</td>
                <td>: {{ number_format($filterInfo['total_quotes']) }}</td>
                <td>Penawaran Terpilih</td>
                <td>: {{ number_format($filterInfo['total_selected']) }}</td>
            </tr>
            <tr>
                <td>Total Nilai Penawaran</td>
                <td>: Rp {{ number_format($filterInfo['total_value'], 0, ',', '.') }}</td>
                <td>Tanggal Export</td>
                <td>: {{ $filterInfo['export_date'] }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Supplier</th>
                <th width="15%">Email / Telepon</th>
                <th width="15%">PIC</th>
                <th width="12%">Total Penawaran</th>
                <th width="12%">Penawaran Terpilih</th>
                <th width="21%">Total Nilai Penawaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $index => $supplier)
            @php
                $totalQuotes = $supplier->vendorQuotes->count();
                $totalSelected = $supplier->vendorQuotes->where('status_terpilih', true)->count();
                $totalValue = $supplier->vendorQuotes->sum('total_penawaran');
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $supplier->nama_supplier }}</td>
                <td>
                    {{ $supplier->email ?? '-' }}<br>
                    <small>{{ $supplier->telepon ?? '-' }}</small>
                </td>
                <td>{{ $supplier->pic ?? '-' }}</td>
                <td>{{ number_format($totalQuotes) }}</td>
                <td>
                    @if($totalSelected > 0)
                        <span class="badge-success">{{ number_format($totalSelected) }}</span>
                    @else
                        {{ number_format($totalSelected) }}
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($totalValue, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem. Data dapat berubah sewaktu-waktu.</p>
        <p>PERUMDAM Tirta Bengkayang - Sistem Pengadaan Barang/Jasa</p>
    </div>

    <div class="signature">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Mengetahui,<br>Pimpinan PERUMDAM Tirta Bengkayang</p>
        </div>
    </div>
</body>
</html>