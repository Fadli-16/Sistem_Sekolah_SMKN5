<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('users')
            // left join ke siswa dan guru
            ->leftJoin('siswa', 'users.id', '=', 'siswa.user_id')
            ->leftJoin('guru',  'users.id', '=', 'guru.user_id')
            ->select([
                'users.nama',
                'users.email',
                'users.role',
                // untuk export, kita tidak ekspor hash, tapi placeholder
                DB::raw("'' as password_plain"),
                // kolom siswa
                'siswa.nis as nis_siswa',
                'siswa.kelas_id as kelas_id_siswa',
                'siswa.kelas as kelas_siswa',
                'siswa.jurusan as jurusan_siswa',
                'siswa.tanggal_lahir as lahir_siswa',
                'siswa.alamat as alamat_siswa',
                'siswa.no_hp as hp_siswa',
                // kolom guru
                'guru.nip as nip_guru',
                'guru.kelas as kelas_guru',
                'guru.jurusan as jurusan_guru',
                'guru.tanggal_lahir as lahir_guru',
                'guru.alamat as alamat_guru',
                'guru.no_hp as hp_guru',
                'users.created_at',
            ])
            ->orderBy('users.created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Role',
            'Password (plain)',

            // kolom siswa
            'NIS Siswa',
            'Kelas ID Siswa',
            'Kelas Siswa',
            'Jurusan Siswa',
            'Tanggal Lahir Siswa',
            'Alamat Siswa',
            'No HP Siswa',

            // kolom guru
            'NIP Guru',
            'Kelas Guru',
            'Jurusan Guru',
            'Tanggal Lahir Guru',
            'Alamat Guru',
            'No HP Guru',

            'Created At',
        ];
    }
}
