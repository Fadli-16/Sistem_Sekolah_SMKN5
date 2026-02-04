<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InventarisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Inventaris Laboratorium';
        $header = 'Daftar Inventaris Laboratorium';
        $inventaris = Inventaris::orderBy('created_at', 'desc')->get();
        
        return view('admin.main.inventaris.index', compact('title', 'header', 'inventaris'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Tambah Inventaris';
        $header = 'Tambah Inventaris Baru';
        
        // Get all laboratories for the dropdown
        $laborList = \App\Models\Labor::orderBy('nama_labor', 'asc')->get();
        
        return view('admin.main.inventaris.create', compact('title', 'header', 'laborList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


     /**
 * Handle borrowing of an item.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function pinjam(Request $request)
{
    $request->validate([
        'inventaris_id' => 'required|exists:inventaris,id',
        'peminjam' => 'required|string|max:255',
        'tanggal_pinjam' => 'required|date',
        'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
    ]);

    // Buatkan data peminjaman (pastikan ada model Peminjaman)
    \App\Models\Peminjaman::create([
        'inventaris_id' => $request->inventaris_id,
        'peminjam' => $request->peminjam,
        'tanggal_pinjam' => $request->tanggal_pinjam,
        'tanggal_kembali' => $request->tanggal_kembali,
        'status' => 'Dipinjam',
    ]);

    // Update status inventaris jadi Tidak Tersedia
    $inventaris = Inventaris::findOrFail($request->inventaris_id);
    $inventaris->status = 'Tidak Tersedia';
    $inventaris->save();

    return redirect()->back()
        ->with('status', 'success')
        ->with('title', 'Berhasil')
        ->with('message', 'Inventaris berhasil dipinjam');
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_inventaris' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'lokasi' => 'required|string|max:100',
            'tanggal_pengadaan' => 'required|date',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:Tersedia,Tidak Tersedia'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('status', 'error')
                ->with('title', 'Gagal')
                ->with('message', 'Validasi gagal, mohon periksa form kembali');
        }

        $inventaris = new Inventaris();
        $inventaris->nama_inventaris = $request->nama_inventaris;
        $inventaris->kategori = $request->kategori;
        $inventaris->jumlah = $request->jumlah;
        $inventaris->kondisi = $request->kondisi;
        $inventaris->lokasi = $request->lokasi;
        $inventaris->tanggal_pengadaan = $request->tanggal_pengadaan;
        $inventaris->deskripsi = $request->deskripsi;
        $inventaris->status = $request->status;

        // Handle image upload if provided
        if ($request->hasFile('gambar')) {
            // Store in inventaris subfolder with original filename
            $path = $request->file('gambar')->store('inventaris', 'public');
            $inventaris->gambar = $path;  // This will store "inventaris/filename.jpg"
        }

        $inventaris->save();
        
        return redirect()->route('admin.inventaris.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Inventaris berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Detail Inventaris';
        $header = 'Informasi Inventaris';
        $inventaris = Inventaris::findOrFail($id);
        
        return view('admin.main.inventaris.show', compact('title', 'header', 'inventaris'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit Inventaris';
        $header = 'Edit Inventaris';
        
        $inventaris = Inventaris::findOrFail($id);
        
        // Get all laboratories for the dropdown
        $laborList = \App\Models\Labor::orderBy('nama_labor', 'asc')->get();
        
        return view('admin.main.inventaris.edit', compact('title', 'header', 'inventaris', 'laborList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_inventaris' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'lokasi' => 'required|string|max:100',
            'tanggal_pengadaan' => 'required|date',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:Tersedia,Tidak Tersedia'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('status', 'error')
                ->with('title', 'Gagal')
                ->with('message', 'Validasi gagal, mohon periksa form kembali');
        }

        $inventaris = Inventaris::findOrFail($id);
        $inventaris->nama_inventaris = $request->nama_inventaris;
        $inventaris->kategori = $request->kategori;
        $inventaris->jumlah = $request->jumlah;
        $inventaris->kondisi = $request->kondisi;
        $inventaris->lokasi = $request->lokasi;
        $inventaris->tanggal_pengadaan = $request->tanggal_pengadaan;
        $inventaris->deskripsi = $request->deskripsi;
        $inventaris->status = $request->status;

        // Handle image upload if provided
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($inventaris->gambar && Storage::disk('public')->exists($inventaris->gambar)) {
                Storage::disk('public')->delete($inventaris->gambar);
            }
            
            // Store new image
            $path = $request->file('gambar')->store('inventaris', 'public');
            $inventaris->gambar = $path;
        }

        $inventaris->save();
        
        return redirect()->route('admin.inventaris.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Inventaris berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        
        // Delete image if exists
        if ($inventaris->gambar) {
            Storage::delete('public/' . $inventaris->gambar);
        }
        
        $inventaris->delete();
        
        return redirect()->route('admin.inventaris.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Inventaris berhasil dihapus');
    }
}