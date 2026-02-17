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
use Barryvdh\DomPDF\Facade\Pdf;

class CourseController extends Controller
{
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

            $span = $eIndex - $sIndex + 1;
            if ($span < 1) $span = 1;

            $startSlotId = $slotOrder[$sIndex];

            // if the place is already occupied (collision), skip or optionally push to log
            if (! empty($matrix[$hari][$startSlotId])) {
                // collision — skip placing to avoid overrides (could log)
                Log::warning("Timetable collision placing course id {$course->id} on {$hari} slot {$startSlotId}");
                continue;
            }

            // assign the course cell at start slot
            $matrix[$hari][$startSlotId] = [
                'course' => $course,
                'span' => $span,
            ];

            // mark subsequent slots as skipped so blade won't render them separately
            for ($i = $sIndex + 1; $i <= $eIndex; $i++) {
                $sid = $slotOrder[$i];
                $matrix[$hari][$sid] = ['skipped' => true];
            }
        }

        return [
            'days' => $days,
            'slotOrder' => $slotOrder,
            'slotDetails' => $slotDetails,
            'matrix' => $matrix,
        ];
    }

    /* ===========================
     * PUBLIC ACTIONS (resource)
     * =========================== */

    public function index(Request $request)
    {
        $title = 'Kelola Course & Mata Pelajaran';
        $header = 'Jadwal Mata Pelajaran';

        $query = Course::with(['mataPelajaran.guru', 'kelas', 'siswa'])
            ->orderBy('hari')
            ->orderBy('jam_mulai');

        // jika user adalah guru, batasi hanya ke course yang berkaitan dengan guru tersebut
        if (Auth::check() && Auth::user()->role === 'guru') {
            $user = Auth::user();
            $guruUserId = $user->id;
            $guruModelId = null;

            // jika ada relasi guru() pada User dan instance tersedia, ambil id model Guru (jika ada)
            if (method_exists($user, 'guru') && $user->guru) {
                $guruModelId = $user->guru->id ?? null;
                // juga jika model Guru menyimpan user_id, kita bisa gunakan user id-nya
                $possibleUserId = $user->guru->user_id ?? null;
                if ($possibleUserId) {
                    $guruUserId = $possibleUserId;
                }
            }

            $query->whereHas('mataPelajaran', function ($q) use ($guruUserId, $guruModelId) {
                $q->where('guru_id', $guruUserId);
                if ($guruModelId) {
                    $q->orWhere('guru_id', $guruModelId);
                }
            });
        }

        if (Auth::check() && Auth::user()->role === 'siswa' && Auth::user()->siswa) {
            $kelasId = Auth::user()->siswa->kelas_id;
            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            } else {
                // kalau siswa record ada tapi belum ada kelas, hasilkan kosong
                $query->whereRaw('1 = 0');
            }
        }

        $courses = $query->get();

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        if (Auth::check() && Auth::user()->role === 'guru') {
            // hanya kelas dimana guru punya course
            $user = Auth::user();
            $guruUserId = $user->id;
            if (method_exists($user, 'guru') && $user->guru) {
                $possibleUserId = $user->guru->user_id ?? null;
                if ($possibleUserId) $guruUserId = $possibleUserId;
            }

            $kelasIds = Course::whereHas('mataPelajaran', function ($q) use ($guruUserId) {
                $q->where('guru_id', $guruUserId);
            })->pluck('kelas_id')->unique()->filter()->values()->toArray();

            $kelasList = Kelas::whereIn('id', $kelasIds)->orderBy('nama_kelas')->get();
        }

        // terima selectedKelas dari querystring (bila user memilih langsung dari index)
        $selectedKelasId = $request->query('kelas_id', null);

        return view('sistem_akademik.course.index', compact(
            'courses',
            'title',
            'header',
            'kelasList',
            'selectedKelasId'
        ));
    }

    public function create()
    {
        $title = 'Tambah Jadwal';
        $header = 'Tambah Jadwal Mapel';

        $kelas = Kelas::all();
        $mapels = MataPelajaran::with('guru')->get();
        $slots = $this->selectableSlots();

        // KUNCI: kirim semua siswa agar view menampilkan nama siswa secara default (fallback)
        $siswa = Siswa::with('user')->orderBy('id')->get();

        return view('sistem_akademik.course.createOrEdit', compact('kelas', 'mapels', 'slots', 'siswa', 'title', 'header'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:users,id',
            'kelas_id' => 'required|exists:kelas,id',
            'nama_course' => 'nullable|string|max:255',
            'hari' => 'required|string',
            'slot_start' => 'nullable|string',
            'slot_end' => 'nullable|string',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:255',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        if (!in_array($request->hari, $this->allowedDays())) {
            return back()->withErrors(['hari' => 'Hari harus antara Senin sampai Jumat.'])->withInput();
        }

        try {
            if ($request->filled('slot_start') && $request->filled('slot_end')) {
                [$jamMulai, $jamSelesai] = $this->slotRangeToTimes($request->slot_start, $request->slot_end);
            } elseif ($request->filled('jam_mulai') && $request->filled('jam_selesai')) {
                $jamMulai = $request->jam_mulai;
                $jamSelesai = $request->jam_selesai;
            } else {
                return back()->withErrors(['slot' => 'Pilih slot atau masukkan jam mulai & selesai.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['slot' => $e->getMessage()])->withInput();
        }

        $guruId = null;
        if ($request->filled('mata_pelajaran_id')) {
            $mp = MataPelajaran::find($request->mata_pelajaran_id);
            if (! $mp) {
                return back()->withErrors(['mata_pelajaran_id' => 'Mata pelajaran tidak ditemukan.'])->withInput();
            }
            $guruId = $mp->guru_id ?? null;
        } elseif ($request->filled('guru_id')) {
            $u = User::find($request->guru_id);
            if (! $u || ($u->role ?? '') !== 'guru') {
                return back()->withErrors(['guru_id' => 'Guru tidak valid.'])->withInput();
            }
            $guruId = $u->id;
        }

        // cek konflik (termasuk ruangan)
        $conflicts = $this->checkConflicts($request->hari, $jamMulai, $jamSelesai, $guruId, $request->ruangan, $request->kelas_id);

        if (!$conflicts['guru']->isEmpty() || !$conflicts['ruangan']->isEmpty() || !$conflicts['kelas']->isEmpty()) {
            $recommendations = $this->findAvailableSlots($request->kelas_id, $guruId, $request->ruangan, $request->hari);

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

        // simpan course
        $course = Course::create([
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id ?? null,
            'hari' => $request->hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'ruangan' => $request->ruangan,
        ]);

        if ($request->filled('siswa_ids') && method_exists($course, 'siswa')) {
            $course->siswa()->attach($request->siswa_ids);
        }

        return redirect()->route('sistem_akademik.course.index')
            ->with('status', 'success')
            ->with('message', 'Jadwal berhasil dibuat.');
    }

    public function show(Course $course)
    {
        $course->load(['mataPelajaran.guru', 'kelas', 'siswa.user']);
        return view('sistem_akademik.course.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $title = 'Edit Jadwal';
        $header = 'Edit Jadwal Mapel';

        $kelas = Kelas::all();
        $mapels = MataPelajaran::with('guru')->get();
        $slots = $this->selectableSlots();

        $selected = ['slot_start' => null, 'slot_end' => null];
        foreach ($this->slotDetails() as $id => $d) {
            if ($d['selectable']) {
                if (substr($course->jam_mulai, 0, 5) == $d['start']) $selected['slot_start'] = $id;
                if (substr($course->jam_selesai, 0, 5) == $d['end']) $selected['slot_end'] = $id;
            }
        }

        // include siswa list as fallback (harmless) so view can show options if needed
        $siswa = Siswa::with('user')->orderBy('id')->get();

        $selectedSiswaIds = method_exists($course, 'siswa') ? $course->siswa->pluck('id')->toArray() : [];

        return view('sistem_akademik.course.createOrEdit', compact(
            'course',
            'kelas',
            'mapels',
            'slots',
            'siswa',
            'selected',
            'selectedSiswaIds',
            'title',
            'header'
        ));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:users,id',
            'kelas_id' => 'required|exists:kelas,id',
            'nama_course' => 'nullable|string|max:255',
            'hari' => 'required|string',
            'slot_start' => 'nullable|string',
            'slot_end' => 'nullable|string',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:255',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        if (!in_array($request->hari, $this->allowedDays())) {
            return back()->withErrors(['hari' => 'Hari harus antara Senin sampai Jumat.'])->withInput();
        }

        try {
            if ($request->filled('slot_start') && $request->filled('slot_end')) {
                [$jamMulai, $jamSelesai] = $this->slotRangeToTimes($request->slot_start, $request->slot_end);
            } elseif ($request->filled('jam_mulai') && $request->filled('jam_selesai')) {
                $jamMulai = $request->jam_mulai;
                $jamSelesai = $request->jam_selesai;
            } else {
                return back()->withErrors(['slot' => 'Pilih slot atau masukkan jam mulai & selesai.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['slot' => $e->getMessage()])->withInput();
        }

        // tentukan guru berdasarkan mata_pelajaran_id jika ada; jika tidak, gunakan guru_id (jika diberikan)
        $guruId = null;
        if ($request->filled('mata_pelajaran_id')) {
            $mp = MataPelajaran::find($request->mata_pelajaran_id);
            if (! $mp) {
                return back()->withErrors(['mata_pelajaran_id' => 'Mata pelajaran tidak ditemukan.'])->withInput();
            }
            $guruId = $mp->guru_id ?? null;
        } elseif ($request->filled('guru_id')) {
            $u = User::find($request->guru_id);
            if (! $u || ($u->role ?? '') !== 'guru') {
                return back()->withErrors(['guru_id' => 'Guru tidak valid.'])->withInput();
            }
            $guruId = $u->id;
        }

        // normalisasi nilai lama & baru untuk perbandingan
        $oldHari = $course->hari ?? '';
        $oldJamMulai = substr($course->jam_mulai ?? '', 0, 5);
        $oldJamSelesai = substr($course->jam_selesai ?? '', 0, 5);
        $oldRuangan = strtolower(trim($course->ruangan ?? ''));
        $oldKelasId = $course->kelas_id;
        $oldGuruId = $course->mataPelajaran?->guru_id ?? null;

        $newHari = $request->hari;
        $newJamMulai = substr($jamMulai, 0, 5);
        $newJamSelesai = substr($jamSelesai, 0, 5);
        $newRuangan = strtolower(trim($request->ruangan ?? ''));
        $newKelasId = $request->kelas_id;
        $newGuruId = $guruId;

        $isCriticalChanged =
            ($oldHari !== $newHari) ||
            ($oldJamMulai !== $newJamMulai) ||
            ($oldJamSelesai !== $newJamSelesai) ||
            ($oldRuangan !== $newRuangan) ||
            ($oldKelasId != $newKelasId) ||
            ($oldGuruId != $newGuruId);

        // HANYA cek konflik jika ada perubahan kritikal
        if ($isCriticalChanged) {
            // gunakan ruangan tanpa perubahan case/space karena checkConflicts akan membandingkan trim,
            // namun kita juga kirim lowercased value supaya konsisten.
            $conflicts = $this->checkConflicts(
                $newHari,
                $newJamMulai,
                $newJamSelesai,
                $newGuruId,
                $request->ruangan,
                $newKelasId,
                $course->id // exclude current course
            );

            // Safety: filter apapun yang masih refer ke current course (kadang exclude belum bekerja jika id falsy)
            $conflicts['guru'] = $conflicts['guru']->filter(fn($c) => $c->id !== $course->id)->values();
            $conflicts['ruangan'] = $conflicts['ruangan']->filter(fn($c) => $c->id !== $course->id)->values();
            $conflicts['kelas'] = $conflicts['kelas']->filter(fn($c) => $c->id !== $course->id)->values();

            if (!$conflicts['guru']->isEmpty() || !$conflicts['ruangan']->isEmpty() || !$conflicts['kelas']->isEmpty()) {
                $recommendations = $this->findAvailableSlots($request->kelas_id, $newGuruId, $request->ruangan, $request->hari);

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
                    ->with('message', 'Terjadi bentrok jadwal saat update. Lihat rekomendasi slot.')
                    ->with('conflicts', $conflicts)
                    ->with('conflict_details', $conflictDetails)
                    ->with('recommendations', $recommendations)
                    ->withInput();
            }
        }

        // Simpan perubahan (tidak terpengaruh pengecekan konflik bila tidak kritikal)
        $course->update([
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id ?? null,
            'hari' => $request->hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'ruangan' => $request->ruangan,
        ]);

        if ($request->filled('siswa_ids') && method_exists($course, 'siswa')) {
            $course->siswa()->sync($request->siswa_ids);
        } elseif (method_exists($course, 'siswa')) {
            $course->siswa()->detach();
        }

        Log::info('Update debug', compact('oldHari', 'oldJamMulai', 'oldJamSelesai', 'oldRuangan', 'oldKelasId', 'oldGuruId', 'newHari', 'newJamMulai', 'newJamSelesai', 'newRuangan', 'newKelasId', 'newGuruId'));

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

    public function getRecommendations(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:users,id',
            'ruangan' => 'nullable|string',
            'hari' => 'required|string',
            'exclude_course_id' => 'nullable|integer',
        ]);

        if (!in_array($request->hari, $this->allowedDays())) {
            return response()->json(['success' => false, 'message' => 'Hari harus antara Senin sampai Jumat.'], 422);
        }

        $guruId = null;
        if ($request->filled('mata_pelajaran_id')) {
            $mp = MataPelajaran::find($request->mata_pelajaran_id);
            $guruId = $mp->guru_id ?? null;
        } elseif ($request->filled('guru_id')) {
            $u = User::find($request->guru_id);
            $guruId = ($u && ($u->role ?? '') === 'guru') ? $u->id : null;
        }

        $exclude = $request->input('exclude_course_id') ? (int) $request->input('exclude_course_id') : null;

        $available = $this->findAvailableSlots($request->kelas_id, $guruId, $request->ruangan, $request->hari, $exclude);

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
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
            'guru_id' => 'nullable|exists:users,id',
            'ruangan' => 'nullable|string',
            'slot_start' => 'nullable|string',
            'slot_end' => 'nullable|string',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'hari' => 'required|string',
            'exclude_course_id' => 'nullable|integer'
        ]);

        if (!in_array($request->hari, $this->allowedDays())) {
            return response()->json(['success' => false, 'message' => 'Hari tidak valid.'], 422);
        }

        // tentukan jam mulai & selesai dari slot atau gunakan jam langsung
        try {
            if ($request->filled('slot_start') && $request->filled('slot_end')) {
                [$jamMulai, $jamSelesai] = $this->slotRangeToTimes($request->slot_start, $request->slot_end);
            } elseif ($request->filled('jam_mulai') && $request->filled('jam_selesai')) {
                $jamMulai = $request->jam_mulai;
                $jamSelesai = $request->jam_selesai;
            } else {
                return response()->json(['success' => false, 'message' => 'Slot atau jam harus diisi.'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        // tentukan guru id seperti di store/update (jika mata_pelajaran_id diberikan)
        $guruId = null;
        if ($request->filled('mata_pelajaran_id')) {
            $mp = MataPelajaran::find($request->mata_pelajaran_id);
            $guruId = $mp->guru_id ?? null;
        } elseif ($request->filled('guru_id')) {
            $u = User::find($request->guru_id);
            $guruId = ($u && ($u->role ?? '') === 'guru') ? $u->id : null;
        }

        $excludeId = $request->input('exclude_course_id') ? (int)$request->input('exclude_course_id') : null;
        $conflicts = $this->checkConflicts($request->hari, $jamMulai, $jamSelesai, $guruId, $request->ruangan, $request->kelas_id, $excludeId);

        $conflictDetails = [
            'guru' => $conflicts['guru']->map(fn($c) => [
                'course_id' => $c->id,
                'kelas_id' => $c->kelas?->id ?? null,
                'kelas' => $c->kelas?->nama_kelas ?? null,
                'jurusan' => $c->kelas?->jurusan ?? null,
                'tahun_ajaran' => $c->kelas?->tahun_ajaran ?? null,
                'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
                'jam_mulai' => $c->jam_mulai,
                'jam_selesai' => $c->jam_selesai,
                'ruangan' => $c->ruangan,
            ])->values(),
            'ruangan' => $conflicts['ruangan']->map(fn($c) => [
                'course_id' => $c->id,
                'kelas_id' => $c->kelas?->id ?? null,
                'kelas' => $c->kelas?->nama_kelas ?? null,
                'jurusan' => $c->kelas?->jurusan ?? null,
                'tahun_ajaran' => $c->kelas?->tahun_ajaran ?? null,
                'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
                'jam_mulai' => $c->jam_mulai,
                'jam_selesai' => $c->jam_selesai,
                'ruangan' => $c->ruangan,
            ])->values(),
            'kelas' => $conflicts['kelas']->map(fn($c) => [
                'course_id' => $c->id,
                'kelas_id' => $c->kelas?->id ?? null,
                'kelas' => $c->kelas?->nama_kelas ?? null,
                'jurusan' => $c->kelas?->jurusan ?? null,
                'tahun_ajaran' => $c->kelas?->tahun_ajaran ?? null,
                'mata_pelajaran' => $c->mataPelajaran?->nama_mata_pelajaran ?? null,
                'jam_mulai' => $c->jam_mulai,
                'jam_selesai' => $c->jam_selesai,
                'ruangan' => $c->ruangan,
            ])->values(),
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

        // daftar kelas untuk selector (admin/guru bisa pilih)
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        // tentukan kelas yang diminta / default
        $selectedKelasId = $request->input('kelas_id');

        // role-specific defaults
        if (! $selectedKelasId) {
            if (Auth::check() && Auth::user()->role === 'siswa' && Auth::user()->siswa) {
                $selectedKelasId = Auth::user()->siswa->kelas_id;
            } elseif (Auth::check() && Auth::user()->role === 'guru') {
                // default: null -> tampilkan semua course milik guru (across kelas)
                $selectedKelasId = null;
            } else {
                // admin: default pilih kelas pertama jika ada
                $selectedKelasId = $kelasList->first()?->id ?? null;
            }
        }

        // build base query for courses to include in timetable
        $query = Course::with(['mataPelajaran.guru', 'kelas', 'siswa.user']);

        // filter by kelas if provided
        if ($selectedKelasId) {
            $query->where('kelas_id', $selectedKelasId);
        }

        // jika user role guru: batasi ke mata pelajaran yang milik guru (tetapi biarkan optional untuk admin)
        if (Auth::check() && Auth::user()->role === 'guru') {
            $user = Auth::user();
            $guruUserId = $user->id;
            $guruModelId = null;
            if (method_exists($user, 'guru') && $user->guru) {
                $guruModelId = $user->guru->id ?? null;
                $possibleUserId = $user->guru->user_id ?? null;
                if ($possibleUserId) $guruUserId = $possibleUserId;
            }
            $query->whereHas('mataPelajaran', function ($q) use ($guruUserId, $guruModelId) {
                $q->where('guru_id', $guruUserId);
                if ($guruModelId) $q->orWhere('guru_id', $guruModelId);
            });
        }

        // jika siswa: batasi sesuai kelas (sudah di atas) - atau pakai pivot jika ingin ketat
        if (Auth::check() && Auth::user()->role === 'siswa' && Auth::user()->siswa) {
            // already handled with selectedKelasId above (default). Optionally ensure where('kelas_id', ...)
        }

        $courses = $query->get();

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

        // base query helper for guru filter
        $isGuru = Auth::check() && Auth::user()->role === 'guru';
        $guruUserId = null;
        if ($isGuru) {
            $user = Auth::user();
            $guruUserId = $user->id;
            if (method_exists($user, 'guru') && $user->guru) {
                $possibleUserId = $user->guru->user_id ?? null;
                if ($possibleUserId) $guruUserId = $possibleUserId;
            }
        }

        // If a single kelas is requested -> render single page only
        $pages = [];

        if ($selectedKelasId) {
            $kelas = Kelas::find($selectedKelasId);
            if ($kelas) {
                $query = Course::with(['mataPelajaran.guru', 'kelas', 'siswa.user'])
                    ->where('kelas_id', $kelas->id);

                if ($isGuru) {
                    $query->whereHas('mataPelajaran', function ($q) use ($guruUserId) {
                        $q->where('guru_id', $guruUserId)->orWhere('guru_id', $guruUserId);
                    });
                }

                $courses = $query->get();
                $timetable = $this->buildTimetableData($courses, $slotOrder, $slotDetails);

                $pages[] = [
                    'kelas' => $kelas,
                    'timetable' => $timetable,
                ];
            }
        } else {
            // No kelas selected -> generate pages per kelas
            if ($isGuru) {
                // only kelas where this guru has courses
                $kelasIds = Course::whereHas('mataPelajaran', function ($q) use ($guruUserId) {
                    $q->where('guru_id', $guruUserId)->orWhere('guru_id', $guruUserId);
                })->pluck('kelas_id')->unique()->filter()->values()->toArray();

                $kelasList = Kelas::whereIn('id', $kelasIds)->orderBy('nama_kelas')->get();
            } else {
                // admin / other -> all kelas
                $kelasList = Kelas::orderBy('nama_kelas')->get();
            }

            foreach ($kelasList as $kelas) {
                $query = Course::with(['mataPelajaran.guru', 'kelas', 'siswa.user'])
                    ->where('kelas_id', $kelas->id);

                if ($isGuru) {
                    $query->whereHas('mataPelajaran', function ($q) use ($guruUserId) {
                        $q->where('guru_id', $guruUserId)->orWhere('guru_id', $guruUserId);
                    });
                }

                $courses = $query->get();
                $timetable = $this->buildTimetableData($courses, $slotOrder, $slotDetails);

                $pages[] = [
                    'kelas' => $kelas,
                    'timetable' => $timetable,
                ];
            }
        }

        // if nothing to render, fallback to single empty page with message
        if (empty($pages)) {
            // create an empty "dummy" page
            $pages[] = [
                'kelas' => null,
                'timetable' => [
                    'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                    'slotOrder' => $slotOrder,
                    'slotDetails' => $slotDetails,
                    'matrix' => [], // view will render empties
                ],
            ];
        }

        // kelasList for header lookups (optional)
        $kelasListAll = Kelas::orderBy('nama_kelas')->get();

        // render a single view that loops pages
        $viewHtml = view('sistem_akademik.course.download', [
            'pages' => $pages,
            'kelasList' => $kelasListAll,
            'selectedKelasId' => $selectedKelasId,
        ])->render();

        if (class_exists(Pdf::class)) {
            $fileLabel = $selectedKelasId ? ('kelas_' . $selectedKelasId) : ($isGuru ? 'guru_' . ($guruUserId ?? 'user') : 'semua_kelas');
            $pdf = Pdf::loadHTML($viewHtml)->setPaper('a4', 'landscape');
            return $pdf->stream("jadwal_{$fileLabel}.pdf");
        }

        // fallback: HTML preview
        return response($viewHtml);
    }
}
