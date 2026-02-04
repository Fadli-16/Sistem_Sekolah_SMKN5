<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PinjamInventaris;
use Illuminate\Http\Request;

class PeminjamanInventarisController extends Controller

{
    public function index()
    {
        $title = 'Kelola Peminjaman';
        $header = 'Daftar Peminjaman Inventaris';

        $peminjaman = PinjamInventaris::orderBy('tanggal_peminjaman', 'desc')->get();

        return view('admin.main.peminjaman.index', compact('title', 'header', 'peminjaman'));
    }
}
