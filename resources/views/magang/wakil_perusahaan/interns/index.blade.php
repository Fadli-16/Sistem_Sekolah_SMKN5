@extends('magang.layouts.main')

@section('css')
<style>
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: var(--primary);
        border-bottom: 2px solid var(--primary);
        font-weight: 600;
    }
    .intern-card {
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .intern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .intern-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-bottom: 1px solid #eaeaea;
        border-radius: 8px 8px 0 0;
    }
    .intern-body {
        padding: 15px;
    }
    .intern-meta {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    .intern-meta-item {
        display: flex;
        align-items: center;
        color: #6c757d;
        font-size: 0.9rem;
        margin-right: 16px;
        margin-bottom: 8px;
    }
    .intern-meta-item i {
        margin-right: 6px;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
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
        margin-top: 15px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }
    .empty-icon {
        font-size: 3rem;
        color: #adb5bd;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Siswa Magang</h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#pending">
                        <i class="bi bi-hourglass me-1"></i> Menunggu 
                        <span class="badge bg-warning text-dark ms-1">{{ $pendingInterns->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#approved">
                        <i class="bi bi-check-circle me-1"></i> Disetujui
                        <span class="badge bg-success ms-1">{{ $approvedInterns->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#rejected">
                        <i class="bi bi-x-circle me-1"></i> Ditolak
                        <span class="badge bg-danger ms-1">{{ $rejectedInterns->count() }}</span>
                    </a>
                </li>
            </ul>
            
            <div class="tab-content">
                <!-- Pending Applications Tab -->
                <div class="tab-pane fade show active" id="pending">
                    @if($pendingInterns->count() > 0)
                        @foreach($pendingInterns as $intern)
                            <div class="intern-card">
                                <div class="intern-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $intern->nama }}</h5>
                                    <span class="status-badge status-pending">
                                        <i class="bi bi-hourglass me-1"></i> Menunggu Konfirmasi
                                    </span>
                                </div>
                                <div class="intern-body">
                                    <div class="intern-meta">
                                        <div class="intern-meta-item">
                                            <i class="bi bi-briefcase"></i>
                                            <span>{{ $intern->opening->judul ?? 'Program Magang' }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-envelope"></i>
                                            <span>{{ $intern->email }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-telephone"></i>
                                            <span>{{ $intern->no_hp ?? 'Tidak ada nomor' }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="intern-meta">
                                        <div class="intern-meta-item">
                                            <i class="bi bi-calendar-event"></i>
                                            <span>Mulai: {{ \Carbon\Carbon::parse($intern->tanggal_mulai)->format('d M Y') }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-calendar-check"></i>
                                            <span>Selesai: {{ \Carbon\Carbon::parse($intern->tanggal_selesai)->format('d M Y') }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-clock-history"></i>
                                            <span>Daftar: {{ $intern->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="action-buttons">
                                        <a href="{{ route('magang.wakil_perusahaan.interns.show', $intern->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $intern->id }}">
                                            <i class="bi bi-check-circle me-1"></i> Setujui
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $intern->id }}">
                                            <i class="bi bi-x-circle me-1"></i> Tolak
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Approve Modal for each intern -->
                            <div class="modal fade" id="approveModal{{ $intern->id }}" tabindex="-1" aria-hidden="true">
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
                                                    <label for="catatan{{ $intern->id }}" class="form-label">Catatan (opsional)</label>
                                                    <textarea class="form-control" id="catatan{{ $intern->id }}" name="catatan" rows="3" placeholder="Tambahkan catatan untuk siswa..."></textarea>
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
                            
                            <!-- Reject Modal for each intern -->
                            <div class="modal fade" id="rejectModal{{ $intern->id }}" tabindex="-1" aria-hidden="true">
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
                                                    <label for="alasan{{ $intern->id }}" class="form-label">Alasan Penolakan</label>
                                                    <textarea class="form-control" id="alasan{{ $intern->id }}" name="alasan" rows="3" placeholder="Berikan alasan penolakan untuk siswa..." required></textarea>
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
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-hourglass"></i>
                            </div>
                            <h5>Tidak Ada Pendaftaran Menunggu</h5>
                            <p class="text-muted">Belum ada siswa yang mendaftar program magang Anda atau semua pendaftaran telah diproses.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Approved Tab -->
                <div class="tab-pane fade" id="approved">
                    @if($approvedInterns->count() > 0)
                        @foreach($approvedInterns as $intern)
                            <div class="intern-card">
                                <div class="intern-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $intern->nama }}</h5>
                                    <span class="status-badge status-approved">
                                        <i class="bi bi-check-circle me-1"></i> Disetujui
                                    </span>
                                </div>
                                <div class="intern-body">
                                    <!-- Similar content as pending interns -->
                                    <div class="intern-meta">
                                        <div class="intern-meta-item">
                                            <i class="bi bi-briefcase"></i>
                                            <span>{{ $intern->opening->judul ?? 'Program Magang' }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-envelope"></i>
                                            <span>{{ $intern->email }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-telephone"></i>
                                            <span>{{ $intern->no_hp ?? 'Tidak ada nomor' }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="intern-meta">
                                        <div class="intern-meta-item">
                                            <i class="bi bi-calendar-event"></i>
                                            <span>Mulai: {{ \Carbon\Carbon::parse($intern->tanggal_mulai)->format('d M Y') }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-calendar-check"></i>
                                            <span>Selesai: {{ \Carbon\Carbon::parse($intern->tanggal_selesai)->format('d M Y') }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-check2-circle"></i>
                                            <span>Disetujui: {{ $intern->updated_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="action-buttons">
                                        <a href="{{ route('magang.wakil_perusahaan.interns.show', $intern->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h5>Belum Ada Siswa Disetujui</h5>
                            <p class="text-muted">Anda belum menyetujui siswa magang apapun.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Rejected Tab -->
                <div class="tab-pane fade" id="rejected">
                    @if($rejectedInterns->count() > 0)
                        @foreach($rejectedInterns as $intern)
                            <div class="intern-card">
                                <div class="intern-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $intern->nama }}</h5>
                                    <span class="status-badge status-rejected">
                                        <i class="bi bi-x-circle me-1"></i> Ditolak
                                    </span>
                                </div>
                                <div class="intern-body">
                                    <!-- Similar content as other interns -->
                                    <div class="intern-meta">
                                        <div class="intern-meta-item">
                                            <i class="bi bi-briefcase"></i>
                                            <span>{{ $intern->opening->judul ?? 'Program Magang' }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-envelope"></i>
                                            <span>{{ $intern->email }}</span>
                                        </div>
                                        <div class="intern-meta-item">
                                            <i class="bi bi-telephone"></i>
                                            <span>{{ $intern->no_hp ?? 'Tidak ada nomor' }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="intern-meta">
                                        <div class="intern-meta-item">
                                            <i class="bi bi-calendar-x"></i>
                                            <span>Ditolak: {{ $intern->updated_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <h6><i class="bi bi-chat-left-text me-2"></i>Alasan Penolakan:</h6>
                                        <div class="alert alert-light">
                                            {{ $intern->catatan ?: 'Tidak ada alasan yang diberikan.' }}
                                        </div>
                                    </div>
                                    
                                    <div class="action-buttons">
                                        <a href="{{ route('magang.wakil_perusahaan.interns.show', $intern->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <h5>Tidak Ada Pendaftaran Ditolak</h5>
                            <p class="text-muted">Anda belum menolak siswa magang apapun.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection