<?php

namespace App\Http\Controllers;

use App\Services\ModuleStatusService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    private const ACCESS_DENIED_CODES = [1142, 1143, 1044, 1045, 1370];

    public function checkConnection()
    {
        return response()->json([
            'logged_in_as'     => Auth::user()->name ?? 'guest',
            'role'             => Auth::user()->role ?? 'none',
            'using_connection' => DB::getDefaultConnection(),
            'mysql_user'       => $this->currentMysqlUser(),
            'database'         => DB::connection()->getDatabaseName(),
        ]);
    }

    public function testPrivilege(Request $request, $action)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk menguji hak akses.');
        }

        $tests = $this->privilegeTests();

        if (!isset($tests[$action])) {
            return redirect()->back()->with('error', 'Aksi pengujian tidak dikenali.');
        }

        $user       = Auth::user();
        $role       = strtoupper($user->role);
        $connection = DB::getDefaultConnection();
        [$label, $sql, $query] = $tests[$action];

        $result = [
            'action'     => $label,
            'role'       => $role,
            'connection' => $connection,
            'mysql_user' => $this->currentMysqlUser(),
            'sql'        => $sql,
        ];

        if (!ModuleStatusService::privilegeIntegrated($user->role)) {
            return redirect()->back()->with('privilege_test_result', $result + [
                'status'  => 'pending',
                'allowed' => false,
                'message' => 'Masih login ke MySQL sebagai ' . ModuleStatusService::mysqlUser()
                    . ', seharusnya sebagai ' . strtolower($role) . '. Kerjakan Bagian 6.',
            ]);
        }

        try {
            DB::beginTransaction();
            $info = $query();
            DB::rollBack();

            $result += [
                'status'  => 'success',
                'allowed' => true,
                'message' => "Berhasil. {$info}",
            ];
        } catch (QueryException $e) {
            DB::rollBack();

            $denied = in_array($e->errorInfo[1] ?? null, self::ACCESS_DENIED_CODES, true);

            $result += [
                'status'  => 'danger',
                'allowed' => false,
                'message' => $denied
                    ? "Ditolak MySQL: " . ($e->errorInfo[2] ?? $e->getMessage())
                    : 'Error: ' . ($e->errorInfo[2] ?? $e->getMessage()),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            $result += [
                'status'  => 'danger',
                'allowed' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }

        return redirect()->back()->with('privilege_test_result', $result);
    }

    private function privilegeTests(): array
    {
        return [
            'select' => [
                'SELECT (Membaca Data)',
                'SELECT COUNT(*) FROM customers, products, transactions',
                function () {
                    $c = DB::table('customers')->count();
                    $p = DB::table('products')->count();
                    $t = DB::table('transactions')->count();

                    return "customers: {$c}, products: {$p}, transactions: {$t}.";
                },
            ],
            'insert' => [
                'INSERT (Menambah Data Pesanan)',
                'INSERT INTO transactions (...) VALUES (...)',
                function () {
                    $customerId = DB::table('customers')->value('id');

                    if (!$customerId) {
                        throw new \RuntimeException('Tabel customers kosong, jalankan seeder.');
                    }

                    DB::table('transactions')->insert([
                        'customer_id' => $customerId,
                        'user_id'     => Auth::id(),
                        'tanggal'     => now(),
                        'total_harga' => 0,
                        'status'      => 'pending',
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);

                    return 'Data di-rollback.';
                },
            ],
            'update' => [
                'UPDATE (Memperbarui Data Transaksi)',
                'UPDATE transactions SET updated_at = updated_at LIMIT 1',
                function () {
                    DB::update('UPDATE transactions SET updated_at = updated_at LIMIT 1');

                    return 'Data di-rollback.';
                },
            ],
            'delete' => [
                'DELETE (Menghapus Data Master/Toko)',
                'DELETE FROM products WHERE 1 = 0',
                function () {
                    DB::delete('DELETE FROM products WHERE 1 = 0');

                    return 'Tidak ada baris yang terhapus.';
                },
            ],
            'manage_users' => [
                'MANAJEMEN USER (Ubah Tabel users)',
                'UPDATE users SET updated_at = updated_at WHERE 1 = 0',
                function () {
                    DB::update('UPDATE users SET updated_at = updated_at WHERE 1 = 0');

                    return 'Tidak ada baris yang berubah.';
                },
            ],
        ];
    }

    private function currentMysqlUser(): string
    {
        try {
            return (string) DB::selectOne('SELECT CURRENT_USER() AS u')->u;
        } catch (\Throwable $e) {
            return '-';
        }
    }
}
