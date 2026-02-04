<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <div class="brand-logo-container">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK">
            </div>
            <span class="ms-2 text-white fw-bold">SMKN 5 Padang</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list text-white fs-3"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door me-1"></i> Beranda
                    </a>
                </li>
                
                <!-- Profil Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-building me-1"></i> Profil
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#tentang">Tentang SMK 5 Padang</a></li>
                        <li><a class="dropdown-item" href="#visi-misi">Visi & Misi</a></li>
                        <li><a class="dropdown-item" href="#sejarah">Sejarah</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#struktur-organisasi">Struktur Organisasi</a></li>
                        <li><a class="dropdown-item" href="#guru">Profil Guru</a></li>
                    </ul>
                </li>
                
                <!-- Akademik Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-journal-text me-1"></i> Akademik
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('sistem_akademik.dashboard') }}">Dashboard Akademik</a></li>
                        <li><a class="dropdown-item" href="{{ route('sistem_akademik.mataPelajaran.index') }}">Mata Pelajaran</a></li>
                        <li><a class="dropdown-item" href="#kompetensi-keahlian">Kompetensi Keahlian</a></li>
                    </ul>
                </li>
                
                <!-- Layanan Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear me-1"></i> Layanan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('perpustakaan.buku.index') }}">Perpustakaan</a></li>
                        <li><a class="dropdown-item" href="{{ Auth::check() && Auth::user()->role == 'siswa' ? route('siswa.labor.index') : route('lab.dashboard') }}">Laboratorium</a></li>
                        <li><a class="dropdown-item" href="{{ route('magang.dashboard') }}">Program Magang</a></li>
                        <li><a class="dropdown-item" href="{{ route('ppdb.index') }}">PPDB</a></li>
                    </ul>
                </li>
                
                <!-- Kontak Link -->
                <li class="nav-item">
                    <a class="nav-link" href="#kontak">
                        <i class="bi bi-telephone me-1"></i> Kontak
                    </a>
                </li>
                
                <!-- Authentication Menu -->
                @if(Auth::check())
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            @if(Auth::user()->role == 'super_admin')
                            <li>
                                <a class="dropdown-item text-start" href="{{ route('admin.manage.index') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->role == 'wakil_perusahaan')
                            <li>
                                <a class="dropdown-item text-start" href="{{ route('magang.wakil_perusahaan.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard Mitra
                                </a>
                            </li>
                            @endif
                            
                            <li>
                                <a class="dropdown-item text-start" href="javascript:void(0)" onclick="logout()">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item ms-lg-2">
                        <a class="login-btn nav-link d-inline-block" href="{{ route('login') }}">
                            <i class="bi bi-person-circle me-1"></i> Login
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<!-- Keep the hidden logout form -->
<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
