<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduleGeneration;
use App\Models\ScheduleDraft;
use App\Services\Scheduling\ScheduleGeneratorService;
use App\Services\Ai\ScheduleAiService;

class GenerateScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $generationId;

    public function __construct($generationId)
    {
        $this->generationId = $generationId;
    }

    public function handle(ScheduleGeneratorService $generatorService)
    {
        $generation = ScheduleGeneration::find($this->generationId);
        if (!$generation) return;

        $generation->update(['status' => 'processing']);

        try {
            // Get classes to schedule
            $kelasIds = $generation->kelas_ids ?? [];
            if (empty($kelasIds)) {
                // fallback to all classes in jurusan if none specified
                $kelasIds = \App\Models\Kelas::where('jurusan', $generation->jurusan)->pluck('id')->toArray();
            }

            $mapelIds = $generation->mapel_ids ?? [];
            if (empty($mapelIds)) {
                $mapelIds = \App\Models\MataPelajaran::where('jurusan', $generation->jurusan)->pluck('id')->toArray();
            }

            // Run algorithm
            $options = $generation->options ?? [];
            $result = $generatorService->generateSchedule($kelasIds, $mapelIds, $options);

            // Save drafts
            foreach ($result['schedule'] as $item) {
                ScheduleDraft::create([
                    'generation_id' => $generation->id,
                    'kelas_id' => $item['kelas_id'],
                    'mata_pelajaran_id' => $item['mata_pelajaran_id'],
                    'guru_id' => $item['guru_id'],
                    'hari' => $item['hari'],
                    'jam_mulai' => $item['jam_mulai'],
                    'jam_selesai' => $item['jam_selesai'],
                    'ruangan' => $item['ruangan']
                ]);
            }

            // Hitung konflik aktual (di simulasi greedy ini 0 karena sudah divalidasi)
            // Namun di GA bisa ada konflik
            
            $generation->update([
                'status' => 'completed',
                'skor_kualitas' => $result['score'],
                'total_konflik' => 0 
            ]);

            // Panggil AI Service untuk generate catatan_ai
            $aiService = new ScheduleAiService();
            $aiService->evaluateScheduleInsight($generation);

        } catch (\Exception $e) {
            $generation->update([
                'status' => 'failed',
                'catatan_ai' => 'Error: ' . $e->getMessage()
            ]);
            throw $e; // Re-throw to fail the job
        }
    }
}
