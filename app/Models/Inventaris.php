<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use HasFactory;

    protected $table = 'inventaris';

    protected $fillable = [
        'nama_inventaris',
        'kategori',
        'jumlah',
        'kondisi',
        'lokasi',
        'tanggal_pengadaan',
        'deskripsi',
        'gambar',
        'status'
    ];

    // Define custom date fields if needed
    protected $dates = [
        'tanggal_pengadaan',
        'created_at',
        'updated_at'
    ];
}