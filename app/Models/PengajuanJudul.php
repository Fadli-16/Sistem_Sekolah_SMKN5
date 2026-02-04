<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanJudul extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'wakil_perusahaan_id',
    'jurusan',
    'judul_laporan',
    'alasan',
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wakilPerusahaan()
    {
        return $this->belongsTo(WakilPerusahaan::class);
    }

}
