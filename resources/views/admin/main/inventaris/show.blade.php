@extends('admin.layouts.main')

@section('css')
<style>
    .detail-card {
        background-color: var(--bg-light);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 0;
        overflow: hidden;
    }
    
    .detail-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        position: relative;
    }
    
    .detail-image {
        width: 100%;
        height: 300px;
        object-fit: contain;
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem;
    }
    
    .detail-body {
        padding: 1.5rem;
    }
    
    .detail-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .detail-subtitle {
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
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
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }
    
    .detail-deskripsi {
        white-space: pre-line;
    }
    
    .no-image-placeholder {
        height: 300px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: var(--text-muted);
    }
    
    .no-image-placeholder i {
        font-size: 3rem;
        margin-bottom: 1rem;
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
        <a href="{{ route('admin.inventaris.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="detail-card">
                <div class="detail-header">
                    <h2 class="detail-title">{{ $inventaris->nama_inventaris }}</h2>
                    <p class="detail-subtitle">
                        <i class="bi bi-tag me-1"></i> {{ $inventaris->kategori }} &nbsp;|&nbsp; 
                        <i class="bi bi-geo-alt me-1"></i> {{ $inventaris->lokasi }}
                    </p>
                </div>
                
                @if($inventaris->gambar)
                    <img src="{{ asset('storage/' . $inventaris->gambar) }}" alt="{{ $inventaris->nama_inventaris }}" class="detail-image">
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
                            <div class="detail-item-value">{{ $inventaris->jumlah }} unit</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-item-label">Kondisi</div>
                            <div class="detail-item-value">
                                <span class="badge-kondisi kondisi-{{ str_replace(' ', '-', $inventaris->kondisi) }}">
                                    {{ $inventaris->kondisi }}
                                </span>
                            </div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-item-label">Status</div>
                            <div class="detail-item-value">
                                <span class="status-badge status-{{ explode(' ', $inventaris->status)[0] }}">
                                    {{ $inventaris->status }}
                                </span>
                            </div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-item-label">Tanggal Pengadaan</div>
                            <div class="detail-item-value">{{ \Carbon\Carbon::parse($inventaris->tanggal_pengadaan)->format('d F Y') }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-item-label">Deskripsi</div>
                            <div class="detail-item-value detail-deskripsi">{{ $inventaris->deskripsi ?: 'Tidak ada deskripsi' }}</div>
                        </li>
                    </ul>
                    
                    <div class="action-buttons">
                        <a href="{{ route('admin.inventaris.edit', $inventaris->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        <button class="btn btn-danger" onclick="confirmDelete('{{ $inventaris->id }}')">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                        <form id="deleteForm{{ $inventaris->id }}" action="{{ route('admin.inventaris.destroy', $inventaris->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
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
                            <td width="40%"><strong>ID Inventaris</strong></td>
                            <td>{{ $inventaris->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kategori</strong></td>
                            <td>{{ $inventaris->kategori }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah</strong></td>
                            <td>{{ $inventaris->jumlah }} unit</td>
                        </tr>
                        <tr>
                            <td><strong>Lokasi</strong></td>
                            <td>{{ $inventaris->lokasi }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ditambahkan</strong></td>
                            <td>{{ \Carbon\Carbon::parse($inventaris->created_at)->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Terakhir Diubah</strong></td>
                            <td>{{ \Carbon\Carbon::parse($inventaris->updated_at)->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-people me-2"></i> Riwayat Peminjaman</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-3">Tidak ada riwayat peminjaman</p>
                    </div>
                    <!-- Bisa ditambahkan data riwayat peminjaman jika tersedia -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Inventaris yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4ecdc4",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }
</script>
@endsection