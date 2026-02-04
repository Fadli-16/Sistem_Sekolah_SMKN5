<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MataPelajaranController extends Controller
{
    public function __construct()
    {
        // Opsi: batasi akses create/update/delete hanya untuk role tertentu
        // jika Anda punya middleware role, aktifkan baris berikut; jika tidak, hapus/komentari.
        // $this->middleware('role:super_admin,admin_sa')->except(['index', 'show']);
    }

    /**
     * Tampilkan daftar mata pelajaran.
     * View: sistem_akademik.mata_pelajaran.index
     * Variabel: mapels (collection), title, header
     */
    public function index()
    {
        $query = MataPelajaran::with('guru');

        // Jika user adalah guru, tampilkan hanya mapel yang dia ampu (jika applicable)
        if (Auth::check() && Auth::user()->role === 'guru') {
            $user = Auth::user();

            // Jika ada model Guru terpisah dan user mempunyai relasi guru, gunakan itu
            if (method_exists($user, 'guru') && $user->guru) {
                $query->where('guru_id', $user->guru->id);
            } else {
                // fallback: guru_id menyimpan users.id
                $query->where('guru_id', $user->id);
            }
        }

        $mapels = $query->get();

        return view('sistem_akademik.mata_pelajaran.index', [
            'mapels' => $mapels,
            'title'  => 'Data Mata Pelajaran',
            'header' => 'Data Mata Pelajaran',
        ]);
    }

    /**
     * Tampilkan form tambah.
     * View: sistem_akademik.mata_pelajaran.createOrEdit
     * Variabel: mataPelajaran (null), gurus (collection), header
     */
    public function create()
    {
        $gurus = $this->getGuruList();

        return view('sistem_akademik.mata_pelajaran.createOrEdit', [
            'mataPelajaran' => null,
            'gurus' => $gurus,
            'header' => 'Tambah Mata Pelajaran',
        ]);
    }

    /**
     * Simpan mata pelajaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mata_pelajaran' => 'required|string|max:255',
            'guru_id' => 'required|integer',
        ]);

        // validasi existence guru sesuai model yang tersedia
        $guruModel = $this->detectGuruModel();
        if (! $guruModel::find($request->guru_id)) {
            return back()->withErrors(['guru_id' => 'Guru tidak ditemukan.'])->withInput();
        }

        MataPelajaran::create([
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
            'guru_id' => $request->guru_id,
        ]);

        return redirect()->route('sistem_akademik.mataPelajaran.index')
            ->with('status', 'success')
            ->with('message', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit.
     */
    public function edit(MataPelajaran $mataPelajaran)
    {
        $gurus = $this->getGuruList();

        return view('sistem_akademik.mata_pelajaran.createOrEdit', [
            'mataPelajaran' => $mataPelajaran,
            'gurus' => $gurus,
            'header' => 'Edit Mata Pelajaran',
        ]);
    }

    /**
     * Update data mata pelajaran.
     */
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama_mata_pelajaran' => 'required|string|max:255',
            'guru_id' => 'required|integer',
        ]);

        $guruModel = $this->detectGuruModel();
        if (! $guruModel::find($request->guru_id)) {
            return back()->withErrors(['guru_id' => 'Guru tidak ditemukan.'])->withInput();
        }

        $mataPelajaran->update([
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
            'guru_id' => $request->guru_id,
        ]);

        return redirect()->route('sistem_akademik.mataPelajaran.index')
            ->with('status', 'success')
            ->with('message', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Hapus mata pelajaran.
     */
    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();

        return redirect()->route('sistem_akademik.mataPelajaran.index')
            ->with('status', 'success')
            ->with('message', 'Mata pelajaran berhasil dihapus.');
    }

    /* -----------------------
     * Helper methods
     * ---------------------- */

    /**
     * Tentukan class model yang menyimpan data guru:
     * - Jika ada App\Models\Guru gunakan itu
     * - Jika tidak ada, fallback ke App\Models\User
     *
     * @return string
     */
    protected function detectGuruModel(): string
    {
        if (class_exists(\App\Models\Guru::class)) {
            return \App\Models\Guru::class;
        }

        return \App\Models\User::class;
    }

    /**
     * Ambil daftar guru untuk dropdown (collection).
     * Jika menggunakan model Guru, ambil semua Guru; jika tidak, ambil users dengan role guru.
     */
    protected function getGuruList()
    {
        $model = $this->detectGuruModel();

        if ($model === \App\Models\Guru::class) {
            return \App\Models\Guru::all();
        }

        return \App\Models\User::where('role', 'guru')->get();
    }
}
