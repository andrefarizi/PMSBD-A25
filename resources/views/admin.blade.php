@extends('layouts.app')

@section('title', 'Panel Admin - TokoKita')

@section('content')

    <div class="neu-card p-6 sm:p-7 space-y-5">
        <div class="pb-4 border-b border-slate-200/60">
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Panel Admin</h1>
            <p class="text-xs text-slate-500 mt-1">Hak akses: ALL PRIVILEGES</p>
        </div>

        @include('partials.privilege-checker', [
            'roleLabel' => 'ADMIN',
            'privItems' => [
                ['action'=>'select',       'label'=>'1. SELECT',    'desc'=>'Baca semua data database', 'expected'=>true],
                ['action'=>'insert',       'label'=>'2. INSERT',    'desc'=>'Tambah data apapun',       'expected'=>true],
                ['action'=>'update',       'label'=>'3. UPDATE',    'desc'=>'Ubah data apapun',         'expected'=>true],
                ['action'=>'delete',       'label'=>'4. DELETE',    'desc'=>'Hapus data apapun',        'expected'=>true],
                ['action'=>'manage_users', 'label'=>'5. USER MGMT', 'desc'=>'Ubah data akun pengguna',  'expected'=>true],
            ],
        ])
    </div>

@endsection
