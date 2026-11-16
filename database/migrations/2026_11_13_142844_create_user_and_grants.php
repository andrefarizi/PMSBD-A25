<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $db = config('database.connections.mysql.database');

        $kasir     = env('DB_USERNAME_KASIR', 'kasir');
        $kasirPass = env('DB_PASSWORD_KASIR', 'kasir');
        $admin     = env('DB_USERNAME_ADMIN', 'admin');
        $adminPass = env('DB_PASSWORD_ADMIN', 'admin');

        // 1. Membuat user MySQL
        DB::unprepared("CREATE USER IF NOT EXISTS '{$kasir}'@'localhost' IDENTIFIED BY '{$kasirPass}'");
        DB::unprepared("CREATE USER IF NOT EXISTS '{$admin}'@'localhost' IDENTIFIED BY '{$adminPass}'");

        // 2. Hak akses kasir: diberikan per tabel (least privilege)
        $kasirGrants = [
            'customers'           => 'SELECT',
            'products'            => 'SELECT',
            'users'               => 'SELECT',
            'transactions'        => 'SELECT, INSERT, UPDATE',
            'transaction_details' => 'SELECT, INSERT',
            'log_pembatalan'      => 'SELECT',
            'log_aktivitas'       => 'SELECT',
            'v_transaksi_sukses'  => 'SELECT',
            'sessions'            => 'SELECT, INSERT, UPDATE, DELETE',
        ];

        foreach ($kasirGrants as $table => $privileges) {
            DB::unprepared("GRANT {$privileges} ON `{$db}`.`{$table}` TO '{$kasir}'@'localhost'");
        }

        // Izin menjalankan Stored Procedure & Stored Function
        DB::unprepared("GRANT EXECUTE ON `{$db}`.* TO '{$kasir}'@'localhost'");

        // 3. Hak akses admin: penuh, tetapi hanya pada database praktikum
        DB::unprepared("GRANT ALL PRIVILEGES ON `{$db}`.* TO '{$admin}'@'localhost'");
    }

    public function down(): void
    {
        $kasir = env('DB_USERNAME_KASIR', 'kasir');
        $admin = env('DB_USERNAME_ADMIN', 'admin');

        DB::unprepared("DROP USER IF EXISTS '{$kasir}'@'localhost'");
        DB::unprepared("DROP USER IF EXISTS '{$admin}'@'localhost'");
    }
};