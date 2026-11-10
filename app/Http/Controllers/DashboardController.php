<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ModuleStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Tidak ada perubahan yang diperlukan di file ini.
class DashboardController extends Controller
{
    public function index()
    {
        if ($redirect = $this->redirectByRole()) {
            return $redirect;
        }

        return view('welcome', $this->dashboardData());
    }

    public function kasirDashboard()
    {
        return view('kasir', $this->dashboardData());
    }

    public function adminDashboard()
    {
        return view('admin', [
            'currentUser'       => Auth::user(),
            'currentConnection' => DB::getDefaultConnection(),
            'moduleStatus'      => ModuleStatusService::check(),
            'appUsers'          => User::orderBy('id')->get(),
            'privilegeIntegrated' => ModuleStatusService::privilegeIntegrated(Auth::user()?->role),
        ]);
    }

    private function redirectByRole()
    {
        if (!Auth::check()) {
            return null;
        }

        return match (Auth::user()->role) {
            'kasir' => redirect()->route('kasir.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => null,
        };
    }

    private function dashboardData(): array
    {
        return [
            'customers'          => Customer::orderBy('nama')->get(),
            'products'           => Product::orderBy('nama')->get(),
            'activeTransactions' => Transaction::with(['customer', 'user'])
                ->where('status', '!=', 'dibatalkan')
                ->latest('tanggal')
                ->get(),
            'stats'              => $this->revenueStats(),
            'viewTransactions'   => $this->safeQuery(fn () => DB::table('v_transaksi_sukses')->orderBy('transaksi_id', 'desc')->limit(10)->get()),
            'cancellationLogs'   => $this->safeQuery(fn () => DB::table('log_pembatalan')->orderBy('id', 'desc')->limit(10)->get()),
            'triggerLogs'        => $this->safeQuery(fn () => DB::table('log_aktivitas')->orderBy('id', 'desc')->limit(10)->get()),
            'moduleStatus'       => ModuleStatusService::check(),
            'currentConnection'  => DB::getDefaultConnection(),
            'currentUser'        => Auth::user(),
            'privilegeIntegrated' => ModuleStatusService::privilegeIntegrated(Auth::user()?->role),
        ];
    }

    private function revenueStats(): array
    {
        $stats = [
            'todayRevenue'      => 0.00,
            'monthRevenue'      => 0.00,
            'totalRevenue'      => 0.00,
            'todayTransactions' => 0,
            'monthTransactions' => 0,
            'userRevenue'       => 0.00,
        ];

        try {
            if (method_exists(Transaction::class, 'getTodayRevenue')) {
                $stats['todayRevenue']      = Transaction::getTodayRevenue();
                $stats['monthRevenue']      = Transaction::getMonthRevenue();
                $stats['totalRevenue']      = Transaction::getTotalRevenue();
                $stats['todayTransactions'] = Transaction::getTodayTransactionsCount();
                $stats['monthTransactions'] = Transaction::getMonthTransactionsCount();
                $stats['userRevenue']       = Auth::check() ? Transaction::getUserRevenue(Auth::id()) : 0.00;
            }
        } catch (\Throwable $e) {
        }

        return $stats;
    }

    private function safeQuery(callable $query)
    {
        try {
            return $query();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
