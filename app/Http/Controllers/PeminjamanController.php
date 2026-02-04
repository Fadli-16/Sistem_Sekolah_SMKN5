<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PeminjamanController extends Controller
{
    public function index()
    {
        $title = 'Perpustakaan';
        $header = 'Peminjam Buku';
        $peminjaman = Peminjaman::with('buku')->get();
        return view('perpustakaan.peminjaman.index', compact('peminjaman', 'title', 'header'));
    }

    public function create()
    {
        $title = 'Peminjaman Buku';
        $header = 'Form Peminjaman Buku';
        $buku = Buku::where('stok', '>', 0)->get(); // Hanya buku yang stoknya tersedia
        
        // Get authenticated user's name if logged in
        $nama = auth()->check() ? auth()->user()->name : '';
        $isStudent = auth()->check() && auth()->user()->role == 'siswa';
        
        return view('perpustakaan.peminjaman.create', compact('buku', 'title', 'header', 'nama', 'isStudent'));
    }

    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'nama' => 'required',
            'buku_id' => 'required',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        // If user is a student, use their actual name instead of the form input
        if (auth()->check() && auth()->user()->role == 'siswa') {
            $request->merge(['nama' => auth()->user()->name]);
        }

        $buku = Buku::find($request->buku_id);
        if ($buku->stok <= 0) {
            return redirect()->back()->with('error', 'Stok buku habis');
        }

        $buku->decrement('stok');
        Peminjaman::create([
            'nama' => $request->nama,
            'buku_id' => $request->buku_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => 'Menunggu',
        ]);

        return redirect()->route('perpustakaan.peminjaman.create')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Buku berhasil dipinjam');
    }

    public function edit(Peminjaman $peminjaman)
    {
        $title = 'Peminjaman Buku';
        $header = 'Form Peminjaman Buku';
        $buku = Buku::all();
        return view('perpustakaan.peminjaman.edit', compact('peminjaman', 'buku', 'title', 'header'));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Ditolak,Dikembalikan',
        ]);
    
        $peminjaman->update([
            'status' => $request->status,
        ]);
    
        if ($request->status == 'Dikembalikan') {
            $buku = Buku::find($peminjaman->buku_id);
            $buku->increment('stok');
        }
    
        return redirect()
            ->route('perpustakaan.peminjaman.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Status peminjaman berhasil diperbarui.');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        $peminjaman->delete();
        return redirect()->route('perpustakaan.peminjaman.index')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Peminjamanan berhasil dihapus');
    }

    public function history()
    {
        $title = 'Riwayat Peminjaman';
        $header = 'Riwayat Peminjaman Buku';
        
        // Get current authenticated user's name
        $userNama = auth()->user()->name;
        
        // Get peminjaman records for this user
        $peminjaman = Peminjaman::with('buku')
                        ->where('nama', $userNama)
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('perpustakaan.peminjaman.history', compact('peminjaman', 'title', 'header'));
    }
}