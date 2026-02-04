@extends('magang.layouts.main')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Status Pendaftaran Magang</h5>
    </div>
    <div class="card-body">
        @if($applications->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Perusahaan</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                        <tr>
                            <td>{{ $application->opening->judul ?? 'Program Magang' }}</td>
                            <td>{{ $application->wakilPerusahaan->nama_perusahaan }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($application->tanggal_mulai)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($application->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td>
                                @if($application->status == 'Menunggu')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass me-1"></i> Menunggu
                                    </span>
                                @elseif($application->status == 'Disetujui')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Disetujui
                                    </span>
                                @elseif($application->status == 'Ditolak')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $application->id }}">
                                    <i class="bi bi-info-circle me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailModal{{ $application->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Pendaftaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h6>{{ $application->opening->judul ?? 'Program Magang' }}</h6>
                                        <p class="mb-4"><strong>Perusahaan:</strong> {{ $application->wakilPerusahaan->nama_perusahaan }}</p>
                                        
                                        <div class="mb-3">
                                            <h6 class="mb-2">Informasi Status</h6>
                                            <div class="alert {{ $application->status == 'Disetujui' ? 'alert-success' : ($application->status == 'Ditolak' ? 'alert-danger' : 'alert-warning') }}">
                                                @if($application->status == 'Menunggu')
                                                    <i class="bi bi-hourglass me-1"></i> Pendaftaran Anda sedang menunggu konfirmasi dari perusahaan.
                                                @elseif($application->status == 'Disetujui')
                                                    <i class="bi bi-check-circle me-1"></i> Selamat! Pendaftaran magang Anda telah disetujui.
                                                @elseif($application->status == 'Ditolak')
                                                    <i class="bi bi-x-circle me-1"></i> Maaf, pendaftaran magang Anda ditolak.
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($application->catatan)
                                        <div class="mb-3">
                                            <h6 class="mb-2">Catatan dari Perusahaan</h6>
                                            <div class="alert alert-light">
                                                {{ $application->catatan }}
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <h6 class="mb-2">Periode Magang</h6>
                                            <p class="mb-1"><strong>Mulai:</strong> {{ \Carbon\Carbon::parse($application->tanggal_mulai)->format('d M Y') }}</p>
                                            <p><strong>Selesai:</strong> {{ \Carbon\Carbon::parse($application->tanggal_selesai)->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i> Anda belum mendaftar program magang apapun.
            </div>
        @endif
    </div>
</div>
@endsection