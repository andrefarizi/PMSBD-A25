<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up():void
    {
        DB::unprepared(
            "DROP TRIGGER IF EXISTS trg_after_transaksi_insert"
        );

        DB::unprepared(
            "DROP TRIGGER IF EXISTS trg_after_transaksi_update"
        );

        // 1. Trigger setelah transaksi baru dibuat
        DB::unprepared("
            CREATE TRIGGER trg_after_transaksi_insert
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                INSERT INTO log_aktivitas (
                    transaksi_id,
                    aksi,
                    keterangan,
                    created_at
                )
                VALUES (
                    NEW.id,
                    'PESANAN_BARU',
                    CONCAT(
                        'Pesanan baru ID #',
                        NEW.id,
                        ' dibuat dengan total Rp ',
                        FORMAT(NEW.total_harga, 0),
                        ' (Status: ',
                        NEW.status,
                        ')'
                    ),
                    NOW()
                );
            END
        ");

        // 2. Trigger setelah status transaksi berubah
        DB::unprepared("
            CREATE TRIGGER trg_after_transaksi_update
            AFTER UPDATE ON transactions
            FOR EACH ROW
            BEGIN
                IF OLD.status <> NEW.status THEN
                    INSERT INTO log_aktivitas (
                        transaksi_id,
                        aksi,
                        keterangan,
                        created_at
                    )
                    VALUES (
                        NEW.id,
                        'STATUS_PESANAN_BERUBAH',
                        CONCAT(
                            'Status pesanan ID #',
                            NEW.id,
                            ' berubah dari [',
                            OLD.status,
                            '] menjadi [',
                            NEW.status,
                            ']'
                        ),
                        NOW()
                    );
                END IF;
            END
        ");
    }

    public function down():void
    {
        DB::unprepared(
            "DROP TRIGGER IF EXISTS trg_after_transaksi_insert"
        );

        DB::unprepared(
            "DROP TRIGGER IF EXISTS trg_after_transaksi_update"
        );
    }
};