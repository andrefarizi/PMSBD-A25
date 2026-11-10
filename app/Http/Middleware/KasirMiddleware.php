<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KasirMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses Panel Kasir.');
        }

        if (Auth::user()->role !== 'kasir') {
            return redirect()->route('home')->with('error', 'Akses Ditolak: Halaman/Fitur ini hanya dapat diakses oleh Kasir Sales.');
        }

        return $next($request);
    }
}
