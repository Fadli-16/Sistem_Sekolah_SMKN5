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
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = MataPelajaran::with('guru');

        if ($user && $user->role === 'guru') {
            $status = $user->guru->status ?? '';
            if (!in_array($status, ['kepala sekolah', 'wakil kepala'])) {
                $query->where('guru_id', $user->id);
            }
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        $mapels = $query->orderBy('nama_mata_pelajaran')->get();
        
        $jurusans = [
            'Umum',
            'Bisnis Konstruksi dan Properti',
            'Desain Pemodelan dan Informasi Bangunan',
            'Teknik Audio Video',
            'Teknik Elektronika Industri',
            'Teknik Instalasi Tenaga Listrik',
            'Teknik Pemesinan',
            'Teknik Kendaraan Ringan',
            'Teknik Bodi Kendaraan Ringan',
            'Teknik Bisnis Sepeda Motor',
            'Teknik Pendingin dan Tata Udara',
            'Teknik Komputer Jaringan'
        ];

        return view('sistem_akademik.mata_pelajaran.index', [
            'mapels' => $mapels,
            'jurusans' => $jurusans,
            'title'  => 'Daftar Mata Pelajaran',
            'header' => 'Daftar Mata Pelajaran',
        ]);
    }

    public function create()
    {
        $gurus = $this->getGuruList();
        $namaMapelList = MataPelajaran::select('nama_mata_pelajaran')
            ->distinct()->orderBy('nama_mata_pelajaran')->pluck('nama_mata_pelajaran');

        $jurusans = [
            'Umum',
            'Bisnis Konstruksi dan Properti',
            'Desain Pemodelan dan Informasi Bangunan',
            'Teknik Audio Video',
            'Teknik Elektronika Industri',
            'Teknik Instalasi Tenaga Listrik',
            'Teknik Pemesinan',
            'Teknik Kendaraan Ringan',
            'Teknik Bodi Kendaraan Ringan',
            'Teknik Bisnis Sepeda Motor',
            'Teknik Pendingin dan Tata Udara',
            'Teknik Komputer Jaringan'
        ];

        return view('sistem_akademik.mata_pelajaran.createOrEdit', [
            'mapel'         => null,
            'gurus'         => $gurus,
            'namaMapelList' => $namaMapelList,
            'jurusans'      => $jurusans,
            'header'        => 'Tambah Mata Pelajaran',
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
            'jurusan' => 'required|string',
        ]);

        $exists = MataPelajaran::where('nama_mata_pelajaran', $request->nama_mata_pelajaran)
            ->where('guru_id', $request->guru_id)
            ->where('jurusan', $request->jurusan)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('status', 'error')->with('message', 'Data mata pelajaran sudah ada.');
        }

        MataPelajaran::create([
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
            'guru_id' => $request->guru_id,
            'jurusan' => $request->jurusan,
        ]);

        return redirect()->route('sistem_akademik.mata_pelajaran.index')
            ->with('status', 'success')
            ->with('message', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        $gurus = $this->getGuruList();
        $namaMapelList = MataPelajaran::select('nama_mata_pelajaran')
            ->distinct()->orderBy('nama_mata_pelajaran')->pluck('nama_mata_pelajaran');

        $jurusans = [
            'Umum',
            'Bisnis Konstruksi dan Properti',
            'Desain Pemodelan dan Informasi Bangunan',
            'Teknik Audio Video',
            'Teknik Elektronika Industri',
            'Teknik Instalasi Tenaga Listrik',
            'Teknik Pemesinan',
            'Teknik Kendaraan Ringan',
            'Teknik Bodi Kendaraan Ringan',
            'Teknik Bisnis Sepeda Motor',
            'Teknik Pendingin dan Tata Udara',
            'Teknik Komputer Jaringan'
        ];

        return view('sistem_akademik.mata_pelajaran.createOrEdit', [
            'mapel'         => $mataPelajaran,
            'gurus'         => $gurus,
            'namaMapelList' => $namaMapelList,
            'jurusans'      => $jurusans,
            'header'        => 'Edit Mata Pelajaran',
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
            'jurusan' => 'required|string',
        ]);

        $exists = MataPelajaran::where('nama_mata_pelajaran', $request->nama_mata_pelajaran)
            ->where('guru_id', $request->guru_id)
            ->where('jurusan', $request->jurusan)
            ->where('id', '!=', $mataPelajaran->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('status', 'error')->with('message', 'Data mata pelajaran sudah ada.');
        }

        $mataPelajaran->update([
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
            'guru_id' => $request->guru_id,
            'jurusan' => $request->jurusan,
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

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        try {
            MataPelajaran::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => count($ids) . ' data mata pelajaran berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
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
            $gurus = \App\Models\Guru::query()
                ->whereNotIn('status', ['kepala sekolah', 'pegawai', 'pegawai tidak tetap']);

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

                // buat objek sederhana yang blade bisa baca ->id, ->nama / ->name, dan ->jurusan
                return (object) [
                    'id'      => $id,
                    'nama'    => $nama,
                    'jurusan' => $g->jurusan ?? 'Umum',
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
            ->get()
            ->map(function ($u) {
                $u->jurusan = 'Umum';
                return $u;
            });
    }
}
