<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    protected $table = 'admin_profiles';

    protected $fillable = [
        'user_id',
        'image',
        'jurusan',
        'jenis_kelamin',
        'tanggal_lahir',
        'agama',
        'alamat',
        'no_hp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getJurusanAttribute()
    {
        return $this->attributes['jurusan'];
    }

    public function getJenisKelaminAttribute()
    {
        return $this->attributes['jenis_kelamin'];
    }
    }
