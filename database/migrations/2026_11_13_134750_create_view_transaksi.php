<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up():void
    {
        DB::unprepared("
            CREATE OR REPLACE VIEW v_transaksi_sukses AS
            SELECT
                t.id AS transaksi_id,
                c.nama AS nama_customer,
                c.telepon AS telepon_customer,
                u.name AS nama_kasir,
                t.tanggal,
                t.total_harga,
                t.status,
                t.created_at
            FROM transactions t
            INNER JOIN customers c
                ON t.customer_id = c.id
            INNER JOIN users u
                ON t.user_id = u.id
            WHERE t.status = 'selesai'
              AND t.deleted_at IS NULL
        ");
    }

    public function down():void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_transaksi_sukses");
    }
};