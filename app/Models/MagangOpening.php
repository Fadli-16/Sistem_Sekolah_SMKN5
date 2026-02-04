<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagangOpening extends Model
{
    use HasFactory;
    
    protected $table = 'magang_openings';
    
    protected $fillable = [
        'wakil_perusahaan_id',  // Changed from perusahaan_id
        'posisi',
        'deskripsi',
        'keahlian',
        'jumlah_posisi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];
    
    // Update relationship to use WakilPerusahaan model
    public function wakilPerusahaan()
    {
        return $this->belongsTo(WakilPerusahaan::class);
    }
    
    public function applicants()
    {
        return $this->hasMany(MagangSiswa::class, 'opening_id');
    }

    // App\Models\MagangOpening.php
    public function pelamar()
    {
        return $this->hasMany(\App\Models\MagangSiswa::class, 'opening_id');
    }

}