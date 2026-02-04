<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamInventaris extends Model
{
    use HasFactory;

    protected $table = 'pinjam_inventaris';

    protected $fillable = [
        'nama',
        'kelas',
        'inventaris',
        'tanggal_peminjaman',
        'tujuan',
    ];
}
