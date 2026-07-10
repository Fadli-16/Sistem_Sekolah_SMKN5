<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Siswa;

class SuperAdminController extends Controller
{
    public function index()
    {
        $title = 'Super Admin Dashboard';
        $header = 'Super Admin - Manajemen Sistem';
        return view('admin.manage.index', compact('title', 'header'));
    }

    public function users()
    {
        $title  = 'Manajemen Pengguna';
        $header = 'Kelola Pengguna Sistem';
        $users  = User::all();

        // Untuk filter export Siswa
        $jurusanSiswaList = \App\Models\Kelas::select('jurusan')
            ->whereNotNull('jurusan')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        // Untuk filter export Guru
        $jurusanGuruList = \App\Models\Guru::select('jurusan')
            ->whereNotNull('jurusan')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        $kelasList = \App\Models\Kelas::orderBy('jurusan')->orderBy('nama_kelas')->get();

        return view('admin.manage.users.index', compact('users', 'title', 'header', 'jurusanSiswaList', 'jurusanGuruList', 'kelasList'));
    }

    public function createUser()
    {
        $title = 'Tambah Pengguna';
        $header = 'Tambah Pengguna Baru';
        $roles = [
            'super_admin' => 'Super Admin',
            'admin_ppdb' => 'Admin PPDB',
            'admin_sa' => 'Admin Sistem Akademik',
            'admin_perpus' => 'Admin Perpustakaan',
            'admin_lab' => 'Admin Laboratorium',
            'admin_magang' => 'Admin Magang',
            'wakil_perusahaan' => 'wakil_perusahaan',
            'guru' => 'Guru',
            'siswa' => 'Siswa'
        ];

        return view('admin.manage.users.createOrEdit', compact('title', 'header', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nis_nip' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:4',
            'role' => ['required', Rule::in(['super_admin', 'admin_ppdb', 'admin_sa', 'admin_perpus', 'admin_lab', 'admin_magang', 'wakil_perusahaan', 'guru', 'siswa'])],
        ]);

        $email = $request->email ?: \Illuminate\Support\Str::slug($request->nama) . '@smkn5padang.sch.id';

        $user = User::create([
            'nis_nip' => $request->nis_nip,
            'nama' => $request->nama,
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($data['role'] === 'siswa') {
            $user->siswa()->create([
                'nis'          =>  $data['nis_nip'],
                'kelas_id'      => null,
                'tanggal_lahir' => null,
                'alamat'        => null,
                'no_hp'         => null,
            ]);
        } elseif ($data['role'] === 'guru') {
            $user->guru()->create([
                'nip'          => $data['nis_nip'],
                'kelas'        => null,
                'jurusan'      => null,
                'tanggal_lahir' => null,
                'alamat'       => null,
                'no_hp'        => null,
            ]);
        } elseif (in_array($data['role'], ['super_admin', 'admin_ppdb', 'admin_sa', 'admin_perpus', 'admin_lab', 'admin_magang'])) {
            $user->adminProfile()->create([]);
        }

        return redirect()->route('admin.manage.users')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Pengguna berhasil ditambahkan');
    }

    public function editUser(User $user)
    {
        $title = 'Edit Pengguna';
        $header = 'Edit Data Pengguna';
        $roles = [
            'super_admin' => 'Super Admin',
            'admin_ppdb' => 'Admin PPDB',
            'admin_sa' => 'Admin Sistem Akademik',
            'admin_perpus' => 'Admin Perpustakaan',
            'admin_lab' => 'Admin Laboratorium',
            'admin_magang' => 'Admin Magang',
            'wakil_perusahaan' => 'wakil_perusahaan',
            'guru' => 'Guru',
            'siswa' => 'Siswa'
        ];

        return view('admin.manage.users.createOrEdit', compact('title', 'header', 'user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'nama'              => 'required|string|max:255',
            'nis_nip'           => 'nullable|string|max:20',
            'email'             => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'old_password'      => ['required_with:password', function ($attr, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password lama tidak sesuai.');
                }
            }],
            'password'          => 'nullable|string|min:4|confirmed',
            'password_confirmation' => 'nullable',
            'role'              => ['required', Rule::in([
                'super_admin',
                'admin_ppdb',
                'admin_sa',
                'admin_perpus',
                'admin_lab',
                'admin_magang',
                'wakil_perusahaan',
                'guru',
                'siswa'
            ])],
        ]);

        $email = $request->email ?: \Illuminate\Support\Str::slug($request->nama) . '@smkn5padang.sch.id';

        $update = [
            'nama'    => $data['nama'],
            'nis_nip' => $data['nis_nip'] ?? null,
            'email'   => $email,
            'role'    => $data['role'],
        ];

        $oldRole = $user->role;
        $newRole = $data['role'];

        $user->update($update);

        // Jika ada new password, hash dan include
        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        // Kelola data siswa
        if ($newRole === 'siswa') {
            $user->siswa()->updateOrCreate([], [
                'nis' => $data['nis_nip'],
                'kelas_id'      => null,
                'tanggal_lahir' => null,
                'agama'         => null,
                'alamat'        => null,
                'no_hp'         => null,
            ]);
        } else {
            $user->siswa()->delete();
        }

        // Kelola data guru
        if ($newRole === 'guru') {
            $user->guru()->updateOrCreate([], [
                'nip' => $data['nis_nip'],
                'kelas'         => null,
                'jurusan'       => null,
                'tanggal_lahir' => null,
                'agama'         => null,
                'alamat'        => null,
                'no_hp'         => null,
            ]);
        } else {
            $user->guru()->delete();
        }

        // Kelola data admin
        if (in_array($newRole, ['super_admin', 'admin_ppdb', 'admin_sa', 'admin_perpus', 'admin_lab', 'admin_magang'])) {
            $user->adminProfile()->updateOrCreate([], []);
        } else {
            $user->adminProfile()->delete();
        }

        return redirect()->route('admin.manage.users')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Data pengguna berhasil diperbarui');
    }

    public function destroyUser(User $user)
    {
        $this->deleteUserPhoto($user);
        
        $user->siswa()->delete();
        $user->guru()->delete();
        $user->adminProfile()->delete();
        $user->delete();

        return redirect()->route('admin.manage.users')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Pengguna berhasil dihapus');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('selected_users', []);
        if (!empty($ids)) {
            $users = User::whereIn('id', $ids)->get();
            foreach ($users as $u) {
                $this->deleteUserPhoto($u);
                $u->siswa()->delete();
                $u->guru()->delete();
                $u->adminProfile()->delete();
            }
            User::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' user berhasil dihapus.');
        }
        return back()->with('warning', 'Tidak ada user yang dipilih.');
    }

    private function deleteUserPhoto(User $user)
    {
        $image = null;
        if ($user->siswa) $image = $user->siswa->image;
        elseif ($user->guru) $image = $user->guru->image;
        elseif ($user->adminProfile) $image = $user->adminProfile->image;

        if ($image) {
            $path = public_path('assets/profile/' . $image);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
