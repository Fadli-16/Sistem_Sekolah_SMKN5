<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Peminatan;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PeminatanController extends Controller
{
    // Analitik dan statistik untuk halaman index
    private function buildPeminatanAnalytics($filteredCollection, array $options, Request $request, $kelasList): array
    {
        $emptyStats = array_fill_keys(array_keys($options), 0);

        if ($filteredCollection->isEmpty()) {
            return [
                'totalRespondents' => 0,
                'statsPerOption' => $emptyStats,
                'years' => [],
                'perOptionPerYear' => array_fill_keys(array_keys($options), []),
                'chartPie' => [
                    'labels' => array_values($options),
                    'totals' => array_values($emptyStats),
                ],
                'summaryText' => 'Belum ada data peminatan untuk kombinasi filter saat ini.',
                'trendSummary' => [],
                'detailedCounts' => [],
                'topReasonsGlobal' => [],
                'topReasonsPerOption' => [],
            ];
        }

        $totalRespondents = $filteredCollection->count();

        $statsPerOption = $filteredCollection
            ->groupBy('minat')
            ->map->count()
            ->toArray();

        $statsPerOption = array_replace($emptyStats, $statsPerOption);

        $years = $filteredCollection
            ->map(fn($p) => Carbon::parse($p->created_at)->year)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        if (empty($years)) {
            $years = Peminatan::select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->orderBy('year')
                ->pluck('year')
                ->toArray();
        }

        $groupedByYear = $filteredCollection
            ->groupBy(fn($p) => Carbon::parse($p->created_at)->year)
            ->map(fn($rows) => $rows->groupBy('minat')->map->count()->toArray());

        $perOptionPerYear = [];
        foreach (array_keys($options) as $key) {
            $perOptionPerYear[$key] = array_map(
                fn($year) => $groupedByYear[$year][$key] ?? 0,
                $years
            );
        }

        $chartPie = [
            'labels' => array_values($options),
            'totals' => array_map(fn($k) => $statsPerOption[$k] ?? 0, array_keys($options)),
        ];

        $normalizedReasons = $filteredCollection
            ->pluck('alasan')
            ->filter()
            ->map(fn($r) => Str::of(trim($r))->lower()->substr(0, 200)->__toString());

        $topReasonsGlobal = $normalizedReasons
            ->countBy()
            ->sortDesc()
            ->take(3)
            ->toArray();

        $topReasonsPerOption = [];
        $byMinat = $filteredCollection->groupBy('minat');

        foreach (array_keys($options) as $opt) {
            $topReasonsPerOption[$opt] = ($byMinat[$opt] ?? collect())
                ->pluck('alasan')
                ->filter()
                ->map(fn($r) => Str::of(trim($r))->lower()->substr(0, 200)->__toString())
                ->countBy()
                ->sortDesc()
                ->take(3)
                ->toArray();
        }

        $trendSummary = [];
        if (count($years) >= 2) {
            $prevYear = $years[count($years) - 2];
            $currYear = $years[count($years) - 1];

            foreach ($options as $key => $label) {
                $prev = $groupedByYear[$prevYear][$key] ?? 0;
                $curr = $groupedByYear[$currYear][$key] ?? 0;
                $diff = $curr - $prev;

                $pct = $prev == 0
                    ? ($curr > 0 ? 100.0 : 0.0)
                    : round(($diff / max(1, $prev)) * 100, 1);

                if ($diff > 0) {
                    $trendText = "Meningkat {$diff} siswa ({$pct}%) dibandingkan tahun {$prevYear} → {$currYear}";
                } elseif ($diff < 0) {
                    $trendText = "Menurun " . abs($diff) . " siswa (" . abs($pct) . "%) dibandingkan tahun {$prevYear} → {$currYear}";
                } else {
                    $trendText = "Stabil antara tahun {$prevYear} dan {$currYear} (tidak ada perubahan).";
                }

                $trendSummary[$key] = [
                    'label' => $label,
                    'prev' => $prev,
                    'curr' => $curr,
                    'diff' => $diff,
                    'pct' => $pct,
                    'text' => $trendText,
                ];
            }
        } else {
            foreach ($options as $key => $label) {
                $trendSummary[$key] = [
                    'label' => $label,
                    'text' => 'Tidak cukup data tahun untuk menghitung tren (butuh minimal 2 tahun).',
                ];
            }
        }

        $detailedCounts = $filteredCollection
            ->groupBy('minat')
            ->map->count()
            ->sortDesc()
            ->toArray();

        $topMinat = array_key_first($detailedCounts);
        $topCount = $detailedCounts[$topMinat] ?? 0;
        $topPct = $totalRespondents ? round(($topCount / $totalRespondents) * 100, 1) : 0;

        if ($request->filled('jurusan')) {
            $jurusanText = $request->jurusan;
        } elseif ($request->filled('kelas')) {
            $jurusanText = optional($kelasList->firstWhere('id', (int) $request->kelas))->jurusan ?? 'tidak diketahui';
        } else {
            $jurusanCounts = $filteredCollection
                ->where('minat', $topMinat)
                ->pluck('user.siswa.kelas.jurusan')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->toArray();

            $topJurusan = array_keys(array_slice($jurusanCounts, 0, 2, true));
            $jurusanText = !empty($topJurusan) ? implode(' dan ', $topJurusan) : 'berbagai jurusan';
        }

        $lastUpdated = $filteredCollection->max('updated_at') ?? $filteredCollection->max('created_at');
        $lastUpdatedFormatted = $lastUpdated ? Carbon::parse($lastUpdated)->isoFormat('D MMMM Y') : null;

        $summaryText = "Berdasarkan data filter saat ini, minat terbanyak adalah <strong>"
            . ucfirst($topMinat) . "</strong> (sekitar <strong>{$topPct}%</strong> dari <strong>{$totalRespondents}</strong> responden). "
            . "Mayoritas pemilih minat ini berasal dari jurusan <strong>{$jurusanText}</strong>. "
            . ($lastUpdatedFormatted ? "Data terakhir diperbarui pada <strong>{$lastUpdatedFormatted}</strong>." : "");

        return [
            'totalRespondents' => $totalRespondents,
            'statsPerOption' => $statsPerOption,
            'years' => $years,
            'perOptionPerYear' => $perOptionPerYear,
            'chartPie' => $chartPie,
            'summaryText' => $summaryText,
            'trendSummary' => $trendSummary,
            'detailedCounts' => $detailedCounts,
            'topReasonsGlobal' => $topReasonsGlobal,
            'topReasonsPerOption' => $topReasonsPerOption,
        ];
    }

    /**
     * Tampilkan daftar peminatan (dengan filter).
     */
    public function index(Request $request)
    {
        $header = 'Data Peminatan';
        $user = Auth::user();

        $kelasList = Kelas::select('id', 'nama_kelas', 'jurusan', 'tahun_ajaran')
            ->orderBy('nama_kelas')
            ->get()
            ->map(function ($k) {
                $k->label = trim($k->nama_kelas . ' · ' . $k->jurusan);
                return $k;
            });

        $guruBkIds = Kelas::whereNotNull('guru_bk_id')->distinct()->pluck('guru_bk_id');
        $guruBKList = User::whereIn('id', $guruBkIds)->orderBy('nama')->get();
        
        $jurusanList = Kelas::whereNotNull('jurusan')->distinct()->orderBy('jurusan')
            ->pluck('jurusan');
        
            $tahunAjaranList = Kelas::whereNotNull('tahun_ajaran')->distinct()
            ->orderBy('tahun_ajaran')->pluck('tahun_ajaran');

        $options = [
            'bekerja'   => 'Bekerja',
            'wirausaha' => 'Wirausaha',
            'kuliah'    => 'Kuliah',
            'lainnya'   => 'Lainnya',
        ];

        $query = Peminatan::with(['user.siswa.kelas']);

        // Filter Pencarian (Nama atau NIS)
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->whereHas('user', function ($sq) use ($request) {
                $search = $request->search;
                $sq->where('nama', 'like', "%{$search}%")
                   ->orWhereHas('siswa', function($ssq) use ($search) {
                       $ssq->where('nis', 'like', "%{$search}%");
                   });
            });
        });

        $query->when($request->filled('kelas'), function ($q) use ($request) {
            $q->whereHas('user.siswa', function ($sq) use ($request) {
                $sq->where('kelas_id', $request->kelas);
            });
        });

        $query->when($request->filled('guru_bk'), function ($q) use ($request) {
            $q->whereHas('user.siswa.kelas', function ($kq) use ($request) {
                $kq->where('guru_bk_id', $request->guru_bk);
            });
        });

        $query->when($request->filled('minat'), function ($q) use ($request) {
            $q->where('minat', $request->minat);
        });

        $query->when($request->filled('jurusan'), function ($q) use ($request) {
            $q->whereHas('user.siswa.kelas', function ($kc) use ($request) {
                $kc->where('jurusan', $request->jurusan);
            });
        });

        $query->when($request->filled('tahun_ajaran'), function ($q) use ($request) {
            $q->whereHas('user.siswa.kelas', function ($kc) use ($request) {
                $kc->where('tahun_ajaran', $request->tahun_ajaran);
            });
        });

        $filteredCollection = (clone $query)->get();
        
        if ($user && $user->role === 'siswa') {
            $query->orderByRaw("CASE WHEN user_id = ? THEN 0 ELSE 1 END", [$user->id]);
        }
        
        $peminatans = $query->latest()->paginate(25)->appends($request->query());

        $hasOwnPeminatan = false;
        if ($user && $user->role === 'siswa') {
            $hasOwnPeminatan = Peminatan::where('user_id', $user->id)->exists();
        }

        $totalStudents = User::where('role', 'siswa')->count();
        $analytics = $this->buildPeminatanAnalytics($filteredCollection, $options, $request, $kelasList);
        
        // Tambahkan alias 'counts' agar sinkron dengan View
        $analytics['counts'] = $analytics['statsPerOption'];
        
        $kelas = $kelasList;

        return view('sistem_akademik.peminatan.index', array_merge(
            compact('peminatans','header','hasOwnPeminatan',
                'totalStudents','kelasList','kelas',
                'jurusanList','tahunAjaranList','guruBKList'
            ), $analytics
        ));
    }

    /**
     * Form tambah peminatan.
     */
    public function create(Request $request)
    {
        $header = 'Tambah Data Peminatan';

        $users = User::where('role', 'siswa')
            ->whereDoesntHave('peminatan')
            ->orderBy('nama')
            ->get();

        // optional: jika front-end menyediakan filter kelas pada form create, terima parameter 'kelas' 
        if ($request->filled('kelas')) { 
            $kelasFilter = $request->kelas; 
            $users->whereHas('siswa', function ($q) use ($kelasFilter) { 
                $q->where('kelas_id', $kelasFilter); }); }

        $kelasList = Kelas::select('id', 'nama_kelas')
        ->orderBy('nama_kelas')->get();

        return view('sistem_akademik.peminatan.createOrEdit', 
        compact('users', 'header', 'kelasList'));
    }
    /**
     * Simpan data baru.
     */
    public function store(Request $request)
    {
        // Validasi dengan aturan kondisional
        $rules = [
            'minat' => ['required', Rule::in(['bekerja', 'wirausaha', 'kuliah', 'lainnya'])],
            'alasan' => 'required|string',
            'jenis_pekerjaan'   => 'required_if:minat,bekerja|nullable|string|max:255',
            'ide_bisnis'        => 'required_if:minat,wirausaha|nullable|string|max:255',
            'pemilihan_jurusan' => 'required_if:minat,kuliah|nullable|string|max:255',
            'penghasilan_ortu'    => 'nullable|integer',
            'tanggungan_keluarga' => 'nullable|integer',
            'file_angket'         => 'nullable|url',
            'file_raport'         => 'nullable|url',
        ];

        // Jika admin (admin_sa) membuat untuk siswa, user_id wajib
        if (Auth::user()->role === 'admin_sa' || Auth::user()->role === 'super_admin') {
            $rules['user_id'] = ['required', 'integer', Rule::exists('users', 'id')->where(function ($q) {
                $q->where('role', 'siswa');
            })];
        }

        $validated = $request->validate($rules);

        // Tentukan user_id (jika siswa, pakai auth)
        if (Auth::user()->role === 'siswa') {
            $userId = Auth::id();
        } else {
            $userId = $validated['user_id'] ?? $request->input('user_id');
        }

        // Cek: apakah siswa ini sudah punya peminatan?
        if (Peminatan::where('user_id', $userId)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['user_id' => 'Siswa ini sudah memiliki data peminatan (1 siswa = 1 peminatan).']);
        }

        // Simpan
        $data = [
            'user_id' => $userId,
            'minat' => $validated['minat'],
            'alasan' => $validated['alasan'],
            'pemilihan_jurusan' => $validated['pemilihan_jurusan'] ?? null,
            'jenis_pekerjaan' => $validated['jenis_pekerjaan'] ?? null,
            'ide_bisnis' => $validated['ide_bisnis'] ?? null,
            'penghasilan_ortu' => $validated['penghasilan_ortu'] ?? null,
            'tanggungan_keluarga' => $validated['tanggungan_keluarga'] ?? null,
            'file_angket' => $validated['file_angket'] ?? null,
            'file_raport' => $validated['file_raport'] ?? null,
        ];

        Peminatan::create($data);

        return redirect()
            ->route('sistem_akademik.peminatan.index')
            ->with('status', 'success')
            ->with('message', 'Data peminatan berhasil ditambah.');
    }

    /**
     * Form edit.
     */
    public function edit(Peminatan $peminatan)
    {
        $header = 'Edit Data Peminatan';

        $users = User::where('role', 'siswa')
            ->where(function ($q) use ($peminatan) {
                $q->whereDoesntHave('peminatan')
                    ->orWhere('id', $peminatan->user_id);
            })
            ->orderBy('nama')
            ->get();

        $kelasList = Kelas::select('id', 'nama_kelas')->orderBy('nama_kelas')->get();

        return view('sistem_akademik.peminatan.createOrEdit', compact('peminatan', 'users', 'header', 'kelasList'));
    }

    /**
     * Update data.
     */
    public function update(Request $request, Peminatan $peminatan)
    {
        $rules = [
            'minat' => ['required', Rule::in(['bekerja', 'wirausaha', 'kuliah', 'lainnya'])],
            'alasan' => 'required|string',
            'jenis_pekerjaan'   => 'required_if:minat,bekerja|nullable|string|max:255',
            'ide_bisnis'        => 'required_if:minat,wirausaha|nullable|string|max:255',
            'pemilihan_jurusan' => 'required_if:minat,kuliah|nullable|string|max:255',
            'penghasilan_ortu'    => 'nullable|integer',
            'tanggungan_keluarga' => 'nullable|integer',
            'file_angket'         => 'nullable|url',
            'file_raport'         => 'nullable|url',
        ];

        if (Auth::user()->role === 'admin_sa' || Auth::user()->role === 'super_admin') {
            $rules['user_id'] = ['required', 'integer', Rule::exists('users', 'id')->where(function ($q) {
                $q->where('role', 'siswa');
            })];
        }

        $validated = $request->validate($rules);

        // Tentukan user_id
        if (Auth::user()->role === 'siswa') {
            // siswa hanya boleh update miliknya sendiri
            if ($peminatan->user_id !== Auth::id()) {
                abort(403);
            }
            $userId = Auth::id();
        } else {
            $userId = $validated['user_id'] ?? $request->input('user_id');
        }

        if ($userId != $peminatan->user_id && Peminatan::where('user_id', $userId)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['user_id' => 'Siswa tujuan sudah memiliki data peminatan.']);
        }

        $data = [
            'user_id' => $userId,
            'minat' => $validated['minat'],
            'alasan' => $validated['alasan'],
            'pemilihan_jurusan' => $validated['pemilihan_jurusan'] ?? null,
            'jenis_pekerjaan' => $validated['jenis_pekerjaan'] ?? null,
            'ide_bisnis' => $validated['ide_bisnis'] ?? null,
            'penghasilan_ortu' => $validated['penghasilan_ortu'] ?? null,
            'tanggungan_keluarga' => $validated['tanggungan_keluarga'] ?? null,
            'file_angket' => $validated['file_angket'] ?? null,
            'file_raport' => $validated['file_raport'] ?? null,
        ];

        $peminatan->update($data);

        return redirect()
            ->route('sistem_akademik.peminatan.index')
            ->with('status', 'success')
            ->with('message', 'Data peminatan berhasil diupdate.');
    }

    /**
     * Hapus data.
     */
    public function destroy(Peminatan $peminatan)
    {
        $peminatan->delete();

        return redirect()
            ->route('sistem_akademik.peminatan.index')
            ->with('status', 'success')
            ->with('message', 'Data peminatan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        try {
            Peminatan::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => count($ids) . ' data peminatan berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
