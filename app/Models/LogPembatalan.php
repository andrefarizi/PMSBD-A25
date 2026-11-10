<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Tidak ada perubahan yang diperlukan di file ini.
class LogPembatalan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_pembatalan';

    protected $fillable = [
        'transaksi_id',
        'alasan',
        'dibatalkan_oleh',
        'deleted_at',
    ];
}
