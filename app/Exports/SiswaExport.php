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
    protected bool    $withPeminatan;

    public function __construct(?string $jurusan = null, ?int $kelasId = null, bool $withPeminatan = false)
    {
        $this->jurusan = $jurusan;
        $this->kelasId = $kelasId;
        $this->withPeminatan = $withPeminatan;
    }

    public function collection()
    {
        $query = DB::table('users')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id');
        $selects = [
            'siswa.nis',
            'users.nama',
            'users.email',
            'kelas.jurusan',
            'kelas.nama_kelas as kelas',
            'siswa.jenis_kelamin',
            'siswa.agama',
            'siswa.tanggal_lahir',
            'siswa.alamat',
            'siswa.no_hp',
        ];

        if ($this->withPeminatan) {
            $query->leftJoin('peminatans', 'siswa.id', '=', 'peminatans.siswa_id');
            $selects = array_merge($selects, [
                'peminatans.minat',
                'peminatans.alasan',
                'peminatans.pemilihan_jurusan',
                'peminatans.jenis_pekerjaan',
                'peminatans.ide_bisnis',
                'peminatans.penghasilan_ortu',
                'peminatans.tanggungan_keluarga',
                'peminatans.file_raport',
                'peminatans.file_angket',
            ]);
        }

        $query->select($selects)->orderBy('users.nama');

        if ($this->jurusan) {
            $query->where('kelas.jurusan', $this->jurusan);
        }

        if ($this->kelasId) {
            $query->where('siswa.kelas_id', $this->kelasId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        $headings = [
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

        if ($this->withPeminatan) {
            $headings = array_merge($headings, [
                'Minat',
                'Alasan',
                'Pilihan Jurusan Kuliah',
                'Jenis Pekerjaan',
                'Ide Bisnis',
                'Penghasilan Ortu',
                'Tanggungan Keluarga',
                'Link Raport',
                'Link Angket',
            ]);
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
