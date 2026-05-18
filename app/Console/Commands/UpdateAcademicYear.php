<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateAcademicYear extends Command
{
    /**
     * Nama dan tanda tangan perintah (command)
     */
    protected $signature = 'app:update-academic-year';

    /**
     * Deskripsi perintah
     */
    protected $description = 'Memperbarui tahun ajaran kelas secara otomatis pada periode Juni/Juli';

    /**
     * Eksekusi perintah
     */
    public function handle()
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        // Tahun ajaran baru yang diharapkan (Tahun Sekarang / Tahun Depan)
        $newTA = $year . '/' . ($year + 1);
        
        // Cari kelas yang tahun ajarannya belum sesuai dengan tahun ajaran baru
        $classesToUpdate = Kelas::where('tahun_ajaran', '!=', $newTA)->get();

        if ($classesToUpdate->isEmpty()) {
            $this->info('Semua data kelas sudah menggunakan tahun ajaran terbaru (' . $newTA . ').');
            return 0;
        }

        // Otomasi hanya berjalan di bulan Juni (6) atau Juli (7)
        if ($month == 6 || $month == 7) {
            $count = 0;
            foreach ($classesToUpdate as $kelas) {
                $oldTA = $kelas->tahun_ajaran;
                $kelas->update(['tahun_ajaran' => $newTA]);
                $this->info("Berhasil: Kelas {$kelas->nama_kelas} diperbarui dari {$oldTA} ke {$newTA}");
                $count++;
            }

            Log::info("Otomasi Tahun Ajaran: Berhasil memperbarui {$count} data kelas ke {$newTA}");
            $this->info("Total {$count} kelas berhasil diperbarui.");
        } else {
            $this->warn('Saat ini bukan periode pergantian tahun ajaran (Juni/Juli). Update otomatis dibatalkan.');
            $this->line('Tahun ajaran saat ini di sistem: ' . ($year - 1) . '/' . $year);
        }

        return 0;
    }
}
