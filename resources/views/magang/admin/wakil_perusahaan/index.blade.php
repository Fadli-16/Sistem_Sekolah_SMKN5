@extends('magang.layouts.main')

@section('css')
<style>
    .applicant-card {
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: none;
    }
    
    .applicant-card .card-header {
        background-color: var(--primary);
        color: white;
        font-weight: 600;
        padding: 1rem 1.5rem;
    }
    
    .applicant-card .card-body {
        padding: 1.5rem;
    }
    
    .applicant-info {
        margin-bottom: 1.5rem;
    }
    
    .applicant-info .label {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .applicant-info .value {
        font-weight: 500;
        color: var(--text-dark);
    }
    
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }
    
    .status-badge i {
        margin-right: 0.35rem;
    }
    
    .status-pending {
        background-color: rgba(255, 191, 0, 0.15);
        color: #cc9700;
    }
    
    .status-accepted {
        background-color: rgba(78, 205, 196, 0.15);
        color: var(--secondary-dark);
    }
    
    .status-rejected {
        background-color: rgba(231, 76, 60, 0.15);
        color: #c0392b;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.75rem;
    }
    
    .action-buttons .btn {
        border-radius: var(--radius);
        padding: 0.5rem 1rem;
        font-weight: 500;
    }
    
    .file-preview {
        background-color: var(--bg-gray);
        border-radius: var(--radius);
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .file-preview a {
        display: flex;
        align-items: center;
        color: var(--primary);
        text-decoration: none;
    }
    
    .file-preview i {
        font-size: 1.5rem;
        margin-right: 0.75rem;
    }
    
    .modal-header {
        background-color: var(--primary);
        color: white;
    }
    
    .modal-footer {
        border-top: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Kelola Pendaftaran Mitra Magang</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#pending">
                                <i class="bi bi-hourglass me-1"></i> Menunggu 
                                <span class="badge bg-warning ms-1">{{ $applicants->where('status', 'Pending')->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#accepted">
                                <i class="bi bi-check-circle me-1"></i> Diterima
                                <span class="badge bg-success ms-1">{{ $applicants->where('status', 'Accepted')->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#rejected">
                                <i class="bi bi-x-circle me-1"></i> Ditolak
                                <span class="badge bg-danger ms-1">{{ $applicants->where('status', 'Rejected')->count() }}</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="pending">
                            @if($applicants->where('status', 'Pending')->count() > 0)
                                @foreach($applicants->where('status', 'Pending') as $applicant)
                                    <div class="applicant-card card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-building me-2"></i> {{ $applicant->nama_perusahaan }}
                                            </div>
                                            <div class="status-badge status-pending">
                                                <i class="bi bi-hourglass"></i> Menunggu
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="applicant-info">
                                                        <div class="label">Nama Lengkap</div>
                                                        <div class="value">{{ $applicant->nama }}</div>
                                                    </div>
                                                    <div class="applicant-info">
                                                        <div class="label">Email</div>
                                                        <div class="value">{{ $applicant->email }}</div>
                                                    </div>
                                                    <div class="applicant-info">
                                                        <div class="label">Tanggal Pendaftaran</div>
                                                        <div class="value">{{ $applicant->created_at->format('d F Y, H:i') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="applicant-info mb-4">
                                                        <div class="label">Bukti Lampiran</div>
                                                        <div class="file-preview">
                                                            <a href="{{ Storage::url($applicant->bukti_lampiran) }}" target="_blank">                                                                <i class="bi bi-file-earmark-pdf"></i>
                                                                <span>Lihat Dokumen</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $applicant->id }}">
                                                            <i class="bi bi-check-circle me-1"></i> Setujui
                                                        </button>
                                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $applicant->id }}">
                                                            <i class="bi bi-x-circle me-1"></i> Tolak
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $applicant->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info">
                                                        <i class="bi bi-info-circle me-2"></i>
                                                        <strong>Informasi:</strong> Menyetujui pendaftaran akan membuat akun untuk wakil perusahaan dan mengirimkan email notifikasi.
                                                    </div>
                                                    
                                                    <p>Apakah Anda yakin ingin menyetujui pendaftaran dari <strong>{{ $applicant->nama }}</strong> sebagai wakil dari <strong>{{ $applicant->nama_perusahaan }}</strong>?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="bi bi-x me-1"></i> Batal
                                                    </button>
                                                    <form action="{{ route('admin.magang.wakil_perusahaan.approve', $applicant->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="bi bi-check-circle me-1"></i> Setujui
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $applicant->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Penolakan</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.magang.wakil_perusahaan.reject', $applicant->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="alert alert-warning">
                                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                                            <strong>Peringatan:</strong> Email notifikasi akan dikirimkan ke pendaftar beserta catatan penolakan yang Anda berikan.
                                                        </div>
                                                        
                                                        <p>Apakah Anda yakin ingin menolak pendaftaran dari <strong>{{ $applicant->nama }}</strong>?</p>
                                                        
                                                        <div class="mb-3">
                                                            <label for="notes{{ $applicant->id }}" class="form-label">Catatan Penolakan (opsional)</label>
                                                            <textarea class="form-control" id="notes{{ $applicant->id }}" name="notes" rows="3" 
                                                            placeholder="Berikan alasan penolakan untuk pendaftar..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="bi bi-x me-1"></i> Batal
                                                        </button>
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-x-circle me-1"></i> Tolak
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> Tidak ada pendaftaran yang menunggu persetujuan saat ini.
                                </div>
                            @endif
                        </div>
                        
                        <div class="tab-pane fade" id="accepted">
                            @if($applicants->where('status', 'Accepted')->count() > 0)
                                @foreach($applicants->where('status', 'Accepted') as $applicant)
                                    <div class="applicant-card card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-building me-2"></i> {{ $applicant->nama_perusahaan }}
                                            </div>
                                            <div class="status-badge status-accepted">
                                                <i class="bi bi-check-circle"></i> Diterima
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="applicant-info">
                                                        <div class="label">Nama Lengkap</div>
                                                        <div class="value">{{ $applicant->nama }}</div>
                                                    </div>
                                                    <div class="applicant-info">
                                                        <div class="label">Email</div>
                                                        <div class="value">{{ $applicant->email }}</div>
                                                    </div>
                                                    <div class="applicant-info">
                                                        <div class="label">Tanggal Pendaftaran</div>
                                                        <div class="value">{{ $applicant->created_at->format('d F Y, H:i') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="applicant-info">
                                                        <div class="label">Status Akun</div>
                                                        <div class="value">
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-person-check me-1"></i> Akun Aktif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="applicant-info">
                                                        <div class="label">Bukti Lampiran</div>
                                                        <div class="file-preview">
                                                            <a href="{{ asset('storage/'.$applicant->bukti_lampiran) }}" target="_blank">
                                                                <i class="bi bi-file-earmark-pdf"></i>
                                                                <span>Lihat Dokumen</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> Belum ada pendaftaran yang disetujui.
                                </div>
                            @endif
                        </div>
                        
                        <div class="tab-pane fade" id="rejected">
                            @if($applicants->where('status', 'Rejected')->count() > 0)
                                @foreach($applicants->where('status', 'Rejected') as $applicant)
                                    <div class="applicant-card card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-building me-2"></i> {{ $applicant->nama_perusahaan }}
                                            </div>
                                            <div class="status-badge status-rejected">
                                                <i class="bi bi-x-circle"></i> Ditolak
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="applicant-info">
                                                        <div class="label">Nama Lengkap</div>
                                                        <div class="value">{{ $applicant->nama }}</div>
                                                    </div>
                                                    <div class="applicant-info">
                                                        <div class="label">Email</div>
                                                        <div class="value">{{ $applicant->email }}</div>
                                                    </div>
                                                    <div class="applicant-info">
                                                        <div class="label">Tanggal Pendaftaran</div>
                                                        <div class="value">{{ $applicant->created_at->format('d F Y, H:i') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="applicant-info">
                                                        <div class="label">Catatan Penolakan</div>
                                                        <div class="value">
                                                            @if($applicant->notes)
                                                                {{ $applicant->notes }}
                                                            @else
                                                                <span class="text-muted">Tidak ada catatan</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="applicant-info">
                                                        <div class="label">Bukti Lampiran</div>
                                                        <div class="file-preview">
                                                            <a href="{{ asset('storage/'.$applicant->bukti_lampiran) }}" target="_blank">
                                                                <i class="bi bi-file-earmark-pdf"></i>
                                                                <span>Lihat Dokumen</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> Belum ada pendaftaran yang ditolak.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection