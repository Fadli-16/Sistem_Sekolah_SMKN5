@extends('siswa.layouts.main')

@section('css')
<style>
    .detail-card {
        background-color: var(--bg-light);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 2rem;
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
    
    .detail-subtitle {
        color: var(--text-muted);
        margin-bottom: 0;
    }
    
    .detail-image {
        width: 100%;
        max-height: 350px;
        object-fit: cover;
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
    }
    
    .no-image-placeholder {
        height: 250px;
        border-radius: var(--radius);
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }
    
    .no-image-placeholder i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .detail-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .detail-item {
        display: flex;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .detail-item:last-child {
        border-bottom: none;
    }
    
    .detail-item-label {
        width: 140px;
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .detail-item-value {
        flex: 1;
        color: var(--text-muted);
    }
    
    .badge-kondisi {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        border-radius: var(--radius-sm);
        display: inline-block;
    }
    
    .kondisi-Baik {
        background-color: #2ecc71;
        color: #fff;
    }
    
    .kondisi-Rusak-Ringan {
        background-color: #f39c12;
        color: #fff;
    }
    
    .kondisi-Rusak-Berat {
        background-color: #e74c3c;
        color: #fff;
    }
    
    .status-badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        border-radius: var(--radius-sm);
        display: inline-block;
    }
    
    .status-Tersedia {
        background-color: #4ecdc4;
        color: #fff;
    }
    
    .status-Tidak {
        background-color: #ff6b6b;
        color: #fff;
    }
    
    .detail-deskripsi {
        white-space: pre-line;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Detail Inventaris</h1>
            <p class="text-muted">Informasi lengkap tentang inventaris laboratorium</p>
        </div>
        <a href="{{ route('siswa.inventaris.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="detail-card">
                <div class="detail-header">
                    <h2 class="detail-title">{{ $item->nama_inventaris }}</h2>
                    <p class="detail-subtitle">
                        <i class="bi bi-tag me-1"></i> {{ $item->kategori }} &nbsp;|&nbsp; 
                        <i class="bi bi-geo-alt me-1"></i> {{ $item->lokasi }}
                    </p>
                </div>
                
                @if($item->gambar)
                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama_inventaris }}" class="detail-image">
                @else
                    <div class="no-image-placeholder">
                        <i class="bi bi-image"></i>
                        <p>Tidak ada gambar inventaris</p>
                    </div>
                @endif
                
                <div class="detail-body">
                    <ul class="detail-list">
                        <li class="detail-item">
                            <div class="detail-item-label">Jumlah</div>
                            <div class="detail-item-value">{{ $item->jumlah }} unit</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-item-label">Kondisi</div>
                            <div class="detail-item-value">
                                <span class="badge-kondisi kondisi-{{ str_replace(' ', '-', $item->kondisi ?? 'Baik') }}">
                                    {{ $item->kondisi ?? 'Baik' }}
                                </span>
                            </div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-item-label">Status</div>
                            <div class="detail-item-value">
                                <span class="status-badge status-{{ explode(' ', $item->status)[0] }}">
                                    {{ $item->status }}
                                </span>
                            </div>
                        </li>
                        @if($item->tanggal_pengadaan)
                        <li class="detail-item">
                            <div class="detail-item-label">Tanggal Pengadaan</div>
                            <div class="detail-item-value">{{ \Carbon\Carbon::parse($item->tanggal_pengadaan)->format('d F Y') }}</div>
                        </li>
                        @endif
                        <li class="detail-item">
                            <div class="detail-item-label">Deskripsi</div>
                            <div class="detail-item-value detail-deskripsi">{{ $item->deskripsi ?: 'Tidak ada deskripsi' }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i> Ringkasan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Kategori</strong></td>
                            <td>{{ $item->kategori }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah</strong></td>
                            <td>{{ $item->jumlah }} unit</td>
                        </tr>
                        <tr>
                            <td><strong>Lokasi</strong></td>
                            <td>{{ $item->lokasi }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ditambahkan</strong></td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Terakhir Diubah</strong></td>
                            <td>{{ \Carbon\Carbon::parse($item->updated_at)->format('d F Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-exclamation-triangle me-2"></i> Peringatan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        @if($item->status == 'Tersedia')
                            Inventaris ini tersedia untuk digunakan. Jika Anda menemukan kerusakan, mohon untuk melaporkannya melalui fitur laporan kerusakan.
                        @else
                            Inventaris ini sedang tidak tersedia. Harap hubungi petugas laboratorium untuk informasi lebih lanjut.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection