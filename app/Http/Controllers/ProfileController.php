<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan halaman profil
    public function index()
    {
        $user = Auth::user();
        $supplier = null;
        
        // Jika user adalah vendor, cari data supplier terkait
        if ($user->hasRole('vendor')) {
            $supplier = Supplier::where('email', $user->email)->first();
        }
        
        return view('profile.index', compact('user', 'supplier'));
    }

    // Menampilkan form edit profil
    public function edit()
    {
        $user = Auth::user();
        $supplier = null;
        
        if ($user->hasRole('vendor')) {
            $supplier = Supplier::where('email', $user->email)->first();
        }
        
        return view('profile.edit', compact('user', 'supplier'));
    }

    // Update profil
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Jika user adalah vendor, update juga data supplier
        if ($user->hasRole('vendor')) {
            $supplier = Supplier::where('email', $user->getOriginal('email'))->first();
            if ($supplier) {
                $supplier->update([
                    'nama_supplier' => $request->name,
                    'email' => $request->email,
                    'telepon' => $request->telepon ?? $supplier->telepon,
                    'alamat' => $request->alamat ?? $supplier->alamat,
                    'pic' => $request->pic ?? $supplier->pic,
                ]);
            }
        }

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui');
    }

    // Menampilkan form ganti password
    public function changePasswordForm()
    {
        return view('profile.change-password');
    }

    // Proses ganti password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini tidak sesuai');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password berhasil diubah');
    }

    // ==================== UPLOAD FOTO PROFIL (DIPERBAIKI) ====================
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->photo && Storage::exists('public/photos/' . $user->photo)) {
            Storage::delete('public/photos/' . $user->photo);
        }

        // Upload foto baru
        $fileName = time() . '_' . $user->id . '.' . $request->photo->extension();
        $request->photo->storeAs('public/photos', $fileName);

        // Update database
        $user->update(['photo' => $fileName]);

        return back()->with('success', 'Foto profil berhasil diupload');
    }

    // ==================== HAPUS FOTO PROFIL ====================
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->photo && Storage::exists('public/photos/' . $user->photo)) {
            Storage::delete('public/photos/' . $user->photo);
        }

        $user->update(['photo' => null]);

        return back()->with('success', 'Foto profil berhasil dihapus');
    }
}