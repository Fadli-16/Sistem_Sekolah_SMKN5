<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlangSiswa extends Model
{
    use HasFactory;

    protected $table = 'daftar_ulang_siswa';
    
    protected $fillable = [
        'name',
        'email',
        'major',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'password',
        'status',
        'notes'
    ];

    // Hide password from any JSON output
    protected $hidden = [
        'password',
    ];
}