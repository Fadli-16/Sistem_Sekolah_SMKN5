@extends('magang.layouts.main')

@section('content')
<h3>Daftar Pengajuan Judul Laporan Akhir</h3>

<a href="{{ route('admin.pengajuan-judul.export-pdf') }}" class="btn btn-sm btn-danger mb-3">Export PDF</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIS</th>
            <th>Perusahaan</th>
            <th>Jurusan</th>
            <th>Judul</th>
            <th>Alasan</th>
            <th>Status</th>
            <th>Review</th>
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
            <td>
                <form action="{{ route('admin.pengajuan-judul.review', $item->id) }}" method="POST">
                    @csrf
                    <select name="status" class="form-select mb-1" required>
                        <option value="">-- Pilih --</option>
                        <option value="diterima">Diterima</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                    <textarea name="catatan_admin" class="form-control mb-1" placeholder="Catatan..."></textarea>
                    <button class="btn btn-sm btn-primary" type="submit">Review</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
