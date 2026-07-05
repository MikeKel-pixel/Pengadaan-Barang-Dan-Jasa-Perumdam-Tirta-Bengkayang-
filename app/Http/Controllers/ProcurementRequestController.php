<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\ProcurementDetail;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:pengadaan|admin');
    }

    // Menampilkan daftar pengajuan
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            $procurements = ProcurementRequest::with(['user', 'details'])
                            ->latest()
                            ->paginate(10);
        } else {
            $procurements = ProcurementRequest::with(['user', 'details'])
                            ->where('user_id', $user->id)
                            ->latest()
                            ->paginate(10);
        }
        
        return view('procurements.index', compact('procurements'));
    }

    // Form buat pengajuan baru
    public function create()
    {
        $items = Item::with('category')->orderBy('nama_barang')->get();
        return view('procurements.create', compact('items'));
    }

    // Menyimpan pengajuan baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'keterangan' => 'nullable|string',
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|exists:items,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
            'harga_estimasi' => 'required|array',
            'harga_estimasi.*' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $kode = 'PR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            while (ProcurementRequest::where('kode_pengajuan', $kode)->exists()) {
                $kode = 'PR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }

            $procurement = ProcurementRequest::create([
                'kode_pengajuan' => $kode,
                'user_id' => auth()->id(),
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'status' => 'draft',
                'keterangan' => $request->keterangan,
                'total_estimasi' => 0
            ]);

            $total = 0;

            foreach ($request->item_id as $key => $itemId) {
                $jumlah = $request->jumlah[$key];
                $harga = $request->harga_estimasi[$key];
                $subtotal = $jumlah * $harga;
                
                ProcurementDetail::create([
                    'procurement_request_id' => $procurement->id,
                    'item_id' => $itemId,
                    'jumlah' => $jumlah,
                    'harga_estimasi' => $harga,
                    'subtotal' => $subtotal
                ]);
                
                $total += $subtotal;
            }

            $procurement->update(['total_estimasi' => $total]);

            DB::commit();

            return redirect()->route('procurements.index')
                ->with('success', 'Pengajuan berhasil dibuat dengan kode: ' . $kode);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan detail pengajuan
    public function show(ProcurementRequest $procurement)
    {
        $procurement->load(['user', 'details.item.category', 'approvals.user', 'vendorQuotes.supplier']);
        return view('procurements.show', compact('procurement'));
    }

    // Form edit pengajuan (hanya untuk status draft)
    public function edit(ProcurementRequest $procurement)
    {
        if (!$procurement->isDraft()) {
            return redirect()->route('procurements.index')
                ->with('error', 'Pengajuan tidak dapat diedit karena sudah diajukan');
        }
        
        $items = Item::with('category')->orderBy('nama_barang')->get();
        return view('procurements.edit', compact('procurement', 'items'));
    }

    // Update pengajuan
    public function update(Request $request, ProcurementRequest $procurement)
    {
        if (!$procurement->isDraft()) {
            return redirect()->route('procurements.index')
                ->with('error', 'Pengajuan tidak dapat diupdate karena sudah diajukan');
        }

        $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'keterangan' => 'nullable|string',
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|exists:items,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
            'harga_estimasi' => 'required|array',
            'harga_estimasi.*' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $procurement->update([
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'keterangan' => $request->keterangan
            ]);

            $procurement->details()->delete();

            $total = 0;

            foreach ($request->item_id as $key => $itemId) {
                $jumlah = $request->jumlah[$key];
                $harga = $request->harga_estimasi[$key];
                $subtotal = $jumlah * $harga;
                
                ProcurementDetail::create([
                    'procurement_request_id' => $procurement->id,
                    'item_id' => $itemId,
                    'jumlah' => $jumlah,
                    'harga_estimasi' => $harga,
                    'subtotal' => $subtotal
                ]);
                
                $total += $subtotal;
            }

            $procurement->update(['total_estimasi' => $total]);

            DB::commit();

            return redirect()->route('procurements.index')
                ->with('success', 'Pengajuan berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Hapus pengajuan
    public function destroy(ProcurementRequest $procurement)
    {
        if (!$procurement->isDraft()) {
            return redirect()->route('procurements.index')
                ->with('error', 'Pengajuan tidak dapat dihapus karena sudah diajukan atau diproses');
        }

        try {
            DB::beginTransaction();
            
            $procurement->details()->delete();
            $procurement->delete();
            
            DB::commit();

            return redirect()->route('procurements.index')
                ->with('success', 'Pengajuan dengan kode ' . $procurement->kode_pengajuan . ' berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('procurements.index')
                ->with('error', 'Terjadi kesalahan saat menghapus: ' . $e->getMessage());
        }
    }

    // Submit pengajuan
    public function submit($id)
    {
        $procurement = ProcurementRequest::findOrFail($id);
        
        if (!$procurement->isDraft()) {
            return redirect()->route('procurements.index')
                ->with('error', 'Pengajuan sudah diajukan sebelumnya');
        }

        if ($procurement->details()->count() == 0) {
            return redirect()->route('procurements.index')
                ->with('error', 'Pengajuan tidak memiliki item');
        }

        $procurement->update(['status' => 'diajukan']);

        return redirect()->route('procurements.index')
            ->with('success', 'Pengajuan berhasil diajukan ke pimpinan');
    }

    // ==================== TAMBAHKAN METHOD COMPLETE DI SINI ====================
    /**
     * Menyelesaikan pengadaan (status diproses -> selesai)
     * POST /procurements/{id}/complete
     */
    public function complete($id)
    {
        try {
            DB::beginTransaction();

            $procurement = ProcurementRequest::findOrFail($id);
            
            // Cek apakah status sudah diproses
            if ($procurement->status !== 'diproses') {
                return redirect()->route('procurements.show', $procurement->id)
                    ->with('error', 'Pengajuan tidak dapat diselesaikan karena statusnya ' . $procurement->status);
            }

            // Update status menjadi selesai
            $procurement->update(['status' => 'selesai']);

            DB::commit();

            return redirect()->route('procurements.show', $procurement->id)
                ->with('success', 'Pengajuan ' . $procurement->kode_pengajuan . ' telah SELESAI!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('procurements.show', $procurement->id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // API Internal: Get item details for AJAX
    public function getItem($id)
    {
        $item = Item::with('category')->findOrFail($id);
        return response()->json([
            'id' => $item->id,
            'nama_barang' => $item->nama_barang,
            'satuan' => $item->satuan,
            'harga_estimasi_default' => $item->harga_estimasi_default ?? 0,
            'kategori' => $item->category->nama_kategori,
            'spesifikasi' => $item->spesifikasi
        ]);
    }
}