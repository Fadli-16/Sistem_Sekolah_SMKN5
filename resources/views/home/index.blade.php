<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Meta tags tambahan untuk SEO -->
    <meta name="description" content="SMK Negeri 5 Padang - Lembaga pendidikan kejuruan unggulan yang mempersiapkan siswa untuk menjadi tenaga kerja profesional dan kompetitif di bidangnya.">
    <meta name="keywords" content="SMK Negeri 5 Padang, SMK, Padang, Pendidikan, Kejuruan, Teknik, Sekolah">
    <meta name="author" content="SMK Negeri 5 Padang">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title }} - SMK Padang">
    <meta property="og:description" content="SMK Negeri 5 Padang - Lembaga pendidikan kejuruan unggulan.">
    <meta property="og:image" content="{{ asset('assets/images/logo.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title }} - SMK Padang">
    <meta property="twitter:description" content="SMK Negeri 5 Padang - Lembaga pendidikan kejuruan unggulan.">
    <meta property="twitter:image" content="{{ asset('assets/images/logo.png') }}">

    <title>{{ $title }} - SMK Negeri 5 Padang</title>
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- AOS CSS for scroll animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <!-- Modern Navbar - Include from separate file -->
    @include('home.sections.navbar')

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content" data-aos="fade-right" data-aos-delay="100">
                <h1 class="hero-title">Selamat Datang di SMK Negeri 5 Padang</h1>
                <p class="hero-subtitle">Platform terpadu untuk mengelola kegiatan akademik, perpustakaan, laboratorium, dan program magang. Temukan informasi lengkap tentang sekolah dan tingkatkan pengalaman belajar Anda bersama kami.</p>
                <div class="hero-buttons">
                    <a href="{{ route('daftar-ulang.create') }}" class="btn btn-secondary">
                        <i class="bi bi-pen me-2"></i> Daftar Ulang
                    </a>
                    <a href="{{ route('magang.wakil_perusahaan.register') }}" class="btn btn-outline-light">
                        <i class="bi bi-building me-2"></i> Daftar Mitra Magang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Berita & Informasi Section -->
    <section id="berita-section" class="py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2>Berita & Informasi Terkini</h2>
                <p>Ikuti perkembangan terbaru dan prestasi dari SMK Negeri 5 Padang.</p>
            </div>

            <div id="berita-content-wrapper">
                <!-- Filter & Search -->
                <div class="row mb-5" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-12">
                        <form action="{{ route('dashboard') }}" method="GET" class="berita-filter-form d-flex flex-column flex-md-row gap-3 bg-white p-3 rounded-4 shadow-sm border border-light-subtle">
                            <div class="flex-grow-1 position-relative">
                                <i class="bi bi-search position-absolute top-50 translate-middle-y ms-4 text-muted"></i>
                                <input type="text" name="search" class="form-control border-0 bg-light ps-5 rounded-pill" placeholder="Cari berita atau informasi terbaru..." value="{{ request('search') }}">
                            </div>
                            <div class="d-flex gap-2">
                                <select name="kategori" class="form-select border-0 bg-light rounded-pill px-4" style="min-width: 200px;">
                                    <option value="">Semua Kategori</option>
                                    <option value="informasi" {{ request('kategori') == 'informasi' ? 'selected' : '' }}>Informasi</option>
                                    <option value="prestasi" {{ request('kategori') == 'prestasi' ? 'selected' : '' }}>Prestasi</option>
                                </select>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="bi bi-search d-md-none"></i><span class="d-none d-md-inline">Cari</span></button>
                                @if(request('search') || request('kategori'))
                                    <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4 shadow-sm border"><i class="bi bi-x-circle d-md-none"></i><span class="d-none d-md-inline">Reset</span></a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

            <!-- Berita List -->
            <div class="row g-4">
                @forelse($berita as $item)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden transition-hover">
                            @if($item->foto)
                                <img src="{{ asset('assets/berita/' . $item->foto) }}" class="card-img-top" alt="{{ $item->judul }}" style="height: 220px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 220px;">
                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            <div class="card-body p-4 flex-grow-1 d-flex flex-column">
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <span class="badge bg-{{ $item->kategori == 'prestasi' ? 'warning' : 'primary' }} rounded-pill px-3 py-2">
                                        {{ ucfirst($item->kategori) }}
                                    </span>
                                    <small class="text-muted"><i class="bi bi-calendar3"></i> {{ $item->created_at->format('d M Y') }}</small>
                                </div>
                                <h5 class="card-title fw-bold mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $item->judul }}
                                </h5>
                                <p class="card-text text-muted mb-4 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ Str::limit(strip_tags($item->isi), 120) }}
                                </p>
                                <button type="button" class="btn btn-outline-primary w-100 rounded-pill mt-auto" data-bs-toggle="modal" data-bs-target="#beritaModal{{ $item->id }}">
                                    Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Full Content -->
                    <div class="modal fade" id="beritaModal{{ $item->id }}" tabindex="-1" aria-labelledby="beritaModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                            <div class="modal-content border-0 rounded-4 shadow">
                                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4 pt-2">
                                    <div class="text-center mb-4">
                                        <span class="badge bg-{{ $item->kategori == 'prestasi' ? 'warning' : 'primary' }} rounded-pill px-3 py-2 mb-3">
                                            {{ ucfirst($item->kategori) }}
                                        </span>
                                        <h3 class="fw-bold" id="beritaModalLabel{{ $item->id }}">{{ $item->judul }}</h3>
                                        <div class="text-muted small mt-2">
                                            <span><i class="bi bi-calendar3"></i> {{ $item->created_at->format('d F Y') }}</span>
                                            <span class="ms-3"><i class="bi bi-person"></i> {{ $item->user->name ?? 'Admin' }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($item->foto)
                                        <img src="{{ asset('assets/berita/' . $item->foto) }}" class="img-fluid rounded-4 mb-4 w-100" alt="{{ $item->judul }}" style="max-height: 400px; object-fit: cover;">
                                    @endif
                                    
                                    <div class="berita-content" style="line-height: 1.8;">
                                        {!! $item->isi !!}
                                    </div>

                                    @if($item->file)
                                        <div class="mt-4 p-3 bg-light rounded border border-light-subtle">
                                            <h6 class="fw-bold mb-2"><i class="bi bi-paperclip"></i> Lampiran File:</h6>
                                            <a href="{{ asset('file/' . $item->file) }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="bi bi-download"></i> Unduh File
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer border-top-0 px-4 pb-4 d-flex justify-content-between align-items-center">
                                    <div class="share-buttons">
                                        <span class="text-muted small me-2">Bagikan:</span>
                                        <a href="https://api.whatsapp.com/send?text={{ urlencode($item->judul . ' - ' . route('dashboard') . '?berita_id=' . $item->id) }}" target="_blank" class="btn btn-sm btn-success rounded-circle" title="Bagikan ke WhatsApp"><i class="bi bi-whatsapp"></i></a>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('dashboard') . '?berita_id=' . $item->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-circle" title="Bagikan ke Facebook"><i class="bi bi-facebook"></i></a>
                                        <button type="button" class="btn btn-sm rounded-circle text-white" style="background: #E1306C;" title="Bagikan ke Instagram" onclick="navigator.clipboard.writeText('{{ route('dashboard') }}?berita_id={{ $item->id }}'); alert('Tautan disalin! Silakan buka aplikasi Instagram untuk membagikan tautan ini.');"><i class="bi bi-instagram"></i></button>
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle" title="Salin Tautan" onclick="navigator.clipboard.writeText('{{ route('dashboard') }}?berita_id={{ $item->id }}'); const icon = this.innerHTML; this.innerHTML = '<i class=\'bi bi-check2 text-success\'></i>'; setTimeout(() => this.innerHTML = icon, 2000);"><i class="bi bi-link-45deg"></i></button>
                                    </div>
                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="bg-white p-5 rounded-4 shadow-sm border border-light-subtle d-inline-block">
                            <i class="bi bi-newspaper text-muted mb-3" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mb-0">Belum ada berita atau informasi saat ini.</h5>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($berita->hasPages())
                <div class="d-flex justify-content-center mt-5 custom-pagination" data-aos="fade-up">
                    {{ $berita->links('pagination::bootstrap-4') }}
                </div>
            @endif
            </div>
        </div>
    </section>

    @if(isset($sharedBerita))
    <!-- Shared Modal for Full Content -->
    <div class="modal fade" id="sharedBeritaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <div class="text-center mb-4">
                        <span class="badge bg-{{ $sharedBerita->kategori == 'prestasi' ? 'warning' : 'primary' }} rounded-pill px-3 py-2 mb-3">
                            {{ ucfirst($sharedBerita->kategori) }}
                        </span>
                        <h3 class="fw-bold">{{ $sharedBerita->judul }}</h3>
                        <div class="text-muted small mt-2">
                            <span><i class="bi bi-calendar3"></i> {{ $sharedBerita->created_at->format('d F Y') }}</span>
                            <span class="ms-3"><i class="bi bi-person"></i> {{ $sharedBerita->user->name ?? 'Admin' }}</span>
                        </div>
                    </div>
                    
                    @if($sharedBerita->foto)
                        <img src="{{ asset('assets/berita/' . $sharedBerita->foto) }}" class="img-fluid rounded-4 mb-4 w-100" alt="{{ $sharedBerita->judul }}" style="max-height: 400px; object-fit: cover;">
                    @endif
                    
                    <div class="berita-content" style="line-height: 1.8;">
                        {!! $sharedBerita->isi !!}
                    </div>

                    @if($sharedBerita->file)
                        <div class="mt-4 p-3 bg-light rounded border border-light-subtle">
                            <h6 class="fw-bold mb-2"><i class="bi bi-paperclip"></i> Lampiran File:</h6>
                            <a href="{{ asset('file/' . $sharedBerita->file) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="bi bi-download"></i> Unduh File
                            </a>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 d-flex justify-content-between align-items-center">
                    <div class="share-buttons">
                        <span class="text-muted small me-2">Bagikan:</span>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($sharedBerita->judul . ' - ' . route('dashboard') . '?berita_id=' . $sharedBerita->id) }}" target="_blank" class="btn btn-sm btn-success rounded-circle" title="Bagikan ke WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('dashboard') . '?berita_id=' . $sharedBerita->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-circle" title="Bagikan ke Facebook"><i class="bi bi-facebook"></i></a>
                        <button type="button" class="btn btn-sm rounded-circle text-white" style="background: #E1306C;" title="Bagikan ke Instagram" onclick="navigator.clipboard.writeText('{{ route('dashboard') }}?berita_id={{ $sharedBerita->id }}'); alert('Tautan disalin! Silakan buka aplikasi Instagram untuk membagikan tautan ini.');"><i class="bi bi-instagram"></i></button>
                        <button type="button" class="btn btn-sm btn-light border rounded-circle" title="Salin Tautan" onclick="navigator.clipboard.writeText('{{ route('dashboard') }}?berita_id={{ $sharedBerita->id }}'); const icon = this.innerHTML; this.innerHTML = '<i class=\'bi bi-check2 text-success\'></i>'; setTimeout(() => this.innerHTML = icon, 2000);"><i class="bi bi-link-45deg"></i></button>
                    </div>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($sharedBerita))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sharedModal = new bootstrap.Modal(document.getElementById('sharedBeritaModal'));
            sharedModal.show();
            
            // Clean up the URL without reloading
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('berita_id');
                window.history.replaceState({}, '', url);
            }
        });
    </script>
    @endif

    <!-- About Section -->
    <section id="tentang" class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-delay="100">
                    <div class="about-img">
                        <img src="{{ asset('assets/images/about-img.jpg') }}" alt="SMK Padang" class="img-fluid" loading="lazy">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="about-content">
                        <h2>Tentang SMK 5 Padang</h2>
                        <p>SMK Padang adalah lembaga pendidikan kejuruan unggulan yang mempersiapkan siswa untuk menjadi tenaga kerja profesional dan kompetitif di bidangnya. Dengan fasilitas modern dan tenaga pengajar berkualitas, kami berkomitmen memberikan pendidikan yang berkualitas.</p>
                        <p>Kami menawarkan berbagai jurusan kejuruan yang relevan dengan kebutuhan industri saat ini, didukung dengan laboratorium lengkap dan program magang yang terintegrasi dengan dunia industri.</p>

                        <div class="about-features">
                            <div class="about-feature-item" data-aos="fade-up" data-aos-delay="300">
                                <div class="about-feature-icon">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="about-feature-text">Laboratorium Komputer, Jaringan, dan Multimedia Modern</div>
                            </div>
                            <div class="about-feature-item" data-aos="fade-up" data-aos-delay="400">
                                <div class="about-feature-icon">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="about-feature-text">Program Magang dengan Perusahaan Partner</div>
                            </div>
                            <div class="about-feature-item" data-aos="fade-up" data-aos-delay="500">
                                <div class="about-feature-icon">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="about-feature-text">Perpustakaan Digital dan Fisik Lengkap</div>
                            </div>
                            <div class="about-feature-item" data-aos="fade-up" data-aos-delay="600">
                                <div class="about-feature-icon">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="about-feature-text">Sistem Informasi Akademik Terintegrasi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visi & Misi Section -->
    <section id="visi-misi">
        @include('home.sections.vision-mission')
    </section>


    <!-- Struktur Organisasi Section -->
    <section id="struktur-organisasi">
        @include('home.sections.organization')
    </section>

    <!-- Profil Guru Section -->
    <section id="guru">
        @include('home.sections.teacher-profiles')
    </section>

    <!-- Features/Services Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Layanan Kami</h2>
                <p>Akses berbagai fitur lengkap untuk mendukung kegiatan akademik dan pengembangan siswa</p>
            </div>

            <div class="row g-4">
                <!-- PPDB Card -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card feature-card">
                        <div class="card-body d-flex flex-column">
                            <div class="feature-icon">
                                <i class="fa-solid fa-school"></i>
                            </div>
                            <h3>Informasi PPDB</h3>
                            <p>Akses informasi penerimaan siswa baru, syarat pendaftaran, dan jadwal penting secara lengkap.</p>
                            <a href="{{ route('ppdb.index') }}" class="btn-feature mt-auto">
                                Kunjungi <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sistem Akademik Card -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="card feature-card">
                        <div class="card-body d-flex flex-column">
                            <div class="feature-icon">
                                <i class="fa-solid fa-medal"></i>
                            </div>
                            <h3>Sistem Akademik</h3>
                            <p>Kelola nilai, jadwal mata pelajaran, dan informasi akademik lainnya dengan mudah dan efisien.</p>
                            <a href="{{ route('sistem_akademik.dashboard') }}" class="btn-feature mt-auto">
                                Kunjungi <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Perpustakaan Card -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="card feature-card">
                        <div class="card-body d-flex flex-column">
                            <div class="feature-icon">
                                <i class="fa-solid fa-book"></i>
                            </div>
                            <h3>Perpustakaan</h3>
                            <p>Akses koleksi buku digital, peminjaman buku, dan layanan perpustakaan lainnya dengan praktis.</p>
                            <a href="{{ route('perpustakaan.buku.index') }}" class="btn-feature mt-auto">
                                Kunjungi <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Laboratorium Card -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="card feature-card">
                        <div class="card-body d-flex flex-column">
                            <div class="feature-icon">
                                <i class="fa-solid fa-flask-vial"></i>
                            </div>
                            <h3>Laboratorium</h3>
                            <p>Kelola jadwal penggunaan laboratorium, inventaris, dan pelaporan kerusakan alat dengan sistem terintegrasi.</p>
                            <a href="{{ route('lab.dashboard') }}" class="btn-feature mt-auto">
                                Kunjungi <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Magang Card -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="card feature-card">
                        <div class="card-body d-flex flex-column">
                            <div class="feature-icon">
                                <i class="fa-solid fa-briefcase"></i>
                            </div>
                            <h3>Program Magang</h3>
                            <p>Dapatkan informasi tentang program magang, perusahaan partner, dan pendaftaran magang untuk siswa.</p>
                            <a href="{{ route('magang.dashboard') }}" class="btn-feature mt-auto">
                                Kunjungi <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Login Card -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="card feature-card">
                        <div class="card-body d-flex flex-column">
                            <div class="feature-icon">
                                <i class="fa-solid fa-user-lock"></i>
                            </div>
                            <h3>Area Pengguna</h3>
                            <p>Login untuk mengakses fitur lengkap dan personalisasi pengalaman Anda di sistem informasi SMK.</p>
                            <a href="{{ route('login') }}" class="btn-feature mt-auto">
                                Login <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sejarah Section -->
    <section id="sejarah">
        @include('home.sections.history')
    </section>

    <!-- Kompetensi Keahlian Section -->
    <section id="kompetensi-keahlian">
        @include('home.sections.majors')
    </section>

    <!-- Footer -->
    <footer id="kontak" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 footer-col">
                    <div class="footer-logo">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK">
                    </div>
                    <div class="footer-about">
                        <p>SMK 5 Padang adalah lembaga pendidikan kejuruan unggulan yang mempersiapkan siswa untuk menjadi tenaga kerja profesional dan kompetitif di bidangnya.</p>
                    </div>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 footer-col">
                    <h4 class="footer-heading">Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('dashboard') }}"><i class="bi bi-chevron-right"></i> Beranda</a></li>
                        <li><a href="{{ route('ppdb.index') }}"><i class="bi bi-chevron-right"></i> PPDB</a></li>
                        <li><a href="{{ route('sistem_akademik.dashboard') }}"><i class="bi bi-chevron-right"></i> Akademik</a></li>
                        <li><a href="{{ route('perpustakaan.buku.index') }}"><i class="bi bi-chevron-right"></i> Perpustakaan</a></li>
                        <li><a href="{{ route('lab.dashboard') }}"><i class="bi bi-chevron-right"></i> Laboratorium</a></li>
                        <li><a href="{{ route('magang.dashboard') }}"><i class="bi bi-chevron-right"></i> Magang</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 footer-col">
                    <h4 class="footer-heading">Layanan</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('lab.jadwal') }}"><i class="bi bi-chevron-right"></i> Jadwal Laboratorium</a></li>
                        <li><a href="{{ route('inv.index') }}"><i class="bi bi-chevron-right"></i> Inventaris Lab</a></li>
                        <li><a href="{{ route('inv.laporan') }}"><i class="bi bi-chevron-right"></i> Laporan Kerusakan</a></li>
                        <li><a href="{{ route('sistem_akademik.mata_pelajaran.index') }}"><i class="bi bi-chevron-right"></i> Mata Pelajaran</a></li>
                        <li><a href="{{ route('perpustakaan.buku.index') }}"><i class="bi bi-chevron-right"></i> Buku Perpustakaan</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 footer-col">
                    <h4 class="footer-heading">Kontak Kami</h4>
                    <ul class="footer-contact footer-links">
                        <li>
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>Jalan Beringin No. 4 RT. 02 RW. 02 Kelurahan Lolong Belanti,
                                Kecamatan Padang Utara, Padang, Sumatera Barat, Indonesia</span>
                        </li>
                        <li>
                            <i class="bi bi-envelope-fill"></i>
                            <span>info@smkpadang.sch.id</span>
                        </li>
                        <li>
                            <i class="bi bi-telephone-fill"></i>
                            <span>(0751) 7053201</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 SMK 5 Kota Padang</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous" defer></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js" defer></script>
    <script src="{{ asset('assets/js/custom.js') }}" defer></script>
    <!-- AOS Script for scroll animations -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 450,
                easing: 'ease-in-out',
                once: true,
                mirror: false,
                offset: 100
            });

            // Sweet Alert notifications
            @if(session('status'))
                Swal.fire({
                    title: '{{ session('title') }}',
                    text: '{{ session('message') }}',
                    icon: '{{ session('status') }}',
                    confirmButtonColor: '#004080'
                });
            @endif
        });

        // Sticky navbar effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.padding = '0.3rem 1rem';
                navbar.style.boxShadow = '0 4px 10px rgba(0,0,0,0.1)';
            } else {
                navbar.style.padding = '0.5rem 1rem';
                navbar.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            }
        });

        // Logout confirmation
        function logout(e) {
            Swal.fire({
                title: "Apakah anda yakin?",
                text: "Logout dari akun",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#004080',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // Delete confirmation
        function confirmDelete(e) {
            Swal.fire({
                title: "Apakah anda yakin?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#004080',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + e).submit();
                }
            });
        }
        // AJAX for Berita Section
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('berita-content-wrapper');
            if (!wrapper) return;

            // Handle Pagination and Reset Button Clicks
            wrapper.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination a') || e.target.closest('a.btn-light');
                if (link) {
                    e.preventDefault();
                    fetchBerita(link.href);
                }
            });

            // Handle Form Submission (Search/Filter)
            wrapper.addEventListener('submit', function(e) {
                if (e.target.classList.contains('berita-filter-form')) {
                    e.preventDefault();
                    const url = e.target.action + '?' + new URLSearchParams(new FormData(e.target)).toString();
                    fetchBerita(url);
                }
            });

            // Handle Dropdown Change
            wrapper.addEventListener('change', function(e) {
                if (e.target.name === 'kategori') {
                    e.preventDefault();
                    const form = e.target.closest('form');
                    const url = form.action + '?' + new URLSearchParams(new FormData(form)).toString();
                    fetchBerita(url);
                }
            });

            function fetchBerita(url) {
                wrapper.style.opacity = '0.5';
                wrapper.style.pointerEvents = 'none';
                
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('berita-content-wrapper');
                    
                    if (newContent) {
                        wrapper.innerHTML = newContent.innerHTML;
                        
                        // Re-initialize AOS if used on the dynamically added elements
                        if (typeof AOS !== 'undefined') {
                            // Strip data-aos so it doesn't stay invisible if it hasn't scrolled
                            wrapper.querySelectorAll('[data-aos]').forEach(el => {
                                el.removeAttribute('data-aos');
                                el.style.opacity = '1';
                                el.style.transform = 'none';
                            });
                        }
                        
                        // Update URL silently
                        window.history.pushState({}, '', url);
                    }
                })
                .catch(err => console.error('Error fetching berita:', err))
                .finally(() => {
                    wrapper.style.opacity = '1';
                    wrapper.style.pointerEvents = 'auto';
                });
            }
        });
    </script>
</body>
</html>
