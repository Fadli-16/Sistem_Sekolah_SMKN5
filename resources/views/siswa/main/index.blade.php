@extends('siswa.layouts.main')

@section('css')
<style>
    .stat-card {
        background-color: #fff;
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: var(--shadow);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        font-size: 2rem;
        color: var(--secondary);
        margin-bottom: 1rem;
    }
    
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--primary);
    }
    
    .stat-label {
        color: var(--text-muted);
        margin-bottom: 0;
    }
    
    .announcement-item {
        padding: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .announcement-item:last-child {
        border-bottom: none;
    }
    
    .announcement-icon {
        width: 40px;
        height: 40px;
        background-color: var(--secondary-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary);
    }
    
    .announcement-date {
        font-size: 0.75rem;
        color: var(--text-muted);
        display: block;
        margin-bottom: 0.25rem;
    }
    
    .announcement-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .announcement-content {
        margin: 0.25rem 0 0;
        font-size: 0.9rem;
    }
    
    .card {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        height: 100%;
    }
    
    .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .section-title {
        margin: 0;
        color: var(--primary);
        font-weight: 600;
    }
    
    .section-title i {
        color: var(--secondary);
        margin-right: 0.5rem;
    }
    
    .view-all {
        font-size: 0.9rem;
        color: var(--secondary);
        text-decoration: none;
    }
    
    .view-all:hover {
        text-decoration: underline;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
    }
    
    .badge-success {
        background-color: #4ecdc4;
    }
    
    .badge-warning {
        background-color: #FFE15D;
    }
    
    .badge-secondary {
        background-color: #8789C0;
    }
    
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">Dashboard</h1>
            <p class="text-muted">Selamat datang di Sistem Laboratorium SMK Negeri 5 Padang</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-value">3</div>
                <p class="stat-label">Laboratorium</p>
            </div>
        </div>
        
        <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-calendar2-week"></i>
                </div>
                <div class="stat-value">{{ App\Models\Laboratorium::whereDate('start', now())->count() }}</div>
                <p class="stat-label">Jadwal Hari Ini</p>
            </div>
        </div>
        
        <div class="col-md-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="stat-value">{{ App\Models\Inventaris::count() }}</div>
                <p class="stat-label">Total Inventaris</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-header">
                        <h5 class="section-title mb-0"><i class="bi bi-megaphone"></i> Pengumuman</h5>
                        <a href="#" class="view-all">Lihat Semua <i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="announcement-item d-flex align-items-center">
                        <div class="announcement-icon me-3">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <span class="announcement-date">Hari ini, 09:30</span>
                            <h6 class="announcement-title">Jadwal Praktikum Diperbaharui</h6>
                            <p class="announcement-content">Kelas XI RPL 2 jadwal praktikum pemrograman web dipindahkan ke hari Jumat.</p>
                        </div>
                    </div>
                    
                    <div class="announcement-item d-flex align-items-center">
                        <div class="announcement-icon me-3">
                            <i class="bi bi-tools"></i>
                        </div>
                        <div>
                            <span class="announcement-date">Kemarin, 14:30</span>
                            <h6 class="announcement-title">Penambahan Inventaris Baru</h6>
                            <p class="announcement-content">5 unit komputer baru telah ditambahkan ke Laboratorium RPL.</p>
                        </div>
                    </div>
                    
                    <div class="announcement-item d-flex align-items-center">
                        <div class="announcement-icon me-3">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <span class="announcement-date">3 Mei 2025, 10:15</span>
                            <h6 class="announcement-title">Pemeliharaan Lab TKJ</h6>
                            <p class="announcement-content">Lab TKJ akan ditutup pada hari Sabtu untuk pemeliharaan rutin.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="section-header">
                        <h5 class="section-title mb-0"><i class="bi bi-calendar2-week"></i> Jadwal Hari Ini</h5>
                        <a href="{{ route('siswa.jadwal.index') }}" class="view-all">Kalender <i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <p class="mb-1"><strong>Lab RPL</strong></p>
                            <span class="badge bg-success">08:00 - 10:30</span>
                        </div>
                        <p class="mb-0 text-muted">Pemrograman Web - Kelas XI RPL 2</p>
                    </div>
                    
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <p class="mb-1"><strong>Lab MM</strong></p>
                            <span class="badge bg-secondary">10:30 - 12:00</span>
                        </div>
                        <p class="mb-0 text-muted">Desain Grafis - Kelas X MM 1</p>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between">
                            <p class="mb-1"><strong>Lab TKJ</strong></p>
                            <span class="badge bg-warning text-dark">13:00 - 15:30</span>
                        </div>
                        <p class="mb-0 text-muted">Jaringan Komputer - Kelas XII TKJ 1</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection