<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'image',
        'kelas',
        'jurusan',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'alamat',
        'tahun_masuk',
        'no_hp',
        'kelas_id',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function kelasData()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
