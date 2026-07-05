<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\ProcurementRequest;
use App\Models\VendorQuote;
use Spatie\Permission\Models\Role;
use App\Charts\PengadaanChart;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(PengadaanChart $chart)
    {
        // Statistik Users
        $totalUsers = User::count();
        $totalRoles = Role::count();
        
        // Statistik Data Master
        $totalCategories = Category::count();
        $totalItems = Item::count();
        $totalSuppliers = Supplier::count();
        
        // Statistik Pengajuan
        $totalProcurements = ProcurementRequest::count();
        $totalDraft = ProcurementRequest::where('status', 'draft')->count();
        $totalSubmitted = ProcurementRequest::where('status', 'diajukan')->count();
        $totalApproved = ProcurementRequest::where('status', 'disetujui')->count();
        $totalRejected = ProcurementRequest::where('status', 'ditolak')->count();
        $totalProcessed = ProcurementRequest::where('status', 'diproses')->count();
        $totalCompleted = ProcurementRequest::where('status', 'selesai')->count();
        
        // Total Nilai Pengadaan
        $totalValue = ProcurementRequest::where('status', 'selesai')->sum('total_estimasi');
        
        // Data untuk Chart (6 bulan terakhir)
        $months = [];
        $procurementCounts = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $count = ProcurementRequest::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count();
            $procurementCounts[] = $count;
        }
        
        // Build chart
        $chart = new PengadaanChart();
        $chart->labels($months);
        $chart->dataset('Jumlah Pengajuan', 'line', $procurementCounts)
              ->options([
                  'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                  'borderColor' => 'rgba(54, 162, 235, 1)',
                  'borderWidth' => 2,
                  'pointBackgroundColor' => 'rgba(54, 162, 235, 1)',
                  'pointBorderColor' => '#fff',
                  'pointRadius' => 4,
                  'pointHoverRadius' => 6,
                  'fill' => true,
              ]);
        
        $chart->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Trend Pengajuan 6 Bulan Terakhir'
                ]
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1
                    ]
                ]
            ]
        ]);
        
        return view('dashboard.admin', compact(
            'totalUsers', 'totalRoles', 'totalCategories', 'totalItems', 
            'totalSuppliers', 'totalProcurements', 'totalDraft', 
            'totalSubmitted', 'totalApproved', 'totalRejected', 
            'totalProcessed', 'totalCompleted', 'totalValue', 'chart'
        ));
    }
}