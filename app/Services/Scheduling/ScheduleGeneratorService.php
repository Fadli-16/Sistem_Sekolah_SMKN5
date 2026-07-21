<?php

namespace App\Services\Scheduling;

use App\Models\Course;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Services\Scheduling\ConstraintValidatorService;

class ScheduleGeneratorService
{
    protected $validator;
    protected $slotDetails;
    protected $slotOrder;
    protected $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    public function __construct(ConstraintValidatorService $validator)
    {
        $this->validator = $validator;
        $this->setupSlots();
    }

    protected function setupSlots()
    {
        // Copy slot setup dari CourseController
        $this->slotOrder = [
            '1', '2', '3', 'istirahat', '4', '5', '6', 'ISHOMA', 
            '7', '8', '9', '10', 'ISHO', '11', '12', '13'
        ];

        $this->slotDetails = [
            '1' => ['start' => '07:15', 'end' => '08:00', 'selectable' => true],
            '2' => ['start' => '08:00', 'end' => '08:45', 'selectable' => true],
            '3' => ['start' => '08:45', 'end' => '09:30', 'selectable' => true],
            'istirahat' => ['start' => '09:30', 'end' => '10:00', 'selectable' => false],
            '4' => ['start' => '10:00', 'end' => '10:45', 'selectable' => true],
            '5' => ['start' => '10:45', 'end' => '11:30', 'selectable' => true],
            '6' => ['start' => '11:30', 'end' => '12:15', 'selectable' => true],
            'ISHOMA' => ['start' => '12:15', 'end' => '13:15', 'selectable' => false],
            '7' => ['start' => '13:15', 'end' => '13:45', 'selectable' => true],
            '8' => ['start' => '13:45', 'end' => '14:15', 'selectable' => true],
            '9' => ['start' => '14:15', 'end' => '14:45', 'selectable' => true],
            '10' => ['start' => '14:45', 'end' => '15:15', 'selectable' => true],
            'ISHO' => ['start' => '15:15', 'end' => '15:45', 'selectable' => false],
            '11' => ['start' => '15:45', 'end' => '16:15', 'selectable' => true],
            '12' => ['start' => '16:15', 'end' => '16:45', 'selectable' => true],
            '13' => ['start' => '16:45', 'end' => '17:00', 'selectable' => true],
        ];
    }

