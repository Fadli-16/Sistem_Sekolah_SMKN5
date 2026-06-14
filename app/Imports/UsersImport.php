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
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class UsersImport extends DefaultValueBinder implements ToCollection, WithHeadingRow, WithCustomValueBinder
{
    use Importable;

    public function bindValue(Cell $cell, $value)
    {
        // Prevent scientific notation format issue by forcing string data type for numeric values
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Priority: Role column in CSV
            $role = isset($row['role']) && !empty(trim((string)$row['role'])) ? strtolower(trim((string)$row['role'])) : null;
            
            // Allow role to be guru or siswa
            if (!in_array($role, ['guru', 'siswa'])) {
                continue; // skip invalid or missing role
            }

            $emailRaw = trim((string) ($row['email'] ?? ''));
            $password = isset($row['password']) && !empty(trim((string)$row['password'])) ? trim((string)$row['password']) : 'user123';
            
            $raw_nis_nip = $row['nis_nip'] ?? ($row['nisnip'] ?? ($row['nis'] ?? ($row['nip'] ?? '')));
            $nis_nip = trim((string) $raw_nis_nip);
            if ($nis_nip === '') {
                $nis_nip = null;
            }
            
            // Batasi panjang NIP/NIS maksimal 20 karakter
            if ($nis_nip !== null && strlen($nis_nip) > 20) {
                $nis_nip = substr($nis_nip, 0, 20);
            }

            // Parse date
            $tanggal_lahir = null;
            if (isset($row['tanggal_lahir'])) {
                $rawDate = trim((string) $row['tanggal_lahir']);
                if (!empty($rawDate)) {
                    try {
                        if (is_numeric($rawDate)) {
                            $tanggal_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate)->format('Y-m-d');
                        } else {
                            $tanggal_lahir = \Carbon\Carbon::parse(str_replace('/', '-', $rawDate))->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $tanggal_lahir = null;
                    }
                }
            }
            
            $nohp_val = $row['no_hp'] ?? ($row['nohp'] ?? null);

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
                
                // Get ID Kelas from template
                $idKelas = $row['id_kelas'] ?? ($row['kelas'] ?? null);
                if (!empty($idKelas) && is_numeric($idKelas)) {
                    $siswaData['kelas_id'] = $idKelas;
                    $kelasObj = \App\Models\Kelas::find($idKelas);
                    if ($kelasObj) {
                        $siswaData['kelas'] = $kelasObj->nama_kelas;
                        if (!isset($row['jurusan']) || empty(trim($row['jurusan']))) {
                            $siswaData['jurusan'] = $kelasObj->jurusan;
                        }
                    } else {
                        $siswaData['kelas'] = $idKelas;
                    }
                } elseif (!empty($idKelas)) {
                    $siswaData['kelas'] = $idKelas;
                }

                if (isset($row['jurusan']) && !empty(trim($row['jurusan']))) $siswaData['jurusan'] = $row['jurusan'];
                if (isset($row['tempat_lahir'])) $siswaData['tempat_lahir'] = $row['tempat_lahir'];
                if ($tanggal_lahir !== null) $siswaData['tanggal_lahir'] = $tanggal_lahir;
                if (isset($row['jenis_kelamin'])) $siswaData['jenis_kelamin'] = $row['jenis_kelamin'];
                if (isset($row['agama'])) $siswaData['agama'] = $row['agama'];
                if (isset($row['alamat'])) $siswaData['alamat'] = $row['alamat'];
                if (isset($row['tahun_masuk'])) $siswaData['tahun_masuk'] = $row['tahun_masuk'];
                if ($nohp_val !== null) $siswaData['no_hp'] = $nohp_val;

                Siswa::updateOrCreate(
                    ['user_id' => $user->id],
                    $siswaData
                );
            } elseif ($role === 'guru') {
                $guruData = ['nip' => $nis_nip];
                if (isset($row['jurusan'])) $guruData['jurusan'] = $row['jurusan'];
                if (isset($row['tempat_lahir'])) $guruData['tempat_lahir'] = $row['tempat_lahir'];
                if ($tanggal_lahir !== null) $guruData['tanggal_lahir'] = $tanggal_lahir;
                if (isset($row['jenis_kelamin'])) $guruData['jenis_kelamin'] = $row['jenis_kelamin'];
                if (isset($row['agama'])) $guruData['agama'] = $row['agama'];
                if (isset($row['alamat'])) $guruData['alamat'] = $row['alamat'];
                if ($nohp_val !== null) $guruData['no_hp'] = $nohp_val;

                Guru::updateOrCreate(
                    ['user_id' => $user->id],
                    $guruData
                );
            }
        }
    }
}
