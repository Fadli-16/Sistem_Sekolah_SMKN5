<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\MataPelajaran;
use App\Models\Course;

// 1. Add column if not exists
if (!Schema::hasColumn('mata_pelajaran', 'jp')) {
    Schema::table('mata_pelajaran', function (Blueprint $table) {
        $table->integer('jp')->default(1)->after('nama_mata_pelajaran');
    });
    echo "Column 'jp' added to mata_pelajaran table.\n";
} else {
    echo "Column 'jp' already exists in mata_pelajaran table.\n";
}

// 2. Populate jp based on courses
$mapels = MataPelajaran::all();

$order = [
    '1', '2', '3', 'istirahat', '4', '5', '6', 'ISHOMA', 
    '7', '8', '9', '10', 'ISHO', '11', '12', '13'
];

$details = [
    '1' => ['label' => 'Jam 1',  'start' => '07:15', 'end' => '08:00',  'selectable' => true],
    '2' => ['label' => 'Jam 2',  'start' => '08:00', 'end' => '08:45',  'selectable' => true],
    '3' => ['label' => 'Jam 3',  'start' => '08:45', 'end' => '09:30',  'selectable' => true],
    'istirahat' => ['label' => 'Istirahat', 'start' => '09:30', 'end' => '10:00', 'selectable' => false],
    '4' => ['label' => 'Jam 4',  'start' => '10:00', 'end' => '10:45',  'selectable' => true],
    '5' => ['label' => 'Jam 5',  'start' => '10:45', 'end' => '11:30',  'selectable' => true],
    '6' => ['label' => 'Jam 6',  'start' => '11:30', 'end' => '12:15',  'selectable' => true],
    'ISHOMA' => ['label' => 'ISHOMA', 'start' => '12:15', 'end' => '13:15', 'selectable' => false],
    '7' => ['label' => 'Jam 7',  'start' => '13:15', 'end' => '13:45',  'selectable' => true],
    '8' => ['label' => 'Jam 8',  'start' => '13:45', 'end' => '14:15',  'selectable' => true],
    '9' => ['label' => 'Jam 9',  'start' => '14:15', 'end' => '14:45',  'selectable' => true],
    '10' => ['label' => 'Jam 10', 'start' => '14:45', 'end' => '15:15',  'selectable' => true],
    'ISHO' => ['label' => 'ISHO', 'start' => '15:15', 'end' => '15:45', 'selectable' => false],
    '11' => ['label' => 'Jam 11', 'start' => '15:45', 'end' => '16:15',  'selectable' => true],
    '12' => ['label' => 'Jam 12', 'start' => '16:15', 'end' => '16:45',  'selectable' => true],
    '13' => ['label' => 'Jam 13', 'start' => '16:45', 'end' => '17:00',  'selectable' => true],
];

foreach ($mapels as $mapel) {
    // Find the max selectable slots used by any course of this mapel
    $courses = Course::where('mata_pelajaran_id', $mapel->id)->get();
    
    $maxJp = 1; // default at least 1
    
    foreach ($courses as $c) {
        if (!$c->jam_mulai || !$c->jam_selesai) continue;
        $cStart = substr($c->jam_mulai, 0, 5);
        $cEnd = substr($c->jam_selesai, 0, 5);
        
        $sIndex = -1;
        $eIndex = -1;
        
        // Find start and end slot index
        foreach ($order as $i => $id) {
            if ($details[$id]['start'] === $cStart) {
                $sIndex = $i;
            }
            if ($details[$id]['end'] === $cEnd) {
                $eIndex = $i;
            }
        }
        
        if ($sIndex !== -1 && $eIndex !== -1 && $eIndex >= $sIndex) {
            $jpCount = 0;
            for ($i = $sIndex; $i <= $eIndex; $i++) {
                $id = $order[$i];
                if ($details[$id]['selectable']) {
                    $jpCount++;
                }
            }
            if ($jpCount > $maxJp) {
                $maxJp = $jpCount;
            }
        }
    }
    
    // Update mapel
    MataPelajaran::where('id', $mapel->id)->update(['jp' => $maxJp]);
    echo "Updated {$mapel->nama_mata_pelajaran} to {$maxJp} JP\n";
}

echo "Finished updating JP data.\n";
