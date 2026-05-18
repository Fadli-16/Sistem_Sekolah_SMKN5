<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuruExport implements FromCollection, WithHeadings, WithStyles
{
    protected ?string $jurusan;

    public function __construct(?string $jurusan = null)
    {
        $this->jurusan = $jurusan;
    }

    public function collection()
    {
        $query = DB::table('users')
            ->join('guru', 'users.id', '=', 'guru.user_id')
            ->select([
                'users.nama',
                'users.email',
                DB::raw("'guru' as role"),
                DB::raw("'' as password"),
                'guru.nip as nis_nip',
                'guru.jurusan',
                'guru.kelas',
                'guru.tanggal_lahir',
                'guru.jenis_kelamin',
                'guru.alamat',
                'guru.no_hp',
                'guru.agama',
            ])
            ->orderBy('users.nama');

        if ($this->jurusan) {
            $query->where('guru.jurusan', $this->jurusan);
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
