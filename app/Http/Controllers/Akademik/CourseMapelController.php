<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CourseMapelController extends Controller
{
    /**
     * Urutan slot termasuk break label
     */
    protected function slotOrder(): array
    {
        return [
            '1','2','3','istirahat',
            '4','5','6','ISHOMA',
            '7', '8','9','10','ISHO',
            '11','12','13'
        ];
    }

    /**
     * Detail waktu tiap slot (format H:i) dan apakah selectable
     */
    protected function slotDetails(): array
    {
        return [
            '1' => ['label' => 'Jam 1',  'start' => '07:15', 'end' => '08:00',  'selectable' => true],
            '2' => ['label' => 'Jam 2',  'start' => '08:00', 'end' => '08:45',  'selectable' => true],
            '3' => ['label' => 'Jam 3',  'start' => '08:45', 'end' => '09:30',  'selectable' => true],
            'istirahat' => ['label' => 'Istirahat', 'start' => '09:30', 'end' => '10:00', 'selectable' => false],
            '4' => ['label' => 'Jam 4',  'start' => '10:00', 'end' => '11:30',  'selectable' => true],
            '5' => ['label' => 'Jam 5',  'start' => '10:45', 'end' => '11:30',  'selectable' => true],
            '6' => ['label' => 'Jam 6',  'start' => '11:30', 'end' => '12:15',  'selectable' => true],
            'ISHOMA' => ['label' => 'ISHOMA', 'start' => '12:15', 'end' => '13:15', 'selectable' => false],
            '7' => ['label' => 'Jam 7',  'start' => '13:15', 'end' => '13:45',  'selectable' => true],
            '8' => ['label' => 'Jam 8',  'start' => '13:45', 'end' => '14:45',  'selectable' => true],
            '9' => ['label' => 'Jam 9',  'start' => '14:15', 'end' => '14:45',  'selectable' => true],
            '10' => ['label' => 'Jam 10', 'start' => '14:45', 'end' => '15:15',  'selectable' => true],
            'ISHO' => ['label' => 'ISHO', 'start' => '15:15', 'end' => '15:45', 'selectable' => false],
            '11' => ['label' => 'Jam 11', 'start' => '15:45', 'end' => '16:15',  'selectable' => true],
            '12' => ['label' => 'Jam 12', 'start' => '16:15', 'end' => '16:45',  'selectable' => true],
            '13' => ['label' => 'Jam 13', 'start' => '16:45', 'end' => '17:00',  'selectable' => true],
        ];
    }

    /**
     * Hari yang diperbolehkan (business rule)
     */
    protected function allowedDays(): array
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    }

    /**
     * Slot yang dapat dipilih (exclude breaks)
     */
    protected function selectableSlots(): array
    {
        $details = $this->slotDetails();
        return array_filter($details, fn($s) => $s['selectable']);
    }

    /**
     * Konversi slot range ke jam mulai/jam selesai.
     * Melempar Exception jika range tidak valid atau melintasi break.
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

        // pastikan tidak melewati break/ISHOMA/ISHO
        for ($i = $s; $i <= $e; $i++) {
            $id = $order[$i];
            if (!isset($details[$id])) throw new \InvalidArgumentException("Detail slot tidak ditemukan ($id).");
            if ($details[$id]['selectable'] === false) {
                throw new \InvalidArgumentException('Range melintasi waktu istirahat/ISHOMA/ISHO.');
            }
        }

        return [$details[$startId]['start'], $details[$endId]['end']];
    }

    /**
     * Cek apakah dua interval waktu overlap
     */
    protected function timesOverlap(string $aStart, string $aEnd, string $bStart, string $bEnd): bool
    {
        $aS = Carbon::createFromFormat('H:i', $aStart);
        $aE = Carbon::createFromFormat('H:i', $aEnd);
        $bS = Carbon::createFromFormat('H:i', $bStart);
        $bE = Carbon::createFromFormat('H:i', $bEnd);

        return $aS->lt($bE) && $bS->lt($aE);
    }

    /**
     * Cek konflik berdasarkan hari/start/end terhadap seluruh course pada hari yang sama.
     * Mengembalikan koleksi course yang bentrok per kategori: guru, ruangan, kelas.
     *
     * Important: karena `courses` tidak menyimpan guru_id, guru diambil dari $course->mataPelajaran->guru_id
     */
    protected function checkConflicts(string $hari, string $start, string $end, ?int $guruId = null, ?string $ruangan = null, ?int $kelasId = null, ?int $excludeCourseId = null): array
    {
        $conflicts = [
            'guru' => collect(),
            'ruangan' => collect(),
            'kelas' => collect(),
        ];

        $query = Course::with('mataPelajaran')->where('hari', $hari);
        if ($excludeCourseId) $query->where('id', '!=', $excludeCourseId);
        $courses = $query->get();

        foreach ($courses as $c) {
            if (!$c->jam_mulai || !$c->jam_selesai) continue;

            if ($this->timesOverlap($start, $end, substr($c->jam_mulai, 0, 5), substr($c->jam_selesai, 0, 5))) {
                // guru dari mataPelajaran
                $mp = $c->mataPelajaran;
                $cGuruId = $mp->guru_id ?? null;

                if ($guruId && $cGuruId && $cGuruId == $guruId) {
                    $conflicts['guru']->push($c);
                }
                if ($ruangan && $c->ruangan && $c->ruangan == $ruangan) {
                    $conflicts['ruangan']->push($c);
                }
                if ($kelasId && $c->kelas_id == $kelasId) {
                    $conflicts['kelas']->push($c);
                }
            }
        }

        return $conflicts;
    }

    /**
     * Cari slot tunggal yang tersedia pada hari untuk kombinasi kelas,guru,ruangan.
     * (Rekomendasi single-slot — mudah ditampilkan di UI)
     */
    protected function findAvailableSlots(?int $kelasId, ?int $guruId, ?string $ruangan, string $hari): array
    {
        $available = [];
        $order = $this->slotOrder();
        $details = $this->slotDetails();

        foreach ($order as $id) {
            if (!isset($details[$id])) continue;
            if ($details[$id]['selectable'] === false) continue;

            $start = $details[$id]['start'];
            $end = $details[$id]['end'];

            $conflicts = $this->checkConflicts($hari, $start, $end, $guruId, $ruangan, $kelasId);
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

    /* ===========================
     * PUBLIC ACTIONS (resource)
     * =========================== */

    public function index()
    {
        $title = 'Kelola Course & Mata Pelajaran';
        $header = 'Jadwal Mata Pelajaran';

        $courses = Course::with(['mataPelajaran', 'kelas'])->orderBy('hari')->orderBy('jam_mulai')->get();

        return view('sistem_akademik.coursemapel.index', compact('courses', 'title', 'header'));
    }

    public function create()
    {
        $title = 'Tambah Jadwal';
        $header = 'Tambah Jadwal Mapel';

        $kelas = Kelas::all();
        $mapels = MataPelajaran::with('guru')->get(); // tiap mapel tahu gurunya
        $slots = $this->selectableSlots();

        return view('sistem_akademik.coursemapel.createOrEdit', compact('kelas', 'mapels', 'slots', 'title', 'header'));
    }

    /**
     * Store new course.
     * Input can be slot_start+slot_end OR jam_mulai+jam_selesai.
     * Note: guruId dihitung dari mata_pelajaran yang dipilih.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
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

        // hari business rule
        if (!in_array($request->hari, $this->allowedDays())) {
            return back()->withErrors(['hari' => 'Hari harus antara Senin sampai Jumat.'])->withInput();
        }

        // tentukan jam mulai/selesai
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

        // Ambil guru dari mataPelajaran
        $mp = MataPelajaran::find($request->mata_pelajaran_id);
        if (!$mp) {
            return back()->withErrors(['mata_pelajaran_id' => 'Mata pelajaran tidak ditemukan.'])->withInput();
        }
        $guruId = $mp->guru_id ?? null;

        // cek konflik (kelas, ruangan, guru)
        $conflicts = $this->checkConflicts($request->hari, $jamMulai, $jamSelesai, $guruId, $request->ruangan, $request->kelas_id);

        if (!$conflicts['guru']->isEmpty() || !$conflicts['ruangan']->isEmpty() || !$conflicts['kelas']->isEmpty()) {
            $recommendations = $this->findAvailableSlots($request->kelas_id, $guruId, $request->ruangan, $request->hari);

            return back()
                ->with('status', 'error')
                ->with('message', 'Terjadi bentrok jadwal (guru/ruangan/kelas). Lihat rekomendasi slot kosong.')
                ->with('conflicts', $conflicts)
                ->with('recommendations', $recommendations)
                ->withInput();
        }

        // simpan course
        $course = Course::create([
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'hari' => $request->hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'ruangan' => $request->ruangan,
        ]);

        // attach siswa jika ada (asumsi relasi many-to-many ada)
        if ($request->filled('siswa_ids') && method_exists($course, 'siswa')) {
            $course->siswa()->attach($request->siswa_ids);
        }

        return redirect()->route('sistem_akademik.coursemapel.index')
            ->with('status', 'success')
            ->with('message', 'Jadwal berhasil dibuat.');
    }

    public function show(Course $course)
    {
        $course->load(['mataPelajaran.guru', 'kelas', 'siswa']);
        return view('sistem_akademik.coursemapel.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $title = 'Edit Jadwal';
        $header = 'Edit Jadwal Mapel';

        $kelas = Kelas::all();
        $mapels = MataPelajaran::with('guru')->get();
        $slots = $this->selectableSlots();

        // mapping selected slot jika sesuai
        $selected = ['slot_start' => null, 'slot_end' => null];
        foreach ($this->slotDetails() as $id => $d) {
            if ($d['selectable']) {
                if (substr($course->jam_mulai, 0, 5) == $d['start']) $selected['slot_start'] = $id;
                if (substr($course->jam_selesai, 0, 5) == $d['end']) $selected['slot_end'] = $id;
            }
        }

        $selectedSiswaIds = method_exists($course, 'siswa') ? $course->siswa->pluck('id')->toArray() : [];

        return view('sistem_akademik.coursemapel.createOrEdit', compact(
            'course',
            'kelas',
            'mapels',
            'slots',
            'selected',
            'selectedSiswaIds',
            'title',
            'header'
        ));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
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

        $mp = MataPelajaran::find($request->mata_pelajaran_id);
        $guruId = $mp->guru_id ?? null;

        // cek konflik exclude current course
        $conflicts = $this->checkConflicts($request->hari, $jamMulai, $jamSelesai, $guruId, $request->ruangan, $request->kelas_id, $course->id);

        if (!$conflicts['guru']->isEmpty() || !$conflicts['ruangan']->isEmpty() || !$conflicts['kelas']->isEmpty()) {
            $recommendations = $this->findAvailableSlots($request->kelas_id, $guruId, $request->ruangan, $request->hari);

            return back()
                ->with('status', 'error')
                ->with('message', 'Terjadi bentrok jadwal saat update. Lihat rekomendasi slot.')
                ->with('conflicts', $conflicts)
                ->with('recommendations', $recommendations)
                ->withInput();
        }

        $course->update([
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
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

        return redirect()->route('sistem_akademik.coursemapel.index')
            ->with('status', 'success')
            ->with('message', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        if (method_exists($course, 'siswa')) {
            $course->siswa()->detach();
        }
        $course->delete();

        return redirect()->route('sistem_akademik.coursemapel.index')
            ->with('status', 'success')
            ->with('message', 'Jadwal berhasil dihapus.');
    }

    /**
     * Endpoint AJAX: rekomendasi slot kosong untuk kombinasi kelas,guru,ruangan pada hari tertentu.
     * Request: kelas_id (nullable), mata_pelajaran_id (nullable), ruangan (nullable), hari (required)
     * Mengembalikan available_slots.
     */
    public function getRecommendations(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
            'ruangan' => 'nullable|string',
            'hari' => 'required|string',
        ]);

        if (!in_array($request->hari, $this->allowedDays())) {
            return response()->json(['success' => false, 'message' => 'Hari harus antara Senin sampai Jumat.'], 422);
        }

        $guruId = null;
        if ($request->filled('mata_pelajaran_id')) {
            $mp = MataPelajaran::find($request->mata_pelajaran_id);
            $guruId = $mp->guru_id ?? null;
        }

        $available = $this->findAvailableSlots($request->kelas_id, $guruId, $request->ruangan, $request->hari);

        return response()->json([
            'success' => true,
            'available_slots' => $available
        ]);
    }
}
