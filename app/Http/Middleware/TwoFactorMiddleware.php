<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Cek apakah user login dan memiliki 2FA aktif
        if ($user && $user->hasTwoFactorEnabled() && !session('two_factor_verified')) {
            // Jangan redirect ke verify jika sudah di halaman verify
            if (!$request->routeIs('two-factor.verify') && !$request->routeIs('two-factor.verify.submit')) {
                return redirect()->route('two-factor.verify');
            }
        }
        
        return $next($request);
    }
}