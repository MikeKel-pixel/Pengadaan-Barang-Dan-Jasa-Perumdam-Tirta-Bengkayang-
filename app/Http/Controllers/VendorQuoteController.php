<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\Supplier;
use App\Models\VendorQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorQuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:pengadaan|admin');
    }

    // Menampilkan daftar pengajuan yang sudah disetujui untuk diproses vendor
    public function index()
    {
        $procurements = ProcurementRequest::with(['user', 'vendorQuotes.supplier'])
                        ->where('status', 'disetujui')
                        ->latest()
                        ->paginate(10);
        
        return view('vendor-quotes.index', compact('procurements'));
    }

    // Menampilkan detail pengajuan untuk input penawaran vendor
    public function show($id)
    {
        $procurement = ProcurementRequest::with([
            'user', 
            'details.item.category', 
            'vendorQuotes.supplier'
        ])->findOrFail($id);
        
        // Cek status
        if (!in_array($procurement->status, ['disetujui', 'diproses'])) {
            return redirect()->route('vendor-quotes.index')
                ->with('error', 'Pengajuan ini tidak dapat diproses');
        }
        
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        
        return view('vendor-quotes.show', compact('procurement', 'suppliers'));
    }

    // Menyimpan penawaran vendor
    public function store(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'total_penawaran' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $procurement = ProcurementRequest::findOrFail($id);
            
            // Cek apakah pengajuan sudah diproses
            if ($procurement->status !== 'disetujui' && $procurement->status !== 'diproses') {
                return redirect()->route('vendor-quotes.index')
                    ->with('error', 'Pengajuan tidak dapat menerima penawaran');
            }

            // Cek apakah supplier sudah memberikan penawaran untuk pengajuan ini
            $existingQuote = VendorQuote::where('procurement_request_id', $id)
                                        ->where('supplier_id', $request->supplier_id)
                                        ->first();
            
            if ($existingQuote) {
                return back()->with('error', 'Supplier sudah memberikan penawaran untuk pengajuan ini');
            }

            // Simpan penawaran
            VendorQuote::create([
                'procurement_request_id' => $id,
                'supplier_id' => $request->supplier_id,
                'total_penawaran' => $request->total_penawaran,
                'keterangan' => $request->keterangan,
                'status_terpilih' => false
            ]);

            return back()->with('success', 'Penawaran berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Memilih vendor terbaik
    public function selectVendor($procurementId, $quoteId)
    {
        try {
            DB::beginTransaction();

            $procurement = ProcurementRequest::findOrFail($procurementId);
            $quote = VendorQuote::findOrFail($quoteId);

            // Validasi
            if ($procurement->status !== 'disetujui') {
                return redirect()->route('vendor-quotes.index')
                    ->with('error', 'Pengajuan belum disetujui atau sudah diproses');
            }

            if ($quote->procurement_request_id != $procurementId) {
                return redirect()->route('vendor-quotes.index')
                    ->with('error', 'Data penawaran tidak valid');
            }

            // Reset semua vendor jadi tidak terpilih untuk pengajuan ini
            VendorQuote::where('procurement_request_id', $procurementId)
                        ->update(['status_terpilih' => false]);

            // Set vendor terpilih
            $quote->update(['status_terpilih' => true]);

            // Update status pengajuan menjadi diproses
            $procurement->update(['status' => 'diproses']);

            DB::commit();

            return redirect()->route('vendor-quotes.show', $procurementId)
                ->with('success', 'Vendor ' . $quote->supplier->nama_supplier . ' berhasil dipilih');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menyelesaikan pengadaan
    public function complete($id)
    {
        try {
            $procurement = ProcurementRequest::findOrFail($id);
            
            if ($procurement->status !== 'diproses') {
                return redirect()->route('vendor-quotes.index')
                    ->with('error', 'Pengajuan tidak dapat diselesaikan');
            }

            $procurement->update(['status' => 'selesai']);

            return redirect()->route('vendor-quotes.show', $id)
                ->with('success', 'Pengadaan telah selesai');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Hapus penawaran vendor
    public function destroy($procurementId, $quoteId)
    {
        try {
            $procurement = ProcurementRequest::findOrFail($procurementId);
            
            if ($procurement->status !== 'disetujui') {
                return redirect()->route('vendor-quotes.show', $procurementId)
                    ->with('error', 'Tidak dapat menghapus penawaran karena pengajuan sudah diproses');
            }

            $quote = VendorQuote::findOrFail($quoteId);
            
            if ($quote->status_terpilih) {
                return redirect()->route('vendor-quotes.show', $procurementId)
                    ->with('error', 'Tidak dapat menghapus penawaran yang sudah terpilih');
            }

            $quote->delete();

            return redirect()->route('vendor-quotes.show', $procurementId)
                ->with('success', 'Penawaran berhasil dihapus');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}