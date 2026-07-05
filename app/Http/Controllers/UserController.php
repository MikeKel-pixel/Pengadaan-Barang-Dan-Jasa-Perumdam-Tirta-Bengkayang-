<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:user');
    }

    // Dashboard User
    public function dashboard()
    {
        // Cek apakah user sudah mendaftar sebagai vendor
        $supplierRegistration = Supplier::where('email', Auth::user()->email)->first();
        $vendorStatus = null;
        
        if ($supplierRegistration) {
            if ($supplierRegistration->isPending()) {
                $vendorStatus = 'pending';
            } elseif ($supplierRegistration->isVerified()) {
                $vendorStatus = 'verified';
            } elseif ($supplierRegistration->isRejected()) {
                $vendorStatus = 'rejected';
            }
        }
        
        // Pengajuan yang sudah selesai
        $completedProcurements = ProcurementRequest::with(['user', 'details.item', 'vendorQuotes.supplier'])
                                ->where('status', 'selesai')
                                ->latest()
                                ->paginate(6);
        
        $totalCompleted = ProcurementRequest::where('status', 'selesai')->count();
        $totalSuppliers = Supplier::count();
        $totalCategories = \App\Models\Category::count();
        
        $ongoingProcurements = ProcurementRequest::with(['user', 'details.item'])
                                ->whereIn('status', ['disetujui', 'diproses'])
                                ->latest()
                                ->limit(5)
                                ->get();
        
        return view('user.dashboard', compact('completedProcurements', 'totalCompleted', 'totalSuppliers', 'totalCategories', 'ongoingProcurements', 'vendorStatus', 'supplierRegistration'));
    }

    // Melihat detail pengadaan
    public function showProcurement($id)
    {
        $procurement = ProcurementRequest::with(['user', 'details.item.category', 'vendorQuotes.supplier'])
                        ->where('status', 'selesai')
                        ->findOrFail($id);
        
        return view('user.procurement-detail', compact('procurement'));
    }

    // Daftar semua pengadaan selesai
    public function procurements()
    {
        $procurements = ProcurementRequest::with(['user', 'details.item'])
                        ->where('status', 'selesai')
                        ->latest()
                        ->paginate(12);
        
        return view('user.procurements', compact('procurements'));
    }

    // Form pendaftaran vendor
    public function registerVendorForm()
    {
        // Cek apakah user sudah terdaftar sebagai supplier
        $existingSupplier = Supplier::where('email', Auth::user()->email)->first();
        
        if ($existingSupplier) {
            if ($existingSupplier->isPending()) {
                return redirect()->route('user.dashboard')
                    ->with('info', 'Pendaftaran vendor Anda sedang menunggu verifikasi admin. Silakan tunggu konfirmasi.');
            } elseif ($existingSupplier->isVerified()) {
                return redirect()->route('vendor.dashboard')
                    ->with('success', 'Anda sudah terverifikasi sebagai vendor. Selamat datang di dashboard vendor!');
            } elseif ($existingSupplier->isRejected()) {
                // Jika ditolak, izinkan mendaftar ulang
                return view('user.register-vendor', ['isRejected' => true, 'oldData' => $existingSupplier]);
            }
        }
        
        return view('user.register-vendor', ['isRejected' => false, 'oldData' => null]);
    }

    // Proses pendaftaran vendor
    public function registerVendor(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'required|string|min:10',
            'telepon' => 'required|string|max:20',
            'pic' => 'required|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'bidang_usaha' => 'nullable|string|max:255',
        ], [
            'nama_supplier.required' => 'Nama perusahaan wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.min' => 'Alamat minimal 10 karakter',
            'telepon.required' => 'Nomor telepon wajib diisi',
            'pic.required' => 'Nama PIC (Person In Charge) wajib diisi',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah sudah ada pendaftaran sebelumnya
            $existingSupplier = Supplier::where('email', Auth::user()->email)->first();
            
            if ($existingSupplier) {
                if ($existingSupplier->isPending()) {
                    return redirect()->route('user.dashboard')
                        ->with('warning', 'Pendaftaran Anda sedang dalam proses verifikasi. Silakan tunggu.');
                } elseif ($existingSupplier->isVerified()) {
                    return redirect()->route('vendor.dashboard')
                        ->with('success', 'Anda sudah terverifikasi sebagai vendor.');
                } elseif ($existingSupplier->isRejected()) {
                    // Update data yang ditolak
                    $existingSupplier->update([
                        'nama_supplier' => $request->nama_supplier,
                        'alamat' => $request->alamat,
                        'telepon' => $request->telepon,
                        'pic' => $request->pic,
                        'npwp' => $request->npwp,
                        'bidang_usaha' => $request->bidang_usaha,
                        'status' => 'pending',
                        'rejection_reason' => null,
                        'registered_at' => now(),
                    ]);
                    
                    DB::commit();
                    return redirect()->route('user.dashboard')
                        ->with('success', 'Pendaftaran ulang vendor berhasil! Silakan tunggu verifikasi dari admin.');
                }
            }

            // Simpan data supplier baru dengan status pending
            Supplier::create([
                'nama_supplier' => $request->nama_supplier,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => Auth::user()->email,
                'pic' => $request->pic,
                'npwp' => $request->npwp,
                'bidang_usaha' => $request->bidang_usaha,
                'status' => 'pending',
                'registered_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('user.dashboard')
                ->with('success', 'Pendaftaran vendor berhasil! Data Anda akan diverifikasi oleh admin. Status Anda saat ini adalah "Menunggu Verifikasi".');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Melihat perkembangan pengadaan (tracking)
    public function trackProgress()
    {
        $ongoingProcurements = ProcurementRequest::with(['user', 'details.item', 'vendorQuotes.supplier'])
                                ->whereIn('status', ['diajukan', 'disetujui', 'diproses'])
                                ->latest()
                                ->paginate(10);
        
        return view('user.track-progress', compact('ongoingProcurements'));
    }

    // Detail perkembangan pengadaan
    public function trackDetail($id)
    {
        $procurement = ProcurementRequest::with(['user', 'details.item.category', 'approvals.user', 'vendorQuotes.supplier'])
                        ->whereIn('status', ['diajukan', 'disetujui', 'diproses', 'selesai'])
                        ->findOrFail($id);
        
        return view('user.track-detail', compact('procurement'));
    }
    
    // Batalkan pendaftaran vendor (kembali menjadi user biasa)
    public function cancelVendorRegistration()
    {
        try {
            DB::beginTransaction();
            
            $supplier = Supplier::where('email', Auth::user()->email)->first();
            
            if (!$supplier) {
                return redirect()->route('user.dashboard')
                    ->with('error', 'Tidak ditemukan pendaftaran vendor.');
            }
            
            if (!$supplier->isPending()) {
                return redirect()->route('user.dashboard')
                    ->with('error', 'Hanya pendaftaran dengan status "Menunggu" yang dapat dibatalkan.');
            }
            
            // Hapus data supplier
            $supplier->delete();
            
            DB::commit();
            
            return redirect()->route('user.dashboard')
                ->with('success', 'Pendaftaran vendor berhasil dibatalkan. Anda kembali menjadi user biasa.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}