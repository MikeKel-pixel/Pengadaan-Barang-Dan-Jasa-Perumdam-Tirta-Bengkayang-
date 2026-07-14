<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\VendorQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ==================== DASHBOARD ADMIN ====================
    public function adminDashboard()
    {
        // Statistik Utama
        $totalProcurements = ProcurementRequest::count();
        $totalUsers = User::count();
        $totalCategories = Category::count();
        $totalItems = Item::count();
        $totalSuppliers = Supplier::count();

        // Statistik berdasarkan status
        $statusCounts = ProcurementRequest::select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')
                        ->get()
                        ->pluck('total', 'status')
                        ->toArray();

        $draftCount = $statusCounts['draft'] ?? 0;
        $submittedCount = $statusCounts['diajukan'] ?? 0;
        $approvedCount = $statusCounts['disetujui'] ?? 0;
        $rejectedCount = $statusCounts['ditolak'] ?? 0;
        $processedCount = $statusCounts['diproses'] ?? 0;
        $completedCount = $statusCounts['selesai'] ?? 0;

        // Data untuk chart (per bulan)
        $monthlyData = ProcurementRequest::select(
                            DB::raw('MONTH(created_at) as month'),
                            DB::raw('COUNT(*) as total'),
                            DB::raw('SUM(total_estimasi) as total_value')
                        )
                        ->whereYear('created_at', date('Y'))
                        ->groupBy('month')
                        ->orderBy('month')
                        ->get();

        $months = [];
        $procurementCounts = [];
        $procurementValues = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $months[] = $monthName;
            
            $data = $monthlyData->firstWhere('month', $i);
            $procurementCounts[] = $data ? $data->total : 0;
            $procurementValues[] = $data ? $data->total_value : 0;
        }

        // Data untuk chart kategori terlaris
        $topCategories = DB::table('procurement_details')
                        ->join('items', 'procurement_details.item_id', '=', 'items.id')
                        ->join('categories', 'items.category_id', '=', 'categories.id')
                        ->select('categories.nama_kategori', DB::raw('SUM(procurement_details.jumlah) as total_quantity'))
                        ->groupBy('categories.id', 'categories.nama_kategori')
                        ->orderBy('total_quantity', 'desc')
                        ->limit(5)
                        ->get();

        $categoryNames = $topCategories->pluck('nama_kategori')->toArray();
        $categoryQuantities = $topCategories->pluck('total_quantity')->toArray();

        // Data untuk chart performa vendor
        $vendorPerformance = VendorQuote::with('supplier')
                            ->select('supplier_id', DB::raw('COUNT(*) as total_quotes'), DB::raw('SUM(CASE WHEN status_terpilih = 1 THEN 1 ELSE 0 END) as selected_count'))
                            ->groupBy('supplier_id')
                            ->orderBy('selected_count', 'desc')
                            ->limit(5)
                            ->get();

        $vendorNames = [];
        $vendorQuotes = [];
        $vendorSelected = [];

        foreach ($vendorPerformance as $vendor) {
            if ($vendor->supplier) {
                $vendorNames[] = $vendor->supplier->nama_supplier;
                $vendorQuotes[] = $vendor->total_quotes;
                $vendorSelected[] = $vendor->selected_count;
            }
        }

        // Pengajuan terbaru
        $recentProcurements = ProcurementRequest::with('user')
                            ->latest()
                            ->limit(5)
                            ->get();

        return view('dashboard.admin_new', compact(
            'totalProcurements', 'totalUsers', 'totalCategories', 'totalItems', 'totalSuppliers',
            'draftCount', 'submittedCount', 'approvedCount', 'rejectedCount', 'processedCount', 'completedCount',
            'months', 'procurementCounts', 'procurementValues',
            'categoryNames', 'categoryQuantities',
            'vendorNames', 'vendorQuotes', 'vendorSelected',
            'recentProcurements'
        ));
    }

    // ==================== DASHBOARD PENGAADAAN ====================
    public function pengadaanDashboard()
    {
        $user = auth()->user();
        
        // Statistik pengajuan yang dibuat user ini
        $myProcurements = ProcurementRequest::where('user_id', $user->id);
        
        $totalMyDraft = (clone $myProcurements)->where('status', 'draft')->count();
        $totalMySubmitted = (clone $myProcurements)->where('status', 'diajukan')->count();
        $totalMyApproved = (clone $myProcurements)->where('status', 'disetujui')->count();
        $totalMyRejected = (clone $myProcurements)->where('status', 'ditolak')->count();
        $totalMyProcessed = (clone $myProcurements)->where('status', 'diproses')->count();
        $totalMyCompleted = (clone $myProcurements)->where('status', 'selesai')->count();
        
        $totalMyValue = (clone $myProcurements)->sum('total_estimasi');

        // Pengajuan menunggu approval (untuk diproses vendor)
        $waitingForVendor = ProcurementRequest::where('status', 'disetujui')->count();

        // Pengajuan terbaru user ini
        $recentProcurements = ProcurementRequest::with('user')
                            ->where('user_id', $user->id)
                            ->latest()
                            ->limit(5)
                            ->get();

        return view('dashboard.pengadaan_new', compact(
            'totalMyDraft', 'totalMySubmitted', 'totalMyApproved', 
            'totalMyRejected', 'totalMyProcessed', 'totalMyCompleted',
            'totalMyValue', 'waitingForVendor', 'recentProcurements'
        ));
    }

    // ==================== DASHBOARD PIMPINAN ====================
    public function pimpinanDashboard()
    {
        // Statistik pengajuan yang menunggu approval
        $waitingApproval = ProcurementRequest::where('status', 'diajukan')->count();
        $totalApproved = ProcurementRequest::where('status', 'disetujui')->count();
        $totalRejected = ProcurementRequest::where('status', 'ditolak')->count();
        $totalCompleted = ProcurementRequest::where('status', 'selesai')->count();
        
        $totalProcurementValue = ProcurementRequest::where('status', 'selesai')->sum('total_estimasi');

        // Data untuk chart approval per bulan
        $approvalData = ProcurementRequest::select(
                            DB::raw('MONTH(created_at) as month'),
                            DB::raw('SUM(CASE WHEN status = "disetujui" THEN 1 ELSE 0 END) as approved'),
                            DB::raw('SUM(CASE WHEN status = "ditolak" THEN 1 ELSE 0 END) as rejected')
                        )
                        ->whereYear('created_at', date('Y'))
                        ->groupBy('month')
                        ->orderBy('month')
                        ->get();

        $months = [];
        $approvedCounts = [];
        $rejectedCounts = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $months[] = $monthName;
            
            $data = $approvalData->firstWhere('month', $i);
            $approvedCounts[] = $data ? $data->approved : 0;
            $rejectedCounts[] = $data ? $data->rejected : 0;
        }

        // Pengajuan terbaru menunggu approval
        $recentProcurements = ProcurementRequest::with('user')
                            ->where('status', 'diajukan')
                            ->latest()
                            ->limit(5)
                            ->get();

        return view('dashboard.pimpinan_new', compact(
            'waitingApproval', 'totalApproved', 'totalRejected', 'totalCompleted',
            'totalProcurementValue', 'months', 'approvedCounts', 'rejectedCounts',
            'recentProcurements'
        ));
    }

    // ==================== DASHBOARD VENDOR ====================
    public function vendorDashboard()
    {
        // Cari supplier berdasarkan email vendor yang login
        $supplier = Supplier::where('email', auth()->user()->email)->first();
        
        if ($supplier) {
            // Statistik penawaran vendor ini
            $totalQuotes = VendorQuote::where('supplier_id', $supplier->id)->count();
            $totalSelected = VendorQuote::where('supplier_id', $supplier->id)->where('status_terpilih', true)->count();
            $totalQuoteValue = VendorQuote::where('supplier_id', $supplier->id)->sum('total_penawaran');
            
            // Penawaran terbaru
            $recentQuotes = VendorQuote::with('procurementRequest')
                            ->where('supplier_id', $supplier->id)
                            ->latest()
                            ->limit(5)
                            ->get();
        } else {
            $totalQuotes = 0;
            $totalSelected = 0;
            $totalQuoteValue = 0;
            $recentQuotes = collect();
        }

        // Pengajuan yang sedang terbuka (status disetujui) untuk ditawari
        $openProcurements = ProcurementRequest::where('status', 'disetujui')
                            ->with(['user', 'details.item'])
                            ->latest()
                            ->get();

        return view('vendor.dashboard', compact(
            'totalQuotes', 'totalSelected', 'totalQuoteValue',
            'recentQuotes', 'openProcurements', 'supplier'
        ));
    }
}"// Rekomendasi" 
