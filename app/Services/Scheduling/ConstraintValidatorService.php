<?php

namespace App\Services\Scheduling;

class ConstraintValidatorService
{
    /**
     * Mengevaluasi hard constraints untuk sebuah penempatan kelas
     */
    public function isValidPlacement($guruId, $kelasId, $ruangan, $hari, $jamMulai, $jamSelesai, $matrix)
    {
        // $matrix berisi semua jadwal yang sudah ditempatkan sementara
        // format: $matrix[$hari][$jamMulai] = [...]

        // Cek apakah guru sudah mengajar di jam yang sama (hari, rentang waktu)
        if ($this->hasGuruConflict($guruId, $hari, $jamMulai, $jamSelesai, $matrix)) {
            return false;
        }

        // Cek apakah kelas sudah belajar di jam yang sama
        if ($this->hasKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $matrix)) {
            return false;
        }

        // Cek apakah ruangan sudah dipakai
        if ($ruangan && $this->hasRuanganConflict($ruangan, $hari, $jamMulai, $jamSelesai, $matrix)) {
            return false;
        }

        return true;
    }

    protected function hasGuruConflict($guruId, $hari, $jamMulai, $jamSelesai, $matrix)
    {
        if (!isset($matrix[$hari])) return false;
        foreach ($matrix[$hari] as $p) {
            if ($p['guru_id'] == $guruId) {
                if ($this->timesOverlap($jamMulai, $jamSelesai, $p['jam_mulai'], $p['jam_selesai'])) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function hasKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $matrix)
    {
        if (!isset($matrix[$hari])) return false;
        foreach ($matrix[$hari] as $p) {
            if ($p['kelas_id'] == $kelasId) {
                if ($this->timesOverlap($jamMulai, $jamSelesai, $p['jam_mulai'], $p['jam_selesai'])) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function hasRuanganConflict($ruangan, $hari, $jamMulai, $jamSelesai, $matrix)
    {
        if (!isset($matrix[$hari])) return false;
        foreach ($matrix[$hari] as $p) {
            if ($p['ruangan'] && strtolower(trim($p['ruangan'])) === strtolower(trim($ruangan))) {
                if ($this->timesOverlap($jamMulai, $jamSelesai, $p['jam_mulai'], $p['jam_selesai'])) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function timesOverlap($aStart, $aEnd, $bStart, $bEnd)
    {
        $aS = strtotime($aStart);
        $aE = strtotime($aEnd);
        $bS = strtotime($bStart);
        $bE = strtotime($bEnd);

        return $aS < $bE && $bS < $aE;
    }

    /**
     * Hitung fitness score dari populasi / jadwal
     */
    public function calculateFitnessScore($scheduleList)
    {
        $score = 100;
        // Penalti soft constraints:
        // 1. Mapel Umum sebaiknya Senin/Selasa
        // 2. Mapel Jurusan sebaiknya Rabu-Jumat
        
        foreach ($scheduleList as $s) {
            $kat = $s['kategori'] ?? '';
            if (strtolower($kat) === 'umum') {
                if (!in_array($s['hari'], ['Senin', 'Selasa'])) {
                    $score -= 1; // penalti
                }
            }
            if (strtolower($kat) === 'jurusan') {
                if (in_array($s['hari'], ['Senin', 'Selasa'])) {
                    $score -= 1; // penalti
                }
            }
        }
        
        return max(0, $score);
    }
}
