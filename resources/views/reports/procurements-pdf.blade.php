<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajuan Pengadaan</title>
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
            width: 120px;
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
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-draft { background-color: #e5e7eb; color: #374151; }
        .badge-diajukan { background-color: #fef3c7; color: #92400e; }
        .badge-disetujui { background-color: #d1fae5; color: #065f46; }
        .badge-ditolak { background-color: #fee2e2; color: #991b1b; }
        .badge-diproses { background-color: #dbeafe; color: #1e40af; }
        .badge-selesai { background-color: #e9d5ff; color: #6b21a5; }
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
        <div class="subtitle">Laporan Pengajuan Pengadaan Barang/Jasa</div>
        <div class="subtitle">Jl. Raya Pontianak, Eks. Kantor BPBD Bengkayang, No. 95, Bengkayang</div>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>Filter Status</td>
                <td>: {{ $filterInfo['status'] }}</td>
                <td>Total Pengajuan</td>
                <td>: {{ number_format($filterInfo['total_records']) }}</td>
            </tr>
            <tr>
                <td>Periode Mulai</td>
                <td>: {{ $filterInfo['start_date'] }}</td>
                <td>Total Nilai</td>
                <td>: Rp {{ number_format($filterInfo['total_value'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Periode Sampai</td>
                <td>: {{ $filterInfo['end_date'] }}</td>
                <td>Tanggal Export</td>
                <td>: {{ $filterInfo['export_date'] }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Kode Pengajuan</th>
                <th width="10%">Tanggal</th>
                <th width="15%">Pembuat</th>
                <th width="12%">Total Estimasi</th>
                <th width="10%">Status</th>
                <th width="8%">Jumlah Item</th>
                <th width="28%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($procurements as $index => $proc)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $proc->kode_pengajuan }}</td>
                <td>{{ \Carbon\Carbon::parse($proc->tanggal_pengajuan)->format('d/m/Y') }}</td>
                <td>{{ $proc->user->name }}</td>
                <td class="text-right">Rp {{ number_format($proc->total_estimasi, 0, ',', '.') }}</td>
                <td>
                    @php
                        $badgeClass = match($proc->status) {
                            'draft' => 'badge-draft',
                            'diajukan' => 'badge-diajukan',
                            'disetujui' => 'badge-disetujui',
                            'ditolak' => 'badge-ditolak',
                            'diproses' => 'badge-diproses',
                            'selesai' => 'badge-selesai',
                            default => ''
                        };
                        $statusLabel = match($proc->status) {
                            'draft' => 'Draft',
                            'diajukan' => 'Diajukan',
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            'diproses' => 'Diproses',
                            'selesai' => 'Selesai',
                            default => ucfirst($proc->status)
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                </td>
                <td>{{ $proc->details->count() }}</td>
                <td class="text-left">{{ Str::limit($proc->keterangan ?? '-', 50) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #e5e7eb;">
                <td colspan="4" style="text-align: right; font-weight: bold;">GRAND TOTAL</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($filterInfo['total_value'], 0, ',', '.') }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
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