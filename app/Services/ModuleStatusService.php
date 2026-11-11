<?php

namespace App\Services;

use App\Http\Controllers\CancelTransactionController;
use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

// Tidak ada perubahan yang diperlukan di file ini.
class ModuleStatusService
{
    public static function check(): array
    {
        return [
            // Penomoran mengikuti urutan modul di Notion.
            1 => self::checkProcedure(),
            2 => self::checkFunction(),
            3 => self::checkView(),
            4 => self::checkTrigger(),
            5 => self::checkTransaction(),
            6 => self::checkUserManagement(),
        ];
    }

    public static function privilegeIntegrated(?string $role): bool
    {
        if (!in_array($role, ['kasir', 'admin'], true)) {
            return false;
        }

        return DB::getDefaultConnection() === 'mysql_' . $role;
    }

    public static function mysqlUser(): string
    {
        try {
            return explode('@', (string) DB::selectOne('SELECT CURRENT_USER() AS u')->u)[0];
        } catch (\Throwable $e) {
            return '-';
        }
    }

    private static function methodSource(string $class, string $method): string
    {
        try {
            $ref   = new \ReflectionMethod($class, $method);
            $lines = file($ref->getFileName());

            return implode('', array_slice(
                $lines,
                $ref->getStartLine() - 1,
                $ref->getEndLine() - $ref->getStartLine() + 1
            ));
        } catch (\Throwable $e) {
            return '';
        }
    }

    private static function checkTransaction(): array
    {
        $code     = self::methodSource(TransactionController::class, 'storeTransaction');
        $hasBegin = str_contains($code, 'DB::beginTransaction');
        $hasCommit   = str_contains($code, 'DB::commit');
        $hasRollback = str_contains($code, 'DB::rollBack');

        $active  = false;
        $message = 'storeTransaction() belum memakai DB::beginTransaction().';

        if ($hasBegin && $hasCommit && $hasRollback) {
            $active  = true;
            $message = 'COMMIT & ROLLBACK aktif.';
        } elseif ($hasBegin) {
            $message = 'DB::commit() atau DB::rollBack() belum ada.';
        }

        return self::result('5. TRANSAKSI (COMMIT / ROLLBACK)', $active, $message);
    }

    private static function checkProcedure(): array
    {
        $hasProcedure = false;
        try {
            $dbName = DB::connection()->getDatabaseName();
            $res = DB::select("SHOW PROCEDURE STATUS WHERE Db = ? AND Name = 'log_pembatalan_procedure'", [$dbName]);
            $hasProcedure = !empty($res);
        } catch (\Throwable $e) {}

        $code    = self::methodSource(CancelTransactionController::class, 'cancelTransaction');
        $hasCall = str_contains($code, 'log_pembatalan_procedure');

        if (!$hasProcedure) {
            return self::result('1. STORED PROCEDURE', false, 'Procedure log_pembatalan_procedure belum dibuat.');
        }

        if (!$hasCall) {
            return self::result('1. STORED PROCEDURE', false, 'Procedure belum dipanggil di cancelTransaction().');
        }

        return self::result('1. STORED PROCEDURE', true, 'Procedure aktif.');
    }

    private static function checkView(): array
    {
        $active  = false;
        $message = 'View v_transaksi_sukses belum dibuat.';

        try {
            $views = collect(DB::select("SHOW FULL TABLES WHERE Table_type = 'VIEW'"))
                ->map(fn ($item) => array_values((array) $item)[0])
                ->toArray();

            if (in_array('v_transaksi_sukses', $views)) {
                try {
                    DB::table('v_transaksi_sukses')->limit(1)->get();
                    $active  = true;
                    $message = 'View aktif.';
                } catch (\Throwable $err) {
                    $message = 'View error (' . $err->getMessage() . ').';
                }
            }
        } catch (\Throwable $e) {}

        return self::result('3. DATABASE VIEW', $active, $message);
    }

    private static function checkFunction(): array
    {
        $hasDbFunction = false;
        try {
            $dbName = DB::connection()->getDatabaseName();
            $funcs = DB::select("SHOW FUNCTION STATUS WHERE Db = ? AND Name = 'calculate_today_revenue'", [$dbName]);
            $hasDbFunction = !empty($funcs);
        } catch (\Throwable $e) {}

        if (!$hasDbFunction) {
            return self::result('2. DATABASE FUNCTION', false, 'Function calculate_today_revenue() belum dibuat.');
        }

        if (!method_exists(Transaction::class, 'getTodayRevenue')) {
            return self::result('2. DATABASE FUNCTION', false, 'Method getTodayRevenue() belum ada di Model Transaction.');
        }

        try {
            DB::selectOne('SELECT calculate_today_revenue() AS total');
            return self::result('2. DATABASE FUNCTION', true, 'Function aktif.');
        } catch (\Throwable $err) {
            return self::result('2. DATABASE FUNCTION', false, 'Function error (' . $err->getMessage() . ').');
        }
    }

    private static function checkTrigger(): array
    {
        $active  = false;
        $message = 'Trigger trg_after_transaksi_insert belum dibuat.';

        try {
            if (!empty(DB::select("SHOW TRIGGERS WHERE `Trigger` = 'trg_after_transaksi_insert'"))) {
                $active  = true;
                $message = 'Trigger aktif.';
            }
        } catch (\Throwable $e) {}

        return self::result('4. DATABASE TRIGGER', $active, $message);
    }

    private static function checkUserManagement(): array
    {
        $name     = '6. USER MANAGEMENT & GRANT';
        $hasKasir = config()->has('database.connections.mysql_kasir');
        $hasAdmin = config()->has('database.connections.mysql_admin');

        if (!$hasKasir && !$hasAdmin) {
            return self::result($name, false, 'Koneksi mysql_kasir & mysql_admin belum ada di config/database.php.');
        }
        if (!$hasKasir) {
            return self::result($name, false, 'Koneksi mysql_kasir belum ada di config/database.php.');
        }
        if (!$hasAdmin) {
            return self::result($name, false, 'Koneksi mysql_admin belum ada di config/database.php.');
        }

        try {
            DB::connection('mysql_kasir')->getPdo();
        } catch (\Throwable $e) {
            return self::result($name, false, 'Gagal login MySQL sebagai kasir (' . $e->getMessage() . ').');
        }

        try {
            DB::connection('mysql_admin')->getPdo();
        } catch (\Throwable $e) {
            return self::result($name, false, 'Gagal login MySQL sebagai admin (' . $e->getMessage() . ').');
        }

        return self::result($name, true, 'User MySQL kasir & admin aktif.');
    }

    private static function result(string $name, bool $active, string $message): array
    {
        return [
            'name'      => $name,
            'is_active' => $active,
            'message'   => $message,
        ];
    }
}
