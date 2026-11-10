<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Tidak ada perubahan yang diperlukan di file ini.
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
