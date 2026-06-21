<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'minat',
        'alasan',
        'pemilihan_jurusan',
        'jenis_pekerjaan',
        'ide_bisnis',
        'penghasilan_ortu',
        'tanggungan_keluarga',
        'file_raport',
        'file_angket',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
