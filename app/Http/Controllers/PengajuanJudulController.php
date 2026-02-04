<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanJudul;
use App\Models\WakilPerusahaan;
use Illuminate\Support\Facades\Auth;
use App\Models\Magangsiswa;
use Barryvdh\DomPDF\Facade\Pdf;


class PengajuanJudulController extends Controller
{


public function index()
{
    $pengajuan = \App\Models\PengajuanJudul::with(['user', 'wakilPerusahaan'])->latest()->get();
    return view('magang.admin.pengajuan_judul.index', compact('pengajuan'));
}

public function review(Request $request, $id)
{
    $pengajuan = \App\Models\PengajuanJudul::findOrFail($id);
    $pengajuan->status = $request->input('status'); // contoh: 'diterima', 'ditolak'
    $pengajuan->catatan = $request->input('catatan');
    $pengajuan->save();

    return back()->with('success', 'Pengajuan berhasil direview.');
}

public function exportPdf()
{
    $pengajuan = \App\Models\PengajuanJudul::with(['user', 'wakilPerusahaan'])->get();
    $pdf = Pdf::loadView('magang.admin.pengajuan_judul.pdf', compact('pengajuan'));
    return $pdf->download('daftar-pengajuan-judul.pdf');
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
public function store(Request $request)
{
    $user = Auth::user();
    $magangSiswa = $user->magangssiswa;

    if (!$magangSiswa || !$magangSiswa->wakilPerusahaan) {
        return redirect()->back()->with('error', 'Data perusahaan magang tidak ditemukan.');
    }

    $request->validate([
        'judul_laporan' => 'required|string|max:255',
        'alasan' => 'required|string',
        'jurusan' => 'required|string',
        'wakil_perusahaan_id' => 'required|exists:wakil_perusahaan,id',
    ]);

     PengajuanJudul::create([
        'user_id' => $user->id,
        'jurusan' => $request->jurusan,
        'wakil_perusahaan_id' => $request->wakil_perusahaan_id,
        'judul_laporan' => $request->judul_laporan,
        'alasan' => $request->alasan,
        'status' => 'menunggu',
    ]);

    return redirect()->route('magang.pengajuan_judul.indexsiswa')->with('success', 'Pengajuan judul berhasil dikirim.');
}

}