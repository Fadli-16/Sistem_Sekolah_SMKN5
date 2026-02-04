<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaan';

    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'nama_pembimbing',
        'no_perusahaan',
    ];

    // Relasi ke tabel magang_siswa
    public function magangSiswa()
    {
        return $this->hasMany(MagangSiswa::class, 'perusahaan_id');
    }
}
