<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'generation_id',
        'kelas_id',
        'mata_pelajaran_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan'
    ];

    public function generation()
    {
        return $this->belongsTo(ScheduleGeneration::class, 'generation_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }
}
