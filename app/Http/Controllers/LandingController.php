<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Statistik untuk landing page
        $totalProcurements = ProcurementRequest::where('status', 'selesai')->count();
        $totalSuppliers = Supplier::count();
        $totalCategories = Category::count();

        // Pengajuan terbaru yang sudah selesai
        $recentProcurements = ProcurementRequest::with('user')
                            ->where('status', 'selesai')
                            ->latest()
                            ->limit(6)
                            ->get();

        return view('landing.index', compact(
            'totalProcurements',
            'totalSuppliers',
            'totalCategories',
            'recentProcurements'
        ));
    }
}