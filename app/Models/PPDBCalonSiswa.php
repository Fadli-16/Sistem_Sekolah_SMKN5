<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPDBCalonSiswa extends Model
{
    use HasFactory;

    protected $table = 'ppdb_calon_siswa';

    protected $fillable = [
        'nama',
        'tanggal_lahir',
        'alamat',
        'sekolah_asal',
        'no_hp',
        'email',
        'status_pendaftaran',
        'tanggal_pendaftaran',
        'nilai_rapor',
    ];
}
