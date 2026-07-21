<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\ScheduleGeneration;
use App\Models\ScheduleDraft;
use App\Models\Course;
use App\Jobs\GenerateScheduleJob;

class AutoScheduleController extends Controller
{
    public function index()
    {
        $title = "Auto-Scheduling";
        $header = "Jadwal Otomatis AI";
        $jurusans = Kelas::whereNotNull('jurusan')->distinct()->pluck('jurusan');
        
        $kelasRooms = Kelas::whereNotNull('ruangan')->where('ruangan', '!=', '')->pluck('ruangan')->toArray();
        $courseRooms = Course::whereNotNull('ruangan')->where('ruangan', '!=', '')->pluck('ruangan')->toArray();
        
        $existingRooms = array_unique(array_diff($courseRooms, $kelasRooms));
        sort($existingRooms);

        $aiModelName = env('GEMINI_MODEL', 'Gemini Pro');

        return view('sistem_akademik.auto_schedule.wizard', compact('title', 'header', 'jurusans', 'existingRooms', 'aiModelName'));
    }

    public function getClasses(Request $request)
    {
        $jurusan = $request->jurusan;
        $kelas = Kelas::where('jurusan', $jurusan)->get();
        return response()->json($kelas);
    }

    public function getMapel(Request $request)
    {
        $jurusan = $request->jurusan;
        
        $mapels = MataPelajaran::with('guru')->where(function($q) use ($jurusan) {
            $q->where('jurusan', $jurusan)
              ->orWhereNull('jurusan')
              ->orWhere('jurusan', '')
              ->orWhereRaw('LOWER(jurusan) = ?', ['umum'])
              ->orWhereRaw('LOWER(kategori_penjadwalan) = ?', ['umum']);
        })->get();

        // Calculate existing guru workload
        $guruJp = DB::table('courses')
            ->join('mata_pelajaran', 'courses.mata_pelajaran_id', '=', 'mata_pelajaran.id')
            ->select('courses.kelas_id', 'mata_pelajaran.id as mp_id', 'mata_pelajaran.guru_id', 'mata_pelajaran.jp')
            ->distinct()
            ->get()
            ->groupBy('guru_id')
            ->map(function($items) { return $items->sum('jp'); });

        // Append existing workload to mapel objects
        $mapels->each(function($mapel) use ($guruJp) {
            $mapel->guru_existing_jp = $guruJp->get($mapel->guru_id, 0);
        });

        return response()->json($mapels);
    }

    public function validateData(Request $request)
    {
        $jurusan = $request->jurusan;
        $kelasIds = $request->kelas_ids ?? [];
        
        $kelasQuery = Kelas::whereIn('id', $kelasIds);
        $kelasList = $kelasQuery->get();

        $errors = [];

        // Check if all classes have a room (ruangan)
        foreach ($kelasList as $kelas) {
            if (empty($kelas->ruangan)) {
                $errors[] = "Kelas {$kelas->nama_kelas} belum memiliki ruangan yang diatur.";
            }
        }

        // Check mapel for guru and JP
        $mapelIds = $request->mapel_ids ?? [];
        if (empty($mapelIds)) {
            $errors[] = "Tidak ada mata pelajaran yang dipilih.";
        } else {
            $mapels = MataPelajaran::whereIn('id', $mapelIds)->get();
            foreach ($mapels as $mp) {
                if (empty($mp->guru_id)) {
                    $errors[] = "Mata Pelajaran {$mp->nama_mata_pelajaran} belum memiliki guru pengampu.";
                }
                if (empty($mp->jp) || $mp->jp < 1) {
                    $errors[] = "Mata Pelajaran {$mp->nama_mata_pelajaran} tidak memiliki JP yang valid.";
                }
            }
        }

        return response()->json([
            'success' => count($errors) === 0,
            'errors' => $errors
        ]);
    }

    public function generate(Request $request)
    {
        $jurusan = $request->jurusan;
        $kelasIds = $request->kelas_ids ?? [];
        $mapelIds = $request->mapel_ids ?? [];

        $options = [
            'opt_umum' => $request->opt_umum,
            'opt_jurusan' => $request->opt_jurusan,
            'mapel_rooms' => $request->mapel_rooms ?? [],
            'mapel_distributions' => $request->mapel_distributions ?? [],
        ];
        
        $generation = ScheduleGeneration::create([
            'jurusan' => $jurusan,
            'kelas_ids' => $kelasIds,
            'mapel_ids' => $mapelIds,
            'status' => 'pending',
            'options' => $options
        ]);

        dispatch(new GenerateScheduleJob($generation->id));

        return response()->json([
            'success' => true,
            'generation_id' => $generation->id
        ]);
    }

    public function status($id)
    {
        $gen = ScheduleGeneration::findOrFail($id);
        return response()->json([
            'status' => $gen->status,
            'skor_kualitas' => $gen->skor_kualitas,
            'total_konflik' => $gen->total_konflik
        ]);
    }

    public function result($id)
    {
        $title = "Hasil Auto-Scheduling";
        $header = "Review Draf Jadwal";
        $generation = ScheduleGeneration::with(['drafts.kelas', 'drafts.mataPelajaran.guru'])->findOrFail($id);

        // Jika statusnya belum selesai, kembalikan ke index
        if ($generation->status !== 'completed' && $generation->status !== 'failed') {
            return redirect()->route('sistem_akademik.auto-schedule.index')->with('error', 'Proses generate belum selesai.');
        }

        return view('sistem_akademik.auto_schedule.result', compact('title', 'header', 'generation'));
    }

    public function apply($id)
    {
        $generation = ScheduleGeneration::with('drafts')->findOrFail($id);

        if ($generation->status !== 'completed') {
            return back()->with('error', 'Hanya draf yang sudah selesai yang bisa diterapkan.');
        }

        // Hapus jadwal lama untuk kelas-kelas yang bersangkutan?
        // User bilang "jangan lakukan migrate fresh yang akan menghapus jadwal yang sudah ada"
        // Namun, jika kita generate ulang, apakah jadwal lama dihapus?
        // Secara logika, iya, tapi kita harus spesifik hanya kelas yang digenerate.
        $kelasIds = $generation->kelas_ids;
        if (!empty($kelasIds)) {
            Course::whereIn('kelas_id', $kelasIds)->delete();
        }

        // Insert draf ke course
        foreach ($generation->drafts as $draft) {
            Course::create([
                'kelas_id' => $draft->kelas_id,
                'mata_pelajaran_id' => $draft->mata_pelajaran_id,
                'hari' => $draft->hari,
                'jam_mulai' => $draft->jam_mulai,
                'jam_selesai' => $draft->jam_selesai,
                'ruangan' => $draft->ruangan
            ]);
        }

        return redirect()->route('sistem_akademik.course.index')->with([
            'status' => 'success',
            'message' => 'Jadwal otomatis berhasil diterapkan!'
        ]);
    }
}
