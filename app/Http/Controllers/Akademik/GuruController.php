<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with('user')->get();
        return view('sistem_akademik.guru.index', compact('gurus'));
    }

    public function create()
    {
        $title = 'Guru';
        $header = 'Tambah Data Guru';
        return view('sistem_akademik.guru.createOrEdit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:6',
            'nip'           => 'required|string|unique:guru',
            'kelas'         => 'required|string',
            'jurusan'       => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required',
            'no_hp'         => 'required',
        ]);

        // Create user with role 'guru'
        $user = User::create([
            'nis_nip'  => $request->nip,
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'guru',
        ]);

        // Create guru record
        Guru::create([
            'user_id'       => $user->id,
            'nip'           => $request->nip,
            'kelas'         => $request->kelas,
            'jurusan'       => $request->jurusan,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
            'no_hp'         => $request->no_hp,
        ]);

        return redirect()->route('sistem_akademik.guru.index')
            ->with('status', 'success')
            ->with('message', 'Guru berhasil ditambahkan');
    }

    public function edit(Guru $guru)
    {
        $guru->load('user');
        return view('sistem_akademik.guru.createOrEdit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $guru->user_id,
            'nip'           => 'required|string|unique:guru,nip,' . $guru->id,
            'kelas'         => 'required|string',
            'jurusan'       => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required',
            'no_hp'         => 'required',
        ]);

        // Update user
        $guru->user->update([
            'nama'  => $request->nama,
            'email' => $request->email,
            'nis_nip' => $request->nip,
        ]);

        // Update guru
        $guru->update([
            'nip'           => $request->nip,
            'kelas'         => $request->kelas,
            'jurusan'       => $request->jurusan,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
            'no_hp'         => $request->no_hp,
        ]);

        return redirect()->route('sistem_akademik.guru.index')
            ->with('status', 'success')
            ->with('message', 'Data guru berhasil diubah');
    }

    public function destroy(Guru $guru)
    {
        $guru->user()->delete();
        return redirect()->route('sistem_akademik.guru.index')
            ->with('status', 'success')
            ->with('message', 'Data guru berhasil dihapus');
    }

    public function profile()
    {
        $title = 'Profile Guru';
        $guru = auth()->user()->guru; // Assuming authentication is implemented
        return view('sistem_akademik.guru.profile', compact('guru', 'title'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $guru = $user->guru;

        $request->validate([
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required|string',
            'no_hp'         => 'required|string',
        ]);

        $guru->update([
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
            'no_hp'         => $request->no_hp,
        ]);

        return redirect()->route('sistem_akademik.profile')
            ->with('status', 'success')
            ->with('message', 'Profile berhasil diperbarui');
    }
}
