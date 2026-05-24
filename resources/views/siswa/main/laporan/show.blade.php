@extends('siswa.layouts.main')

@section('css')
<style>
    .detail-card {
        background-color: var(--bg-light);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 2rem;
    }
    
    .status-badge {
        font-size: 0.8rem;
        padding: 0.35rem 0.65rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
    }
    
    .status-pending {
        background-color: #ffeeba;
        color: #856404;
    }
    
    .status-process {
        background-color: #b8daff;
        color: #004085;
    }
    
    .status-completed {
        background-color: #c3e6cb;
        color: #155724;
    }
    
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .detail-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .detail-title {
        color: var(--primary);
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .detail-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        color: var(--text-muted);
    }
    
    .detail-meta span {
        display: flex;
        align-items: center;
    }
    
    .detail-meta i {
        margin-right: 0.5rem;
    }
    
    .detail-content {
        margin-bottom: 1.5rem;
    }
    
    .detail-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
    }
    
    .detail-value {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: var(--radius-sm);
        white-space: pre-line;
    }
    
    .detail-footer {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 2rem;
        padding-bottom: 1.5rem;
        border-left: 1px dashed #ccc;
        margin-left: 1rem;
    }
    
    .timeline-item:last-child {
        border-left: 0;
        padding-bottom: 0;
    }
    
    .timeline-dot {
        position: absolute;
        left: -0.65rem;
        top: 0;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 50%;
        background-color: var(--secondary);
    }
    
    .timeline-content {
        margin-left: 0.5rem;
    }
    
    .timeline-date {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .timeline-text {
        margin-bottom: 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Detail Laporan</h1>
            <p class="text-muted">Informasi lengkap laporan kerusakan</p>
        </div>
        <a href="{{ route('siswa.laporan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="detail-card">
                <div class="detail-header">
                    <h2 class="detail-title">{{ $laporan->nama_alat }}</h2>
                    <div class="detail-meta">
                        <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->format('d F Y') }}</span>
                        <span><i class="bi bi-person"></i> {{ $laporan->nama_pelapor }}</span>
                        <span>
                            <i class="bi bi-flag"></i>
                            <span class="status-badge status-{{ $laporan->status ?? 'pending' }}">
                                {{ ucfirst($laporan->status ?? 'Pending') }}
                            </span>
                        </span>
                    </div>
                </div>
                
                <div class="detail-content">
                    <div class="mb-4">
                        <div class="detail-label">Deskripsi Kerusakan</div>
                        <div class="detail-value">{{ $laporan->deskripsi_kerusakan }}</div>
                    </div>
                    
                    @if(isset($laporan->lokasi))
                    <div class="mb-4">
                        <div class="detail-label">Lokasi</div>
                        <div class="detail-value">{{ $laporan->lokasi }}</div>
                    </div>
                    @endif
                </div>
                
                @if(isset($laporan->tanggapan) && !empty($laporan->tanggapan))
                <div class="detail-footer mt-4 pt-4 border-top">
                    <h4 class="text-primary mb-3"><i class="bi bi-chat-left-text me-2"></i>Tanggapan Admin</h4>
                    <div class="detail-value p-3 bg-light border-start border-4 border-primary">
                        {{ $laporan->tanggapan }}
                    </div>
                    <div class="text-muted mt-2 small">
                        <i class="bi bi-clock me-1"></i> Diperbarui: {{ \Carbon\Carbon::parse($laporan->updated_at)->format('d M Y, H:i') }}
                    </div>
                </div>
                @else
                <div class="detail-footer mt-4 pt-4 border-top">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> Belum ada tanggapan dari admin untuk laporan ini.
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i> Status Laporan</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y, H:i') }}</div>
                                <h6 class="mb-0">Laporan Dikirim</h6>
                                <p class="timeline-text">Laporan kerusakan telah berhasil dikirim ke admin.</p>
                            </div>
                        </div>
                        
                        @if($laporan->status == 'process')
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ \Carbon\Carbon::parse($laporan->updated_at)->format('d M Y, H:i') }}</div>
                                <h6 class="mb-0">Dalam Proses</h6>
                                <p class="timeline-text">Laporan sedang ditindaklanjuti oleh admin.</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($laporan->status == 'completed')
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ \Carbon\Carbon::parse($laporan->updated_at)->format('d M Y, H:i') }}</div>
                                <h6 class="mb-0">Selesai</h6>
                                <p class="timeline-text">Masalah telah berhasil diselesaikan.</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($laporan->status == 'rejected')
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ \Carbon\Carbon::parse($laporan->updated_at)->format('d M Y, H:i') }}</div>
                                <h6 class="mb-0">Ditolak</h6>
                                <p class="timeline-text">Laporan tidak dapat diproses. Silakan periksa tanggapan admin.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection