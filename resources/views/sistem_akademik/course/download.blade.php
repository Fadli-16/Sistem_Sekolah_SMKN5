{{-- resources/views/sistem_akademik/course/download.blade.php --}}
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Jadwal</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 18mm;
        }

        html,
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            background: #fff;
        }

        .page {
            width: 100%;
            box-sizing: border-box;
            padding: 8mm;
            page-break-after: always;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .brand-table {
            border-collapse: collapse;
        }

        .brand-table td {
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell img {
            width: 64px;
            height: 64px;
            display: block;
            margin-right: 10px;
        }

        .text-cell {
            padding-left: 10px;
        }

        .school {
            font-weight: 700;
            font-size: 14px;
            margin: 0;
        }

        .subtitle {
            font-size: 11px;
            margin: 0;
        }

        .title {
            text-align: center;
            flex: 1 1 auto;
            padding: 0 10px;
        }

        .title h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 1px;
        }

        .title h3 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }

        .timetable {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10px;
            margin-top: 6px;
        }

        .timetable th,
        .timetable td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }

        .day-col {
            width: 60px;
            font-weight: 700;
            font-size: 13px;
            background: #f5f5f5;
        }

        .slot-header {
            font-weight: 700;
            font-size: 11px;
            padding: 3px;
        }

        .cell-course {
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        .course-guru {
            font-size: 10px;
            font-weight: 600;
        }

        .course-mapel {
            font-size: 12px;
            font-weight: 700;
            margin-top: 3px;
            line-height: 1.03;
            word-break: break-word;
        }

        .course-ruangan {
            font-size: 10px;
            margin-top: 4px;
            color: #333;
        }

        /* footer as table for stable alignment */
        .footer-table {
            width: 100%;
            margin-top: 8px;
            font-size: 10px;
            border-top: 1px solid #ddd;
            border-collapse: collapse;
            padding-top: 6px;
        }

        .footer-table td {
            vertical-align: top;
            padding-top: 6px;
        }

        .footer-left {
            text-align: left;
            width: 50%;
        }

        .footer-right {
            text-align: right;
            width: 50%;
        }

        /* ensure last page doesn't get an extra blank page after render: dompdf respects page-break-after */
        .page:last-child {
            page-break-after: auto;
        }
    </style>
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