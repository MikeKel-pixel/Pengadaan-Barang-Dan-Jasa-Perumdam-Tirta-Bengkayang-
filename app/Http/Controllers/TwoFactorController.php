<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Halaman setup 2FA
    public function setup()
    {
        $user = Auth::user();
        return view('profile.two-factor-setup', compact('user'));
    }

    // Aktifkan 2FA
    public function enable(Request $request)
    {
        $user = Auth::user();
        $user->enableTwoFactor();

        return redirect()->route('profile.index')
            ->with('success', '✅ Two Factor Authentication berhasil diaktifkan!');
    }

    // Nonaktifkan 2FA
    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->disableTwoFactor();

        return redirect()->route('profile.index')
            ->with('success', '❌ Two Factor Authentication berhasil dinonaktifkan.');
    }

    // Halaman verifikasi 2FA saat login
    public function verifyForm()
    {
        return view('auth.two-factor-verify');
    }

    // Proses verifikasi 2FA
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();

        // Cek kode 2FA
        if ($user->verifyTwoFactorCode($request->code)) {
            // Hapus kode setelah digunakan
            $user->clearTwoFactorCode();

            // Set session bahwa user sudah lolos 2FA
            session(['two_factor_verified' => true]);

            // Redirect berdasarkan role
            if ($user->hasRole('admin')) {
                return redirect('/admin');
            } elseif ($user->hasRole('pengadaan')) {
                return redirect('/pengadaan');
            } elseif ($user->hasRole('pimpinan')) {
                return redirect('/pimpinan');
            } elseif ($user->hasRole('vendor')) {
                return redirect('/vendor');
            } elseif ($user->hasRole('user')) {
                return redirect('/user-dashboard');
            }

            return redirect('/dashboard');
        }

        return back()->with('error', '❌ Kode verifikasi tidak valid. Silakan coba lagi.');
    }

    // Kirim ulang kode 2FA
    public function resend()
    {
        $user = Auth::user();
        $user->sendTwoFactorCode();

        return back()->with('info', '📧 Kode verifikasi baru telah dikirim ke email Anda.');
    }
}