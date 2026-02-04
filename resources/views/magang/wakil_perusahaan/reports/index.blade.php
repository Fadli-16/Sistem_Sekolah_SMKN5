@extends('magang.layouts.main')

@section('css')
<style>
    .report-tabs .nav-link {
        padding: 0.75rem 1.25rem;
        font-weight: 500;
    }

    .report-tabs .nav-link.active {
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

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
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .student-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 600;
        color: #495057;
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
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs report-tabs" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                        <i class="bi bi-hourglass me-1"></i> Menunggu Review
                        <span class="badge bg-warning text-dark ms-1">{{ $pendingLaporans->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviewed-tab" data-bs-toggle="tab" data-bs-target="#reviewed" type="button" role="tab" aria-controls="reviewed" aria-selected="false">
                        <i class="bi bi-check2-all me-1"></i> Sudah Direview
                        <span class="badge bg-secondary ms-1">{{ $reviewedLaporans->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="reportTabsContent">
                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    @if($pendingLaporans->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <h5>Tidak Ada Laporan yang Menunggu Review</h5>
                            <p class="text-muted">Semua laporan siswa magang telah Anda review.</p>
                        </div>
                    @else
                        @foreach($pendingLaporans as $laporan)
                        <div class="report-card">
                            <div class="report-header">
                                <div>
                                    <h5 class="report-title">{{ $laporan->judul }}</h5>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            {{ substr($laporan->magangSiswa->nama, 0, 1) }}
                                        </div>
                                        <span>{{ $laporan->magangSiswa->nama }}</span>
                                    </div>
                                    <div class="report-meta">
                                        <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d M Y') }}</span>
                                        <span><i class="bi bi-clock"></i> {{ $laporan->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="week-badge">Hari #{{ $laporan->minggu_ke }}</span>
                                    <span class="status-badge status-submitted">
                                        <i class="bi bi-hourglass me-1"></i> Menunggu Review
                                    </span>
                                </div>
                            </div>

                            <div class="report-body">
                                <p>{{ Str::limit($laporan->deskripsi, 200) }}</p>
                            </div>

                            <div class="report-footer">
                                <div></div>
                                <div>
                                    <a href="{{ route('magang.wakil_perusahaan.reports.show', $laporan->id) }}" class="btn btn-secondary">
                                        <i class="bi bi-clipboard-check me-1"></i> Review Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                <div class="tab-pane fade" id="reviewed" role="tabpanel" aria-labelledby="reviewed-tab">
                    @if($reviewedLaporans->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-journal-check"></i>
                            </div>
                            <h5>Belum Ada Laporan yang Direview</h5>
                            <p class="text-muted">Anda belum mereview laporan kegiatan siswa magang.</p>
                        </div>
                    @else
                        @foreach($reviewedLaporans as $laporan)
                        <div class="report-card">
                            <div class="report-header">
                                <div>
                                    <h5 class="report-title">{{ $laporan->judul }}</h5>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            {{ substr($laporan->magangSiswa->nama, 0, 1) }}
                                        </div>
                                        <span>{{ $laporan->magangSiswa->nama }}</span>
                                    </div>
                                    <div class="report-meta">
                                        <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($laporan->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d M Y') }}</span>
                                        <span><i class="bi bi-clock"></i> Direview {{ $laporan->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="week-badge">Hari #{{ $laporan->minggu_ke }}</span>
                                    @if($laporan->status == 'approved')
                                        <span class="status-badge status-approved">
                                            <i class="bi bi-check-circle me-1"></i> Disetujui
                                        </span>
                                    @elseif($laporan->status == 'rejected')
                                        <span class="status-badge status-rejected">
                                            <i class="bi bi-x-circle me-1"></i> Ditolak
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="report-body">
                                <p>{{ Str::limit($laporan->deskripsi, 200) }}</p>

                                @if($laporan->komentar)
                                <div class="mt-3 pt-3 border-top">
                                    <h6><i class="bi bi-chat-left-text me-2"></i>Komentar Pembimbing:</h6>
                                    <p class="mb-0 fst-italic">"{{ Str::limit($laporan->komentar, 150) }}"</p>
                                </div>
                                @endif
                            </div>

                            <div class="report-footer">
                                <div></div>
                                <div>
                                    <a href="{{ route('magang.wakil_perusahaan.reports.show', $laporan->id) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
