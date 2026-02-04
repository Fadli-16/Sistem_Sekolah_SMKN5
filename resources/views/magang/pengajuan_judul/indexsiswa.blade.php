@extends('magang.layouts.main')

@section('content')
<div class="container">
    <h3>Daftar Pengajuan Judul Laporan Akhir Magang</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(Auth::user()->role == 'siswa' && $pengajuanJuduls->isEmpty())
        <div class="mb-3 text-end">
            <a href="{{ route('magang.pengajuan_judul.create') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Ajukan Judul
            </a>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>NIS/NISN</th>
                    <th>Jurusan</th>
                    <th>Perusahaan</th>
                    <th>Judul</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengajuanJuduls as $pengajuan)
                    <tr>
                        <td>{{ $pengajuan->user->nama }}</td>
                        <td>{{ $pengajuan->user->nis_nip ?? '-' }}</td>
                        <td>{{ $pengajuan->jurusan }}</td>
                        <td>{{ $pengajuan->wakilPerusahaan->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $pengajuan->judul_laporan }}</td>
                        <td>{{ $pengajuan->alasan }}</td>
                        <td>
                            <span class="badge bg-{{ $pengajuan->status == 'diterima' ? 'success' : ($pengajuan->status == 'ditolak' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($pengajuan->status) }}
                            </span>
                        </td>
                        <td>
                            @if(Auth::user()->role == 'admin_magang')
                                @if($pengajuan->status == 'menunggu')
                                    <form action="{{ route('admin.pengajuan-judul.review', $pengajuan->id) }}" method="POST" class="d-flex gap-1">
                                        @csrf
                                        <input type="hidden" name="status" value="diterima">
                                        <button type="submit" class="btn btn-success btn-sm">Terima</button>
                                    </form>
                                    <form action="{{ route('admin.pengajuan-judul.review', $pengajuan->id) }}" method="POST" class="mt-1">
                                        @csrf
                                        <input type="hidden" name="status" value="ditolak">
                                        <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                    </form>
                                @else
                                    <em>Sudah direview</em>
                                @endif
                            @else
                                <em>{{ ucfirst($pengajuan->status) }}</em>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada pengajuan judul.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
