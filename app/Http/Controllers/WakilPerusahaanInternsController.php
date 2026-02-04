<?php

namespace App\Http\Controllers;

use App\Models\MagangSiswa;
use App\Models\WakilPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WakilPerusahaanInternsController extends Controller
{
    public function index()
    {
        $title = 'Siswa Magang';
        $header = 'Daftar Siswa Magang';
        
        $user = Auth::user();
        $wakilPerusahaan = WakilPerusahaan::where('email', $user->email)->first();
        
        if (!$wakilPerusahaan) {
            return redirect()->route('magang.wakil_perusahaan.dashboard')
                ->with('status', 'error')
                ->with('title', 'Error')
                ->with('message', 'Data perusahaan tidak ditemukan.');
        }
        
        $pendingInterns = MagangSiswa::where('perusahaan_id', $wakilPerusahaan->id)
                              ->where('status', 'Menunggu')
                              ->with('opening')
                              ->get();
                              
        $approvedInterns = MagangSiswa::where('perusahaan_id', $wakilPerusahaan->id)
                               ->where('status', 'Disetujui')
                               ->with('opening')
                               ->get();
                               
        $rejectedInterns = MagangSiswa::where('perusahaan_id', $wakilPerusahaan->id)
                               ->where('status', 'Ditolak')
                               ->with('opening')
                               ->get();
        
        return view('magang.wakil_perusahaan.interns.index', compact(
            'title',
            'header',
            'pendingInterns',
            'approvedInterns',
            'rejectedInterns'
        ));
    }
    
    public function show($id)
    {
        $title = 'Detail Siswa Magang';
        $header = 'Detail Siswa Magang';
        
        $user = Auth::user();
        $wakilPerusahaan = WakilPerusahaan::where('email', $user->email)->first();
        
        if (!$wakilPerusahaan) {
            return redirect()->route('magang.wakil_perusahaan.dashboard')
                ->with('status', 'error')
                ->with('title', 'Error')
                ->with('message', 'Data perusahaan tidak ditemukan.');
        }
        
        $intern = MagangSiswa::where('id', $id)
                    ->where('perusahaan_id', $wakilPerusahaan->id)
                    ->with('opening')
                    ->firstOrFail();
        
        return view('magang.wakil_perusahaan.interns.show', compact('title', 'header', 'intern'));
    }
    
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $wakilPerusahaan = WakilPerusahaan::where('email', $user->email)->first();
        
        if (!$wakilPerusahaan) {
            return redirect()->back()
                ->with('status', 'error')
                ->with('title', 'Error')
                ->with('message', 'Data perusahaan tidak ditemukan.');
        }
        
        $intern = MagangSiswa::where('id', $id)
                    ->where('perusahaan_id', $wakilPerusahaan->id)
                    ->where('status', 'Menunggu')
                    ->firstOrFail();
        
        $intern->status = 'Disetujui';
        if ($request->filled('catatan')) {
            $intern->catatan = $request->catatan;
        }
        $intern->save();
        
        // Here you could add a notification to the student about report access
        
        return redirect()->route('magang.wakil_perusahaan.interns')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Pendaftaran siswa berhasil disetujui. Siswa sekarang dapat mengakses menu Laporan Mingguan.');
    }
    
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $wakilPerusahaan = WakilPerusahaan::where('email', $user->email)->first();
        
        if (!$wakilPerusahaan) {
            return redirect()->back()
                ->with('status', 'error')
                ->with('title', 'Error')
                ->with('message', 'Data perusahaan tidak ditemukan.');
        }
        
        $request->validate([
            'alasan' => 'required',
        ]);
        
        $intern = MagangSiswa::where('id', $id)
                    ->where('perusahaan_id', $wakilPerusahaan->id)
                    ->where('status', 'Menunggu')
                    ->firstOrFail();
        
        $intern->status = 'Ditolak';
        $intern->catatan = $request->alasan;
        $intern->save();
        
        // Here you would typically send a notification to the student
        
        return redirect()->route('magang.wakil_perusahaan.interns')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Pendaftaran siswa berhasil ditolak.');
    }
}