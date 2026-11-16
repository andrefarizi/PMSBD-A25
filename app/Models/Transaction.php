<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'user_id',
        'tanggal',
        'total_harga',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'dibatalkan');
    }

    // Modul 2 (Stored Function) - Bagian 2.5: paste method Stored Function dari Notion di sini
    /**
 * Pendapatan hari ini.
 */
public static function getTodayRevenue()
{
    $result =DB::selectOne(
        'SELECT calculate_today_revenue() AS total'
    );

    return $result ?(float) $result->total :0.00;
}

/**
 * Pendapatan bulan ini.
 */
public static function getMonthRevenue()
{
    $result =DB::selectOne(
        'SELECT calculate_month_revenue() AS total'
    );

    return $result ?(float) $result->total :0.00;
}

/**
 * Total seluruh omzet.
 */
public static function getTotalRevenue()
{
    $result =DB::selectOne(
        'SELECT calculate_total_revenue() AS total'
    );

    return $result ?(float) $result->total :0.00;
}

/**
 * Pendapatan kasir tertentu.
 */
public static function getUserRevenue($userId)
{
    $result =DB::selectOne(
        'SELECT calculate_user_revenue(?)AS total',
        [$userId]
    );

    return $result ?(float) $result->total :0.00;
}

/**
 * Jumlah transaksi hari ini.
 */
public static function getTodayTransactionsCount()
{
    $result =DB::selectOne(
        'SELECT count_today_transactions() AS total'
    );

    return $result ?(int) $result->total :0;
}

/**
 * Jumlah transaksi bulan ini.
 */
public static function getMonthTransactionsCount()
{
    $result =DB::selectOne(
        'SELECT count_month_transactions() AS total'
    );

    return $result ?(int) $result->total :0;
}
}
