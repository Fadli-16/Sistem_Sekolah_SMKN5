@extends('sistem_akademik.layouts.main')

@section('css')
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@include('sistem_akademik.layouts.css')
@endsection

@section('content')
<div class="container-fluid">
        <!-- Welcome Section -->
        <div class="dashboard-welcome fade-in-up mb-4" style="background: linear-gradient(135deg, #1e3a5f 0%, #2a5298 100%); border-radius: 16px; padding: 2rem; color: white; position: relative; overflow: hidden;">
            <div class="row align-items-center">
                <div class="col-md-8 position-relative" style="z-index: 2;">
                    <h1 class="fw-bold mb-1" style="font-size: 2rem;">Selamat Datang Kembali!</h1>
                    <h4 class="mb-3" style="opacity: 0.9; font-weight: 400;">{{ Auth::user()->nama }}</h4>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-white text-primary px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                            <i class="bi bi-shield-check me-1"></i> {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}
                        </span>
                        <span class="text-white-50 small">
                            <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </span>
                    </div>
                    <div class="mt-3 p-2 px-3 d-flex align-items-center gap-3" style="background: rgba(255,255,255,0.1); border-radius: 10px; backdrop-filter: blur(5px);">
                        <div class="flex-shrink-0" style="width: 32px; height: 32px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-lightbulb text-warning" style="font-size: 0.9rem;"></i>
                        </div>
                        <p class="mb-0 small text-white-50" style="line-height: 1.2;">
                            <strong class="text-white">Tips:</strong> Jangan lupa untuk selalu mengecek jadwal dan pengumuman terbaru secara rutin 👍
                        </p>
                    </div>
                </div>
                <div class="col-md-4 d-none d-md-flex justify-content-end align-items-center position-relative" style="z-index: 2;">
                    <img src="{{ asset('assets/images/dashboard-illustration.png') }}" alt="Dashboard" class="img-fluid" style="max-height: 150px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));">
                </div>
            </div>
            {{-- Decorative circles --}}
            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -30px; left: 20%; width: 100px; height: 100px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
        </div>

        <!-- STATS SECTION -->
        <div class="row g-3 mb-4">
            @if(in_array(Auth::user()->role, ['super_admin','admin_sa']))
                {{-- ADMIN STATS --}}
                @php
                    $adminStats = [
                        ['label' => 'Total Siswa', 'value' => App\Models\Siswa::count(), 'icon' => 'people', 'color' => 'primary', 'route' => 'sistem_akademik.siswa.index'],
                        ['label' => 'Total Guru', 'value' => App\Models\User::where('role','guru')->count(), 'icon' => 'person-workspace', 'color' => 'success', 'route' => 'sistem_akademik.guru.index'],
                        ['label' => 'Total Course', 'value' => App\Models\Course::count(), 'icon' => 'journal-text', 'color' => 'warning', 'route' => 'sistem_akademik.course.index'],
                        ['label' => 'Total Kelas', 'value' => App\Models\Kelas::count(), 'icon' => 'building', 'color' => 'info', 'route' => 'sistem_akademik.kelas.index'],
                    ];
                @endphp
                @foreach($adminStats as $index => $stat)
                <div class="col-md-3 col-6">
                    <div class="stat-card compact fade-in-up" style="animation-delay: {{ 0.1 * ($index + 1) }}s; border-radius: 12px; border: 1px solid #f1f5f9;">
                        <div class="stat-icon {{ $stat['color'] }}" style="width: 45px; height: 45px; border-radius: 10px;">
                            <i class="bi bi-{{ $stat['icon'] }}"></i>
                        </div>
                        <div class="stat-body">
                            <div class="stat-value" style="font-size: 1.4rem; font-weight: 700;">{{ $stat['value'] }}</div>
                            <div class="stat-label text-muted small fw-bold text-uppercase">{{ $stat['label'] }}</div>
                        </div>
                        <a href="{{ route($stat['route'], $stat['params'] ?? []) }}" class="stretched-link"></a>
                    </div>
                </div>
                @endforeach

            @elseif(Auth::user()->role == 'siswa' && Auth::user()->siswa)
                {{-- STUDENT STATS --}}
                @php
                    $peminatanCount = \App\Models\Peminatan::where('siswa_id', Auth::user()->siswa->id)->count();
                    $kelasId = Auth::user()->siswa->kelas_id;
                    $studentStats = [
                        ['label' => 'Course Saya', 'value' => $kelasId ? \App\Models\Course::where('kelas_id', $kelasId)->count() : 0, 'icon' => 'journal-text', 'color' => 'primary', 'route' => 'sistem_akademik.course.index'],
                        ['label' => 'Status Peminatan', 'value' => ($peminatanCount > 0 ? 'Selesai' : 'Belum'), 'icon' => 'journal-check', 'color' => 'success', 'route' => 'sistem_akademik.peminatan.index'],
                        ['label' => 'Jadwal Hari Ini', 'value' => $kelasId ? \App\Models\Course::where('kelas_id', $kelasId)->where('hari', \Carbon\Carbon::now()->locale('id')->isoFormat('dddd'))->count() : 0, 'icon' => 'calendar-check', 'color' => 'info', 'route' => 'sistem_akademik.course.index', 'params' => ['hari' => \Carbon\Carbon::now()->locale('id')->isoFormat('dddd')]],
                    ];
                @endphp
                @foreach($studentStats as $index => $stat)
                <div class="col-md-4 col-12">
                    <div class="stat-card compact fade-in-up" style="animation-delay: {{ 0.1 * ($index + 1) }}s; border-radius: 12px;">
                        <div class="stat-icon {{ $stat['color'] }}" style="width: 45px; height: 45px;">
                            <i class="bi bi-{{ $stat['icon'] }}"></i>
                        </div>
                        <div class="stat-body">
                            <div class="stat-value" style="font-size: 1.4rem; font-weight: 700;">{{ $stat['value'] }}</div>
                            <div class="stat-label text-muted small fw-bold text-uppercase">{{ $stat['label'] }}</div>
                        </div>
                        <a href="{{ route($stat['route'], $stat['params'] ?? []) }}" class="stretched-link"></a>
                    </div>
                </div>
                @endforeach

            @elseif(Auth::user()->role == 'guru')
                {{-- TEACHER STATS --}}
                @php
                    $guruKelasIds = \App\Models\Course::whereHas('mataPelajaran', fn($q) => $q->where('guru_id', Auth::id()))->pluck('kelas_id')->unique()->filter()->toArray();
                    $teacherStats = [
                        ['label' => 'Course Saya', 'value' => \App\Models\Course::whereHas('mataPelajaran', fn($q) => $q->where('guru_id', Auth::id()))->count(), 'icon' => 'journal-text', 'color' => 'primary', 'route' => 'sistem_akademik.course.index'],
                        ['label' => 'Total Siswa', 'value' => \App\Models\Siswa::whereIn('kelas_id', $guruKelasIds)->count(), 'icon' => 'people', 'color' => 'success', 'route' => '#'],
                        ['label' => 'Jadwal Hari Ini', 'value' => \App\Models\Course::whereHas('mataPelajaran', fn($q) => $q->where('guru_id', Auth::id()))->where('hari', \Carbon\Carbon::now()->locale('id')->isoFormat('dddd'))->count(), 'icon' => 'calendar-check', 'color' => 'info', 'route' => 'sistem_akademik.course.index', 'params' => ['hari' => \Carbon\Carbon::now()->locale('id')->isoFormat('dddd')]],
                    ];
                @endphp
                @foreach($teacherStats as $index => $stat)
                <div class="col-md-4 col-12">
                    <div class="stat-card compact fade-in-up" style="animation-delay: {{ 0.1 * ($index + 1) }}s; border-radius: 12px;">
                        <div class="stat-icon {{ $stat['color'] }}" style="width: 45px; height: 45px;">
                            <i class="bi bi-{{ $stat['icon'] }}"></i>
                        </div>
                        <div class="stat-body">
                            <div class="stat-value" style="font-size: 1.4rem; font-weight: 700;">{{ $stat['value'] }}</div>
                            <div class="stat-label text-muted small fw-bold text-uppercase">{{ $stat['label'] }}</div>
                        </div>
                        <a href="{{ $stat['route'] != '#' ? route($stat['route'], $stat['params'] ?? []) : 'javascript:void(0)' }}" class="stretched-link"></a>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        @if(Auth::user()->role == 'siswa' && !Auth::user()->siswa)
        <div class="alert alert-warning border-0 shadow-sm" style="border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
            <strong>Perhatian:</strong> Data siswa Anda belum lengkap. Silahkan lengkapi profil Anda terlebih dahulu.
        </div>
        @endif

        <!-- Announcements & Quick Actions Section -->
        <div class="row g-4 mb-4">
            {{-- Announcements (Left Side) --}}
            <div class="col-lg-8">

                <div class="section-header d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0" style="color: #1e3a5f;"><i class="bi bi-newspaper me-2 text-primary"></i> Berita & Info</h4>
                    @if(in_array(Auth::user()->role, ['super_admin','admin_sa','admin']))
                        <a href="{{ route('sistem_akademik.berita.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                            <i class="bi bi-gear me-1"></i> Kelola Berita
                        </a>
                    @endif
                </div>

                {{-- Filter & Search Form (More Prominent) --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(to right, #f8fafc, #eff6ff); border: 1px solid #e2e8f0 !important;">
                    <div class="card-body p-3">
                        <form action="{{ route('sistem_akademik.dashboard') }}" method="GET" class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="small fw-bold text-primary mb-0" style="white-space: nowrap;"><i class="bi bi-filter-circle me-1"></i> Kategori:</label>
                                    <select name="filter" class="form-select form-select-sm border-0 shadow-none" style="background-color: white; border-radius: 8px;" onchange="this.form.submit()">
                                        <option value="">Semua Kategori</option>
                                        <option value="terlama" {{ request('filter')=='terlama'?'selected':'' }}>Terlama</option>
                                        <option value="informasi" {{ request('filter')=='informasi'?'selected':'' }}>Informasi</option>
                                        <option value="prestasi" {{ request('filter')=='prestasi'?'selected':'' }}>Prestasi</option>
                                        <option value="pemberitahuan" {{ request('filter')=='pemberitahuan'?'selected':'' }}>Pemberitahuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm bg-white" style="border-radius: 8px; border: 1px solid #cbd5e1;">
                                    <span class="input-group-text bg-transparent border-0 pe-0"><i class="bi bi-search text-primary"></i></span>
                                    <input type="text" name="search" class="form-control form-control-sm border-0 shadow-none bg-transparent" placeholder="Cari judul berita atau pengumuman..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-primary-app w-100 shadow-sm" style="border-radius: 8px;">
                                    <i class="bi bi-search me-1"></i> Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="announcements-container">
                    @if ($berita->isEmpty())
                        <div class="text-center p-5 bg-light" style="border-radius: 12px; border: 2px dashed #e2e8f0;">
                            <i class="bi bi-inbox text-muted h1"></i>
                            <p class="text-muted mt-2">Belum ada berita terbaru saat ini.</p>
                        </div>
                    @else
                        @foreach ($berita as $b)
                            <div class="card mb-3 border-0 shadow-sm hover-lift" style="border-radius: 12px; transition: all 0.3s ease;">
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            @if ($b->foto)
                                                <img src="{{ asset('assets/berita/' . $b->foto) }}" alt="Berita" class="img-fluid" style="width:100%; height:120px; object-fit:cover; border-radius:10px;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width:100%; height:120px; border-radius:10px;">
                                                    <i class="bi bi-image text-muted h2 mb-0"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9 d-flex flex-column">
                                            <div class="d-flex justify-content-between mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-soft-primary text-primary small" style="background-color: #e0e7ff; color: #4338ca;">{{ $b->kategori ?? 'Informasi' }}</span>
                                                    @if(!$b->has_read)
                                                        <span class="badge rounded-pill bg-danger p-1" style="width: 8px; height: 8px; animation: pulse 2s infinite;" title="Baru/Belum dibaca">
                                                            <span class="visually-hidden">Belum dibaca</span>
                                                        </span>
                                                    @endif
                                                </div>
                                                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> {{ optional($b->created_at)->format('d M Y') }}</small>
                                            </div>
                                            <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.1rem; line-height: 1.4;">{{ Str::limit($b->judul, 80) }}</h5>
                                            <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.5;">
                                                {!! Str::limit(strip_tags($b->isi), 150, '...') !!}
                                            </p>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('sistem_akademik.berita.show', $b->id) }}" class="btn btn-sm btn-link p-0 text-primary fw-bold text-decoration-none">Baca Selengkapnya <i class="bi bi-arrow-right"></i></a>
                                                @if (!empty($b->file))
                                                    <a href="{{ asset('file/' . $b->file) }}" class="ms-auto btn btn-sm btn-light p-1 px-2" style="border-radius: 6px;" title="Unduh Lampiran" target="_blank" download>
                                                        <i class="bi bi-download text-primary"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-center mt-4">
                            {!! $berita->appends(request()->query())->links() !!}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions (Right Side) --}}
            <div class="col-lg-4">
                <div class="section-header mb-4">
                    <h4 class="fw-bold mb-0" style="color: #1e3a5f;"><i class="bi bi-lightning-charge me-2 text-warning"></i> Menu Cepat</h4>
                </div>
                
                <div class="row g-3">
                    @if(in_array(Auth::user()->role, ['super_admin','admin_sa']))
                        @php
                            $actions = [
                                ['label' => 'Tambah Course', 'icon' => 'plus-circle', 'color' => '#8b5cf6', 'route' => 'sistem_akademik.course.create'],
                                ['label' => 'Tambah Siswa', 'icon' => 'person-plus', 'color' => '#06b6d4', 'route' => 'sistem_akademik.siswa.create'],
                                ['label' => 'Tambah Kelas', 'icon' => 'building-add', 'color' => '#10b981', 'route' => 'sistem_akademik.kelas.create'],
                                ['label' => 'Buat Berita', 'icon' => 'megaphone', 'color' => '#f59e0b', 'route' => 'sistem_akademik.berita.create'],
                                ['label' => 'Mata Pelajaran', 'icon' => 'journal-plus', 'color' => '#ef4444', 'route' => 'sistem_akademik.mata_pelajaran.create'],
                                ['label' => 'Tambah Guru', 'icon' => 'person-badge', 'color' => '#6366f1', 'route' => 'sistem_akademik.guru.create'],
                            ];
                        @endphp
                    @elseif(Auth::user()->role == 'guru')
                        @php
                            $actions = [
                                ['label' => 'Jadwal Mengajar', 'icon' => 'calendar3', 'color' => '#8b5cf6', 'route' => 'sistem_akademik.course.index'],
                                ['label' => 'Mata Pelajaran', 'icon' => 'book', 'color' => '#06b6d4', 'route' => 'sistem_akademik.mata_pelajaran.index'],
                                ['label' => 'Profil Saya', 'icon' => 'person-circle', 'color' => '#10b981', 'route' => 'sistem_akademik.profile'],
                                ['label' => 'Siswa Saya', 'icon' => 'people', 'color' => '#f59e0b', 'route' => 'sistem_akademik.siswa.index'],
                            ];
                        @endphp
                    @else
                        @php
                            $actions = [
                                ['label' => 'Jadwal Kelas', 'icon' => 'calendar3', 'color' => '#8b5cf6', 'route' => 'sistem_akademik.course.index'],
                                ['label' => 'Isi Peminatan', 'icon' => 'journal-check', 'color' => '#06b6d4', 'route' => 'sistem_akademik.peminatan.index'],
                                ['label' => 'Profil Saya', 'icon' => 'person-circle', 'color' => '#10b981', 'route' => 'sistem_akademik.profile'],
                                ['label' => 'Info Berita', 'icon' => 'newspaper', 'color' => '#f59e0b', 'route' => 'sistem_akademik.dashboard'],
                            ];
                        @endphp
                    @endif

                    @foreach($actions as $action)
                        <div class="col-6">
                            <a href="{{ route($action['route']) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm hover-lift text-center p-3" style="border-radius: 12px; transition: all 0.3s ease;">
                                    <div class="d-flex align-items-center justify-content-center mb-2 mx-auto" style="width: 50px; height: 50px; border-radius: 12px; background-color: {{ $action['color'] }}15;">
                                        <i class="bi bi-{{ $action['icon'] }}" style="color: {{ $action['color'] }}; font-size: 1.5rem;"></i>
                                    </div>
                                    <span class="small fw-bold text-dark">{{ $action['label'] }}</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div> {{-- End of container-fluid --}}
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

                // AJAX Pagination untuk berita
                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    let url = $(this).attr('href');
                    
                    $('.announcements-container').css('opacity', '0.5');
                    
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            let newContent = $(response).find('.announcements-container').html();
                            $('.announcements-container').html(newContent).css('opacity', '1');
                            
                            $('html, body').animate({
                                scrollTop: $('.announcements-container').offset().top - 100
                            }, 300);
                        },
                        error: function() {
                            $('.announcements-container').css('opacity', '1');
                            Swal.fire('Error', 'Gagal memuat berita selanjutnya.', 'error');
                        }
                    });
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