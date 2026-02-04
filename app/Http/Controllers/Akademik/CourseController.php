<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $title = 'Kelola Course';
        $header = 'Data Course';

        // Perbaikan kondisi pengecekan role
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa') {
            // Admin dapat melihat semua course
            $courses = Course::with(['kelas', 'mataPelajaran', 'guru', 'siswa'])->get();
        } elseif (Auth::user()->role == 'guru') {
            // Guru hanya melihat course yang mereka ajar
            $courses = Course::with(['kelas', 'mataPelajaran', 'guru', 'siswa'])
                ->where('guru_id', Auth::id())
                ->get();
        } elseif (Auth::user()->role == 'siswa' && Auth::user()->siswa) {
            // Siswa melihat course berdasarkan kelas mereka dan courses yang mereka ikuti
            $siswa = Auth::user()->siswa;
            $courses = $siswa->courses()->with(['kelas', 'mataPelajaran', 'guru'])->get();

            // Jika siswa belum tergabung dalam course manapun, tampilkan course untuk kelas mereka
            if ($courses->isEmpty() && $siswa->kelas_id) {
                $courses = Course::with(['kelas', 'mataPelajaran', 'guru', 'siswa'])
                    ->where('kelas_id', $siswa->kelas_id)
                    ->get();
            }
        } else {
            // Default case - tampilkan list kosong jika tidak memenuhi kondisi di atas
            $courses = collect();
        }

        return view('sistem_akademik.course.index', compact('courses', 'title', 'header'));
    }

    public function create()
    {
        $title = 'Kelola Course';
        $header = 'Tambah Data Course';

        $kelas = Kelas::all();
        $mataPelajaran = MataPelajaran::all();
        $guru = User::where('role', 'guru')->get();

        // Get unique jurusan list for dropdown
        $jurusanList = Kelas::select('jurusan')->distinct()->pluck('jurusan')->toArray();

        // We'll load students via AJAX after jurusan is selected
        $siswa = collect();

        return view('sistem_akademik.course.createOrEdit', compact(
            'title',
            'header',
            'kelas',
            'mataPelajaran',
            'guru',
            'siswa',
            'jurusanList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:users,id',
            'nama_course' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $course = Course::create([
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => $request->guru_id,
            'nama_course' => $request->nama_course,
            'deskripsi' => $request->deskripsi,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        // Jika ada siswa yang dipilih, attach ke course
        if ($request->has('siswa_ids') && is_array($request->siswa_ids)) {
            $course->siswa()->attach($request->siswa_ids);
        }

        return redirect()->route('sistem_akademik.course.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Data course berhasil ditambahkan');
    }

    public function show(Course $course)
    {
        $title = 'Detail Course';
        $header = 'Detail Course';

        // Load siswa untuk ditampilkan dalam detail
        $course->load('siswa.user');

        return view('sistem_akademik.course.show', compact('course', 'title', 'header'));
    }

    public function edit(Course $course)
    {
        $title = 'Kelola Course';
        $header = 'Edit Data Course';

        $kelas = Kelas::all();
        $mataPelajaran = MataPelajaran::all();
        $guru = User::where('role', 'guru')->get();

        // Get unique jurusan list for dropdown
        $jurusanList = Kelas::select('jurusan')->distinct()->pluck('jurusan')->toArray();

        // For edit, we need to load students from the selected jurusan
        $selectedJurusan = $course->kelas->jurusan;
        $siswa = Siswa::whereHas('kelas', function ($query) use ($selectedJurusan) {
            $query->where('jurusan', $selectedJurusan);
        })->with('user')->get();

        // Ambil ID siswa yang sudah tergabung dengan course
        $selectedSiswaIds = $course->siswa->pluck('id')->toArray();

        return view('sistem_akademik.course.createOrEdit', compact(
            'course',
            'title',
            'header',
            'kelas',
            'mataPelajaran',
            'guru',
            'siswa',
            'selectedSiswaIds',
            'jurusanList',
            'selectedJurusan'
        ));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:users,id',
            'nama_course' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $course->update([
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => $request->guru_id,
            'nama_course' => $request->nama_course,
            'deskripsi' => $request->deskripsi,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        // Sync siswa yang dipilih dengan course
        if ($request->has('siswa_ids')) {
            $course->siswa()->sync($request->siswa_ids);
        } else {
            // Jika tidak ada siswa yang dipilih, hapus semua relasi
            $course->siswa()->detach();
        }

        return redirect()->route('sistem_akademik.course.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Data course berhasil diperbarui');
    }

    public function destroy(Course $course)
    {
        // Detach semua siswa terlebih dahulu
        $course->siswa()->detach();
        // Hapus course
        $course->delete();

        return redirect()->route('sistem_akademik.course.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Data course berhasil dihapus');
    }

    // Add a new method to fetch students by jurusan
    public function getStudentsByJurusan(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'jurusan'  => 'required|string',
        ]);

        $students = Siswa::where('kelas_id', $request->kelas_id)
            ->whereHas('kelas', fn($q) => $q->where('jurusan', $request->jurusan))
            ->with('user')
            ->get();

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }
}
