<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'kasir')->update(['password' => 'kasir']);
        DB::table('users')->where('role', 'admin')->update(['password' => 'admin']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'kasir')->update(['password' => Hash::make('kasir')]);
        DB::table('users')->where('role', 'admin')->update(['password' => Hash::make('admin')]);
    }
};
