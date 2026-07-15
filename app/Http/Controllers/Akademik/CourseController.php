<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,super_admin,admin_sa')->only([
            'create', 'store', 'edit', 'update', 'destroy', 'bulkDestroy'
        ]);
    }

    protected function slotOrder(): array
    {
        return [
            '1',
            '2',
            '3',
            'istirahat',
            '4',
            '5',
            '6',
            'ISHOMA',
            '7',
            '8',
            '9',
            '10',
            'ISHO',
            '11',
            '12',
            '13'
        ];
    }

    protected function slotDetails(): array
    {
        return [
            '1' => ['label' => 'Jam 1',  'start' => '07:15', 'end' => '08:00',  'selectable' => true],
            '2' => ['label' => 'Jam 2',  'start' => '08:00', 'end' => '08:45',  'selectable' => true],
            '3' => ['label' => 'Jam 3',  'start' => '08:45', 'end' => '09:30',  'selectable' => true],
            'istirahat' => ['label' => 'Istirahat', 'start' => '09:30', 'end' => '10:00', 'selectable' => false],
            '4' => ['label' => 'Jam 4',  'start' => '10:00', 'end' => '10:45',  'selectable' => true],
            '5' => ['label' => 'Jam 5',  'start' => '10:45', 'end' => '11:30',  'selectable' => true],
            '6' => ['label' => 'Jam 6',  'start' => '11:30', 'end' => '12:15',  'selectable' => true],
            'ISHOMA' => ['label' => 'ISHOMA', 'start' => '12:15', 'end' => '13:15', 'selectable' => false],
            '7' => ['label' => 'Jam 7',  'start' => '13:15', 'end' => '13:45',  'selectable' => true],
            '8' => ['label' => 'Jam 8',  'start' => '13:45', 'end' => '14:15',  'selectable' => true],
            '9' => ['label' => 'Jam 9',  'start' => '14:15', 'end' => '14:45',  'selectable' => true],
            '10' => ['label' => 'Jam 10', 'start' => '14:45', 'end' => '15:15',  'selectable' => true],
            'ISHO' => ['label' => 'ISHO', 'start' => '15:15', 'end' => '15:45', 'selectable' => false],
            '11' => ['label' => 'Jam 11', 'start' => '15:45', 'end' => '16:15',  'selectable' => true],
            '12' => ['label' => 'Jam 12', 'start' => '16:15', 'end' => '16:45',  'selectable' => true],
            '13' => ['label' => 'Jam 13', 'start' => '16:45', 'end' => '17:00',  'selectable' => true],
        ];
    }

    protected function allowedDays(): array
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    }

    protected function selectableSlots(): array
    {
        $details = $this->slotDetails();
        return array_filter($details, fn($s) => $s['selectable']);
    }

    /**
     * Convert slot id range -> jam mulai & jam selesai.
     * Range boleh melewati slot non-selectable (istirahat/ISHOMA/ISHO).
     */
    protected function slotRangeToTimes(string $startId, string $endId): array
    {
        $order = $this->slotOrder();
        $details = $this->slotDetails();

        $pos = array_flip($order);

        if (!isset($pos[$startId]) || !isset($pos[$endId])) {
            throw new \InvalidArgumentException('Slot tidak valid.');
        }

        $s = $pos[$startId];
        $e = $pos[$endId];

        if ($s > $e) {
            throw new \InvalidArgumentException('Slot akhir harus setelah slot awal.');
        }

        for ($i = $s; $i <= $e; $i++) {
            $id = $order[$i];
            if (!isset($details[$id])) throw new \InvalidArgumentException("Detail slot tidak ditemukan ($id).");
        }

        return [$details[$startId]['start'], $details[$endId]['end']];
    }

    protected function timesOverlap(string $aStart, string $aEnd, string $bStart, string $bEnd): bool
    {
        $aS = Carbon::createFromFormat('H:i', $aStart);
        $aE = Carbon::createFromFormat('H:i', $aEnd);
        $bS = Carbon::createFromFormat('H:i', $bStart);
        $bE = Carbon::createFromFormat('H:i', $bEnd);

        return $aS->lt($bE) && $bS->lt($aE);
    }

    /**
     * Periksa konflik untuk guru, ruangan, dan kelas.
     */
    protected function checkConflicts(string $hari, string $start, string $end, ?int $guruId = null, ?string $ruangan = null, ?int $kelasId = null, ?int $excludeCourseId = null): array
    {
        $conflicts = [
            'guru' => collect(),
            'ruangan' => collect(),
            'kelas' => collect(),
        ];

        $ruanganNorm = $ruangan !== null ? strtolower(trim($ruangan)) : null;

        $query = Course::with(['mataPelajaran.guru', 'kelas'])->where('hari', $hari);
        if ($excludeCourseId) $query->where('id', '!=', $excludeCourseId);
        $courses = $query->get();

        foreach ($courses as $c) {
            // safety: jika course sama dengan exclude (double-safety) skip
            if ($excludeCourseId && $c->id == $excludeCourseId) {
                continue;
            }

            if (!$c->jam_mulai || !$c->jam_selesai) {
                continue;
            }

            $cStart = substr($c->jam_mulai, 0, 5);
            $cEnd   = substr($c->jam_selesai, 0, 5);

            if ($this->timesOverlap($start, $end, $cStart, $cEnd)) {
                // guru conflict: cari guru id pada mataPelajaran relasi (jika ada)
                $mp = $c->mataPelajaran;
                $cGuruId = $mp->guru_id ?? null;
                if ($guruId && $cGuruId && $cGuruId == $guruId) {
                    $conflicts['guru']->push($c);
                }

                // ruangan conflict: normalisasi string sebelum bandingkan
                if ($ruanganNorm && $c->ruangan) {
                    $cRuanganNorm = strtolower(trim($c->ruangan));
                    if ($cRuanganNorm === $ruanganNorm) {
                        $conflicts['ruangan']->push($c);
                    }
                }

                // kelas conflict
                if ($kelasId && $c->kelas_id == $kelasId) {
                    $conflicts['kelas']->push($c);
                }
            }
        }
        // make unique by id to avoid duplicates
        $conflicts['guru'] = $conflicts['guru']->unique('id')->values();
        $conflicts['ruangan'] = $conflicts['ruangan']->unique('id')->values();
        $conflicts['kelas'] = $conflicts['kelas']->unique('id')->values();

        Log::debug('Course conflict check', [
            'exclude' => $excludeCourseId,
            'jam' => [$start, $end],
            'ruanganNorm' => $ruanganNorm,
            'conflicts' => [
                'guru' => $conflicts['guru']->pluck('id'),
                'ruangan' => $conflicts['ruangan']->pluck('id'),
                'kelas' => $conflicts['kelas']->pluck('id'),
            ]
        ]);

        return $conflicts;
    }

    protected function findAvailableSlots(?int $kelasId, ?int $guruId, ?string $ruangan, string $hari, ?int $excludeCourseId = null): array
    {
        $available = [];
        $order = $this->slotOrder();
        $details = $this->slotDetails();

        foreach ($order as $id) {
            if (!isset($details[$id])) continue;
            if ($details[$id]['selectable'] === false) continue;

            $start = $details[$id]['start'];
            $end = $details[$id]['end'];

            $conflicts = $this->checkConflicts($hari, $start, $end, $guruId, $ruangan, $kelasId, $excludeCourseId);
            if ($conflicts['guru']->isEmpty() && $conflicts['ruangan']->isEmpty() && $conflicts['kelas']->isEmpty()) {
                $available[] = [
                    'id' => $id,
                    'label' => $details[$id]['label'],
                    'start' => $start,
                    'end' => $end,
                ];
            }
        }

        return $available;
    }

    protected function buildTimetableData($courses, array $slotOrder, array $slotDetails): array
    {
        // ensure collection
        $courses = collect($courses);
        $days = $this->allowedDays();

        // initialize empty matrix day -> slot -> null
        $matrix = [];
        foreach ($days as $d) {
            foreach ($slotOrder as $sid) {
                $matrix[$d][$sid] = null;
            }
        }

        // helper: map slot start/end times and pos
        $pos = array_flip($slotOrder); // slotId => index
        $slotStart = [];
        $slotEnd = [];
        foreach ($slotOrder as $sid) {
            $slotStart[$sid] = isset($slotDetails[$sid]['start']) ? substr($slotDetails[$sid]['start'], 0, 5) : null;
            $slotEnd[$sid] = isset($slotDetails[$sid]['end']) ? substr($slotDetails[$sid]['end'], 0, 5) : null;
        }

        // function to find slot index by time (start or end). returns index or null
        $findSlotIndexByTime = function (string $time, array $slotTimes, array $slotOrder) {
            $time = substr(trim($time), 0, 5);
            // exact match first
            foreach ($slotOrder as $sid) {
                if (isset($slotTimes[$sid]) && $slotTimes[$sid] === $time) {
                    return array_search($sid, $slotOrder, true);
                }
            }
            // fallback: choose first slot whose start >= time (for starts) or end >= time (for ends)
            foreach ($slotOrder as $sid) {
                if (isset($slotTimes[$sid]) && $slotTimes[$sid] >= $time) {
                    return array_search($sid, $slotOrder, true);
                }
            }
            // or last slot
            return count($slotOrder) - 1;
        };

        // iterate courses and place them
        foreach ($courses as $course) {
            $hari = $course->hari ?? null;
            // skip courses with unexpected day names
            if (! $hari || ! in_array($hari, $days)) {
                // skip courses with unexpected day names
                continue;
            }

            $cStart = $course->jam_mulai ? substr($course->jam_mulai, 0, 5) : null;
            $cEnd = $course->jam_selesai ? substr($course->jam_selesai, 0, 5) : null;

            if (!$cStart || !$cEnd) {
                // skip incomplete time entries
                continue;
            }

            // find start and end indices (try match start to slotStart, end to slotEnd)
            $sIndex = $findSlotIndexByTime($cStart, $slotStart, $slotOrder);
            $eIndex = $findSlotIndexByTime($cEnd, $slotEnd, $slotOrder);

            // safety: ensure sIndex <= eIndex
            if ($sIndex === null) continue;
            if ($eIndex === null) $eIndex = $sIndex;
            if ($sIndex > $eIndex) {
                // swap if reversed due to mismatched rounding
                [$sIndex, $eIndex] = [$eIndex, $sIndex];
            }

            // Check for collision first across all selectable slots in the range
            $hasCollision = false;
            for ($i = $sIndex; $i <= $eIndex; $i++) {
                $sid = $slotOrder[$i];
                $isSelectable = $slotDetails[$sid]['selectable'] ?? true;
                if ($isSelectable && !empty($matrix[$hari][$sid])) {
                    $hasCollision = true;
                    break;
                }
            }

            if ($hasCollision) {
                Log::warning("Timetable collision placing course id {$course->id} on {$hari}");
                continue;
            }

            $currentSpanStart = null;
            $spanLength = 0;

            for ($i = $sIndex; $i <= $eIndex; $i++) {
                $sid = $slotOrder[$i];
                $isSelectable = $slotDetails[$sid]['selectable'] ?? true;

                if (!$isSelectable) {
                    // Save previous chunk
                    if ($currentSpanStart !== null) {
                        $matrix[$hari][$currentSpanStart] = [
                            'course' => $course,
                            'span' => $spanLength,
                        ];
                        $currentSpanStart = null;
                        $spanLength = 0;
                    }
                    continue; // Skip the unselectable slot
                }

                if ($currentSpanStart === null) {
                    $currentSpanStart = $sid;
                    $spanLength = 1;
                } else {
                    $spanLength++;
                    $matrix[$hari][$sid] = ['skipped' => true];
                }
            }

            // Save any remaining chunk
            if ($currentSpanStart !== null) {
                $matrix[$hari][$currentSpanStart] = [
                    'course' => $course,
                    'span' => $spanLength,
                ];
            }
        }

        return [
            'days' => $days,
            'slotOrder' => $slotOrder,
            'slotDetails' => $slotDetails,
            'matrix' => $matrix,
        ];
    }

    protected function resolveGuruIds(?User $user): array
    {
        if (! $user || $user->role !== 'guru') {
            return [];
        }

        $guruModel = $user->guru;
        $status = $guruModel->status ?? '';
        if (in_array(strtolower($status), ['kepala sekolah', 'wakil kepala', 'kepala jurusan'])) {
            return []; // no filter for kepsek/wakil
        }

        return array_values(array_filter([
            $guruModel?->user_id ?? $user->id,
            $guruModel?->id,
        ]));
    }

    protected function applyGuruFilter($query, array $guruIds): void
    {
        if (!empty($guruIds)) {
            $query->whereHas('mataPelajaran', function ($q) use ($guruIds) {
                $q->whereIn('guru_id', $guruIds);
            });
        }
    }

    protected function resolveVisibleKelas(array $guruIds)
    {
        if (empty($guruIds)) {
            return Kelas::orderBy('nama_kelas')->get();
        }

        $kelasIds = Course::whereHas('mataPelajaran', function ($q) use ($guruIds) {
            $q->whereIn('guru_id', $guruIds);
        })
            ->pluck('kelas_id')
            ->unique()
            ->filter()
            ->values();

        return Kelas::whereIn('id', $kelasIds)
            ->orderBy('nama_kelas')
            ->get();
    }

    /* ===========================
     * PUBLIC ACTIONS (resource)
     * =========================== */

    public function index(Request $request)
    {
        $title = 'Kelola Course & Mata Pelajaran';
        $header = 'Jadwal Mata Pelajaran';

        $user = Auth::user();
        $selectedKelasId = $request->query('kelas_id');
        $selectedMapelName = $request->query('nama_mata_pelajaran');
        $selectedGuruId  = $request->query('guru_id');
        $selectedHari    = $request->query('hari');
        $selectedRuangan = $request->query('ruangan');
        $selectedJurusan = $request->query('jurusan');

        $query = Course::with(['mataPelajaran.guru', 'kelas'])
            ->orderBy('hari')
            ->orderBy('jam_mulai');

        // Filter berdasarkan Request
        if ($selectedKelasId) $query->where('kelas_id', $selectedKelasId);
        if ($selectedMapelName) {
            $query->whereHas('mataPelajaran', function($q) use ($selectedMapelName) {
                $q->where('nama_mata_pelajaran', $selectedMapelName);
            });
        }
        if ($selectedHari)    $query->where('hari', $selectedHari);
        if ($selectedRuangan) $query->where('ruangan', 'like', "%{$selectedRuangan}%");
        if ($selectedGuruId) {
            $query->whereHas('mataPelajaran', function($q) use ($selectedGuruId) {
                $q->where('guru_id', $selectedGuruId);
            });
        }
        if ($selectedJurusan) {
            $query->whereHas('kelas', function($q) use ($selectedJurusan) {
                $q->where('jurusan', $selectedJurusan);
            });
        }

        $guruKelasIds = [];

        $isBiasaGuru = false;

        // Role based restriction
        if ($user?->role === 'guru') {
            $guruModel = $user->guru;
            $status = $guruModel->status ?? '';
            if (!in_array(strtolower($status), ['kepala sekolah', 'wakil kepala', 'kepala jurusan'])) {
                $isBiasaGuru = true;
                $guruUserId = $guruModel?->user_id ?? $user->id;
                $guruModelId = $guruModel?->id;

                $guruFilter = function ($q) use ($guruUserId, $guruModelId) {
                    $q->where(function ($subQ) use ($guruUserId, $guruModelId) {
                        $subQ->where('guru_id', $guruUserId);
                        if ($guruModelId) $subQ->orWhere('guru_id', $guruModelId);
                    });
                };

                $query->whereHas('mataPelajaran', $guruFilter);
                $guruKelasIds = Course::whereHas('mataPelajaran', $guruFilter)->pluck('kelas_id')->unique()->filter()->values()->toArray();
            }
        } elseif ($user?->role === 'siswa' && $user->siswa) {
            $kelasId = $user->siswa->kelas_id;
            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $courses = $query->get();

        // Data pendukung filter
        $kelasQuery = Kelas::query();
        if ($isBiasaGuru) {
            $kelasQuery->whereIn('id', $guruKelasIds);
        }
        if ($selectedJurusan) {
            $kelasQuery->where('jurusan', $selectedJurusan);
        }
        $kelasList = $kelasQuery->orderBy('nama_kelas')->get();
        
        $mapelQuery = MataPelajaran::select('nama_mata_pelajaran')->distinct();
        if ($isBiasaGuru) {
            $mapelQuery->where(function ($subQ) use ($guruUserId, $guruModelId) {
                $subQ->where('guru_id', $guruUserId);
                if ($guruModelId) $subQ->orWhere('guru_id', $guruModelId);
            });
        }
        $mapelList = $mapelQuery->orderBy('nama_mata_pelajaran')->get();
        
        $guruQuery = User::where('role', 'guru');
        if ($selectedMapelName) {
            $guruIds = MataPelajaran::where('nama_mata_pelajaran', $selectedMapelName)->pluck('guru_id')->unique()->filter()->toArray();
            $guruQuery->whereIn('id', $guruIds);
        }
        $guruList = $guruQuery->orderBy('nama')->get();
        
        $hariList  = $this->allowedDays();
        
        $ruanganQuery = Course::whereNotNull('ruangan')->distinct();
        if ($isBiasaGuru) {
            $ruanganQuery->whereHas('mataPelajaran', function ($q) use ($guruUserId, $guruModelId) {
                $q->where('guru_id', $guruUserId);
                if ($guruModelId) $q->orWhere('guru_id', $guruModelId);
            });
        }
        $ruanganList = $ruanganQuery->pluck('ruangan');

        $jurusanQuery = Kelas::select('jurusan')->whereNotNull('jurusan')->distinct();
        if ($isBiasaGuru) {
            $jurusanQuery->whereIn('id', $guruKelasIds);
        }
        $jurusanList = $jurusanQuery->orderBy('jurusan')->get();

        return view('sistem_akademik.course.index', compact(
            'courses', 'title', 'header', 'kelasList', 'mapelList', 'guruList', 'hariList', 'ruanganList', 'jurusanList',
            'selectedKelasId', 'selectedMapelName', 'selectedGuruId', 'selectedHari', 'selectedRuangan', 'selectedJurusan'
        ));
    }

    public function create()
    {
        $title  = 'Tambah Jadwal';
        $header = 'Tambah Jadwal Mapel';

        $kelas  = Kelas::all();
        $mapels = MataPelajaran::with('guru')->orderBy('nama_mata_pelajaran')->get();
        $slots  = $this->selectableSlots();

        // Ruangan yang sudah pernah digunakan (untuk autocomplete)
        $ruanganList = Course::whereNotNull('ruangan')
            ->distinct()
            ->orderBy('ruangan')
            ->pluck('ruangan');

        // Map kelas_id -> ruangan dari tabel kelas (sebagai fallback)
        $kelasRuanganMap = Kelas::whereNotNull('ruangan')
            ->pluck('ruangan', 'id');

        return view('sistem_akademik.course.createOrEdit',
            compact('kelas', 'mapels', 'slots', 'title', 'header',
                    'ruanganList', 'kelasRuanganMap'));
    }

    public function store(Request $request)
    {
        $selectableSlots = array_keys($this->selectableSlots());

        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'slot_start' => ['required', 'string', Rule::in($selectableSlots)],
            'slot_end' => ['required', 'string', Rule::in($selectableSlots)],
            'ruangan' => 'nullable|string|max:255',
        ]);

        try {
            [$jamMulai, $jamSelesai] = $this->slotRangeToTimes(
                $request->slot_start,
                $request->slot_end
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['slot_end' => $e->getMessage()])->withInput();
        }

        $mp = MataPelajaran::find($request->mata_pelajaran_id);
        if (! $mp || ! $mp->guru_id) {
            return back()
                ->withErrors
                (['mata_pelajaran_id' => 'Mata pelajaran tidak valid atau belum memiliki guru.'])
                ->withInput();
        }

        $guruId = $mp->guru_id;

        // cek konflik (termasuk ruangan)
        $conflicts = $this->checkConflicts
        ($request->hari, $jamMulai, $jamSelesai, $guruId, $request->ruangan, $request->kelas_id);

        if (!$conflicts['guru']->isEmpty() || 
        !$conflicts['ruangan']->isEmpty() || !$conflicts['kelas']->isEmpty()) {
            $recommendations = $this->findAvailableSlots
            ($request->kelas_id, $guruId, $request->ruangan, $request->hari);

            $conflictDetails = [
                'guru' => $conflicts['guru']->map(fn($c) => [
                    'course_id' => $c->id,
                    'kelas' => $c->kelas?->nama_kelas ?? null,
                    'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
                    'jam_mulai' => $c->jam_mulai,
                    'jam_selesai' => $c->jam_selesai,
                    'ruangan' => $c->ruangan,
                ])->values(),
                'ruangan' => $conflicts['ruangan']->map(fn($c) => [
                    'course_id' => $c->id,
                    'kelas' => $c->kelas?->nama_kelas ?? null,
                    'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
                    'jam_mulai' => $c->jam_mulai,
                    'jam_selesai' => $c->jam_selesai,
                    'ruangan' => $c->ruangan,
                ])->values(),
                'kelas' => $conflicts['kelas']->map(fn($c) => [
                    'course_id' => $c->id,
                    'kelas' => $c->kelas?->nama_kelas ?? null,
                    'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
                    'jam_mulai' => $c->jam_mulai,
                    'jam_selesai' => $c->jam_selesai,
                    'ruangan' => $c->ruangan,
                ])->values(),
            ];

            return back()
                ->with('status', 'error')
                ->with('message', 'Terjadi bentrok jadwal (guru/ruangan/kelas). Lihat rekomendasi slot kosong.')
                ->with('conflicts', $conflicts)
                ->with('conflict_details', $conflictDetails)
                ->with('recommendations', $recommendations)
                ->withInput();
        }

        $course = Course::create([
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'hari' => $request->hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'ruangan' => $request->ruangan,
        ]);

        return redirect()->route('sistem_akademik.course.index')
            ->with('status', 'success')
            ->with('message', 'Jadwal berhasil dibuat.');
    }

    public function show(Course $course)
    {
        $course->load(['mataPelajaran.guru', 'kelas']);
        return view('sistem_akademik.course.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $title  = 'Edit Jadwal';
        $header = 'Edit Jadwal Mapel';

        $kelas  = Kelas::all();
        $mapels = MataPelajaran::with('guru')->orderBy('nama_mata_pelajaran')->get();
        $slots  = $this->selectableSlots();

        $selected = ['slot_start' => null, 'slot_end' => null];
        foreach ($this->slotDetails() as $id => $d) {
            if ($d['selectable']) {
                if (substr($course->jam_mulai, 0, 5) == $d['start']) $selected['slot_start'] = $id;
                if (substr($course->jam_selesai, 0, 5) == $d['end'])  $selected['slot_end']   = $id;
            }
        }



        // Ruangan yang sudah pernah digunakan (untuk autocomplete)
        $ruanganList = Course::whereNotNull('ruangan')
            ->distinct()
            ->orderBy('ruangan')
            ->pluck('ruangan');

        // Map kelas_id -> ruangan dari tabel kelas (sebagai fallback)
        $kelasRuanganMap = Kelas::whereNotNull('ruangan')
            ->pluck('ruangan', 'id');

        return view('sistem_akademik.course.createOrEdit', compact(
            'course', 'kelas', 'mapels', 'slots',
            'selected', 'title', 'header',
            'ruanganList', 'kelasRuanganMap'
        ));
    }

    public function update(Request $request, Course $course)
    {
        $selectableSlots = array_keys($this->selectableSlots());

        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => ['required', Rule::in($this->allowedDays())],
            'slot_start' => ['required', 'string', Rule::in($selectableSlots)],
            'slot_end' => ['required', 'string', Rule::in($selectableSlots)],
            'ruangan' => 'nullable|string|max:255',
        ]);

        try {
            [$jamMulai, $jamSelesai] = $this->slotRangeToTimes(
                $request->slot_start,
                $request->slot_end
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['slot_end' => $e->getMessage()])->withInput();
        }

        $mp = MataPelajaran::find($request->mata_pelajaran_id);

        if (! $mp || ! $mp->guru_id) {
            return back()
                ->withErrors(['mata_pelajaran_id' => 
                'Mata pelajaran tidak valid atau belum memiliki guru.'])
                ->withInput();
        }

        $guruId = $mp->guru_id;

        $oldHari = $course->hari ?? '';
        $oldJamMulai = substr($course->jam_mulai ?? '', 0, 5);
        $oldJamSelesai = substr($course->jam_selesai ?? '', 0, 5);
        $oldRuangan = strtolower(trim($course->ruangan ?? ''));
        $oldKelasId = $course->kelas_id;
        $oldMataPelajaranId = $course->mata_pelajaran_id;

        $newHari = $request->hari;
        $newJamMulai = substr($jamMulai, 0, 5);
        $newJamSelesai = substr($jamSelesai, 0, 5);
        $newRuangan = strtolower(trim($request->ruangan ?? ''));
        $newKelasId = $request->kelas_id;
        $newMataPelajaranId = $request->mata_pelajaran_id;

        $isCriticalChanged =
            ($oldHari !== $newHari) ||
            ($oldJamMulai !== $newJamMulai) ||
            ($oldJamSelesai !== $newJamSelesai) ||
            ($oldRuangan !== $newRuangan) ||
            ($oldKelasId != $newKelasId) ||
            ($oldMataPelajaranId != $newMataPelajaranId);

        if ($isCriticalChanged) {
            $conflicts = $this->checkConflicts(
                $newHari,
                $newJamMulai,
                $newJamSelesai,
                $guruId,
                $request->ruangan,
                $newKelasId,
                $course->id
            );

            $conflicts['guru'] = $conflicts['guru']->filter(fn($c) 
            => $c->id !== $course->id)->values();
            $conflicts['ruangan'] = $conflicts['ruangan']->filter(fn($c) 
            => $c->id !== $course->id)->values();
            $conflicts['kelas'] = $conflicts['kelas']->filter(fn($c) 
            => $c->id !== $course->id)->values();

            if (
                !$conflicts['guru']->isEmpty() ||
                !$conflicts['ruangan']->isEmpty() ||
                !$conflicts['kelas']->isEmpty()
            ) {
                $recommendations = $this->findAvailableSlots(
                    $newKelasId,
                    $guruId,
                    $request->ruangan,
                    $newHari
                );

                $conflictDetails = [
                    'guru' => $conflicts['guru']->map(fn($c) => [
                        'course_id' => $c->id,
                        'kelas' => $c->kelas?->nama_kelas ?? null,
                        'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
                        'jam_mulai' => $c->jam_mulai,
                        'jam_selesai' => $c->jam_selesai,
                        'ruangan' => $c->ruangan,
                    ])->values(),
                    'ruangan' => $conflicts['ruangan']->map(fn($c) => [
                        'course_id' => $c->id,
                        'kelas' => $c->kelas?->nama_kelas ?? null,
                        'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
                        'jam_mulai' => $c->jam_mulai,
                        'jam_selesai' => $c->jam_selesai,
                        'ruangan' => $c->ruangan,
                    ])->values(),
                    'kelas' => $conflicts['kelas']->map(fn($c) => [
                        'course_id' => $c->id,
                        'kelas' => $c->kelas?->nama_kelas ?? null,
                        'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
                        'jam_mulai' => $c->jam_mulai,
                        'jam_selesai' => $c->jam_selesai,
                        'ruangan' => $c->ruangan,
                    ])->values(),
                ];

                return back()
                    ->with('status', 'error')
                    ->with('message', 
                    'Terjadi bentrok jadwal saat update. Lihat rekomendasi slot.')
                    ->with('conflicts', $conflicts)
                    ->with('conflict_details', $conflictDetails)
                    ->with('recommendations', $recommendations)
                    ->withInput();
            }
        }

        $course->update([
            'kelas_id' => $newKelasId,
            'mata_pelajaran_id' => $newMataPelajaranId,
            'hari' => $newHari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'ruangan' => $request->ruangan,
        ]);

        return redirect()->route('sistem_akademik.course.index')
            ->with('status', 'success')
            ->with('message', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        if (method_exists($course, 'siswa')) {
            $course->siswa()->detach();
        }
        $course->delete();

        return redirect()->route('sistem_akademik.course.index')
            ->with('status', 'success')
            ->with('message', 'Jadwal berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        try {
            $courses = Course::whereIn('id', $ids)->get();
            foreach ($courses as $course) {
                if (method_exists($course, 'siswa')) {
                    $course->siswa()->detach();
                }
                $course->delete();
            }
            return response()->json(['success' => true, 'message' => count($ids) . ' data jadwal berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    public function getRecommendations(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'ruangan' => 'nullable|string|max:255',
            'hari' => ['required', Rule::in($this->allowedDays())],
            'exclude_course_id' => 'nullable|integer',
        ]);

        $guruId = MataPelajaran::findOrFail($request->mata_pelajaran_id)->guru_id;
        $exclude = $request->input('exclude_course_id') ? (int) 
        $request->input('exclude_course_id') : null;
        $available = $this->findAvailableSlots
        ($request->kelas_id, $guruId, $request->ruangan, $request->hari, $exclude);

        return response()->json([
            'success' => true,
            'available_slots' => $available
        ]);
    }

    public function getStudentsByJurusan(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $students = Siswa::with('user')
            ->where('kelas_id', $request->kelas_id)
            ->orderBy('id')
            ->get();

        // Jika ada siswa, beri petunjuk agar client otomatis memilih semua (kecuali client mengirim preselect)
        $selectAll = $students->isNotEmpty();

        return response()->json([
            'success' => true,
            'students' => $students,
            'select_all' => $selectAll
        ]);
    }


    public function ajaxCheckConflicts(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'ruangan' => 'nullable|string',
            'slot_start' => 'nullable|string',
            'slot_end' => 'nullable|string',
            'hari' => 'required|string',
            'exclude_course_id' => 'nullable|integer'
        ]);

        if (!in_array($request->hari, $this->allowedDays())) {
            return response()->json(['success' => false, 'message' => 'Hari tidak valid.'], 422);
        }

        // tentukan jam mulai & selesai
        try {
            [$jamMulai, $jamSelesai] = $this->slotRangeToTimes($request->slot_start, $request->slot_end);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        // ambil guru langsung dari mata pelajaran (tanpa decision)
        $guruId = MataPelajaran::findOrFail($request->mata_pelajaran_id)->guru_id;
        $excludeId = $request->input('exclude_course_id') ? (int)$request->input('exclude_course_id') : null;
        $conflicts = $this->checkConflicts($request->hari, $jamMulai, $jamSelesai, $guruId, $request->ruangan, $request->kelas_id, $excludeId);

        $format = fn($c) => [
            'course_id' => $c->id,
            'kelas_id' => $c->kelas?->id ?? null,
            'kelas' => $c->kelas?->nama_kelas ?? null,
            'jurusan' => $c->kelas?->jurusan ?? null,
            'tahun_ajaran' => $c->kelas?->tahun_ajaran ?? null,
            'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
            'jam_mulai' => $c->jam_mulai,
            'jam_selesai' => $c->jam_selesai,
            'ruangan' => $c->ruangan,
        ];

        $conflictDetails = [
            'guru' => $conflicts['guru']->map($format)->values()->all(),
            'ruangan' => $conflicts['ruangan']->map($format)->values()->all(),
            'kelas' => $conflicts['kelas']->map($format)->values()->all(),
        ];

        return response()->json([
            'success' => true,
            'conflict_details' => $conflictDetails,
            'has_conflict' => (!$conflicts['guru']->isEmpty() || !$conflicts['ruangan']->isEmpty() || !$conflicts['kelas']->isEmpty())
        ]);
    }

    /**
     * Tampilkan preview timetable (HTML). 
     * Query params:
     *  - kelas_id (optional) => tampilkan timetable per kelas
     *  - view_as (optional) => 'guru' atau 'siswa' untuk override (biasanya tidak perlu)
     */
    public function timetable(Request $request)
    {
        $slotOrder = $this->slotOrder();
        $slotDetails = $this->slotDetails();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $user = Auth::user();

        $selectedKelasId = $request->input('kelas_id');
        $query = Course::with(['mataPelajaran.guru', 'kelas']);

        if ($user?->role === 'guru') {
            $guruModel = $user->guru;
            $status = $guruModel->status ?? '';
            if (!in_array(strtolower($status), ['kepala sekolah', 'wakil kepala', 'kepala jurusan'])) {
                $guruUserId = $guruModel?->user_id ?? $user->id;
                $guruModelId = $guruModel?->id;

                $guruFilter = function ($q) use ($guruUserId, $guruModelId) {
                    $q->where(function ($subQ) use ($guruUserId, $guruModelId) {
                        $subQ->where('guru_id', $guruUserId);
                        if ($guruModelId) {
                            $subQ->orWhere('guru_id', $guruModelId);
                        }
                    });
                };

                $query->whereHas('mataPelajaran', $guruFilter);

                $kelasIds = Course::whereHas('mataPelajaran', $guruFilter)
                    ->pluck('kelas_id')
                    ->unique()
                    ->filter()
                    ->values()
                    ->toArray();

                $kelasList = Kelas::whereIn('id', $kelasIds)
                    ->orderBy('nama_kelas')
                    ->get();
            }
        } elseif ($user?->role === 'siswa' && $user->siswa) {
            $selectedKelasId = $user->siswa->kelas_id;
            $query->where('kelas_id', $selectedKelasId);
        } else {
            $selectedKelasId = $selectedKelasId ?: $kelasList->first()?->id ?? null;

            if ($selectedKelasId) {
                $query->where('kelas_id', $selectedKelasId);
            }
        }

        $courses = $query->orderBy('hari')->orderBy('jam_mulai')->get();
        $timetable = $this->buildTimetableData($courses, $slotOrder, $slotDetails);

        return view('sistem_akademik.course.show', [
            'timetable' => $timetable,
            'kelasList' => $kelasList,
            'selectedKelasId' => $selectedKelasId,
            'title' => 'Jadwal Kelas',
        ]);
    }

    /**
     * Download timetable as PDF.
     * Query params same as timetable (kelas_id).
     * Returns streamed PDF (download) and opens in browser if target _blank used.
     */
    public function downloadTimetable(Request $request)
    {
        $slotOrder = $this->slotOrder();
        $slotDetails = $this->slotDetails();
        $selectedKelasId = $request->input('kelas_id');

        $user = Auth::user();
        $guruIds = $this->resolveGuruIds($user);
        
        if ($user && $user->role === 'siswa') {
            if ($user->siswa && $user->siswa->kelas_id) {
                $selectedKelasId = $user->siswa->kelas_id;
            } else {
                abort(403, 'Anda belum terdaftar di kelas manapun.');
            }
        }

        $pages = [];

        if ($selectedKelasId) {
            $kelas = Kelas::findOrFail($selectedKelasId);

            $query = Course::with(['mataPelajaran.guru', 'kelas'])
                ->where('kelas_id', $kelas->id);

            $this->applyGuruFilter($query, $guruIds);

            $courses = $query->get();
            $timetable = $this->buildTimetableData($courses, $slotOrder, $slotDetails);

            $pages[] = [
                'kelas' => $kelas,
                'timetable' => $timetable,
            ];
        } else {
            $kelasList = $this->resolveVisibleKelas($guruIds);

            foreach ($kelasList as $kelas) {
                $query = Course::with(['mataPelajaran.guru', 'kelas'])
                    ->where('kelas_id', $kelas->id);

                $this->applyGuruFilter($query, $guruIds);

                $courses = $query->get();
                $timetable = $this->buildTimetableData($courses, $slotOrder, $slotDetails);

                $pages[] = [
                    'kelas' => $kelas,
                    'timetable' => $timetable,
                ];
            }
        }

        if (empty($pages)) {
            $pages[] = [
                'kelas' => null,
                'timetable' => [
                    'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                    'slotOrder' => $slotOrder,
                    'slotDetails' => $slotDetails,
                    'matrix' => [],
                ],
            ];
        }

        $kelasListAll = Kelas::orderBy('nama_kelas')->get();

        $viewHtml = view('sistem_akademik.course.download', [
            'pages' => $pages,
            'kelasList' => $kelasListAll,
            'selectedKelasId' => $selectedKelasId,
        ])->render();

        $fileLabel = $selectedKelasId ? ('kelas_' . $selectedKelasId) : 'semua_kelas';
        $pdf = Pdf::loadHTML($viewHtml)->setPaper('a4', 'landscape');

        return $pdf->stream("jadwal_{$fileLabel}.pdf");
    }
}
