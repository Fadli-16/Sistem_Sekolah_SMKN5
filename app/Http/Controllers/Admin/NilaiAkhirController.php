<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\WakilPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class NilaiAkhirController extends Controller
{
    
    public function exportPdf()
{
    $penilaians = Penilaian::with(['siswa.magangreports',  'wakilPerusahaan'])->get();

    $pdf = Pdf::loadView('magang.wakil_perusahaan.nilaiakhir.pdf', compact('penilaians'));
    return $pdf->stream('rekap-nilai-akhir.pdf');
}


    // Tampilkan semua siswa yang sudah dinilai oleh mitra
    public function index()
    {   
    
        // Ambil semua penilaian yang sudah lengkap dan ada nilai akhir
        $penilaians = Penilaian::with(['siswa', 'wakilPerusahaan'])
            ->whereNotNull('nilai_akhir')
            ->get();

        return view('magang.wakil_perusahaan.nilaiakhir.index', compact('penilaians'));
    }

    // Form input nilai laporan oleh admin/guru
    public function create()
    {
        // Ambil semua siswa yang sudah dinilai mitra
        $penilaians = Penilaian::with('siswa','wakilPerusahaan')
            ->whereNotNull('hard_skill_1')
            ->get();

        

        return view('magang.wakil_perusahaan.nilaiakhir.create', compact('penilaians'));
    }

    // Simpan nilai laporan & hitung nilai akhir
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:users,id',
            'nilai_laporan' => 'required|numeric|min:0|max:100',
        ]);

        $penilaian = Penilaian::where('siswa_id', $request->siswa_id)->first();

        if (!$penilaian) {
            return back()->with('error', 'Penilaian siswa belum ditemukan.');
        }

        // Perhitungan
        $avgHardSkill = ($penilaian->hard_skill_1 + $penilaian->hard_skill_2 + $penilaian->hard_skill_3) / 3;
        $kewirausahaan = $penilaian->kewirausahaan;
        $avgSoftSkill = (
            $penilaian->soft_skill_1 + $penilaian->soft_skill_2 + $penilaian->soft_skill_3 +
            $penilaian->soft_skill_4 + $penilaian->soft_skill_5 + $penilaian->soft_skill_6
        ) / 6;

        $nilaiPKL = round(($avgHardSkill + $kewirausahaan + $avgSoftSkill) / 3, 2);
        $nilaiLaporan = $request->nilai_laporan;
        $nilaiAkhir = round(($nilaiPKL * 0.7) + ($nilaiLaporan * 0.3), 2);

        $penilaian->nilai_laporan = $nilaiLaporan;
        $penilaian->nilai_akhir = $nilaiAkhir;
        $penilaian->save();

        return redirect()->route('magang.wakil_perusahaan.nilaiakhir.index')->with('success', 'Nilai akhir berhasil disimpan!');
    }

    // Detail nilai akhir untuk siswa (bisa digunakan jika butuh tampilan satuan)
    public function show($id)
    {
        $penilaian = Penilaian::with(['siswa', 'wakilPerusahaan'])->findOrFail($id);

        // Validasi nilai laporan
        if ($penilaian->nilai_laporan === null) {
            return back()->with('error', 'Nilai laporan belum tersedia.');
        }

        $avgHardSkill = ($penilaian->hard_skill_1 + $penilaian->hard_skill_2 + $penilaian->hard_skill_3) / 3;
        $kewirausahaan = $penilaian->kewirausahaan;
        $avgSoftSkill = (
            $penilaian->soft_skill_1 + $penilaian->soft_skill_2 + $penilaian->soft_skill_3 +
            $penilaian->soft_skill_4 + $penilaian->soft_skill_5 + $penilaian->soft_skill_6
        ) / 6;

        $nilaiPKL = round(($avgHardSkill + $kewirausahaan + $avgSoftSkill) / 3, 2);
        $nilaiLaporan = $penilaian->nilai_laporan;
        $nilaiAkhir = round(($nilaiPKL * 0.7) + ($nilaiLaporan * 0.3), 2);

        $keterangan = match (true) {
            $nilaiAkhir >= 91 => 'Sangat Baik',
            $nilaiAkhir >= 81 => 'Baik',
            $nilaiAkhir >= 71 => 'Cukup',
            default => 'Kurang'
        };

        return view('magang.wakil_perusahaan.nilaiakhir.show', compact(
            'penilaian', 'nilaiAkhir', 'nilaiPKL', 'nilaiLaporan', 'keterangan'
        ));
    }
}
