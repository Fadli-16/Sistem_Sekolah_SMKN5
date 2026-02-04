<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use Illuminate\Http\Request;
use App\Models\PinjamInventaris;
use App\Http\Controllers\Controller;
use App\Models\Laporan;

class InventarisController extends Controller
{
    public function index()
    {
        $title = 'Inventaris';
        $header = 'Inventaris SMK';
        $inv = Inventaris::all();

        return view('dashboard.main.inventaris.index', compact('title', 'header', 'inv'));
    }

    public function pinjam()
    {
        $title = 'Peminjaman Inventaris';
        $header = 'Peminjaman Inventaris SMK';
        $inv = Inventaris::where('status', 'Tersedia')->get();

        return view('dashboard.main.inventaris.pinjam', compact('title', 'header', 'inv'));
    }

    public function pinjamPost(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'kelas' => 'required',
            'inventaris' => 'required',
            'tanggal_peminjaman' => 'required',
            'tujuan' => 'required',
        ]);

        $inv = PinjamInventaris::create([
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'inventaris' => $request->inventaris,
            'tanggal_peminjaman' => $request->tanggal_peminjaman,
            'tujuan' => $request->tujuan,
        ]);

        if ($inv) {
            return redirect()->route('inv.index')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Berhasi mengajukan peminjaman');
        }
    }

    public function laporan()
    {
        $title = 'Laporan Inventaris';
        $header = 'Laporan Inventaris SMK';
        $inv = Inventaris::all();

        return view('dashboard.main.inventaris.lapor', compact('title', 'header', 'inv'));
    }

    public function laporanPost(Request $request)
    {
        $request->validate([
            'nama_pelapor' => 'required',
            'nama_alat' => 'required',
            'deskripsi_kerusakan' => 'required',
            'tanggal_laporan' => 'required',
        ]);

        $inv = Laporan::create([
            'nama_pelapor' => $request->nama_pelapor,
            'nama_alat' => $request->nama_alat,
            'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
            'tanggal_laporan' => $request->tanggal_laporan,
        ]);

        if ($inv) {
            return redirect()->route('inv.index')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Berhasi mengajukan laporan');
        }
    }
}
