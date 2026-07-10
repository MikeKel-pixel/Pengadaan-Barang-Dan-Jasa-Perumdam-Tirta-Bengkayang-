<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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

        $user = Auth::user();

        // ==================== CEK 2FA ====================
        if ($user->hasTwoFactorEnabled()) {
            // Kirim kode 2FA ke email
            $user->sendTwoFactorCode();

            // Redirect ke halaman verifikasi 2FA
            return redirect()->route('two-factor.verify')
                ->with('info', 'Kode verifikasi telah dikirim ke email Anda. Masukkan kode tersebut untuk melanjutkan.');
        }

        // Jika tidak pakai 2FA, langsung redirect sesuai role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Redirect berdasarkan role user
     */
    protected function redirectBasedOnRole($user): RedirectResponse
    {
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