{{-- resources/views/sistem_akademik/course/download.blade.php --}}
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Jadwal</title>
</head>

<body>
    @foreach($pages as $page)
    @php
    $kelas = $page['kelas'] ?? null;
    $timetable = $page['timetable'] ?? ['days'=>['Senin','Selasa','Rabu','Kamis','Jumat'],'slotOrder'=>[],'slotDetails'=>[],'matrix'=>[]];
    $slotOrder = $timetable['slotOrder'] ?? [];
    $slotDetails = $timetable['slotDetails'] ?? [];
    $days = $timetable['days'] ?? ['Senin','Selasa','Rabu','Kamis','Jumat'];
    $matrix = $timetable['matrix'] ?? [];
    @endphp

    <div class="page">
        <div class="header">
            <table class="brand-table">
                <tr>
                    <td class="logo-cell">
                        <img src="{{ public_path('assets/images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                    </td>
                    <td class="text-cell">
                        <div class="school">SMK NEGERI 5 PADANG</div>
                        <div class="subtitle">Jadwal Pembelajaran</div>
                    </td>
                </tr>
            </table>

            <div class="title">
                <h1>JADWAL PBM</h1>
                @if($kelas)
                <h3>{{ $kelas->nama_kelas }} — {{ $kelas->jurusan }} ({{ $kelas->tahun_ajaran }})</h3>
                @else
                <h3>Semua Kelas</h3>
                @endif
            </div>
        </div>

        {{-- ensure matrix has keys for all slot/day --}}
        @php
        // normalize matrix so view can safely index
        foreach ($days as $d) {
        if (!isset($matrix[$d])) $matrix[$d] = array_fill_keys($slotOrder, null);
        else {
        foreach ($slotOrder as $sid) {
        if (!array_key_exists($sid, $matrix[$d])) $matrix[$d][$sid] = null;
        }
        }
        }
        @endphp

        <table class="timetable">
            <thead>
                <tr>
                    <th class="day-col"></th>
                    @foreach($slotOrder as $sid)
                    <th class="slot-header">
                        <div style="font-weight:700;">{{ $slotDetails[$sid]['label'] ?? $sid }}</div>
                        <div style="font-size:9px; margin-top:4px;">{{ $slotDetails[$sid]['start'] ?? '' }} - {{ $slotDetails[$sid]['end'] ?? '' }}</div>
                    </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($days as $day)
                <tr>
                    <td class="day-col">{{ \Illuminate\Support\Str::substr($day,0,2) }}</td>

                    @foreach($slotOrder as $sid)
                    @php
                    $isBreak = in_array($sid, ['istirahat', 'ISHOMA', 'ISHO']);
                    @endphp

                    @if($isBreak)
                        @if($loop->parent->first)
                            <td rowspan="{{ count($days) }}" class="cell-course" style="font-weight: bold; background-color: #fafafa; font-size: 10px; line-height: 1.2; vertical-align: middle;">
                                {!! implode('<br>', str_split(strtoupper($slotDetails[$sid]['label'] ?? $sid))) !!}
                            </td>
                        @endif
                        @continue
                    @endif

                    @php $cell = $matrix[$day][$sid] ?? null; @endphp

                    @if(isset($cell['skipped']) && $cell['skipped'] === true)
                    @continue
                    @endif

                    @if($cell === null)
                    <td class="cell-course">&nbsp;</td>
                    @else
                    @php
                    $c = $cell['course'] ?? null;
                    $span = max(1, (int)($cell['span'] ?? 1));
                    $guruName = $c ? (optional($c->mataPelajaran->guru)->nama ?? optional($c->mataPelajaran->guru)->name ?? '-') : '-';
                    $mpName = $c ? (optional($c->mataPelajaran)->nama_mata_pelajaran ?? '-') : ($cell['label'] ?? '-');
                    $ruangan = $c ? ($c->ruangan ?? '-') : ($cell['ruangan'] ?? '-');
                    @endphp

                    <td class="cell-course" colspan="{{ $span }}">
                        <div class="course-guru">{{ $guruName }}</div>
                        <div class="course-mapel">{{ $mpName }}</div>
                        <div class="course-ruangan">{{ $ruangan }}</div>
                    </td>
                    @endif
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    <div><strong>Tahun Ajaran:</strong> {{ $kelas->tahun_ajaran ?? ($tahun_ajaran ?? '-') }}</div>
                    <div><strong>Dicetak:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</div>
                </td>
                <td class="footer-right">
                    <div>Generated by Sistem Akademik</div>
                    <div>Catatan: cell menampilkan (Guru / Mapel / Ruangan)</div>
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</body>

</html>
