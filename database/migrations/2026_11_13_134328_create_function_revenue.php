<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up():void
    {
        DB::unprepared(
            "DROP FUNCTION IF EXISTS calculate_today_revenue"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS calculate_month_revenue"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS calculate_total_revenue"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS calculate_user_revenue"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS count_today_transactions"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS count_month_transactions"
        );

        // 1. Omzet hari ini
        DB::unprepared("
            CREATE FUNCTION calculate_today_revenue()
            RETURNS DECIMAL(15,2)
            NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_total DECIMAL(15,2) DEFAULT 0.00;

                SELECT COALESCE(SUM(total_harga), 0.00)
                INTO v_total
                FROM transactions
                WHERE tanggal >= CURDATE()
                  AND tanggal < CURDATE() + INTERVAL 1 DAY
                  AND status != 'dibatalkan';

                RETURN v_total;
            END
        ");

        // 2. Omzet bulan ini
        DB::unprepared("
            CREATE FUNCTION calculate_month_revenue()
            RETURNS DECIMAL(15,2)
            NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_total DECIMAL(15,2) DEFAULT 0.00;

                SELECT COALESCE(SUM(total_harga), 0.00)
                INTO v_total
                FROM transactions
                WHERE tanggal >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                  AND tanggal < DATE_FORMAT(
                      CURDATE() + INTERVAL 1 MONTH, '%Y-%m-01'
                  )
                  AND status != 'dibatalkan';

                RETURN v_total;
            END
        ");

        // 3. Total seluruh omzet
        DB::unprepared("
            CREATE FUNCTION calculate_total_revenue()
            RETURNS DECIMAL(15,2)
            NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_total DECIMAL(15,2) DEFAULT 0.00;

                SELECT COALESCE(SUM(total_harga), 0.00)
                INTO v_total
                FROM transactions
                WHERE status != 'dibatalkan';

                RETURN v_total;
            END
        ");

        // 4. Omzet berdasarkan kasir
        DB::unprepared("
            CREATE FUNCTION calculate_user_revenue(p_user_id INT)
            RETURNS DECIMAL(15,2)
            NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_total DECIMAL(15,2) DEFAULT 0.00;

                SELECT COALESCE(SUM(total_harga), 0.00)
                INTO v_total
                FROM transactions
                WHERE user_id = p_user_id
                  AND status != 'dibatalkan';

                RETURN v_total;
            END
        ");

        // 5. Jumlah transaksi hari ini
        DB::unprepared("
            CREATE FUNCTION count_today_transactions()
            RETURNS INT
            NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_count INT DEFAULT 0;

                SELECT COUNT(*)
                INTO v_count
                FROM transactions
                WHERE tanggal >= CURDATE()
                  AND tanggal < CURDATE() + INTERVAL 1 DAY
                  AND status != 'dibatalkan';

                RETURN v_count;
            END
        ");

        // 6. Jumlah transaksi bulan ini
        DB::unprepared("
            CREATE FUNCTION count_month_transactions()
            RETURNS INT
            NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_count INT DEFAULT 0;

                SELECT COUNT(*)
                INTO v_count
                FROM transactions
                WHERE tanggal >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                  AND tanggal < DATE_FORMAT(
                      CURDATE() + INTERVAL 1 MONTH, '%Y-%m-01'
                  )
                  AND status != 'dibatalkan';

                RETURN v_count;
            END
        ");
    }

    public function down():void
    {
        DB::unprepared(
            "DROP FUNCTION IF EXISTS calculate_today_revenue"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS calculate_month_revenue"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS calculate_total_revenue"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS calculate_user_revenue"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS count_today_transactions"
        );

        DB::unprepared(
            "DROP FUNCTION IF EXISTS count_month_transactions"
        );
    }
};