<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\GuruExport;
use App\Exports\SiswaExport;
use Maatwebsite\Excel\Excel as ExcelWriter;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.manage.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // definisikan role sesuai kebutuhan
        $roles = [
            'super_admin'   => 'Super Admin',
            'admin_ppdb'    => 'Admin PPDB',
            'admin_sa'      => 'Admin Sistem Akademik',
            'admin_perpus'  => 'Admin Perpustakaan',
            'admin_lab'     => 'Admin Laboratorium',
            'admin_magang'  => 'Admin Magang',
            'wakil_perusahaan' => 'Wakil Perusahaan',
            'guru'          => 'Guru',
            'siswa'         => 'Siswa',
        ];

        return view('admin.manage.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:255',
            'nis_nip'  => 'required|string|max:255|unique:users,nis_nip',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:' . implode(',', array_keys($this->rolesList())),
        ]);

        User::create([
            'nama'     => $data['nama'],
            'nis_nip'  => $data['nis_nip'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        return redirect()
            ->route('admin.manage.users')
            ->with('success', 'User berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = $this->rolesList();
        return view('admin.manage.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nama'     => 'required|string|max:255',
            'nis_nip'  => 'nullable|string|max:255|unique:users,nis_nip,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|in:' . implode(',', array_keys($this->rolesList())),
        ]);

        $user->nama  = $data['nama'];
        $user->nis_nip = $data['nis_nip'];
        $user->email = $data['email'];
        $user->role  = $data['role'];
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return redirect()
            ->route('admin.manage.users')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Show the import CSV form.
     */
    public function showImportForm()
    {
        return view('admin.manage.users.import');
    }

    /**
     * Download template CSV for import.
     */
    public function downloadTemplate(string $type = 'siswa')
    {
        $type = in_array($type, ['guru', 'siswa']) ? $type : 'siswa';

        if ($type === 'guru') {
            $headers = ['NIP', 'Nama', 'Email', 'Jurusan', 'Jenis kelamin', 'agama', 'Tanggal lahir', 'Alamat', 'No.hp'];
            $example = ['198501012010011001', 'Contoh Nama Guru', 'guru@sekolah.sch.id', 'Teknik Komputer dan Jaringan', 'Laki-laki', 'Islam', '1985-01-01', 'Jl. Contoh No.1 Padang', '081234567890'];
        } else {
            $headers = ['NIS', 'Nama', 'Email', 'Jurusan', 'kelas', 'Jenis kelamin', 'agama', 'Tanggal lahir', 'Alamat', 'No.hp'];
            $example = ['12345', 'Contoh Nama Siswa', 'siswa@sekolah.sch.id', 'Teknik Komputer dan Jaringan', 'XII TKJ 1', 'Laki-laki', 'Islam', '2006-01-01', 'Jl. Contoh No.1 Padang', '081234567890'];
        }

        $filename = "template-import-{$type}.csv";
        $handle = fopen('php://output', 'w');

        return response()->stream(function () use ($headers, $example, $handle) {
            fputcsv($handle, $headers);
            fputcsv($handle, $example);
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Handle CSV import.
     */
    public function import(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $request->validate([
            'role'     => 'required|in:guru,siswa',
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        Excel::import(new UsersImport($request->role), $request->file('csv_file'));

        return redirect()
            ->route('admin.manage.users')
            ->with('success', 'Import pengguna berhasil.');
    }

    /**
     * Export all users ke CSV atau XLSX.
     */
    public function export(Request $request)
    {
        $format = $request->query('format', 'xlsx');
        $now    = now()->format('Ymd_His');
        $file   = "users-{$now}.{$format}";
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
        return Excel::download(new UsersExport, $file, $writerType);
    }

    /**
     * Export data Guru dengan filter jurusan.
     */
    public function exportGuru(Request $request)
    {
        $jurusan = $request->query('jurusan') ?: null;
        $format  = $request->query('format', 'xlsx');
        $now     = now()->format('Ymd_His');
        $suffix  = $jurusan ? '_' . \Illuminate\Support\Str::slug($jurusan) : '';
        $file    = "guru{$suffix}-{$now}.{$format}";
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
        return Excel::download(new GuruExport($jurusan), $file, $writerType);
    }

    /**
     * Export data Siswa dengan filter jurusan dan kelas.
     */
    public function exportSiswa(Request $request)
    {
        $jurusan = $request->query('jurusan') ?: null;
        $kelasId = $request->query('kelas_id') ? (int) $request->query('kelas_id') : null;
        $format  = $request->query('format', 'xlsx');
        $now     = now()->format('Ymd_His');
        $suffix  = $jurusan ? '_' . \Illuminate\Support\Str::slug($jurusan) : '';
        $file    = "siswa{$suffix}-{$now}.{$format}";
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
        return Excel::download(new SiswaExport($jurusan, $kelasId), $file, $writerType);
    }

    /**
     * Helper: daftar role.
     */
    protected function rolesList()
    {
        return [
            'super_admin'   => 'Super Admin',
            'admin_ppdb'    => 'Admin PPDB',
            'admin_sa'      => 'Admin Sistem Akademik',
            'admin_perpus'  => 'Admin Perpustakaan',
            'admin_lab'     => 'Admin Laboratorium',
            'admin_magang'  => 'Admin Magang',
            'wakil_perusahaan' => 'Wakil Perusahaan',
            'guru'          => 'Guru',
            'siswa'         => 'Siswa',
        ];
    }
}
