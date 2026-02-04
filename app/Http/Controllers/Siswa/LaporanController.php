<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Labor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Laporan Kerusakan';
        $header = 'Daftar Laporan Kerusakan';
        
        $laporan = Laporan::where('nama_pelapor', Auth::user()->nama)
            ->orderBy('tanggal_laporan', 'desc')
            ->get();
        
        return view('siswa.main.laporan.index', compact('title', 'header', 'laporan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Buat Laporan';
        $header = 'Buat Laporan Kerusakan';
        
        $laborList = Labor::orderBy('nama_labor', 'asc')->get();
        
        return view('siswa.main.laporan.create', compact('title', 'header', 'laborList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_alat' => 'required|string|max:255',
            'lokasi' => 'required|string',
            'deskripsi_kerusakan' => 'required|string',
            'nama_pelapor' => 'required|string',
            'tanggal_laporan' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('status', 'error')
                ->with('title', 'Gagal')
                ->with('message', 'Validasi gagal, mohon periksa form kembali');
        }
        
        $laporan = new Laporan();
        $laporan->nama_alat = $request->nama_alat;
        $laporan->nama_pelapor = $request->nama_pelapor;
        $laporan->deskripsi_kerusakan = $request->deskripsi_kerusakan;
        $laporan->tanggal_laporan = $request->tanggal_laporan;
        $laporan->status = 'pending';
        $laporan->save();
        
        return redirect()->route('siswa.laporan.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Laporan kerusakan berhasil dikirim');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Detail Laporan';
        $header = 'Detail Laporan Kerusakan';
        
        $laporan = Laporan::findOrFail($id);
        
        // Verify ownership
        if ($laporan->nama_pelapor != Auth::user()->nama) {
            return redirect()->route('siswa.laporan.index')
                ->with('status', 'error')
                ->with('title', 'Akses Ditolak')
                ->with('message', 'Anda tidak memiliki akses ke laporan tersebut');
        }
        
        return view('siswa.main.laporan.show', compact('title', 'header', 'laporan'));
    }
}