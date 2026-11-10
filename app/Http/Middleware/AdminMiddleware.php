<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses Panel Admin.');
        }

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Akses Ditolak: Halaman/Fitur ini hanya dapat diakses oleh Admin Toko.');
        }

        return $next($request);
    }
}
