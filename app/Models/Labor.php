<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Labor extends Model
{
    use HasFactory;
    
    protected $table = 'labor';
    
    protected $fillable = [
        'nama_labor',
        'kode',
        'penanggung_jawab',
        'teknisi',
        'deskripsi',
        'foto'
    ];
    
    public function jadwal()
    {
        return $this->hasMany(Laboratorium::class, 'labor', 'kode');
    }

    // Relasi user (ganti nama biar gak nabrak)
    public function penanggungJawabUser()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab');
    }

    public function teknisiUser()
    {
        return $this->belongsTo(User::class, 'teknisi');
    }
}
