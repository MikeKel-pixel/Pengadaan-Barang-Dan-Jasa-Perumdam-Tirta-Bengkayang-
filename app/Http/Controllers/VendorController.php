<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\VendorQuote;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:vendor');
    }

    // Dashboard Vendor
    public function index()
    {
        // Cari supplier berdasarkan email vendor yang login
        $supplier = Supplier::where('email', Auth::user()->email)->first();
        
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

    // Menampilkan form buat penawaran
    public function createOffer($id)
    {
        $procurement = ProcurementRequest::with(['details.item', 'user'])
                        ->findOrFail($id);
        
        // Cek apakah status pengajuan adalah disetujui
        if ($procurement->status != 'disetujui') {
            return redirect()->route('vendor.dashboard')
                ->with('error', 'Pengajuan ini tidak dapat ditawari karena statusnya ' . $procurement->status);
        }
        
        // Cek apakah vendor sudah pernah memberikan penawaran untuk pengajuan ini
        $supplier = Supplier::where('email', Auth::user()->email)->first();
        
        if ($supplier) {
            $existingQuote = VendorQuote::where('procurement_request_id', $id)
                                        ->where('supplier_id', $supplier->id)
                                        ->first();
            if ($existingQuote) {
                return redirect()->route('vendor.dashboard')
                    ->with('error', 'Anda sudah pernah memberikan penawaran untuk pengajuan ini');
            }
        }
        
        return view('vendor.create-offer', compact('procurement'));
    }

    // Menyimpan penawaran
    public function storeOffer(Request $request, $id)
    {
        $request->validate([
            'total_penawaran' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $procurement = ProcurementRequest::findOrFail($id);
            
            // Cek apakah status pengajuan adalah disetujui
            if ($procurement->status != 'disetujui') {
                return redirect()->route('vendor.dashboard')
                    ->with('error', 'Pengajuan ini tidak dapat ditawari lagi');
            }

            // Cari supplier berdasarkan email vendor yang login
            $supplier = Supplier::where('email', Auth::user()->email)->first();
            
            if (!$supplier) {
                return redirect()->route('vendor.dashboard')
                    ->with('error', 'Data supplier tidak ditemukan. Silakan hubungi administrator.');
            }

            // Cek duplikasi penawaran
            $existingQuote = VendorQuote::where('procurement_request_id', $id)
                                        ->where('supplier_id', $supplier->id)
                                        ->first();
            
            if ($existingQuote) {
                return redirect()->route('vendor.dashboard')
                    ->with('error', 'Anda sudah pernah memberikan penawaran untuk pengajuan ini');
            }

            // Simpan penawaran
            VendorQuote::create([
                'procurement_request_id' => $id,
                'supplier_id' => $supplier->id,
                'total_penawaran' => $request->total_penawaran,
                'keterangan' => $request->keterangan,
                'status_terpilih' => false
            ]);

            DB::commit();

            return redirect()->route('vendor.dashboard')
                ->with('success', 'Penawaran Anda berhasil dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('vendor.dashboard')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Riwayat penawaran vendor
    public function history()
    {
        $supplier = Supplier::where('email', Auth::user()->email)->first();
        
        if (!$supplier) {
            return view('vendor.history', ['quotes' => collect(), 'supplier' => null])
                ->with('error', 'Data supplier tidak ditemukan. Silakan hubungi administrator.');
        }

        $quotes = VendorQuote::with('procurementRequest')
                    ->where('supplier_id', $supplier->id)
                    ->latest()
                    ->paginate(10);

        return view('vendor.history', compact('quotes', 'supplier'));
    }

    // Detail penawaran
    public function showOffer($id)
    {
        $supplier = Supplier::where('email', Auth::user()->email)->first();
        
        $quote = VendorQuote::with(['procurementRequest', 'procurementRequest.details.item', 'supplier'])
                    ->findOrFail($id);
        
        // Pastikan vendor hanya bisa melihat penawarannya sendiri
        if ($supplier && $quote->supplier_id != $supplier->id) {
            return redirect()->route('vendor.history')
                ->with('error', 'Anda tidak memiliki akses ke penawaran ini');
        }
        
        return view('vendor.show-offer', compact('quote'));
    }
}