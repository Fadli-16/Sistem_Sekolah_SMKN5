<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class UsersImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $role;

    public function __construct($role)
    {
        $this->role = $role;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $role     = $this->role;
            $emailRaw = trim((string) ($row['email'] ?? ''));
            $password = isset($row['password']) && !empty($row['password']) ? $row['password'] : 'password123';
            
            $nis_nip = $role === 'guru' ? ($row['nip'] ?? '') : ($row['nis'] ?? '');

            // Kondisi email valid?
            if (filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
                // Import berdasarkan email: update jika sudah ada
                $user = User::updateOrCreate(
                    ['email' => $emailRaw],
                    [
                        'nama'     => $row['nama'] ?? '',
                        'nis_nip'  => $nis_nip,
                        'password' => Hash::make($password),
                        'role'     => $role,
                    ]
                );
            } else {
                // Email null/invalid → buat record baru
                $user = User::create([
                    'nama'     => $row['nama'] ?? '',
                    'nis_nip'  => $nis_nip,
                    'email'    => null,
                    'password' => Hash::make($password),
                    'role'     => $role,
                ]);
            }

            if ($role === 'siswa') {
                Siswa::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nis'           => $nis_nip,
                        'kelas_id'      => $row['kelas_id'] ?? null,
                        'kelas'         => $row['kelas'] ?? null,
                        'jurusan'       => $row['jurusan'] ?? null,
                        'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                        'jenis_kelamin' => $row['jenis_kelamin'] ?? 'Laki-laki',
                        'agama'         => $row['agama'] ?? null,
                        'alamat'        => $row['alamat'] ?? null,
                        'no_hp'         => $row['no_hp'] ?? null,
                    ]
                );
            } elseif ($role === 'guru') {
                Guru::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip'           => $nis_nip,
                        'kelas'         => $row['kelas'] ?? null,
                        'jurusan'       => $row['jurusan'] ?? null,
                        'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                        'jenis_kelamin' => $row['jenis_kelamin'] ?? 'Laki-laki',
                        'agama'         => $row['agama'] ?? null,
                        'alamat'        => $row['alamat'] ?? null,
                        'no_hp'         => $row['no_hp'] ?? null,
                    ]
                );
            }
        }
    }
}
