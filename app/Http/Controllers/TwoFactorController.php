<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

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
        
        if (!$user->two_factor_secret) {
            $user->generateTwoFactorSecret();
        }
        
        $qrCodeUrl = $user->getTwoFactorQrCodeUrl();
        $recoveryCodes = $user->getRecoveryCodes();
        
        return view('profile.two-factor-setup', compact('qrCodeUrl', 'recoveryCodes'));
    }

    // Confirm 2FA setup
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        
        if ($user->confirmTwoFactor($request->code)) {
            return redirect()->route('profile.index')
                ->with('success', '✅ Two Factor Authentication berhasil diaktifkan!');
        }

        return back()->with('error', '❌ Kode verifikasi tidak valid. Silakan coba lagi.');
    }

    // Disable 2FA
    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->disableTwoFactor();
        
        return redirect()->route('profile.index')
            ->with('success', '❌ Two Factor Authentication berhasil dinonaktifkan.');
    }

    // Regenerate recovery codes
    public function regenerateRecoveryCodes()
    {
        $user = Auth::user();
        $user->regenerateRecoveryCodes();
        
        return back()->with('success', '✅ Kode pemulihan berhasil diperbarui.');
    }

    // Halaman verifikasi 2FA saat login
    public function verifyForm()
    {
        return view('auth.two-factor-verify');
    }

    // Proses verifikasi 2FA saat login
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        
        if ($user->verifyTwoFactor($request->code)) {
            session(['two_factor_verified' => true]);
            
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
}