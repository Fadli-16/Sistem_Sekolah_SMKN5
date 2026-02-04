@extends('magang.layouts.main')

@section('css')
<style>
    .report-detail-card {
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .report-detail-header {
        background-color: var(--primary);
        color: white;
        padding: 1.5rem;
        position: relative;
    }

    .report-detail-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .report-meta {
        display: flex;
        gap: 1.5rem;
        margin-top: 0.75rem;
        font-size: 0.9rem;
    }

    .report-detail-body {
        padding: 1.5rem;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .week-badge {
        background-color: var(--primary);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0.5rem;
        display: inline-flex;
        align-items: center;
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

    .description-section {
        background-color: #f8f9fa;
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .description-content {
        white-space: pre-line;
        line-height: 1.6;
    }

    .comment-section {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .action-buttons {
        margin-top: 2rem;
        display: flex;
        gap: 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">{{ $header }}</h1>
        <a href="{{ route('magang.siswa.laporan.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="report-detail-card">
                <div class="report-detail-header">
                    <span class="week-badge">Hari #{{ $laporan->minggu_ke }}</span>
                    <div class="report-detail-title">{{ $laporan->judul }}</div>
                    <div class="report-meta">
                        <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d M Y') }}</span>
                        <span><i class="bi bi-clock"></i> {{ $laporan->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                <div class="report-detail-body">
                    <div class="mb-3">
                        @if($laporan->status == 'draft')
                            <span class="status-badge status-draft">
                                <i class="bi bi-file-earmark me-2"></i> Draft
                            </span>
                        @elseif($laporan->status == 'submitted')
                            <span class="status-badge status-submitted">
                                <i class="bi bi-hourglass me-2"></i> Menunggu Review
                            </span>
                        @elseif($laporan->status == 'approved')
                            <span class="status-badge status-approved">
                                <i class="bi bi-check-circle me-2"></i> Disetujui pada {{ $laporan->updated_at->format('d M Y, H:i') }}
                            </span>
                        @elseif($laporan->status == 'rejected')
                            <span class="status-badge status-rejected">
                                <i class="bi bi-x-circle me-2"></i> Ditolak pada {{ $laporan->updated_at->format('d M Y, H:i') }}
                            </span>
                        @endif
                    </div>

                    <div class="description-section">
                        <h3 class="section-title">Deskripsi Kegiatan</h3>
                        <div class="description-content">{{ $laporan->deskripsi }}</div>
                    </div>

                    @if($laporan->komentar && in_array($laporan->status, ['approved', 'rejected']))
                        <div class="comment-section">
                            <h3 class="section-title">Komentar Pembimbing</h3>
                            <div class="alert {{ $laporan->status == 'approved' ? 'alert-success' : 'alert-danger' }}">
                                <i class="bi {{ $laporan->status == 'approved' ? 'bi-check-circle' : 'bi-exclamation-circle' }} me-2"></i>
                                {{ $laporan->komentar }}
                            </div>
                        </div>
                    @endif

                    <div class="action-buttons">
                        @if($laporan->status == 'draft')
                            <form action="{{ route('magang.siswa.laporan.update', $laporan->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="judul" value="{{ $laporan->judul }}">
                                <input type="hidden" name="deskripsi" value="{{ $laporan->deskripsi }}">
                                <input type="hidden" name="minggu_ke" value="{{ $laporan->minggu_ke }}">
                                <input type="hidden" name="tanggal_mulai" value="{{ $laporan->tanggal_mulai }}">
                                <input type="hidden" name="tanggal_selesai" value="{{ $laporan->tanggal_selesai }}">
                                <input type="hidden" name="status" value="submitted">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="bi bi-send me-1"></i> Kirim untuk Review
                                </button>
                            </form>
                        @endif

                        @if(in_array($laporan->status, ['draft', 'rejected']))
                            <a href="{{ route('magang.siswa.laporan.edit', $laporan->id) }}" class="btn btn-secondary">
                                <i class="bi bi-pencil me-1"></i> Edit Laporan
                            </a>
                        @endif

                        @if(!in_array($laporan->status, ['approved']))
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash me-1"></i> Hapus Laporan
                            </button>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus laporan Hari #{{ $laporan->minggu_ke }} ini?</p>
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
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Status Laporan</h5>
                </div>
                <div class="card-body">
                    @if($laporan->status == 'draft')
                        <div class="alert alert-secondary mb-3">
                            <h6><i class="bi bi-info-circle me-2"></i>Laporan Belum Dikirim</h6>
                            <p class="mb-0">Laporan ini masih dalam bentuk draft dan belum dikirim untuk direview oleh pembimbing. Klik "Kirim untuk Review" untuk mengirimkan laporan ini.</p>
                        </div>
                    @elseif($laporan->status == 'submitted')
                        <div class="alert alert-warning mb-3">
                            <h6><i class="bi bi-hourglass me-2"></i>Menunggu Review</h6>
                            <p class="mb-0">Laporan ini telah dikirim dan sedang menunggu review dari pembimbing magang Anda.</p>
                        </div>
                    @elseif($laporan->status == 'approved')
                        <div class="alert alert-success mb-3">
                            <h6><i class="bi bi-check-circle me-2"></i>Laporan Disetujui</h6>
                            <p class="mb-0">Selamat! Laporan kegiatan magang minggu #{{ $laporan->minggu_ke }} telah disetujui oleh pembimbing.</p>
                        </div>
                    @elseif($laporan->status == 'rejected')
                        <div class="alert alert-danger mb-3">
                            <h6><i class="bi bi-x-circle me-2"></i>Laporan Ditolak</h6>
                            <p class="mb-0">Laporan ini ditolak oleh pembimbing. Silakan perbaiki sesuai dengan komentar yang diberikan, lalu kirim kembali.</p>
                        </div>
                    @endif

                    <div class="mt-3">
                        <h6>Status Timeline</h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-dot bg-success"></div>
                                <div class="timeline-content">
                                    <div class="timeline-date">{{ $laporan->created_at->format('d M Y, H:i') }}</div>
                                    <h6 class="mb-0">Dibuat</h6>
                                </div>
                            </div>

                            @if($laporan->status != 'draft')
                                <div class="timeline-item">
                                    <div class="timeline-dot {{ $laporan->status == 'submitted' ? 'bg-warning' : ($laporan->status == 'approved' ? 'bg-success' : 'bg-danger') }}"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-date">{{ $laporan->updated_at->format('d M Y, H:i') }}</div>
                                        <h6 class="mb-0">{{ $laporan->status == 'submitted' ? 'Dikirim untuk Review' : ($laporan->status == 'approved' ? 'Disetujui' : 'Ditolak') }}</h6>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-info-circle me-2 text-primary"></i>
                            <h6 class="mb-0">Panduan Laporan</h6>
                        </div>
                        <ul class="mb-0 ps-4">
                            <li>Laporan minggu yang disetujui tidak dapat diedit atau dihapus.</li>
                            <li>Jika laporan ditolak, perbaiki sesuai komentar pembimbing.</li>
                            <li>Pastikan laporan mencakup semua kegiatan selama minggu tersebut.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Timeline CSS */
.timeline {
    position: relative;
    padding-left: 30px;
    margin-top: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
}

.timeline:before {
    content: "";
    position: absolute;
    top: 0;
    left: 10px;
    height: 100%;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-dot {
    position: absolute;
    left: -30px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.timeline-content {
    padding-bottom: 10px;
}

.timeline-date {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 5px;
}
</style>
@endsection
