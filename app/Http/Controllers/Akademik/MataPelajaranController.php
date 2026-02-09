<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $query = MataPelajaran::with('guru');

        if (Auth::check() && Auth::user()->role === 'guru') {
            $user = Auth::user();

            if (method_exists($user, 'guru') && $user->guru) {
                $guruModel = $user->guru;
                $guruModelId = $guruModel->id ?? null;
                $guruUserId = $guruModel->user_id ?? null;

                if ($guruUserId) {
                    $query->where('guru_id', $guruUserId);
                } else {
                    $query->where(function ($q) use ($user, $guruModelId) {
                        $q->where('guru_id', $user->id); 
                        if ($guruModelId) {
                            $q->orWhere('guru_id', $guruModelId);
                        }
                    });
                }
            } else {
                $query->where('guru_id', $user->id);
            }
        }

        $mapels = $query->get();

        return view('sistem_akademik.mata_pelajaran.index', [
            'mapels' => $mapels,
            'title'  => 'Daftar Mata Pelajaran',
            'header' => 'Daftar Mata Pelajaran',
        ]);
    }

    public function create()
    {
        $gurus = $this->getGuruList();

        return view('sistem_akademik.mata_pelajaran.createOrEdit', [
            'mapel'  => null,
            'gurus'  => $gurus,
            'header' => 'Tambah Mata Pelajaran',
        ]);
    }

    public function store(Request $request)
    {
        // Validasi menggunakan tabel users (guru disimpan di users dengan role = 'guru')
        $userTable = (new User())->getTable();

        $request->validate([
            'nama_mata_pelajaran' => 'required|string|max:255',
            'guru_id' => [
                'required',
                'integer',
                Rule::exists($userTable, 'id'),
            ],
        ]);

        MataPelajaran::create([
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
            'guru_id' => $request->guru_id,
        ]);

        return redirect()->route('sistem_akademik.mata_pelajaran.index')
            ->with('status', 'success')
            ->with('message', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        $gurus = $this->getGuruList();

        return view('sistem_akademik.mata_pelajaran.createOrEdit', [
            'mapel'  => $mataPelajaran,
            'gurus'  => $gurus,
            'header' => 'Edit Mata Pelajaran',
        ]);
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $userTable = (new User())->getTable();

        $request->validate([
            'nama_mata_pelajaran' => 'required|string|max:255',
            'guru_id' => [
                'required',
                'integer',
                Rule::exists($userTable, 'id'),
            ],
        ]);

        $mataPelajaran->update([
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
            'guru_id' => $request->guru_id,
        ]);

        return redirect()->route('sistem_akademik.mata_pelajaran.index')
            ->with('status', 'success')
            ->with('message', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();

        return redirect()->route('sistem_akademik.mata_pelajaran.index')
            ->with('status', 'success')
            ->with('message', 'Mata pelajaran berhasil dihapus.');
    }

    /**
     * Ambil daftar guru untuk dropdown.
     * Prioritas:
     * 1) Jika ada model Guru khusus yang menyimpan relasi ke User, coba gunakan dan ambil user terkait.
     * 2) Default: ambil user dengan role = 'guru' dari tabel users.
     *
     * Mengembalikan koleksi objek dengan properti minimal: id, nama (atau name).
     */
    protected function getGuruList()
    {
        // Jika ada model Guru spesifik, coba gunakan (opsional)
        if (class_exists(\App\Models\Guru::class)) {
            $gurus = \App\Models\Guru::query();

            // jika model Guru punya relasi 'user', eager load supaya kita bisa ambil nama user
            if (method_exists(\App\Models\Guru::class, 'user')) {
                $gurus = $gurus->with('user');
            }

            $gurus = $gurus->get();

            // normalisasi: kembalikan collection yang punya id (user id jika tersedia) dan nama
            $normalized = $gurus->map(function ($g) {
                // jika Guru menyimpan user_id, gunakan itu; kalau tidak, fallback ke id Guru sendiri
                $id = $g->user_id ?? $g->id;
                $nama = $g->nama ?? $g->name ?? (isset($g->user) ? ($g->user->nama ?? $g->user->name ?? null) : null);

                // buat objek sederhana yang blade bisa baca ->id dan ->nama / ->name
                return (object) [
                    'id'   => $id,
                    'nama' => $nama,
                    // keep original untuk referensi bila perlu
                    'original' => $g,
                ];
            });

            // filter null nama (opsional)
            return $normalized->filter(function ($item) {
                return ! empty($item->id);
            })->values();
        }

        return User::where('role', 'guru')
            ->select('id', 'name as nama', 'name')
            ->get();
    }
}
