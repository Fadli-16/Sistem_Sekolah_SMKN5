<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        return view('perpustakaan.kategori.index', [
            'kategoris' => $kategoris,
            'title' => 'Kategori Buku'
        ]);
    }

    public function create()
    {
        return view('perpustakaan.kategori.create', [
            'title' => 'Tambah Kategori'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'kode_buku' => 'required|string|max:50',
            'jumlah' => 'required|integer|min:0',
        ]);

        // Mengisi semua kolom pada tabel
        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'kode_buku' => $request->kode_buku,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(Kategori $kategori)
    {
        return view('perpustakaan.kategori.edit', [
            'kategori' => $kategori,
            'title' => 'Edit Kategori'
        ]);
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'kode_buku' => 'required|string|max:50',
            'jumlah' => 'required|integer|min:0',
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'kode_buku' => $request->kode_buku,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
