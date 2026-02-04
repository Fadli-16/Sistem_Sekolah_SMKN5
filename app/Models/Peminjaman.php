<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'nama',
        'buku_id',
        'laboratorium_id',
        'inventaris_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'tujuan',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}
