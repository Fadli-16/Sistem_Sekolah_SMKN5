<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $jurusan = [
            'Teknik Otomotif Sepeda Motor',
            'Teknik Otomotif Kendaraan Ringan',
            'Teknik Pemesinan',
            'Teknik Audio Video',
            'Teknik Gambar Bangunan',
            'Teknik Konstruksi Batu dan Beton',
            'Teknik Komputer Jaringan',
            'Teknik Instalasi Tenaga Listrik'
        ];

        foreach ($jurusan as $j) {
            Kelas::create([
                'nama_kelas' => 'X',
                'jurusan' => $j,
                'tahun_ajaran' => date('Y') . '/' . (date('Y') + 1),
            ]);
        }
    }
}