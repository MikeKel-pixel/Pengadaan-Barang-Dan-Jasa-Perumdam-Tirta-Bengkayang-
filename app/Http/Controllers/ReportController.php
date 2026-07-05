<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\VendorQuote;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Laporan Pengajuan (View)
     * GET /reports/procurements
     */
    public function procurementReport(Request $request)
    {
        $query = ProcurementRequest::with(['user', 'details.item', 'vendorQuotes.supplier']);
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $procurements = $query->latest()->paginate(20);
        
        // Summary
        $summary = [
            'total' => $query->count(),
            'total_value' => $query->sum('total_estimasi'),
            'draft' => (clone $query)->where('status', 'draft')->count(),
            'submitted' => (clone $query)->where('status', 'diajukan')->count(),
            'approved' => (clone $query)->where('status', 'disetujui')->count(),
            'rejected' => (clone $query)->where('status', 'ditolak')->count(),
            'processed' => (clone $query)->where('status', 'diproses')->count(),
            'completed' => (clone $query)->where('status', 'selesai')->count(),
        ];
        
        return view('reports.procurements', compact('procurements', 'summary', 'request'));
    }
    
    /**
     * Export CSV Laporan Pengajuan
     * GET /reports/procurements/export
     */
    public function exportProcurementReport(Request $request)
    {
        $query = ProcurementRequest::with(['user', 'details', 'vendorQuotes']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $procurements = $query->get();
        
        $filename = 'laporan_pengajuan_' . date('Ymd_His') . '.csv';
        
        $handle = fopen('php://temp', 'w');
        
        // Header CSV dengan kolom Total Penawaran
        fputcsv($handle, [
            'No', 'Kode Pengajuan', 'Tanggal', 'Pembuat', 'Total Estimasi', 
            'Total Penawaran', 'Status', 'Jumlah Item', 'Keterangan', 'Dibuat Pada'
        ]);
        
        foreach ($procurements as $index => $proc) {
            // Ambil total penawaran dari vendor yang terpilih
            $selectedQuote = $proc->vendorQuotes->where('status_terpilih', true)->first();
            $totalPenawaran = $selectedQuote ? $selectedQuote->total_penawaran : 0;
            
            fputcsv($handle, [
                $index + 1,
                $proc->kode_pengajuan,
                $proc->tanggal_pengajuan,
                $proc->user->name,
                $proc->total_estimasi,
                $totalPenawaran,
                $proc->status,
                $proc->details->count(),
                $proc->keterangan,
                $proc->created_at
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Laporan Vendor (View)
     * GET /reports/vendors
     */
    public function vendorReport(Request $request)
    {
        $query = Supplier::with(['vendorQuotes' => function($q) {
            $q->with('procurementRequest');
        }]);
        
        if ($request->filled('search')) {
            $query->where('nama_supplier', 'like', '%' . $request->search . '%');
        }
        
        $suppliers = $query->paginate(20);
        
        // Summary
        $totalQuotes = VendorQuote::count();
        $totalSelected = VendorQuote::where('status_terpilih', true)->count();
        $totalQuoteValue = VendorQuote::sum('total_penawaran');
        
        // Top vendors
        $topVendors = VendorQuote::select('supplier_id', DB::raw('COUNT(*) as total_quotes'), DB::raw('SUM(CASE WHEN status_terpilih = 1 THEN 1 ELSE 0 END) as selected_count'))
                    ->groupBy('supplier_id')
                    ->with('supplier')
                    ->orderBy('selected_count', 'desc')
                    ->limit(5)
                    ->get();
        
        return view('reports.vendors', compact('suppliers', 'totalQuotes', 'totalSelected', 'totalQuoteValue', 'topVendors', 'request'));
    }
    
    /**
     * Export CSV Laporan Vendor
     * GET /reports/vendors/export
     */
    public function exportVendorReport(Request $request)
    {
        $query = Supplier::with(['vendorQuotes']);
        
        if ($request->filled('search')) {
            $query->where('nama_supplier', 'like', '%' . $request->search . '%');
        }
        
        $suppliers = $query->get();
        
        $filename = 'laporan_vendor_' . date('Ymd_His') . '.csv';
        
        $handle = fopen('php://temp', 'w');
        
        // Header CSV
        fputcsv($handle, [
            'No', 'Nama Supplier', 'Email', 'Telepon', 'Total Penawaran', 
            'Penawaran Terpilih', 'Total Nilai Penawaran'
        ]);
        
        foreach ($suppliers as $index => $supplier) {
            $totalQuotes = $supplier->vendorQuotes->count();
            $totalSelected = $supplier->vendorQuotes->where('status_terpilih', true)->count();
            $totalValue = $supplier->vendorQuotes->sum('total_penawaran');
            
            fputcsv($handle, [
                $index + 1,
                $supplier->nama_supplier,
                $supplier->email,
                $supplier->telepon,
                $totalQuotes,
                $totalSelected,
                $totalValue
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}