<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $title  = 'Kelola Kelas';
        $header = 'Data Kelas';

        // Ambil semua nilai unik untuk dropdown filter
        $jurusanList    = Kelas::select('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');
        $tahunAjaranList = Kelas::select('tahun_ajaran')->distinct()->orderBy('tahun_ajaran')->pluck('tahun_ajaran');

        // Terapkan filter jika ada
        $query = Kelas::with(['waliKelas', 'guruBK']);

        if ($request->filled('filter_jurusan')) {
            $query->where('jurusan', $request->filter_jurusan);
        }
        if ($request->filled('filter_tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->filter_tahun_ajaran);
        }

        $kelas = $query->get();

        $selectedJurusan    = $request->filter_jurusan;
        $selectedTahunAjaran = $request->filter_tahun_ajaran;

        return view('sistem_akademik.kelas.index', compact(
            'kelas', 'title', 'header',
            'jurusanList', 'tahunAjaranList',
            'selectedJurusan', 'selectedTahunAjaran'
        ));
    }

    public function create()
    {
        $title = 'Kelola Kelas';
        $header = 'Tambah Data Kelas';

        // WALI: semua guru yang belum menjadi wali kelas
        $assignedWaliIds = Kelas::whereNotNull('wali_kelas_id')->pluck('wali_kelas_id')->filter()->toArray();
        $availableWali = User::where('role', 'guru')
            ->whereNotIn('id', $assignedWaliIds)
            ->orderBy('nama')
            ->get();

        // GURU BK: ambil guru dengan jumlah penugasan < 6, jurusan Bimbingan Konseling
        $availableGuruBk = User::where('role', 'guru')
            ->whereHas('guru', function($q) {
                $q->where('jurusan', 'like', '%Bimbingan Konseling%')
                  ->orWhere('jurusan', 'BK');
            })
            ->withCount('kelasBk as kelas_count')
            ->orderBy('nama')
            ->get()
            ->filter(function ($user) {
                return $user->kelas_count < 6;
            })
            ->values();

        $kelas = null;
        $siswaList = Siswa::with('user')->whereNull('kelas_id')->orderBy('nis')->get();
        $selectedSiswaIds = [];

        return view('sistem_akademik.kelas.createOrEdit', compact('kelas', 'title', 'header', 'availableWali', 'availableGuruBk', 'siswaList', 'selectedSiswaIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:255',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'guru_bk_id' => 'nullable|exists:users,id',
            'ruangan' => ['nullable', 'string', 'max:255', 'unique:kelas,ruangan'],
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        if ($request->filled('wali_kelas_id')) {
            $exists = Kelas::where('wali_kelas_id', $request->wali_kelas_id)->exists();
            if ($exists) {
                return back()->withInput()->withErrors(['wali_kelas_id' => 'Guru ini sudah ditunjuk sebagai wali kelas di kelas lain.']);
            }
        }

        if ($request->filled('guru_bk_id')) {
            $count = Kelas::where('guru_bk_id', $request->guru_bk_id)->count();
            if ($count >= 6) {
                return back()->withInput()->withErrors(['guru_bk_id' => 'Guru BK ini sudah ditugaskan ke 6 kelas (maksimal 6).']);
            }
        }

        if ($request->filled('wali_kelas_id') && $request->filled('guru_bk_id') && $request->wali_kelas_id == $request->guru_bk_id) {
            return back()->withInput()->withErrors(['guru_bk_id' => 'Wali kelas dan Guru BK tidak boleh sama orang.']);
        }

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'jurusan' => $request->jurusan,
            'tahun_ajaran' => $request->tahun_ajaran,
            'wali_kelas_id' => $request->wali_kelas_id ?: null,
            'guru_bk_id' => $request->guru_bk_id ?: null,
            'ruangan' => $request->ruangan ?: null,
        ]);

        if ($request->filled('siswa_ids')) {
            Siswa::whereIn('id', $request->siswa_ids)->update(['kelas_id' => $kelas->id]);
        }

        return redirect()->route('sistem_akademik.kelas.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Data kelas berhasil ditambahkan');
    }

    public function edit(Kelas $kelas)
    {
        $title = 'Kelola Kelas';
        $header = 'Edit Data Kelas';

        // Available wali: semua guru yang belum menjadi wali OR guru yg merupakan wali pada kelas ini
        $assignedWaliIds = Kelas::whereNotNull('wali_kelas_id')->where('id', '!=', $kelas->id)->pluck('wali_kelas_id')->filter()->toArray();
        $availableWali = User::where('role', 'guru')
            ->whereNotIn('id', $assignedWaliIds)
            ->orderBy('nama')
            ->get();

        // Available guru_bk: guru dengan <6 kelas OR guru yang saat ini guru_bk untuk kelas ini, jurusan BK
        $guruList = User::where('role', 'guru')
            ->whereHas('guru', function($q) {
                $q->where('jurusan', 'like', '%Bimbingan Konseling%')
                  ->orWhere('jurusan', 'BK');
            })
            ->withCount('kelasBk as kelas_count')
            ->orderBy('nama')
            ->get();

        $availableGuruBk = $guruList->filter(function ($g) use ($kelas) {
            // jika guru ini adalah guru_bk dari kelas yang sedang diedit -> always include
            if ($kelas->guru_bk_id && $g->id == $kelas->guru_bk_id) return true;
            // else only include if kelas_count < 6
            return (int)$g->kelas_count < 6;
        })->values();

        $siswaList = Siswa::with('user')
            ->whereNull('kelas_id')
            ->orWhere('kelas_id', $kelas->id)
            ->orderBy('nis')
            ->get();
        $selectedSiswaIds = $kelas->siswa->pluck('id')->toArray();

        return view('sistem_akademik.kelas.createOrEdit', compact('kelas', 'title', 'header', 'availableWali', 'availableGuruBk', 'siswaList', 'selectedSiswaIds'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:255',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'guru_bk_id' => 'nullable|exists:users,id',
            'ruangan' => ['nullable', 'string', 'max:255', Rule::unique('kelas', 'ruangan')->ignore($kelas->id)],
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        // 1) wali_kelas tidak boleh sudah terpilih pada kelas lain
        if ($request->filled('wali_kelas_id')) {
            $exists = Kelas::where('wali_kelas_id', $request->wali_kelas_id)
                ->where('id', '!=', $kelas->id)
                ->exists();
            if ($exists) {
                return back()->withInput()->withErrors(['wali_kelas_id' => 'Guru ini sudah ditunjuk sebagai wali kelas di kelas lain.']);
            }
        }

        // 2) guru_bk boleh max 6 kelas (exc current)
        if ($request->filled('guru_bk_id')) {
            $count = Kelas::where('guru_bk_id', $request->guru_bk_id)
                ->where('id', '!=', $kelas->id)
                ->count();
            if ($count >= 6) {
                return back()->withInput()->withErrors(['guru_bk_id' => 'Guru BK ini sudah ditugaskan ke 6 kelas (maksimal 6).']);
            }
        }

        // 3) wali_kelas dan guru_bk tidak boleh sama
        if ($request->filled('wali_kelas_id') && $request->filled('guru_bk_id') && $request->wali_kelas_id == $request->guru_bk_id) {
            return back()->withInput()->withErrors(['guru_bk_id' => 'Wali kelas dan Guru BK tidak boleh sama orang.']);
        }

        // 4) ruangan duplicate already handled by unique rule above - but double-check (defensive)
        if ($request->filled('ruangan')) {
            $existsRoom = Kelas::where('ruangan', $request->ruangan)
                ->where('id', '!=', $kelas->id)
                ->exists();
            if ($existsRoom) {
                return back()->withInput()->withErrors(['ruangan' => 'Nama ruangan sudah digunakan oleh kelas lain.']);
            }
        }

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'jurusan' => $request->jurusan,
            'tahun_ajaran' => $request->tahun_ajaran,
            'wali_kelas_id' => $request->wali_kelas_id ?: null,
            'guru_bk_id' => $request->guru_bk_id ?: null,
            'ruangan' => $request->ruangan ?: null,
        ]);

        // Hapus relasi siswa saat ini
        Siswa::where('kelas_id', $kelas->id)->update(['kelas_id' => null]);
        
        // Simpan relasi siswa yang baru
        if ($request->filled('siswa_ids')) {
            Siswa::whereIn('id', $request->siswa_ids)->update(['kelas_id' => $kelas->id]);
        }

        return redirect()->route('sistem_akademik.kelas.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Data kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('sistem_akademik.kelas.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Data kelas berhasil dihapus');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        try {
            Kelas::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => count($ids) . ' data kelas berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}