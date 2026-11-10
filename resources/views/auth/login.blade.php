@extends('layouts.app')

@section('title', 'Login - TokoKita MSBD')
@section('hide-nav', true)

@section('content')

    <div class="min-h-[85vh] flex flex-col items-center justify-center px-4 py-8">
        
        <div class="w-full max-w-md">
            <div class="neu-card p-6 sm:p-8 space-y-6">
                
                <!-- Brand Header TokoKita (Pindah ke dalam Card) -->
                <div class="text-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center space-x-3 group">
                        <div class="w-12 h-12 neu-pill rounded-2xl flex items-center justify-center text-blue-500 font-bold group-hover:scale-105 transition transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <span class="text-xl font-extrabold text-slate-800 tracking-tight block">TokoKita</span>
                            <span class="text-xs font-medium text-slate-500 block -mt-1">Sistem Operasional Basis Data</span>
                        </div>
                    </a>
                </div>

                <!-- Title & Description -->
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Login Sistem TokoKita</h1>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Masuk ke sistem menggunakan <strong>Username</strong> dan password sesuai data seeder praktikum.
                    </p>
                </div>

                <!-- Form Login -->
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Username Pengguna</label>
                        <input type="text" name="username" id="input_username" value="{{ old('username', 'Kasir') }}" required autofocus
                            placeholder="Masukkan username (contoh: Kasir atau Admin)"
                            class="w-full text-xs font-medium text-slate-700 neu-input-inset rounded-xl p-3.5 outline-none border-0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Password</label>
                        <input type="password" name="password" id="input_password" value="kasir" required
                            placeholder="Masukkan password"
                            class="w-full text-xs font-medium text-slate-700 neu-input-inset rounded-xl p-3.5 outline-none border-0">
                    </div>
                    <button type="submit" class="neu-btn-primary w-full text-white font-bold py-3.5 px-6 rounded-xl text-xs transition duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span>Masuk ke Sistem</span>
                    </button>
                </form>

                <!-- Box Akses Cepat (Tanpa Judul Header & Tanpa Icon) -->
                <div class="neu-inset p-3.5 rounded-xl">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <a href="{{ route('user.switch', 'kasir') }}" class="neu-pill p-3 rounded-xl hover:bg-blue-50/60 transition flex items-center justify-between group">
                            <div class="text-left">
                                <span class="font-bold text-xs text-slate-800 block group-hover:text-blue-600">Kasir Sales</span>
                                <span class="text-[10px] text-slate-500 font-mono">user: <strong>Kasir</strong> | pass: kasir</span>
                            </div>
                            <span class="neu-pill px-2 py-0.5 rounded text-[10px] font-bold text-blue-600 uppercase">Kasir</span>
                        </a>
                        <a href="{{ route('user.switch', 'admin') }}" class="neu-pill p-3 rounded-xl hover:bg-indigo-50/60 transition flex items-center justify-between group">
                            <div class="text-left">
                                <span class="font-bold text-xs text-slate-800 block group-hover:text-indigo-600">Admin Toko</span>
                                <span class="text-[10px] text-slate-500 font-mono">user: <strong>Admin</strong> | pass: admin</span>
                            </div>
                            <span class="neu-pill px-2 py-0.5 rounded text-[10px] font-bold text-indigo-600 uppercase">Admin</span>
                        </a>
                    </div>
                </div>

                <!-- Navigasi Kembali -->
                <div class="pt-1 text-center">
                    <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Dashboard Utama</span>
                    </a>
                </div>

            </div>
        </div>
    </div>

@endsection