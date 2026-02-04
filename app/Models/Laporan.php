<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kerusakan';

    protected $fillable = [
        'nama_pelapor',
        'nama_alat',
        'deskripsi_kerusakan',
        'tanggal_laporan',
        'status',
        'tanggapan'
    ];
}