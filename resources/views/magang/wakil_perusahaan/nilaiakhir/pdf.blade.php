<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Akhir PKL</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f97316;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Rekap Nilai Akhir PKL</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Perusahaan</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Pembimbing Lapangan</th>
                <th>Nilai Akhir</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penilaians as $i => $penilaian)
                @php
                    $na = $penilaian->nilai_akhir;
                    $keterangan = $na >= 91 ? 'Sangat Baik' : ($na >= 81 ? 'Baik' : ($na >= 71 ? 'Cukup' : 'Kurang'));
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $penilaian->siswa->name ?? '-' }}</td>
                    <td>{{ $penilaian->wakilPerusahaan->nama_perusahaan ?? '-' }}</td>
                    <td>{{ $penilaian->siswa->magangreports->tanggal_mulai ?? '-' }}</td>
                    <td>{{ $penilaian->siswa->magangreports->tanggal_selesai ?? '-' }}</td>
                    <td>{{ $penilaian->wakilPerusahaan->nama ?? '-' }}</td>
                    <td>{{ $na }}</td>
                    <td>{{ $keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
