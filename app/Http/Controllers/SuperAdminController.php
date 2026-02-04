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
        $title = 'Manajemen Pengguna';
        $header = 'Kelola Pengguna Sistem';
        $users = User::all();
        return view('admin.manage.users.index', compact('users', 'title', 'header'));
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

        return view('admin.manage.users.create', compact('title', 'header', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nis_nip' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4',
            'role' => ['required', Rule::in(['super_admin', 'admin_ppdb', 'admin_sa', 'admin_perpus', 'admin_lab', 'admin_magang', 'wakil_perusahaan', 'guru', 'siswa'])],
        ]);

        $user = User::create([
            'nis_nip' => $request->nis_nip,
            'nama' => $request->nama,
            'email' => $request->email,
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
        }

        if ($data['role'] === 'guru') {
            $user->guru()->create([
                'nip'          => $data['nis_nip'],
                'kelas'        => null,
                'jurusan'      => null,
                'tanggal_lahir' => null,
                'alamat'       => null,
                'no_hp'        => null,
            ]);
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

        return view('admin.manage.users.edit', compact('title', 'header', 'user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'nama'              => 'required|string|max:255',
            'nis_nip'           => 'nullable|string|max:20',
            'email'             => 'required|string|email|max:255|unique:users,email,' . $user->id,
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

        $update = [
            'nama'    => $data['nama'],
            'nis_nip' => $data['nis_nip'],
            'email'   => $data['email'],
            'role'    => $data['role'],
        ];

        // Jika ada new password, hash dan include
        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        // Jika role menjadi siswa → pastikan ada record di siswa
        if ($data['role'] === 'siswa') {
            $s = $user->siswa()
                ->updateOrCreate([], [
                    'nis' => $data['nis_nip'],
                    'kelas_id'      => null,
                    'tanggal_lahir' => null,
                    'agama'         => null,
                    'alamat'        => null,
                    'no_hp'         => null,
                ]);
            // jika sebelumnya guru, hapus guru
            if ($user->wasChanged('role') && $user->getOriginal('role') === 'guru') {
                $user->guru()->delete();
            }
        }

        // Jika role menjadi guru → pastikan ada record di guru
        if ($data['role'] === 'guru') {
            if ($user->guru) {
                $user->guru->update([
                    'nip' => $data['nis_nip'],
                ]);
            } else {
                $user->guru()->create([
                    'nip'           => $data['nis_nip'],
                    'kelas'         => null,
                    'jurusan'       => null,
                    'tanggal_lahir' => null,
                    'agama'         => null,
                    'alamat'        => null,
                    'no_hp'         => null,
                ]);
            }
            // jika sebelumnya siswa, hapus siswa
            if ($user->wasChanged('role') && $user->getOriginal('role') === 'siswa') {
                $user->siswa()->delete();
            }
        }

        $user->update($update);

        return redirect()->route('admin.manage.users')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Data pengguna berhasil diperbarui');
    }

    public function destroyUser(User $user)
    {
        $user->siswa()->delete();
        $user->guru()->delete();
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
            User::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' user berhasil dihapus.');
        }
        return back()->with('warning', 'Tidak ada user yang dipilih.');
    }
}
