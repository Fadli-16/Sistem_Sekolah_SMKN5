@extends('magang.layouts.main')

@section('css')
<style>
    .report-card {
        margin-bottom: 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .report-card:hover {
        box-shadow: var(--shadow);
    }

    .report-header {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .report-title {
        margin: 0;
        font-weight: 600;
    }

    .report-body {
        padding: 1rem;
    }

    .report-footer {
        padding: 1rem;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .week-badge {
        background-color: var(--primary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-draft {
        background-color: #e9ecef;
        color: #495057;
    }

    .status-submitted {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-approved {
        background-color: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }

    .report-meta {
        display: flex;
        gap: 1.5rem;
        margin-top: 0.75rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-icon {
        font-size: 3rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">{{ $header }}</h1>

        <a href="{{ route('magang.siswa.laporan.create') }}" class="btn btn-secondary">
            <i class="bi bi-plus-circle me-1"></i> Buat Laporan Baru
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($laporans->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-journal"></i>
                    </div>
                    <h4>Belum Ada Laporan</h4>
                    <p class="text-muted mb-4">Anda belum membuat laporan kegiatan magang. Klik tombol "Buat Laporan Baru" untuk membuat laporan pertama Anda.</p>
                    <a href="{{ route('magang.siswa.laporan.create') }}" class="btn btn-secondary">
                        <i class="bi bi-plus-circle me-1"></i> Buat Laporan Baru
                    </a>
                </div>
            @else
                @foreach($laporans as $laporan)
                <div class="report-card">
                    <div class="report-header">
                        <div>
                            <h5 class="report-title">{{ $laporan->judul }}</h5>
                            <div class="report-meta">
                                <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d M Y') }}</span>
                                <span><i class="bi bi-clock"></i> {{ $laporan->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <span class="week-badge">Hari #{{ $laporan->minggu_ke }}</span>
                    </div>

                    <div class="report-body">
                        <p>{{ Str::limit($laporan->deskripsi, 150) }}</p>
                    </div>

                    <div class="report-footer">
                        <div>
                            @if($laporan->status == 'draft')
                                <span class="status-badge status-draft">
                                    <i class="bi bi-file-earmark me-1"></i> Draft
                                </span>
                            @elseif($laporan->status == 'submitted')
                                <span class="status-badge status-submitted">
                                    <i class="bi bi-hourglass me-1"></i> Menunggu Review
                                </span>
                            @elseif($laporan->status == 'approved')
                                <span class="status-badge status-approved">
                                    <i class="bi bi-check-circle me-1"></i> Disetujui
                                </span>
                            @elseif($laporan->status == 'rejected')
                                <span class="status-badge status-rejected">
                                    <i class="bi bi-x-circle me-1"></i> Ditolak
                                </span>
                            @endif
                        </div>

                        <div>
                            <a href="{{ route('magang.siswa.laporan.show', $laporan->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>

                            @if($laporan->status != 'approved')
                                <a href="{{ route('magang.siswa.laporan.edit', $laporan->id) }}" class="btn btn-sm btn-outline-secondary ms-1">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>

                                <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $laporan->id }}">
                                    <i class="bi bi-trash me-1"></i> Hapus
                                </button>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $laporan->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus laporan kegiatan Hari #{{ $laporan->minggu_ke }} ini?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('magang.siswa.laporan.destroy', $laporan->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
