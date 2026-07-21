<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use App\Models\ScheduleGeneration;
use Illuminate\Support\Facades\Log;

class ScheduleAiService
{
    /**
     * Memanggil Google AI (Gemini) untuk mengevaluasi draf jadwal
     */
    public function evaluateScheduleInsight(ScheduleGeneration $generation)
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            $this->fallbackAiNote($generation);
            return;
        }

        // Ambil sampel data jadwal (untuk menghemat token, kita summary-kan)
        $drafts = $generation->drafts()->with(['mataPelajaran.guru', 'kelas'])->get();
        
        $summary = "Berikut adalah ringkasan draf jadwal untuk jurusan {$generation->jurusan}:\n";
        $summary .= "Total skor optimalitas: {$generation->skor_kualitas} dari 100.\n";
        $summary .= "Total slot: {$drafts->count()}\n\n";

        // Tambahkan beberapa sampel untuk dianalisis AI
        foreach ($drafts->take(15) as $d) {
            $mapel = $d->mataPelajaran->nama_mata_pelajaran ?? 'Unknown';
            $guru = $d->mataPelajaran->guru->nama ?? 'Unknown';
            $kelas = $d->kelas->nama_kelas ?? 'Unknown';
            $summary .= "- Hari {$d->hari} ({$d->jam_mulai}-{$d->jam_selesai}): Kelas {$kelas} mapel {$mapel} (Guru: {$guru})\n";
        }
        $summary .= "\nBerikan analisis 2 paragraf singkat dalam bahasa Indonesia mengenai kualitas jadwal ini, dan berikan masukan positif layaknya asisten kurikulum yang profesional. Jangan berikan teks format markdown tebal (*).";

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json'
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $summary]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiInsight = $data['candidates'][0]['content']['parts'][0]['text'];
                    $generation->update([
                        'catatan_ai' => $aiInsight
                    ]);
                } else {
                    $this->fallbackAiNote($generation);
                }
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                $this->fallbackAiNote($generation);
            }
        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            $this->fallbackAiNote($generation);
        }
    }

    protected function fallbackAiNote($generation)
    {
        $msg = "Jadwal Anda berhasil disusun secara otomatis dengan distribusi beban (Bin Packing) yang seimbang melintasi hari aktif.\n\nSimulasi Insight: Distribusi jam pelajaran sudah cukup merata (Skor {$generation->skor_kualitas}/100) dan pengelompokan mata pelajaran kejuruan telah disesuaikan agar optimal.";
        
        $generation->update([
            'catatan_ai' => $msg
        ]);
    }
}
