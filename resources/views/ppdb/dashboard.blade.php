@extends('ppdb.layouts.main')

@section('css')
<style>
    /* Banner Section */
    .hero-section {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: var(--text-light);
        padding: 5rem 0 6rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 3rem;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 100%;
        height: 10rem;
        background: linear-gradient(to top left, var(--primary) 49%, transparent 51%);
    }

    .hero-section::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%234ecdc4' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.6;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .hero-subtitle {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        line-height: 1.6;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .hero-buttons .btn {
        padding: 0.8rem 1.8rem;
        font-weight: 600;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .hero-buttons .btn-secondary {
        background-color: var(--secondary);
        border-color: var(--secondary);
        color: var(--primary);
    }

    .hero-buttons .btn-secondary:hover {
        background-color: var(--secondary-dark);
        border-color: var(--secondary-dark);
        transform: translateY(-3px);
    }

    .hero-buttons .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-3px);
    }

    /* Info Cards */
    .info-section {
        padding: 3rem 0;
    }

    .section-title {
        text-align: center;
        margin-bottom: 3rem;
    }

    .section-title h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
        position: relative;
        display: inline-block;
        margin-bottom: 1rem;
    }

    .section-title h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(to right, var(--secondary-dark), var(--secondary));
        border-radius: 2px;
    }

    .section-title p {
        color: var(--text-muted);
        max-width: 800px;
        margin: 0 auto;
    }

    .info-card {
        background-color: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        height: 100%;
        overflow: hidden;
        border: none;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .info-card .card-header {
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        color: white;
        font-weight: 600;
        padding: 1.25rem 1.5rem;
        border-bottom: 3px solid var(--secondary);
    }

    .info-card .card-body {
        padding: 1.5rem;
    }

    .info-card .card-title {
        color: var(--primary);
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.25rem;
    }

    .info-card .card-text {
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }

    .info-icon {
        width: 60px;
        height: 60px;
        background-color: var(--secondary-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        color: var(--secondary);
        font-size: 1.5rem;
    }

    .timeline-section {
        padding: 3rem 0;
        background-color: var(--bg-gray);
    }

    .timeline {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
    }

    .timeline::after {
        content: '';
        position: absolute;
        width: 3px;
        background-color: var(--secondary);
        top: 0;
        bottom: 0;
        left: 50%;
        margin-left: -1.5px;
        border-radius: 1.5px;
    }

    .timeline-item {
        padding: 10px 50px;
        position: relative;
        width: 50%;
        box-sizing: border-box;
    }

    .timeline-item:nth-child(odd) {
        left: 0;
    }

    .timeline-item:nth-child(even) {
        left: 50%;
    }

    .timeline-content {
        padding: 1.5rem;
        background-color: white;
        position: relative;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
    }

    .timeline-content:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .timeline-item:nth-child(odd) .timeline-content::after {
        content: '';
        position: absolute;
        top: 30px;
        right: -20px;
        border-width: 10px 0 10px 20px;
        border-style: solid;
        border-color: transparent transparent transparent white;
    }

    .timeline-item:nth-child(even) .timeline-content::after {
        content: '';
        position: absolute;
        top: 30px;
        left: -20px;
        border-width: 10px 20px 10px 0;
        border-style: solid;
        border-color: transparent white transparent transparent;
    }

    .timeline-dot {
        width: 30px;
        height: 30px;
        background-color: var(--secondary);
        border-radius: 50%;
        position: absolute;
        top: 25px;
        z-index: 2;
        box-shadow: 0 0 0 4px var(--secondary-light);
    }

    .timeline-item:nth-child(odd) .timeline-dot {
        right: -65px;
    }

    .timeline-item:nth-child(even) .timeline-dot {
        left: -65px;
    }

    .timeline-date {
        color: var(--secondary-dark);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .timeline-title {
        color: var(--primary);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .timeline-text {
        color: var(--text-muted);
    }

    @media (max-width: 767px) {
        .hero-section {
            padding: 4rem 0;
        }

        .hero-title {
            font-size: 2rem;
        }

        .hero-buttons {
            flex-direction: column;
        }

        .hero-buttons .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .timeline::after {
            left: 31px;
        }

        .timeline-item {
            width: 100%;
            padding-left: 70px;
            padding-right: 25px;
        }

        .timeline-item:nth-child(even) {
            left: 0;
        }

        .timeline-dot {
            left: 15px !important;
        }

        .timeline-item:nth-child(odd) .timeline-content::after {
            left: -20px;
            border-width: 10px 20px 10px 0;
            border-color: transparent white transparent transparent;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Selamat Datang di Sistem PPDB SMK Negeri 5 Padang</h1>
            <p class="hero-subtitle">Platform digital untuk pendaftaran siswa baru dan daftar ulang dengan mudah, cepat, dan transparan. Dapatkan informasi terkini tentang PPDB tahun ajaran 2025/2026.</p>
            <div class="hero-buttons">
                @if(Auth::check() && Auth::user()->role == 'admin_ppdb')
                    <a href="{{ route('admin.ppdb.daftar-ulang.index') }}" class="btn btn-secondary">
                        <i class="bi bi-people me-2"></i> Kelola Daftar Ulang
                    </a>
                @else
                    <a href="{{ route('daftar-ulang.create') }}" class="btn btn-secondary">
                        <i class="bi bi-pencil-square me-2"></i> Daftar Ulang Sekarang
                    </a>
                @endif
                <a href="{{ route('ppdb.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-info-circle me-2"></i> Informasi PPDB
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Info Cards Section -->
<section class="info-section">
    <div class="container">
        <div class="section-title">
            <h2>Informasi Penting</h2>
            <p>Temukan informasi terkini seputar PPDB dan proses daftar ulang siswa SMK Negeri 5 Padang</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0">Persyaratan Pendaftaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-icon">
                            <i class="bi bi-file-earmark-check"></i>
                        </div>
                        <h5 class="card-title">Dokumen yang Dibutuhkan</h5>
                        <p class="card-text">Persiapkan dokumen-dokumen penting seperti ijazah, kartu keluarga, dan berkas pendukung lainnya untuk kelancaran proses pendaftaran.</p>
                        <a href="{{ route('ppdb.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0">Jadwal Penting</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h5 class="card-title">Timeline PPDB</h5>
                        <p class="card-text">Perhatikan tanggal-tanggal penting dalam proses PPDB, termasuk pendaftaran, verifikasi, pengumuman hasil, dan daftar ulang.</p>
                        <a href="{{ route('ppdb.index') }}#jadwal" class="btn btn-sm btn-outline-primary">
                            Lihat Jadwal <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0">Kompetensi Keahlian</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-icon">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h5 class="card-title">Jurusan yang Tersedia</h5>
                        <p class="card-text">Pelajari berbagai jurusan dan kompetensi keahlian yang tersedia di SMK Negeri 5 Padang untuk memilih yang sesuai dengan minat dan bakat.</p>
                        <a href="{{ route('ppdb.index') }}#jurusan" class="btn btn-sm btn-outline-primary">
                            Lihat Jurusan <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Timeline Section -->
<section class="timeline-section">
    <div class="container">
        <div class="section-title">
            <h2>Alur PPDB 2025/2026</h2>
            <p>Tahapan-tahapan dalam proses Penerimaan Peserta Didik Baru (PPDB) tahun ajaran 2025/2026</p>
        </div>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-date">23 Juni - 27 Juni 2025</div>
                    <h3 class="timeline-title">Pendaftaran Online</h3>
                    <p class="timeline-text">Pendaftaran calon siswa baru melalui website PPDB.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-date">23 - 28 Juni 2025</div>
                    <h3 class="timeline-title">Tes Minat dan Bakat</h3>
                    <p class="timeline-text">Tes minat dan bakat calon siswa</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-date">23 - 28 Juni 2025</div>
                    <h3 class="timeline-title">Verifikasi dan Validasi</h3>
                    <p class="timeline-text">Verifikasi dokumen dan berkas pendaftaran yang telah diunggah oleh calon siswa.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-date">29 Juni 2025</div>
                    <h3 class="timeline-title">Pengumuman</h3>
                    <p class="timeline-text">Pengumuman hasil kelulusan</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-date">30 Juni 2025</div>
                    <h3 class="timeline-title">Daftar Ulang</h3>
                    <p class="timeline-text">Proses Daftar Ulang Bagi yang dinyatakan lulus.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Counter Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="p-4 rounded-4 shadow-sm bg-white">
                    <h3 class="display-4 fw-bold text-primary">8</h3>
                    <p class="mb-0 text-muted">Kompetensi Keahlian</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="p-4 rounded-4 shadow-sm bg-white">
                    <h3 class="display-4 fw-bold text-primary">500+</h3>
                    <p class="mb-0 text-muted">Kuota Siswa Baru</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 shadow-sm bg-white">
                    <h3 class="display-4 fw-bold text-primary">95%</h3>
                    <p class="mb-0 text-muted">Tingkat Penyerapan Lulusan</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection