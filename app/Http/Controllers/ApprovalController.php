<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:pimpinan');
    }

    // Menampilkan daftar pengajuan yang menunggu persetujuan
    public function index()
    {
        $procurements = ProcurementRequest::with(['user', 'details.item'])
                        ->where('status', 'diajukan')
                        ->latest()
                        ->paginate(10);
        
        return view('approvals.index', compact('procurements'));
    }

    // Menampilkan detail pengajuan untuk approval
    public function show($id)
    {
        $procurement = ProcurementRequest::with(['user', 'details.item.category', 'approvals.user'])
                        ->findOrFail($id);
        
        // Cek apakah status sudah diajukan
        if ($procurement->status !== 'diajukan') {
            return redirect()->route('approvals.index')
                ->with('error', 'Pengajuan ini tidak dapat diproses karena statusnya ' . $procurement->status);
        }
        
        return view('approvals.show', compact('procurement'));
    }

    // Proses approval (setujui)
    public function approve(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $procurement = ProcurementRequest::findOrFail($id);
            
            // Cek status
            if ($procurement->status !== 'diajukan') {
                return redirect()->route('approvals.index')
                    ->with('error', 'Pengajuan sudah diproses sebelumnya');
            }

            // Update status procurement
            $procurement->update(['status' => 'disetujui']);

            // Simpan approval record
            Approval::create([
                'procurement_request_id' => $procurement->id,
                'user_id' => auth()->id(),
                'status' => 'disetujui',
                'catatan' => $request->catatan,
                'tanggal_approval' => now()
            ]);

            DB::commit();

            return redirect()->route('approvals.index')
                ->with('success', 'Pengajuan ' . $procurement->kode_pengajuan . ' telah DISETUJUI');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ==================== PROSES TOLAK (PERBAIKAN) ====================
    public function reject(Request $request, $id)
    {
        // Validasi: catatan WAJIB diisi minimal 5 karakter
        $request->validate([
            'catatan' => 'required|string|min:5|max:500'
        ], [
            'catatan.required' => 'Catatan penolakan harus diisi',
            'catatan.min' => 'Catatan penolakan minimal 5 karakter',
            'catatan.max' => 'Catatan penolakan maksimal 500 karakter'
        ]);

        try {
            DB::beginTransaction();

            $procurement = ProcurementRequest::findOrFail($id);
            
            // Cek status
            if ($procurement->status !== 'diajukan') {
                return redirect()->route('approvals.index')
                    ->with('error', 'Pengajuan sudah diproses sebelumnya');
            }

            // Update status procurement menjadi DITOLAK
            $procurement->update(['status' => 'ditolak']);

            // Simpan approval record dengan status ditolak
            Approval::create([
                'procurement_request_id' => $procurement->id,
                'user_id' => auth()->id(),
                'status' => 'ditolak',
                'catatan' => $request->catatan,
                'tanggal_approval' => now()
            ]);

            DB::commit();

            return redirect()->route('approvals.index')
                ->with('success', 'Pengajuan ' . $procurement->kode_pengajuan . ' telah DITOLAK');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan riwayat approval (pengajuan yang sudah diproses)
    public function history()
    {
        $procurements = ProcurementRequest::with(['user', 'approvals.user'])
                        ->whereIn('status', ['disetujui', 'ditolak', 'diproses', 'selesai'])
                        ->latest()
                        ->paginate(10);
        
        return view('approvals.history', compact('procurements'));
    }
}