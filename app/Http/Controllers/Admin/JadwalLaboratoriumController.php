<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laboratorium;
use App\Models\Labor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class JadwalLaboratoriumController extends Controller
{
    public function index()
    {
        $title = 'Jadwal Laboratorium';
        $header = 'Jadwal Laboratorium';
        
        $jadwal = Laboratorium::orderBy('hari')->orderBy('start', 'asc')->get();
        
        return view('admin.main.jadwal.index', compact('title', 'header', 'jadwal'));
    }

    public function create()
    {
        $title = 'Tambah Jadwal';
        $header = 'Tambah Jadwal Laboratorium';
        
        $laborList = Labor::orderBy('nama_labor', 'asc')->get();
        
        return view('admin.main.jadwal.create', compact('title', 'header', 'laborList'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'laboratorium' => 'required|exists:labor,kode',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('status', 'error')
                ->with('title', 'Gagal')
                ->with('message', 'Validasi gagal, mohon periksa form kembali');
        }

        // Format jam saja (hari tidak pakai tanggal)
        $jam_mulai = Carbon::parse($request->jam_mulai)->format('H:i');
        $jam_selesai = Carbon::parse($request->jam_selesai)->format('H:i');

        // Cek jadwal bentrok
        $existingSchedule = Laboratorium::where('labor', $request->laboratorium)
            ->where('hari', $request->hari)
            ->where(function($query) use ($jam_mulai, $jam_selesai) {
                $query->whereBetween('start', [$jam_mulai, $jam_selesai])
                      ->orWhereBetween('end', [$jam_mulai, $jam_selesai])
                      ->orWhere(function($q) use ($jam_mulai, $jam_selesai) {
                          $q->where('start', '<=', $jam_mulai)
                            ->where('end', '>=', $jam_selesai);
                      });
            })
            ->first();
        
        if ($existingSchedule) {
            return redirect()->back()
                ->withInput()
                ->with('status', 'error')
                ->with('title', 'Jadwal Bentrok')
                ->with('message', 'Jadwal bentrok dengan jadwal yang sudah ada');
        }

        // Simpan jadwal
        $jadwal = new Laboratorium();
        $jadwal->labor = $request->laboratorium;
        $jadwal->hari = $request->hari;
        $jadwal->start = $jam_mulai;
        $jadwal->end = $jam_selesai;
        $jadwal->keterangan = $request->keterangan;
        $jadwal->save();

        return redirect()->route('admin.jadwal.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Jadwal laboratorium berhasil ditambahkan');
    }

    public function edit($id)
    {
        $title = 'Edit Jadwal';
        $header = 'Edit Jadwal Laboratorium';
        
        $jadwal = Laboratorium::findOrFail($id);
        
        $laborList = Labor::orderBy('nama_labor', 'asc')->get();
        
        return view('admin.main.jadwal.edit', compact('title', 'header', 'jadwal', 'laborList'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'laboratorium' => 'required|exists:labor,kode',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('status', 'error')
                ->with('title', 'Gagal')
                ->with('message', 'Validasi gagal, mohon periksa form kembali');
        }

        $jadwal = Laboratorium::findOrFail($id);

        $jam_mulai = Carbon::parse($request->jam_mulai)->format('H:i');
        $jam_selesai = Carbon::parse($request->jam_selesai)->format('H:i');

        // Cek jadwal bentrok (kecuali diri sendiri)
        $existingSchedule = Laboratorium::where('labor', $request->laboratorium)
            ->where('hari', $request->hari)
            ->where('id', '!=', $id)
            ->where(function($query) use ($jam_mulai, $jam_selesai) {
                $query->whereBetween('start', [$jam_mulai, $jam_selesai])
                      ->orWhereBetween('end', [$jam_mulai, $jam_selesai])
                      ->orWhere(function($q) use ($jam_mulai, $jam_selesai) {
                          $q->where('start', '<=', $jam_mulai)
                            ->where('end', '>=', $jam_selesai);
                      });
            })
            ->first();
        
        if ($existingSchedule) {
            return redirect()->back()
                ->withInput()
                ->with('status', 'error')
                ->with('title', 'Jadwal Bentrok')
                ->with('message', 'Jadwal bentrok dengan jadwal yang sudah ada');
        }

        // Update jadwal
        $jadwal->labor = $request->laboratorium;
        $jadwal->hari = $request->hari;
        $jadwal->start = $jam_mulai;
        $jadwal->end = $jam_selesai;
        $jadwal->keterangan = $request->keterangan;
        $jadwal->save();

        return redirect()->route('admin.jadwal.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Jadwal laboratorium berhasil diperbarui');
    }

    public function destroy($id)
    {
        $jadwal = Laboratorium::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Jadwal laboratorium berhasil dihapus');
    }
}
