<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|pengadaan');
    }

    // Menampilkan daftar supplier
    public function index()
    {
        $suppliers = Supplier::with('verifier')->latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    // Menampilkan form tambah supplier
    public function create()
    {
        return view('suppliers.create');
    }

    // Menyimpan supplier baru
    public function store(Request $request)
    {
        // ========== VALIDASI LENGKAP ==========
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:suppliers,email', // ✅ Verifikasi 1: UNIQUE
            'pic' => 'nullable|string|max:255'
        ], [
            // Pesan error custom
            'nama_supplier.required' => 'Nama supplier wajib diisi',
            'nama_supplier.max' => 'Nama supplier maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar sebagai supplier lain', // ✅ Pesan error unique
            'telepon.max' => 'Nomor telepon maksimal 20 karakter',
        ]);

        try {
            DB::beginTransaction();

            $supplier = Supplier::create([
                'nama_supplier' => $request->nama_supplier,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'pic' => $request->pic,
                'status' => 'verified', // Supplier yang ditambahkan admin langsung terverifikasi
                'registered_at' => now(),
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan form edit supplier
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    // Update supplier
    public function update(Request $request, Supplier $supplier)
    {
        // ========== VALIDASI LENGKAP ==========
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $supplier->id, // ✅ Verifikasi 1: UNIQUE (ignore sendiri)
            'pic' => 'nullable|string|max:255'
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi',
            'nama_supplier.max' => 'Nama supplier maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar sebagai supplier lain',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter',
        ]);

        try {
            DB::beginTransaction();

            $supplier->update([
                'nama_supplier' => $request->nama_supplier,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'pic' => $request->pic,
            ]);

            DB::commit();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Hapus supplier
    public function destroy(Supplier $supplier)
    {
        // Cek apakah supplier memiliki relasi dengan vendor_quotes
        if ($supplier->vendorQuotes()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak dapat dihapus karena sudah memiliki riwayat penawaran');
        }

        try {
            $supplier->delete();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier berhasil dihapus');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ==================== FITUR VERIFIKASI VENDOR ====================
    
    // Menampilkan detail supplier untuk verifikasi
    public function show($id)
    {
        $supplier = Supplier::with('verifier')->findOrFail($id);
        return view('suppliers.show', compact('supplier'));
    }

    // Verifikasi/Setujui vendor
    public function verify($id)
    {
        try {
            DB::beginTransaction();

            $supplier = Supplier::findOrFail($id);
            
            // Cek apakah sudah terverifikasi
            if ($supplier->isVerified()) {
                return redirect()->route('suppliers.index')
                    ->with('warning', 'Vendor ini sudah terverifikasi sebelumnya.');
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
            if ($user && $user->hasRole('user')) {
                $user->removeRole('user');
                $user->assignRole('vendor');
            }

            DB::commit();

            return redirect()->route('suppliers.index')
                ->with('success', 'Vendor ' . $supplier->nama_supplier . ' berhasil diverifikasi. User telah menjadi vendor.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('suppliers.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Tolak vendor
    public function reject(Request $request, $id)
    {
        // ========== VALIDASI CATATAN PENOLAKAN ==========
        $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter'
        ]);

        try {
            DB::beginTransaction();

            $supplier = Supplier::findOrFail($id);
            
            // Cek apakah sudah terverifikasi
            if ($supplier->isVerified()) {
                return redirect()->route('suppliers.index')
                    ->with('warning', 'Vendor ini sudah terverifikasi, tidak dapat ditolak.');
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

            return redirect()->route('suppliers.index')
                ->with('success', 'Pendaftaran vendor ' . $supplier->nama_supplier . ' ditolak.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('suppliers.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}