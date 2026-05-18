<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK" height="32">
            <span style="font-size:0.95rem; font-weight:700;">Sistem Akademik</span>
        </a>
    </div>

    <div class="menu-items">
        <ul>
            <li class="{{ request()->routeIs('sistem_akademik.dashboard') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.dashboard') }}" data-title="Dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            @if(Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa')

            <div class="sidebar-section-title">Manajemen</div>

            <li class="{{ request()->routeIs('sistem_akademik.berita.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.berita.index') }}" data-title="Kelola Berita">
                    <i class="bi bi-newspaper"></i>
                    <span class="menu-text">Kelola Berita</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('sistem_akademik.guru.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.guru.index') }}" data-title="Kelola Guru">
                    <i class="bi bi-person-workspace"></i>
                    <span class="menu-text">Kelola Guru</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('sistem_akademik.siswa.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.siswa.index') }}" data-title="Kelola Siswa">
                    <i class="bi bi-people-fill"></i>
                    <span class="menu-text">Kelola Siswa</span>
                </a>
            </li>

            <div class="sidebar-section-title">Akademik</div>

            <li class="{{ request()->routeIs('sistem_akademik.kelas.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.kelas.index') }}" data-title="Kelola Kelas">
                    <i class="bi bi-building"></i>
                    <span class="menu-text">Kelola Kelas</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('sistem_akademik.mata_pelajaran.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.mata_pelajaran.index') }}" data-title="Mata Pelajaran">
                    <i class="bi bi-book-fill"></i>
                    <span class="menu-text">Mata Pelajaran</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('sistem_akademik.peminatan.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.peminatan.index') }}" data-title="Kelola Peminatan">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span class="menu-text">Kelola Peminatan</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('sistem_akademik.course.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.course.index') }}" data-title="Kelola Jadwal">
                    <i class="bi bi-calendar3"></i>
                    <span class="menu-text">Kelola Jadwal</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->role == 'guru')

            <div class="sidebar-section-title">Menu Guru</div>

            <li class="{{ request()->routeIs('sistem_akademik.course.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.course.index') }}" data-title="Course Saya">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span class="menu-text">Course Saya</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('sistem_akademik.mata_pelajaran.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.mata_pelajaran.index') }}" data-title="Mata Pelajaran">
                    <i class="bi bi-book-fill"></i>
                    <span class="menu-text">Mata Pelajaran</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('sistem_akademik.peminatan.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.peminatan.index') }}" data-title="Peminatan">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span class="menu-text">Peminatan</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->role == 'siswa')

            <div class="sidebar-section-title">Menu Siswa</div>

            <li class="{{ request()->routeIs('sistem_akademik.course.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.course.index') }}" data-title="Course Saya">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span class="menu-text">Course Saya</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('sistem_akademik.peminatan.*') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.peminatan.index') }}" data-title="Peminatan Saya">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span class="menu-text">Peminatan Saya</span>
                </a>
            </li>
            @endif

            <div class="sidebar-section-title">Akun</div>

            <li class="{{ request()->routeIs('sistem_akademik.profile') ? 'active' : '' }}">
                <a href="{{ route('sistem_akademik.profile') }}" data-title="Profil Saya">
                    <i class="bi bi-person-circle"></i>
                    <span class="menu-text">Profil Saya</span>
                </a>
            </li>
        </ul>
    </div>
</div>