<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Tidak ada perubahan yang diperlukan di file ini.
class LogAktivitas extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table   = 'log_aktivitas';

    protected $fillable = [
        'transaksi_id',
        'aksi',
        'keterangan',
        'created_at',
    ];
}
