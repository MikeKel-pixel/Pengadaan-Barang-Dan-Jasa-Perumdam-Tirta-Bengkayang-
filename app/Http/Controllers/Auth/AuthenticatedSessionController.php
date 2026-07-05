<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ==================== CEK 2FA & REDIRECT ====================
        $user = Auth::user();
        
        // Jika user memiliki 2FA aktif, redirect ke halaman verifikasi
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.verify');
        }
        
        // Jika tidak, redirect sesuai role
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
        
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}