<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'user_id',
        'foto',
        'judul',
        'isi',
        'kategori',
        'file',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
