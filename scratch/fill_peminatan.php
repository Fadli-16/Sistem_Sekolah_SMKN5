<?php

use App\Models\User;
use App\Models\Peminatan;
use Illuminate\Support\Facades\DB;

// Load Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function fillPeminatan() {
    $students = User::where('role', 'siswa')
        ->whereDoesntHave('peminatan')
        ->get();

    $total = $students->count();
    if ($total === 0) {
        echo "Tidak ada siswa yang belum mengisi peminatan.\n";
        return;
    }

    echo "Ditemukan $total siswa tanpa data peminatan. Memproses...\n";

    $countKuliah = floor($total * 0.38);
    $countBekerja = floor($total * 0.36);
    $countWirausaha = floor($total * 0.21);
    $countLainnya = $total - ($countKuliah + $countBekerja + $countWirausaha);

    $dist = [];
    for ($i = 0; $i < $countKuliah; $i++) $dist[] = 'kuliah';
    for ($i = 0; $i < $countBekerja; $i++) $dist[] = 'bekerja';
    for ($i = 0; $i < $countWirausaha; $i++) $dist[] = 'wirausaha';
    for ($i = 0; $i < $countLainnya; $i++) $dist[] = 'lainnya';

    shuffle($dist);

    $alasanList = [
        'Ingin mengembangkan potensi diri lebih dalam.',
        'Membantu ekonomi keluarga di masa depan.',
        'Sesuai dengan hobi dan minat sejak kecil.',
        'Ingin menjadi ahli di bidang yang ditekuni.',
        'Mengejar cita-cita yang sudah lama diimpikan.'
    ];

    $jurusanList = ['Teknik Informatika', 'Manajemen', 'Akuntansi', 'Kedokteran', 'Hukum'];
    $pekerjaanList = ['Software Engineer', 'Administrasi', 'Teknisi', 'Sales', 'Manager'];
    $bisnisList = ['Kuliner', 'Toko Online', 'Jasa Desain', 'Bengkel', 'Pertanian'];

    DB::beginTransaction();
    try {
        foreach ($students as $index => $student) {
            $minat = $dist[$index];
            
            $data = [
                'user_id' => $student->id,
                'minat' => $minat,
                'alasan' => $alasanList[array_rand($alasanList)],
                'penghasilan_ortu' => rand(2000000, 10000000),
                'tanggungan_keluarga' => rand(1, 5),
            ];

            if ($minat === 'kuliah') {
                $data['pemilihan_jurusan'] = $jurusanList[array_rand($jurusanList)];
            } elseif ($minat === 'bekerja') {
                $data['jenis_pekerjaan'] = $pekerjaanList[array_rand($pekerjaanList)];
            } elseif ($minat === 'wirausaha') {
                $data['ide_bisnis'] = $bisnisList[array_rand($bisnisList)];
            }

            Peminatan::create($data);
        }
        DB::commit();
        echo "Berhasil mengisi $total data peminatan.\n";
        echo "Distribusi: Kuliah ($countKuliah), Bekerja ($countBekerja), Wirausaha ($countWirausaha), Lainnya ($countLainnya).\n";
    } catch (\Exception $e) {
        DB::rollBack();
        echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
    }
}

fillPeminatan();
