<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KerusakanController extends Controller
{
    public function index()
    {
        $title = 'Laporan Kerusakan';
        $header = 'Laporan Kerusakan';
        $laporan = Laporan::orderBy('created_at', 'desc')->get();

        return view('admin.main.laporan.index', compact('title', 'header', 'laporan'));
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,process,completed,rejected',
            'tanggapan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('status', 'error')
                ->with('title', 'Gagal')
                ->with('message', 'Validasi gagal, mohon periksa form kembali');
        }

        $laporan = Laporan::findOrFail($id);
        $laporan->status = $request->status;
        $laporan->tanggapan = $request->tanggapan;
        $laporan->save();

        return redirect()->route('admin.kelola.laporan')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Status laporan berhasil diperbarui');
    }
}