@extends('magang.layouts.main')

@section('css')
<style>
    .intern-detail-card {
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 1.5rem;
        background-color: var(--bg-light);
    }
    
    .intern-header {
        background-color: var(--primary);
        color: white;
        padding: 1.5rem;
    }
    
    .intern-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .intern-body {
        padding: 1.5rem;
    }
    
    .info-section {
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .info-item {
        margin-bottom: 1rem;
    }
    
    .info-label {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-weight: 500;
        color: var(--text-dark);
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: 1rem;
    }
    
    .status-pending {
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
    
    .action-buttons {
        margin-top: 2rem;
        display: flex;
        gap: 0.75rem;
    }
    
    .notes-section {
        background-color: #f8f9fa;
        border-radius: var(--radius);
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="page-title">{{ $header }}</h1>
                <a href="{{ route('magang.wakil_perusahaan.interns') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="intern-detail-card">
                <div class="intern-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="intern-title">{{ $intern->nama }}</div>
                        <div>{{ $intern->opening->posisi ?? 'Program Magang' }}</div>
                    </div>
                    @if($intern->status == 'Menunggu')
                        <span class="status-badge status-pending">
                            <i class="bi bi-hourglass me-1"></i> Menunggu Konfirmasi
                        </span>
                    @elseif($intern->status == 'Disetujui')
                        <span class="status-badge status-approved">
                            <i class="bi bi-check-circle me-1"></i> Disetujui
                        </span>
                    @elseif($intern->status == 'Ditolak')
                        <span class="status-badge status-rejected">
                            <i class="bi bi-x-circle me-1"></i> Ditolak
                        </span>
                    @endif
                </div>
                
                <div class="intern-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h3 class="section-title">
                                    <i class="bi bi-person me-2"></i> Informasi Siswa
                                </h3>
                                
                                <div class="info-item">
                                    <div class="info-label">Nama Lengkap</div>
                                    <div class="info-value">{{ $intern->nama }}</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">{{ $intern->email }}</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Nomor Telepon</div>
                                    <div class="info-value">{{ $intern->no_hp ?? 'Tidak ada' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-section">
                                <h3 class="section-title">
                                    <i class="bi bi-calendar-event me-2"></i> Informasi Program
                                </h3>
                                
                                <div class="info-item">
                                    <div class="info-label">Program Magang</div>
                                    <div class="info-value">{{ $intern->opening->posisi ?? 'Tidak ada' }}</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Tanggal Mulai</div>
                                    <div class="info-value">{{ \Carbon\Carbon::parse($intern->tanggal_mulai)->format('d F Y') }}</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Tanggal Selesai</div>
                                    <div class="info-value">{{ \Carbon\Carbon::parse($intern->tanggal_selesai)->format('d F Y') }}</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Status</div>
                                    <div class="info-value">
                                        @if($intern->status == 'Menunggu')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-hourglass me-1"></i> Menunggu
                                            </span>
                                        @elseif($intern->status == 'Disetujui')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Disetujui
                                            </span>
                                        @elseif($intern->status == 'Ditolak')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i> Ditolak
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Tanggal Pendaftaran</div>
                                    <div class="info-value">{{ $intern->created_at->format('d F Y, H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($intern->catatan)
                        <div class="info-section">
                            <h3 class="section-title">
                                <i class="bi bi-chat-left-text me-2"></i> Catatan
                            </h3>
                            <div class="notes-section">
                                {{ $intern->catatan }}
                            </div>
                        </div>
                    @endif
                    
                    @if($intern->status == 'Menunggu')
                        <div class="action-buttons">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-1"></i> Setujui Pendaftaran
                            </button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i> Tolak Pendaftaran
                            </button>
                        </div>
                        
                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('magang.wakil_perusahaan.interns.approve', $intern->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="alert alert-info mb-3">
                                                <i class="bi bi-info-circle me-2"></i>
                                                <strong>Informasi:</strong> Menyetujui siswa akan memberikan akses kepada siswa untuk program magang ini.
                                            </div>
                                            
                                            <p>Apakah Anda yakin ingin menyetujui pendaftaran dari <strong>{{ $intern->nama }}</strong>?</p>
                                            
                                            <div class="mb-3">
                                                <label for="catatan" class="form-label">Catatan (opsional)</label>
                                                <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan untuk siswa..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-circle me-1"></i> Setujui
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Penolakan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('magang.wakil_perusahaan.interns.reject', $intern->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="alert alert-warning mb-3">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                <strong>Peringatan:</strong> Menolak siswa akan membatalkan pendaftaran magang ini.
                                            </div>
                                            
                                            <p>Apakah Anda yakin ingin menolak pendaftaran dari <strong>{{ $intern->nama }}</strong>?</p>
                                            
                                            <div class="mb-3">
                                                <label for="alasan" class="form-label">Alasan Penolakan</label>
                                                <textarea class="form-control" id="alasan" name="alasan" rows="3" placeholder="Berikan alasan penolakan untuk siswa..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="bi bi-x-circle me-1"></i> Tolak
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection