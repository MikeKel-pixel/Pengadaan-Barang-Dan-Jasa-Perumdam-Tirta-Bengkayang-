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
        // ========== AMBIL DATA ==========
        $totalProcurements = ProcurementRequest::where('status', 'selesai')->count();
        $totalSuppliers = Supplier::count();
        $totalCategories = Category::count();

        $recentProcurements = ProcurementRequest::with('user')
                            ->where('status', 'selesai')
                            ->latest()
                            ->limit(6)
                            ->get();

        // ========== KIRIM KE VIEW ==========
        return view('landing.index', [
            'totalProcurements' => $totalProcurements,
            'totalSuppliers' => $totalSuppliers,
            'totalCategories' => $totalCategories,
            'recentProcurements' => $recentProcurements
        ]);
    }

    public function about()
    {
        return view('landing.about');
    }

    public function services()
    {
        return view('landing.services');
    }

    public function contact()
    {
        return view('landing.contact');
    }
}