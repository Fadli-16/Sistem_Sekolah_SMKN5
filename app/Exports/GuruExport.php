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
                'guru.nip',
                'users.nama',
                'users.email',
                'guru.jurusan',
                'guru.jenis_kelamin',
                'guru.agama',
                'guru.tanggal_lahir',
                'guru.alamat',
                'guru.no_hp',
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
            'NIP',
            'Nama',
            'Email',
            'Jurusan',
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
