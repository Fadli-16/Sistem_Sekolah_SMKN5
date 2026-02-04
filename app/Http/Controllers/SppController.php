<?php

namespace App\Http\Controllers;

use App\Models\Spp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SppController extends Controller
{
    public function index()
    {
        $title = 'Data SPP';
        $header = 'Data SPP';
        $query_spps = Spp::query();

        if (Auth::check() && Auth::user()->role == 'siswa') {
            $query_spps->where('user_id', Auth::user()->id);
        } else if(Auth::check() && Auth::user()->role == 'guru') {
            $query_spps->whereHas('user.siswa.kelas', function($query) {
                $query->where('user_id', Auth::user()->id);
            });
        }

        $spps = $query_spps->get();

        return view('sistem_akademik.spp.index', compact('spps', 'title', 'header'));
    }

    public function create()
    {
        $title = 'SPP';
        $header = 'Tambah Data SPP';
        $users = User::where('role', 'siswa')->get();

        return view('sistem_akademik.spp.createOrEdit', compact('title', 'header', 'users'));
    }

    public function store(Request $request)
    {
        $messages = [
            'user_id.required' => 'Kolom Nama wajib dipilih!',
            'jumlah_spp.required' => 'Kolom Jumlah wajib diisi!',
            'status_pembayaran.required' => 'Kolom Status Bayar harus diiisi!',
        ];

        $request->validate([
            'user_id' => 'required',
            'jumlah_spp' => 'required',
            'status_pembayaran' => 'required',
        ], $messages);

        // Get data user
        $user = User::with('siswa')->find($request->user_id);

        // Jiika user tidak ditemukan
        if (!$user) {
            return redirect()->back()->with('status', 'error')->with('title', 'Gagal')->with('message', 'Data user tidak ditemukan !');
        }
    
        // Create spp table
        Spp::create([
            'user_id' => $user->id,
            'nisn' => $user->siswa->nisn,
            'nama' => $user->nama,
            'jumlah_spp' => preg_replace('/\D/', '', $request->jumlah_spp ?? '0'),
            'tanggal_pembayaran' => ($request->status_pembayaran == 'Lunas') ? now() : null,
            'status_pembayaran' => $request->status_pembayaran,
        ]);
    
        return redirect()->route('sistem_akademik.spp.index')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Data berhasil ditambah');
    }

    public function edit(Spp $spp)
    {
        $title = 'SPP';
        $header = 'Edit Data SPP';
        $users = User::where('role', 'siswa')->get();
        return view('sistem_akademik.spp.createOrEdit', compact('spp', 'title', 'header', 'users'));
    }

    public function update(Request $request, Spp $spp)
    {
        $messages = [
            'user_id.required' => 'Kolom Nama wajib dipilih!',
            'jumlah_spp.required' => 'Kolom Jumlah wajib diisi!',
            'status_pembayaran.date' => 'Kolom Status Bayar harus diiisi!',
        ];
        
        $request->validate([
            'user_id' => 'required',
            'jumlah_spp' => 'required',
            'status_pembayaran' => 'required',
        ], $messages);

        // Get data user
        $user = User::with('siswa')->find($request->user_id);

        // Jiika user tidak ditemukan
        if (!$user) {
            return redirect()->route('sistem_akademik.spp.index')->with('status', 'error')->with('title', 'Gagal')->with('message', 'Data user tidak ditemukan !');
        }

        $spp->update([
            'user_id' => $user->id,
            'nisn' => $user->siswa->nisn,
            'nama' => $user->nama,
            'jumlah_spp' => preg_replace('/\D/', '', $request->jumlah_spp ?? '0'),
            'tanggal_pembayaran' => ($request->status_pembayaran == 'Lunas') ? now() : null,
            'status_pembayaran' => $request->status_pembayaran,
        ]);
    
        return redirect()->route('sistem_akademik.spp.index')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Data berhasil diupdate');
    }

    public function destroy(Spp $spp)
    {
        $spp->delete();
        return redirect()->route('sistem_akademik.spp.index')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Data berhasil dihapus');
    }
}