    public function generateSchedule(array $kelasIds, array $mapelIds = [], array $options = [])
    {
        $scheduleList = []; // Hasil penempatan jadwal
        $matrix = []; // Untuk constraint checking

        $optUmum = filter_var($options['opt_umum'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $optJurusan = filter_var($options['opt_jurusan'] ?? true, FILTER_VALIDATE_BOOLEAN);

        // Get mapel for those classes
        if (!empty($mapelIds)) {
            $mapelList = MataPelajaran::whereIn('id', $mapelIds)->get();
        } else {
            $mapelList = MataPelajaran::all();
        }

        // Bagi kategori mapel agar umum ditempatkan duluan
        $mapelUmum = $mapelList->filter(function($m) {
            $kat = $m->kategori_penjadwalan;
            if (!$kat) $kat = ($m->jurusan && strtolower($m->jurusan) === 'umum') ? 'umum' : 'jurusan';
            return strtolower($kat) === 'umum';
        });
        $mapelJurusan = $mapelList->filter(function($m) {
            $kat = $m->kategori_penjadwalan;
            if (!$kat) $kat = ($m->jurusan && strtolower($m->jurusan) === 'umum') ? 'umum' : 'jurusan';
            return strtolower($kat) !== 'umum';
        });

        // Urutkan berdasarkan JP terpanjang ke terpendek (Longest Processing Time First)
        $mapelUmum = $mapelUmum->sortByDesc('jp');
        $mapelJurusan = $mapelJurusan->sortByDesc('jp');

        // LPT: Mapel Jurusan (JP panjang) diproses lebih dulu agar mendapat blok besar, 
        // lalu Mapel Umum (JP kecil) mengisi celah sisanya.
        $mapelPrioritized = $mapelJurusan->merge($mapelUmum);

        // Persiapan pembagian guru (mapel paralel)
        $classAssignments = [];
        foreach ($kelasIds as $kId) {
            $classAssignments[$kId] = [];
        }

        $mapelGroups = $mapelPrioritized->groupBy('nama_mata_pelajaran');
        $mapelDistributions = $options['mapel_distributions'] ?? [];
        
        foreach ($mapelGroups as $name => $groupMapels) {
            $groupMapels = $groupMapels->values();
            $numMapels = $groupMapels->count();
            
            if (isset($mapelDistributions[$name])) {
                foreach ($kelasIds as $kId) {
                    if (isset($mapelDistributions[$name][$kId])) {
                        $assignedMapelId = $mapelDistributions[$name][$kId];
                        $assignedMapel = $groupMapels->firstWhere('id', $assignedMapelId);
                        if ($assignedMapel) {
                            $classAssignments[$kId][] = $assignedMapel;
                        } else {
                            $classAssignments[$kId][] = $groupMapels->first();
                        }
                    } else {
                        $classAssignments[$kId][] = $groupMapels->first();
                    }
                }
            } else {
                $kelasIndex = 0;
                foreach ($kelasIds as $kId) {
                    $assignedMapel = $groupMapels[$kelasIndex % $numMapels];
                    $classAssignments[$kId][] = $assignedMapel;
                    $kelasIndex++;
                }
            }
        }

        foreach ($kelasIds as $kelasId) {
            $kelas = Kelas::find($kelasId);
            if (!$kelas) continue;

            $mapelsForThisClass = collect($classAssignments[$kelasId])->sortByDesc('jp');

            // Hitung total JP untuk kelas ini agar bisa menentukan target harian (Bin Packing)
            $totalJP = $mapelsForThisClass->sum('jp');
            // Target ideal rata-rata JP per hari
            $dayTarget = max(10, ceil($totalJP / count($this->hariList)));

            $ruangan = $kelas->ruangan ?? null;

            foreach ($mapelsForThisClass as $mp) {
                $guruId = $mp->guru_id;
                $jp = $mp->jp ?? 1;

                $mapelRuangan = $ruangan;
                if (!empty($options['mapel_rooms'][$mp->id])) {
                    $mapelRuangan = $options['mapel_rooms'][$mp->id];
                }

                $kat = $mp->kategori_penjadwalan;
                if (!$kat) $kat = ($mp->jurusan && strtolower($mp->jurusan) === 'umum') ? 'umum' : 'jurusan';

                if (strtolower($kat) !== 'umum' && $jp > 6) {
                    $jp1 = ceil($jp / 2);
                    $jp2 = floor($jp / 2);

                    $placed1 = $this->findAndPlaceSlot($guruId, $kelasId, $mapelRuangan, $jp1, $matrix, $kat, [], $optUmum, $optJurusan, $dayTarget);
                    if ($placed1) {
                        $scheduleList[] = array_merge($placed1, [
                            'mata_pelajaran_id' => $mp->id,
                            'kategori' => $kat
                        ]);
                        $matrix[$placed1['hari']][] = $placed1;

                        // Cari untuk blok ke-2 di hari yang berbeda
                        $placed2 = $this->findAndPlaceSlot($guruId, $kelasId, $mapelRuangan, $jp2, $matrix, $kat, [$placed1['hari']], $optUmum, $optJurusan, $dayTarget);
                        if ($placed2) {
                            $scheduleList[] = array_merge($placed2, [
                                'mata_pelajaran_id' => $mp->id,
                                'kategori' => $kat
                            ]);
                            $matrix[$placed2['hari']][] = $placed2;
                        }
                    }
                } else {
                    // Normal
                    $placed = $this->findAndPlaceSlot($guruId, $kelasId, $mapelRuangan, $jp, $matrix, $kat, [], $optUmum, $optJurusan, $dayTarget);

                    if ($placed) {
                        $scheduleList[] = array_merge($placed, [
                            'mata_pelajaran_id' => $mp->id,
                            'kategori' => $kat
                        ]);
                        
                        // Update matrix
                        $hari = $placed['hari'];
                        if (!isset($matrix[$hari])) $matrix[$hari] = [];
                        $matrix[$hari][] = $placed;
                    }
                }
            }
        }

        $skor = $this->validator->calculateFitnessScore($scheduleList);

        return [
            'schedule' => $scheduleList,
            'score' => $skor
        ];
    }

    protected function findAndPlaceSlot($guruId, $kelasId, $ruangan, $jp, $matrix, $kat = 'umum', $excludeHari = [], $optUmum = true, $optJurusan = true, $dayTarget = 10)
    {
        $allSelectableIndices = array_keys(array_filter($this->slotDetails, function($s) {
            return $s['selectable'];
        }));

        $bestPlacement = null;
        $bestScore = PHP_INT_MAX;

        foreach ($this->hariList as $hari) {
            if (in_array($hari, $excludeHari)) continue;
            
            $load = 0;
            if (isset($matrix[$hari])) {
                foreach ($matrix[$hari] as $p) {
                    if ($p['kelas_id'] == $kelasId) {
                        $load += $p['jp'] ?? 1;
                    }
                }
            }
            
            $isPreferred = false;
            if ($optUmum && strtolower($kat) === 'umum' && in_array($hari, ['Senin', 'Selasa'])) {
                $isPreferred = true;
            } elseif ($optJurusan && strtolower($kat) !== 'umum' && in_array($hari, ['Rabu', 'Kamis', 'Jumat'])) {
                $isPreferred = true;
            }

            // Aturan Penalti
            // 1. Kategori: Jika dipaksa keluar dari hari preferensi, beri penalti moderat (500)
            $dayPenalty = $isPreferred ? 0 : 500;

            // 2. Overload: Jika penambahan beban ini melewati target harian, beri penalti sangat besar
            $newLoad = $load + $jp;
            $overloadPenalty = 0;
            if ($newLoad > $dayTarget) {
                // Skala eksponensial sederhana agar sangat tidak disukai
                $overloadPenalty = ($newLoad - $dayTarget) * 1000;
            }

            // 3. Load Balancing: Secara natural mengisi hari yang bebannya paling sedikit (Tujuan Bin Packing)
            $loadPenalty = $load * 10;

            // Aturan: Senin dan Jumat tidak boleh pakai Jam ke-1
            $selectableIndices = $allSelectableIndices;
            if ($hari === 'Senin' || $hari === 'Jumat') {
                $selectableIndices = array_values(array_filter($selectableIndices, function($idx) {
                    return (string)$idx !== '1';
                }));
            }

            for ($i = 0; $i <= count($selectableIndices) - $jp; $i++) {
                
                $startSlotId = $selectableIndices[$i];
                $endSlotId = $selectableIndices[$i + $jp - 1];

                // 4. Kepadatan (Compactness): Pilih jam paling pagi yang tersedia
                $slotPenalty = intval($startSlotId);
                
                $jamMulai = $this->slotDetails[$startSlotId]['start'];
                $jamSelesai = $this->slotDetails[$endSlotId]['end'];

                if ($this->validator->isValidPlacement($guruId, $kelasId, $ruangan, $hari, $jamMulai, $jamSelesai, $matrix)) {
                    
                    // Total Skor Gabungan (Semakin kecil semakin baik)
                    $score = $overloadPenalty + $dayPenalty + $loadPenalty + $slotPenalty;

                    if ($score < $bestScore) {
                        $bestScore = $score;
                        $bestPlacement = [
                            'guru_id' => $guruId,
                            'kelas_id' => $kelasId,
                            'hari' => $hari,
                            'jam_mulai' => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                            'ruangan' => $ruangan,
                            'jp' => $jp
                        ];
                    }

                    // Karena iterasi sudah dari slot paling pagi, tidak perlu cek slot berikutnya di hari ini.
                    break;
                }
            }
        }

        return $bestPlacement;
    }
}
