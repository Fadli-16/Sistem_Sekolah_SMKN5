<?php

namespace App\Http\Controllers\Admin;

use App\Models\Laboratorium;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class LaboratoriumController extends Controller
{
    public function index()
    {
        $title = 'Laboratorium';
        $header = 'Laboratorium';
        
        // Ambil semua data jadwal laboratorium
        $jadwals = Laboratorium::all();

        return view('admin.main.jadwal.index', compact('title', 'header', 'jadwals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'laboratorium' => 'required|string',
            'status' => 'required|string',
        ]);
        
        $l = new Laboratorium();
        $l->start = $request->tanggal;
        $l->end = Carbon::parse($request->tanggal)->addHours(3); // contoh durasi 3 jam
        $l->labor = $request->laboratorium;
    

        // Ambil hari dalam bahasa Inggris dari tanggal, lalu konversi ke bahasa Indonesia
        $englishDay = Carbon::parse($request->tanggal)->format('l');
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $l->hari = $days[$englishDay];

        $l->save();

        return redirect()->route('admin.kelola.lab')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Berhasil menambah jadwal');
    }

    public function show($id)
    {
        // Ambil data labor dengan relasi penanggung jawab dan teknisi
        $labor = Labor::with(['penanggung_jawab', 'teknisi'])->findOrFail($id);
        
        // Ambil jadwal hari ini dan mendatang (contoh, sesuaikan dengan logic kamu)
        $jadwalToday = $labor->jadwal()->whereDate('start', now()->toDateString())->get();
        $jadwalFuture = $labor->jadwal()->whereDate('start', '>', now()->toDateString())->get();

        // Kirim data ke view
        return view('siswa.labor.show', compact('labor', 'jadwalToday', 'jadwalFuture'));
    }
}
