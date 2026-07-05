<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->hasRole('admin')) {
                return redirect('/admin');
            } elseif ($user->hasRole('pengadaan')) {
                return redirect('/pengadaan');
            } elseif ($user->hasRole('pimpinan')) {
                return redirect('/pimpinan');
            } elseif ($user->hasRole('vendor')) {
                return redirect('/vendor');
            }
        }
        
        return $next($request);
    }
}