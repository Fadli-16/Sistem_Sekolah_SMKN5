<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaExport implements FromCollection, WithHeadings, WithStyles
{
    protected ?string $jurusan;
    protected ?int    $kelasId;

    public function __construct(?string $jurusan = null, ?int $kelasId = null)
    {
        $this->jurusan = $jurusan;
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        $query = DB::table('users')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->select([
                'users.nama',
                'users.email',
                DB::raw("'siswa' as role"),
                DB::raw("'' as password"),
                'siswa.nis as nis_nip',
                'siswa.jurusan',
                'kelas.nama_kelas as kelas',
                'siswa.kelas_id',
                'siswa.tanggal_lahir',
                'siswa.jenis_kelamin',
                'siswa.alamat',
                'siswa.no_hp',
                'siswa.agama',
            ])
            ->orderBy('users.nama');

        if ($this->jurusan) {
            $query->where('siswa.jurusan', $this->jurusan);
        }

        if ($this->kelasId) {
            $query->where('siswa.kelas_id', $this->kelasId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'nama',
            'email',
            'role',
            'password',
            'nis_nip',
            'jurusan',
            'kelas',
            'kelas_id',
            'tanggal_lahir',
            'jenis_kelamin',
            'alamat',
            'no_hp',
            'agama',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
