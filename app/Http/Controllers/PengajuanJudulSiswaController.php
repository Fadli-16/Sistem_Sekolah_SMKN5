<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanJudul;
use App\Models\WakilPerusahaan;
use Illuminate\Support\Facades\Auth;
use App\Models\Magangsiswa;
use Barryvdh\DomPDF\Facade\Pdf;


class PengajuanJudulSiswaController extends Controller{


public function index()
{
    // Jika siswa login, tampilkan hanya pengajuan miliknya
    if (Auth::user()->role === 'siswa') {
        $pengajuanJuduls = PengajuanJudul::where('user_id', Auth::id())->get();
    } else {
        // Jika admin_magang login, tampilkan semua
        $pengajuanJuduls = PengajuanJudul::with('user', 'wakilPerusahaan')->get();
    }

    return view('magang.pengajuan_judul.indexsiswa', compact('pengajuanJuduls'));
}

public function store(Request $request)
{
    $request->validate([
        'jurusan' => 'required',
        'judul_laporan' => 'required',
        'alasan' => 'required',
        'wakil_perusahaan_id' => 'required',
    ]);

    PengajuanJudul::create([
        'user_id' => Auth::id(),
        'jurusan' => $request->jurusan,
        'judul_laporan' => $request->judul_laporan,
        'alasan' => $request->alasan,
        'wakil_perusahaan_id' => $request->wakil_perusahaan_id,
        'status' => 'menunggu',
    ]);

    return redirect()->route('magang.pengajuan_judul.indexsiswa')->with('success', 'Pengajuan berhasil dikirim!');
}

public function create()
{
   $user = Auth::user();
    $magangSiswa = $user->magangssiswa; // relasi dari User ke MagangSiswa

    // Ambil nama_perusahaan dari relasi WakilPerusahaan
    $namaPerusahaan = $magangSiswa?->wakilPerusahaan?->nama_perusahaan ?? null;
    $wakilPerusahaanId = $magangSiswa?->wakilPerusahaan?->id ?? null;

    return view('magang.pengajuan_judul.create', compact('namaPerusahaan', 'wakilPerusahaanId'));
}

}