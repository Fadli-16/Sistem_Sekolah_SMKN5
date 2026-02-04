@extends('sistem_akademik.layouts.main')

@section('css')
@include('sistem_akademik.layouts.css')
@endsection

@section('content')
<div class="container-fluid">

    <body>
        <!-- Welcome Section -->
        <div class="dashboard-welcome fade-in-up">
            <div class="row">
                <div class="col-md-8">
                    <h1>Selamat Datang di Sistem Akademik</h1>
                    <p class="user-info">{{ Auth::user()->nama }} &bullet; {{ ucfirst(Auth::user()->role) }}</p>
                    <p class="current-date">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="col-md-4 d-none d-md-flex justify-content-end align-items-center">
                    <img src="{{ asset('assets/images/dashboard-illustration.png') }}" alt="Dashboard" class="img-fluid" style="max-height: 130px;">
                </div>
            </div>
        </div>

        <!-- ADMIN STATS -->
        @if(in_array(Auth::user()->role, ['super_admin','admin_sa']))
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.1s">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ App\Models\Siswa::count() }}</div>
                        <div class="stat-label">Total Siswa</div>
                    </div>
                    <a href="{{ route('sistem_akademik.siswa.index') }}" class="stretched-link"></a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.2s">
                    <div class="stat-icon"><i class="bi bi-person-workspace"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ App\Models\User::where('role','guru')->count() }}</div>
                        <div class="stat-label">Total Guru</div>
                    </div>
                    <a href="{{ route('sistem_akademik.guru.index') }}" class="stretched-link"></a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.3s">
                    <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ App\Models\Course::count() }}</div>
                        <div class="stat-label">Total Course</div>
                    </div>
                    <a href="{{ route('sistem_akademik.course.index') }}" class="stretched-link"></a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.4s">
                    <div class="stat-icon"><i class="bi bi-building"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ App\Models\Kelas::count() }}</div>
                        <div class="stat-label">Total Kelas</div>
                    </div>
                    <a href="{{ route('sistem_akademik.kelas.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        @endif

        <!-- STUDENT STATS -->
        @if(Auth::user()->role == 'siswa' && Auth::user()->siswa)
        <div class="row mb-4">
            <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.1s">
                    <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ Auth::user()->siswa->courses()->count() }}</div>
                        <div class="stat-label">Course Saya</div>
                    </div>
                    <a href="{{ route('sistem_akademik.course.index') }}" class="stretched-link"></a>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.2s">
                    <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
                    <div class="stat-body">
                        @php
                        $count = \App\Models\Peminatan::where('user_id', Auth::id())->count();
                        $filled = $count > 0 ? 1 : 0;
                        @endphp
                        <div class="stat-value">{{ $filled }}/1</div>
                        <div class="stat-label">Status Peminatan</div>
                    </div>
                    <a href="{{ route('sistem_akademik.peminatan.index') }}" class="stretched-link"></a>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.3s">
                    <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ Auth::user()->siswa->courses()->where('hari', \Carbon\Carbon::now()->locale('id')->isoFormat('dddd'))->count() }}</div>
                        <div class="stat-label">Jadwal Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>
        @elseif(Auth::user()->role == 'siswa' && !Auth::user()->siswa)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Perhatian:</strong> Data siswa Anda belum lengkap. Silahkan lengkapi profil Anda terlebih dahulu.
        </div>
        @endif

        <!-- TEACHER STATS -->
        @if(Auth::user()->role == 'guru')
        <div class="row mb-4">
            <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.1s">
                    <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ App\Models\Course::where('guru_id', Auth::user()->id)->count() }}</div>
                        <div class="stat-label">Course Saya</div>
                    </div>
                    <a href="{{ route('sistem_akademik.course.index') }}" class="stretched-link"></a>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.2s">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">
                            {{ App\Models\Siswa::whereHas('courses', function($query) {
                            $query->where('guru_id', Auth::user()->id);
                        })->count() }}
                        </div>
                        <div class="stat-label">Total Siswa</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div class="stat-card compact fade-in-up" style="animation-delay: 0.3s">
                    <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                    <div class="stat-body">
                        <div class="stat-value">{{ App\Models\Course::where('guru_id', Auth::user()->id)->where('hari', \Carbon\Carbon::now()->locale('id')->isoFormat('dddd'))->count() }}</div>
                        <div class="stat-label">Jadwal Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Announcements -->
        <div class="section-header">
            <h2 class="section-title"><i class="bi bi-newspaper"></i> Pengumuman Terbaru</h2>
            @if(Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa')
            <a href="{{ route('sistem_akademik.berita.index') }}" class="view-all">Lihat Semua <i class="bi bi-chevron-right"></i></a>
            @endif
        </div>

        <div class="container-fluid p-0">
            <!-- Header: Judul, Tanggal, Filter, Pencarian, Tombol Tambah -->
            <div class="d-flex justify-content-between align-items-center mb-3 dashboard-header">
                <div class="d-flex align-items-center">
                    <h2 class="mb-0 me-2">Info Terbaru |</h2>
                    <h5 class="text-muted mb-0">{{ now()->format('d M Y') }}</h5>
                </div>

                <div class="d-flex">
                    <form id="beritaFilterForm"
                        action="{{ route('sistem_akademik.dashboard') }}"
                        method="GET"
                        class="row g-2 align-items-end">

                        <!-- Filter Kategori -->
                        <div class="col-12 col-md-2">
                            <select id="kategoriFilter" name="filter" class="form-select">
                                <option value="">Semua</option>
                                <option value="terbaru" {{ request('filter')=='terbaru'?'selected':'' }}>Terbaru</option>
                                <option value="terlama" {{ request('filter')=='terlama'?'selected':'' }}>Terlama</option>
                                <option value="informasi" {{ request('filter')=='informasi'?'selected':'' }}>Informasi</option>
                                <option value="prestasi" {{ request('filter')=='prestasi'?'selected':'' }}>Prestasi</option>
                                <option value="pemberitahuan" {{ request('filter')=='pemberitahuan'?'selected':'' }}>Pemberitahuan</option>
                            </select>
                        </div>

                        <!-- Rentang Tanggal -->
                        <div class="col-12 col-md-7">
                            <div class="input-group">
                                <span class="input-group-text d-none d-sm-flex">Dari</span>
                                <input id="fromDate" type="date" name="from" class="form-control" value="{{ request('from') }}">

                                <span class="input-group-text d-none d-sm-flex">Ke</span>
                                <input id="toDate" type="date" name="to" class="form-control" value="{{ request('to') }}">
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="col-12 col-md-3">
                            <div class="input-group">
                                <input id="searchInput" type="text" name="search" class="form-control" placeholder="Cari berita..." value="{{ request('search') }}">
                                <button id="searchBtn" class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    @if (Auth::check() && Auth::user()->role === 'admin')
                    <a href="{{ route('sistem_akademik.berita.create') }}" class="btn btn-success" title="Tambah">
                        <i class="fas fa-plus"></i>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Jika tidak ada data -->
            <div class="container-fluid mt-1 mb-3 p-0 announcements-container">
                @if ($berita->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    Belum ada berita. Klik "Tambah" untuk menambahkan.
                </div>
                @else
                @foreach ($berita as $b)
                <div class="card mb-3 shadow-sm sleek-card">
                    <div class="card-body d-flex align-items-start">
                        {{-- Gambar --}}
                        @if ($b->foto)
                        <img src="{{ asset('assets/berita/' . $b->foto) }}" alt="Gambar Berita" class="dashboard-img me-3" style="width:140px; height:140px; object-fit:cover; border-radius:6px;">
                        @else
                        <div class="me-3" style="width:140px; height:140px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; border-radius:6px;">
                            <i class="fas fa-image text-muted icon-sm"></i>
                        </div>
                        @endif

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="news-title mb-0">{{ Str::limit($b->judul, 100) }}</h5>
                                <small class="news-meta">{{ optional($b->created_at)->format('d M Y') }}</small>
                            </div>

                            <div class="sleek-content scrollable" style="max-height:110px; overflow:auto;">
                                {!! Str::limit(strip_tags($b->isi), 600, '...') !!}
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="d-flex gap-2">
                                    {{-- VIEW --}}
                                    <a href="{{ route('sistem_akademik.berita.show', $b->id) }}" class="btn btn-action btn-view" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                        <span class="d-none d-md-inline">Lihat</span>
                                    </a>

                                    {{-- DOWNLOAD (jika ada) --}}
                                    @if (!empty($b->file))
                                    <a href="{{ asset('file/' . $b->file) }}" class="btn btn-action btn-download" title="Unduh Lampiran" target="_blank" download>
                                        <i class="bi bi-download icon-sm"></i>
                                        <span class="d-none d-md-inline">Unduh</span>
                                    </a>
                                    @endif
                                </div>

                                {{-- Untuk admin: pindahkan kelola ke halaman index, dashboard hanya menampilkan ringkasan --}}
                                <div class="text-end">
                                    @if (Auth::check() && in_array(Auth::user()->role, ['super_admin','admin_sa','admin']))
                                    <a href="{{ route('sistem_akademik.berita.edit', $b->id) }}" class="text-muted small">Kelola &raquo;</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {!! $berita->appends(request()->query())->links() !!}
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section-header">
            <h2 class="section-title"><i class="bi bi-lightning-charge"></i> Aksi Cepat</h2>
        </div>

        <div class="quick-actions mb-4">
            @if(Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa')
            <a href="{{ route('sistem_akademik.course.create') }}" class="action-card fade-in-up" style="animation-delay: 0.1s">
                <div class="action-icon">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div class="action-title">Tambah Course</div>
            </a>
            <a href="{{ route('sistem_akademik.siswa.create') }}" class="action-card fade-in-up" style="animation-delay: 0.2s">
                <div class="action-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div class="action-title">Tambah Siswa</div>
            </a>
            <a href="{{ route('sistem_akademik.kelas.create') }}" class="action-card fade-in-up" style="animation-delay: 0.3s">
                <div class="action-icon">
                    <i class="bi bi-building-add"></i>
                </div>
                <div class="action-title">Tambah Kelas</div>
            </a>
            <a href="{{ route('sistem_akademik.berita.create') }}" class="action-card fade-in-up" style="animation-delay: 0.4s">
                <div class="action-icon">
                    <i class="bi bi-megaphone"></i>
                </div>
                <div class="action-title">Buat Pengumuman</div>
            </a>
            @elseif(Auth::user()->role == 'guru')
            <a href="{{ route('sistem_akademik.course.index') }}" class="action-card fade-in-up" style="animation-delay: 0.1s">
                <div class="action-icon">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div class="action-title">Lihat Course</div>
            </a>
            <a href="{{ route('sistem_akademik.profile') }}" class="action-card fade-in-up" style="animation-delay: 0.2s">
                <div class="action-icon">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="action-title">Profil Saya</div>
            </a>
            <a href="{{ route('sistem_akademik.mataPelajaran.index') }}" class="action-card fade-in-up" style="animation-delay: 0.3s">
                <div class="action-icon">
                    <i class="bi bi-book"></i>
                </div>
                <div class="action-title">Mata Pelajaran</div>
            </a>
            @elseif(Auth::user()->role == 'siswa')
            <a href="{{ route('sistem_akademik.course.index') }}" class="action-card fade-in-up" style="animation-delay: 0.1s">
                <div class="action-icon">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div class="action-title">Lihat Course</div>
            </a>
            <a href="{{ route('sistem_akademik.peminatan.index') }}" class="action-card fade-in-up" style="animation-delay: 0.2s">
                <div class="action-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="action-title">Lihat Peminatan</div>
            </a>
            <a href="{{ route('sistem_akademik.profile') }}" class="action-card fade-in-up" style="animation-delay: 0.3s">
                <div class="action-icon">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="action-title">Profil Saya</div>
            </a>
            @endif
        </div>
        @endsection

        @section('script')
        <script>
            (function() {
                const form = document.getElementById('beritaFilterForm');
                const kategori = document.getElementById('kategoriFilter');
                const fromInput = document.getElementById('fromDate');
                const toInput = document.getElementById('toDate');

                function saveScroll() {
                    sessionStorage.setItem('scrollPos', window.scrollY);
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const pos = sessionStorage.getItem('scrollPos');
                    if (pos) {
                        window.scrollTo(0, pos);
                        sessionStorage.removeItem('scrollPos');
                    }
                });

                if (kategori) {
                    kategori.addEventListener('change', () => {
                        saveScroll();
                        form.submit();
                    });
                }

                function submitDate() {
                    if (fromInput.value && toInput.value) {
                        if (new Date(fromInput.value) > new Date(toInput.value)) {
                            alert('Tanggal tidak valid');
                            return;
                        }
                        saveScroll();
                        form.submit();
                    }
                }

                if (fromInput) fromInput.addEventListener('change', submitDate);
                if (toInput) toInput.addEventListener('change', submitDate);

                document.addEventListener('click', (e) => {
                    if (e.target.closest('.pagination a')) {
                        saveScroll();
                    }
                });
            })();
        </script>

        <script>
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (!navbar) return;

                if (window.scrollY > 50) {
                    navbar.style.padding = '0.3rem 1rem';
                    navbar.style.boxShadow = '0 4px 10px rgba(0,0,0,.1)';
                } else {
                    navbar.style.padding = '0.5rem 1rem';
                    navbar.style.boxShadow = '0 4px 6px rgba(0,0,0,.1)';
                }
            });

            function logout() {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: 'Logout dari akun',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Logout!'
                }).then(result => {
                    if (result.isConfirmed) {
                        document.getElementById('logoutForm').submit();
                    }
                });
            }

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: 'Data tidak dapat dikembalikan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!'
                }).then(result => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm' + id).submit();
                    }
                });
            }
        </script>
        @endsection