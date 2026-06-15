<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'karyawan') {
            // Cek apakah karyawan aktif
            if (!Auth::user()->isActive()) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your account has been suspended. Please contact HR/Admin for more information.');
            }

            return $next($request);
        }

        return redirect('/')->with('error', "You don't have access to this page");
    }
}
