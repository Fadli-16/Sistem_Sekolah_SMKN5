<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamLabor extends Model
{
    use HasFactory;

    protected $table = 'pinjam_labor'; // Pastikan nama tabel benar
    
    protected $fillable = ['nama', 'laboratorium_id', 'keperluan', 'tanggal', 'waktu'];
}
