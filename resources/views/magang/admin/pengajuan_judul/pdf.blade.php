<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pengajuan Judul</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; }
    </style>
</head>
<body>
    <h3>Daftar Pengajuan Judul Laporan Akhir Magang</h3>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIS</th>
                <th>Perusahaan</th>
                <th>Jurusan</th>
                <th>Judul</th>
                <th>Alasan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengajuan as $item)
            <tr>
                <td>{{ $item->user->nama }}</td>
                <td>{{ $item->user->nis_nip }}</td>
                <td>{{ $item->wakilPerusahaan->nama_perusahaan ?? '-' }}</td>
                <td>{{ $item->jurusan }}</td>
                <td>{{ $item->judul_laporan }}</td>
                <td>{{ $item->alasan }}</td>
                <td>{{ ucfirst($item->status ?? 'Menunggu') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
