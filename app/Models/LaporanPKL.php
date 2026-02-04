<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPKL extends Model
{
    use HasFactory;

    protected $table = 'laporan_pkl';
    protected $fillable = ['siswa_id', 'guru_id', 'nilai_laporan', 'nilai_akhir'];
}
