@extends('admin.layouts.main')

@section('css')
<style>
    .dashboard-header {
        position: relative;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
        background-color: var(--bg-light);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
    }
    
    .dashboard-welcome {
        position: relative;
        padding: 2rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        color: var(--text-light);
    }
    
    .dashboard-welcome::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        transform: translate(50%, -50%);
    }
    
    .dashboard-welcome::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        transform: translate(-30%, 30%);
    }
    
    .stat-card {
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 1.5rem;
        background-color: var(--bg-light);
        height: 100%;
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-icon {
        font-size: 1.8rem;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        background-color: rgba(78, 205, 196, 0.15);
        color: var(--secondary);
    }
    
    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
    }
    
    .stat-label {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .action-card {
        text-align: center;
        padding: 1.25rem 1rem;
        border-radius: var(--radius-lg);
        background-color: var(--bg-light);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        height: 100%;
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        background-color: var(--secondary-light);
    }
    
    .action-card:hover .action-icon {
        background-color: var(--secondary);
        color: white;
    }
    
    .action-icon {
        font-size: 1.5rem;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        background-color: var(--bg-gray);
        color: var(--primary);
        transition: all 0.3s ease;
    }
    
    .action-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: var(--text-dark);
    }
    
    .announcement-card {
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        border: none;
    }
    
    .announcement-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .announcement-item {
        padding: 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: background-color 0.2s ease;
    }
    
    .announcement-item:last-child {
        border-bottom: none;
    }
    
    .announcement-item:hover {
        background-color: var(--bg-gray);
    }
    
    .announcement-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(78, 205, 196, 0.15);
        color: var(--secondary);
        flex-shrink: 0;
    }
    
    .announcement-date {
        display: inline-block;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }
    
    .announcement-title {
        font-weight: 600;
        margin-bottom: 0.35rem;
        color: var(--primary);
    }
    
    .announcement-content {
        font-size: 0.95rem;
        color: var(--text-muted);
        margin-bottom: 0;
        line-height: 1.5;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 0.75rem;
        color: var(--secondary);
    }
    
    .view-all {
        font-size: 0.9rem;
        color: var(--secondary);
        display: flex;
        align-items: center;
        transition: transform 0.2s ease;
    }
    
    .view-all:hover {
        transform: translateX(3px);
    }
    
    .view-all i {
        margin-left: 0.35rem;
        font-size: 0.8rem;
    }
    
    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
    }
    
    .empty-icon {
        font-size: 3rem;
        color: var(--bg-gray);
        margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
        .dashboard-welcome {
            padding: 1.5rem;
        }
        
        .quick-actions {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 576px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .view-all {
            margin-top: 0.5rem;
        }
    }
    
    /* Animation classes */
    .fade-in-up {
        animation: fadeInUp 0.5s ease forwards;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .dashboard-welcome h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }
    
    .dashboard-welcome p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
    }
    
    .dashboard-welcome .user-info {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .dashboard-welcome .current-date {
        font-size: 0.95rem;
        opacity: 0.8;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="dashboard-welcome fade-in-up">
        <div class="row">
            <div class="col-md-8">
                <h1>Dashboard Sistem Laboratorium</h1>
                <p class="user-info">{{ Auth::user()->nama }} &bullet; {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>
                <p class="current-date">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                <p>Kelola inventaris, jadwal, dan laporan kerusakan laboratorium SMK dengan mudah dan efisien.</p>
            </div>
            <div class="col-md-4 d-none d-md-flex justify-content-end align-items-center">
                <img src="{{ asset('assets/images/dashboard-illustration.png') }}" alt="Dashboard" class="img-fluid" style="max-height: 130px;">
            </div>
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stat-card fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-icon">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="stat-value">{{ App\Models\Inventaris::count() ?? 0 }}</div>
                <p class="stat-label">Total Inventaris</p>
            </div>
        </div>
    
    <!-- Quick Actions -->
    <div class="section-header">
        <h5 class="section-title"><i class="bi bi-lightning-charge"></i> Aksi Cepat</h5>
    </div>
    
    <div class="quick-actions">
        <a href="{{ route('admin.kelola.inv') }}" class="action-card fade-in-up" style="animation-delay: 0.1s">
            <div class="action-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <p class="action-title">Kelola Inventaris</p>
        </a>
        
        <a href="{{ route('admin.jadwal.index') }}" class="action-card fade-in-up" style="animation-delay: 0.3s">
            <div class="action-icon">
                <i class="bi bi-building"></i>
            </div>
            <p class="action-title">Kelola Lab</p>
        </a>
        
        <a href="{{ route('admin.kelola.laporan') }}" class="action-card fade-in-up" style="animation-delay: 0.4s">
            <div class="action-icon">
                <i class="bi bi-journal-text"></i>
            </div>
            <p class="action-title">Laporan</p>
        </a>
    </div>
    
   <div class="row mt-4">
    <!-- Statistik Inventaris -->
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i> Statistik Inventaris</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">Persentase Peminjaman Bulan Ini</p>
                <div class="progress mb-4" style="height: 20px;">
                    <div class="progress-bar bg-info" style="width: 75%;">75%</div>
                </div>
                <h6 class="mb-3">Top 3 Inventaris Dipinjam</h6>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Laptop RPL
                        <span class="badge bg-primary rounded-pill">24x</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Proyektor MM
                        <span class="badge bg-secondary rounded-pill">17x</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Kamera DSLR
                        <span class="badge bg-success rounded-pill">10x</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tugas & Pengingat -->
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i> Tugas & Pengingat</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cek kondisi Lab TKJ
                        <span class="badge bg-warning text-dark">Hari Ini</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Verifikasi pengembalian barang
                        <span class="badge bg-danger">Besok</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Jadwal maintenance rutin
                        <span class="badge bg-secondary">Minggu Ini</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add staggered animation for elements with animation-delay
        const fadeElements = document.querySelectorAll('.fade-in-up');
        fadeElements.forEach(element => {
            element.style.opacity = '0';
            setTimeout(() => {
                element.style.opacity = '1';
            }, 100);
        });
        
        // Check for any error counts in stat cards and handle potential undefined values
        document.querySelectorAll('.stat-value').forEach(element => {
            if (element.textContent.trim() === 'null') {
                element.textContent = '0';
            }
        });
    });
</script>
@endsection