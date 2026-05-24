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
                'siswa.nis',
                'users.nama',
                'users.email',
                'siswa.jurusan',
                'kelas.nama_kelas as kelas',
                'siswa.jenis_kelamin',
                'siswa.agama',
                'siswa.tanggal_lahir',
                'siswa.alamat',
                'siswa.no_hp',
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
            'NIS',
            'Nama',
            'Email',
            'Jurusan',
            'kelas',
            'Jenis kelamin',
            'agama',
            'Tanggal lahir',
            'Alamat',
            'No.hp',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
