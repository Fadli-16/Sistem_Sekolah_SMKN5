<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    use HasFactory;

    protected $table = 'spp';

    protected $fillable = [
        'user_id',
        'nisn',
        'nama',
        'jumlah_spp',
        'tanggal_pembayaran',
        'status_pembayaran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
