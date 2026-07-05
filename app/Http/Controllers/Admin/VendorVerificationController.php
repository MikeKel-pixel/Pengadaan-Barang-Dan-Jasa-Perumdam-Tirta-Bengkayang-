<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    // Menampilkan daftar vendor yang menunggu verifikasi
    public function index()
    {
        $pendingVendors = Supplier::with('verifier')
                        ->pending()
                        ->latest('registered_at')
                        ->paginate(10);
        
        $verifiedVendors = Supplier::verified()
                        ->latest('verified_at')
                        ->paginate(10);
        
        $rejectedVendors = Supplier::where('status', 'rejected')
                        ->latest()
                        ->paginate(10);
        
        return view('admin.vendors.verification', compact('pendingVendors', 'verifiedVendors', 'rejectedVendors'));
    }

    // Menampilkan detail vendor
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.vendors.show', compact('supplier'));
    }

    // Verifikasi vendor (setujui)
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $supplier = Supplier::findOrFail($id);
            
            if (!$supplier->isPending()) {
                return redirect()->route('admin.vendors.verification')
                    ->with('error', 'Vendor ini sudah diproses sebelumnya.');
            }

            // Update status supplier
            $supplier->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'rejection_reason' => null
            ]);

            // Update role user menjadi vendor
            $user = User::where('email', $supplier->email)->first();
            if ($user) {
                $user->removeRole('user');
                $user->assignRole('vendor');
            }

            DB::commit();

            return redirect()->route('admin.vendors.verification')
                ->with('success', 'Vendor ' . $supplier->nama_supplier . ' berhasil diverifikasi. User telah menjadi vendor.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Tolak verifikasi vendor
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter'
        ]);

        try {
            DB::beginTransaction();

            $supplier = Supplier::findOrFail($id);
            
            if (!$supplier->isPending()) {
                return redirect()->route('admin.vendors.verification')
                    ->with('error', 'Vendor ini sudah diproses sebelumnya.');
            }

            // Update status supplier menjadi rejected
            $supplier->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            // User tetap sebagai user biasa (tidak diubah role-nya)

            DB::commit();

            return redirect()->route('admin.vendors.verification')
                ->with('success', 'Pendaftaran vendor ' . $supplier->nama_supplier . ' ditolak.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Hapus data vendor yang ditolak
    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            
            // Hanya vendor dengan status rejected atau pending yang bisa dihapus
            if (!in_array($supplier->status, ['pending', 'rejected'])) {
                return redirect()->route('admin.vendors.verification')
                    ->with('error', 'Vendor yang sudah diverifikasi tidak dapat dihapus.');
            }
            
            $supplier->delete();
            
            return redirect()->route('admin.vendors.verification')
                ->with('success', 'Data vendor berhasil dihapus.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}