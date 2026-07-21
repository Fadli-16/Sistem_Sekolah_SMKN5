<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'jurusan',
        'kelas_ids',
        'mapel_ids',
        'status',
        'skor_kualitas',
        'total_konflik',
        'catatan_ai',
        'options'
    ];

    protected $casts = [
        'kelas_ids' => 'array',
        'mapel_ids' => 'array',
        'options' => 'array',
    ];

    public function drafts()
    {
        return $this->hasMany(ScheduleDraft::class, 'generation_id');
    }
}
