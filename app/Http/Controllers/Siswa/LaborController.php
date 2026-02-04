<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Labor;
use App\Models\Laboratorium;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaborController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'Laboratorium';
        $header = 'Daftar Laboratorium';

        // Ambil semua labor untuk dropdown filter
        $laborList = Labor::orderBy('nama_labor', 'asc')->get();

        // Ambil nilai filter dari query string (?labor=...)
        $selectedLabor = $request->input('labor');

        // Ambil labor berdasarkan filter, jika ada
        $labor = Labor::when($selectedLabor, function ($query, $selectedLabor) {
                return $query->where('kode', $selectedLabor);
            })
            ->orderBy('nama_labor', 'asc')
            ->get();

        // Tambahkan status aktif (sedang digunakan atau tidak)
        foreach ($labor as $lab) {
            $lab->is_active = Laboratorium::where('labor', $lab->kode)
                ->whereDate('start', now())
                ->where('start', '<=', now())
                ->where('end', '>=', now())
                ->exists();
        }

        return view('siswa.main.labor.index', compact('title', 'header', 'laborList', 'selectedLabor', 'labor'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Detail Laboratorium';
        $header = 'Informasi Laboratorium';

        $labor = Labor::findOrFail($id);

        // Jadwal hari ini
        $today = Carbon::now();
        $jadwalToday = Laboratorium::where('labor', $labor->kode)
            ->whereDate('start', $today)
            ->orderBy('start', 'asc')
            ->get();

        // Jadwal mendatang
        $jadwalFuture = Laboratorium::where('labor', $labor->kode)
            ->whereDate('start', '>', $today)
            ->orderBy('start', 'asc')
            ->limit(5)
            ->get();

        return view('siswa.main.labor.show', compact('title', 'header', 'labor', 'jadwalToday', 'jadwalFuture'));
    }
}
