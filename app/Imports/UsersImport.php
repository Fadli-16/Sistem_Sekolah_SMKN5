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

            // Kondisi update berdasarkan nis_nip
            if (!empty($nis_nip)) {
                // Import berdasarkan nis_nip: update jika sudah ada
                $user = User::updateOrCreate(
                    ['nis_nip' => $nis_nip],
                    [
                        'nama'     => $row['nama'] ?? '',
                        'email'    => filter_var($emailRaw, FILTER_VALIDATE_EMAIL) ? $emailRaw : null,
                        'password' => Hash::make($password),
                        'role'     => $role,
                    ]
                );
            } elseif (filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
                // Fallback jika nis_nip kosong tapi ada email
                $user = User::updateOrCreate(
                    ['email' => $emailRaw],
                    [
                        'nama'     => $row['nama'] ?? '',
                        'nis_nip'  => null,
                        'password' => Hash::make($password),
                        'role'     => $role,
                    ]
                );
            } else {
                // Keduanya kosong/invalid → lewati atau buat record baru (disini kita buat baru dgn email null)
                $user = User::create([
                    'nama'     => $row['nama'] ?? '',
                    'nis_nip'  => null,
                    'email'    => null,
                    'password' => Hash::make($password),
                    'role'     => $role,
                ]);
            }

            if ($role === 'siswa') {
                $siswaData = ['nis' => $nis_nip];
                if (isset($row['kelas'])) $siswaData['kelas'] = $row['kelas'];
                if (isset($row['jurusan'])) $siswaData['jurusan'] = $row['jurusan'];
                if (isset($row['tanggal_lahir'])) $siswaData['tanggal_lahir'] = $row['tanggal_lahir'];
                if (isset($row['jenis_kelamin'])) $siswaData['jenis_kelamin'] = $row['jenis_kelamin'];
                if (isset($row['agama'])) $siswaData['agama'] = $row['agama'];
                if (isset($row['alamat'])) $siswaData['alamat'] = $row['alamat'];
                if (isset($row['no_hp'])) $siswaData['no_hp'] = $row['no_hp'];

                Siswa::updateOrCreate(
                    ['user_id' => $user->id],
                    $siswaData
                );
            } elseif ($role === 'guru') {
                $guruData = ['nip' => $nis_nip];
                if (isset($row['jurusan'])) $guruData['jurusan'] = $row['jurusan'];
                if (isset($row['tanggal_lahir'])) $guruData['tanggal_lahir'] = $row['tanggal_lahir'];
                if (isset($row['jenis_kelamin'])) $guruData['jenis_kelamin'] = $row['jenis_kelamin'];
                if (isset($row['agama'])) $guruData['agama'] = $row['agama'];
                if (isset($row['alamat'])) $guruData['alamat'] = $row['alamat'];
                if (isset($row['no_hp'])) $guruData['no_hp'] = $row['no_hp'];

                Guru::updateOrCreate(
                    ['user_id' => $user->id],
                    $guruData
                );
            }
        }
    }
}
