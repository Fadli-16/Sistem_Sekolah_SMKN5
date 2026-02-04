@extends('magang.layouts.main')

@section('css')
<style>
    .opening-card {
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        background-color: var(--bg-light);
        border: none;
        transition: all 0.3s ease;
    }

    .opening-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .opening-header {
        background-color: var(--primary);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .opening-body {
        padding: 1.5rem;
    }

    .opening-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: var(--text-dark);
    }

    .opening-description {
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }

    .opening-meta {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .opening-meta-item {
        display: flex;
        align-items: center;
        color: var(--text-dark);
        font-size: 0.9rem;
    }

    .opening-meta-item i {
        color: var(--secondary);
        margin-right: 0.5rem;
        font-size: 1rem;
    }

    .opening-skills {
        margin-bottom: 1.5rem;
    }

    .opening-skills h5 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: var(--text-dark);
    }

    .skill-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        background-color: rgba(78, 205, 196, 0.15);
        color: var(--secondary-dark);
        border-radius: 50px;
        font-size: 0.8rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .status-active {
        background-color: rgba(78, 205, 196, 0.15);
        color: var(--secondary-dark);
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .status-inactive {
        background-color: rgba(231, 76, 60, 0.15);
        color: #c0392b;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .status-badge i {
        margin-right: 0.35rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--bg-gray);
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Program Magang</h1>
        <a href="{{ route('magang.wakil_perusahaan.openings.create') }}" class="btn btn-secondary">
            <i class="bi bi-plus-circle me-2"></i> Tambah Program Baru
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-{{ session('status') }} alert-dismissible fade show" role="alert">
            <strong>{{ session('title') }}</strong> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(count($openings) > 0)
        @foreach($openings as $opening)
            <div class="opening-card">
                <div class="opening-header">
                    <span>Program Magang</span>
                    @if($opening->status == 'Aktif')
                        <span class="status-active">
                            <i class="bi bi-check-circle"></i> Aktif
                        </span>
                    @else
                        <span class="status-inactive">
                            <i class="bi bi-x-circle"></i> Tidak Aktif
                        </span>
                    @endif
                </div>
                <div class="opening-body">
                    <h3 class="opening-title">{{ $opening->posisi }}</h3>

                    <div class="opening-meta">
                        <div class="opening-meta-item">
                            <i class="bi bi-calendar-date"></i>
                            <span>{{ \Carbon\Carbon::parse($opening->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($opening->tanggal_selesai)->format('d M Y') }}</span>
                        </div>
                        <div class="opening-meta-item">
                            <i class="bi bi-people"></i>
                            <span>{{ $opening->jumlah_posisi }} posisi</span>
                        </div>
                        <div class="opening-meta-item">
                            <i class="bi bi-person-check"></i>
                               <span>{{ $opening->pelamar_count }} pelamar</span>
                        </div>
                    </div>

                    <div class="opening-description">
                        <p>{{ $opening->deskripsi }}</p>
                    </div>

                    @if($opening->keahlian)
                        <div class="opening-skills">
                            <h5>Keahlian yang Dibutuhkan:</h5>
                            @foreach(explode(',', $opening->keahlian) as $skill)
                                <span class="skill-badge">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('magang.wakil_perusahaan.openings.applicants', $opening->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-people me-2"></i> Lihat Pelamar
                        </a>

                        <div class="action-buttons">
                            <a href="{{ route('magang.wakil_perusahaan.openings.edit', $opening->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('magang.wakil_perusahaan.openings.destroy', $opening->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program magang ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-clipboard-plus"></i>
                    </div>
                    <h4>Belum ada program magang</h4>
                    <p class="text-muted">Buat program magang baru untuk menarik siswa magang ke perusahaan Anda.</p>
                    <a href="{{ route('magang.wakil_perusahaan.openings.create') }}" class="btn btn-secondary mt-3">
                        <i class="bi bi-plus-circle me-2"></i> Tambah Program Baru
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
