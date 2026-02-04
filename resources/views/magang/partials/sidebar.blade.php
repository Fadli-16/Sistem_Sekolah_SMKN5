<!-- filepath: c:\Users\Izanagi\sistem-sekolah\resources\views\magang\partials\sidebar.blade.php -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a class="navbar-brand" href="{{ route('magang.dashboard') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK" height="32">
            <span>Sistem Magang</span>
        </a>
    </div>
    
    <div class="menu-items">
        <ul>
            <li class="{{ request()->routeIs('magang.dashboard') ? 'active' : '' }}">
                <a href="{{ route('magang.dashboard') }}" data-title="Dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            
            @if(Auth::check() && Auth::user()->role == 'wakil_perusahaan')
                <!-- Wakil Perusahaan Menu Items -->
                <li class="{{ request()->routeIs('magang.wakil_perusahaan.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('magang.wakil_perusahaan.dashboard') }}" data-title="Dashboard Perusahaan">
                        <i class="bi bi-grid-1x2"></i>
                        <span class="menu-text">Dashboard Perusahaan</span>
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('magang.wakil_perusahaan.openings*') ? 'active' : '' }}">
                    <a href="{{ route('magang.wakil_perusahaan.openings.index') }}" data-title="Program Magang">
                        <i class="bi bi-briefcase"></i>
                        <span class="menu-text">Program Magang</span>
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('magang.wakil_perusahaan.interns*') ? 'active' : '' }}">
                    <a href="{{ route('magang.wakil_perusahaan.interns') }}" data-title="Siswa Magang">
                        <i class="bi bi-people"></i>
                        <span class="menu-text">Siswa Magang</span>
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('magang.wakil_perusahaan.reports*') ? 'active' : '' }}">
                    <a href="{{ route('magang.wakil_perusahaan.reports') }}" data-title="Laporan">
                        <i class="bi bi-file-earmark-text"></i>
                        <span class="menu-text">Laporan Harian</span>
                    </a>
                </li>
                 <li class="{{ request()->routeIs('magang.wakil_perusahaan.penilain*') ? 'active' : '' }}">
                    <a href="{{ route('magang.wakil_perusahaan.penilaian.index') }}" data-title="penilaian">
                        <i class="bi bi-clipboard-check text-teal-600 text-4xl"></i>
                        <span class="menu-text">Penilaian</span>
                    </a>
                </li>
                
            @elseif(Auth::check() && Auth::user()->role == 'super_admin')
                <!-- Admin Menu Items -->
                <li class="{{ request()->routeIs('magang.magang.index') ? 'active' : '' }}">
                    <a href="{{ route('magang.magang.index') }}" data-title="Kelola Magang">
                        <i class="bi bi-list-check"></i>
                        <span class="menu-text">Kelola Magang</span>
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('magang.perusahaan.*') ? 'active' : '' }}">
                    <a href="{{ route('magang.perusahaan.index') }}" data-title="Kelola Perusahaan">
                        <i class="bi bi-building"></i>
                        <span class="menu-text">Kelola Perusahaan</span>
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('admin.magang.wakil_perusahaan.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.magang.wakil_perusahaan.index') }}" data-title="Kelola Mitra">
                        <i class="bi bi-person-badge"></i>
                        <span class="menu-text">Kelola Mitra</span>
                    </a>
                </li>
               

              @elseif(Auth::check() && Auth::user()->role == 'admin_magang')
    <!-- Admin Menu Items -->
    <li class="{{ request()->routeIs('magang.magang.index') ? 'active' : '' }}">
        <a href="{{ route('magang.magang.index') }}" data-title="Kelola Magang">
            <i class="bi bi-list-check"></i>
            <span class="menu-text">Kelola Magang</span>
        </a>
    </li>

    <li class="{{ request()->routeIs('magang.perusahaan.*') ? 'active' : '' }}">
        <a href="{{ route('magang.perusahaan.index') }}" data-title="Kelola Perusahaan">
            <i class="bi bi-building"></i>
            <span class="menu-text">Kelola Perusahaan</span>
        </a>
    </li>

    <li class="{{ request()->routeIs('admin.magang.wakil_perusahaan.*') ? 'active' : '' }}">
        <a href="{{ route('admin.magang.wakil_perusahaan.index') }}" data-title="Kelola Mitra">
            <i class="bi bi-person-badge"></i>
            <span class="menu-text">Kelola Mitra</span>
        </a>
    </li>

    <li class="{{ request()->routeIs('magang.wakil_perusahaan.nilaiakhir*') ? 'active' : '' }}">
        <a href="{{ route('magang.wakil_perusahaan.nilaiakhir.index') }}" data-title="Penilaian">
            <i class="bi bi-clipboard-check text-teal-600 text-4xl"></i>
            <span class="menu-text">Penilaian</span>
        </a>
    </li>

    <li class="{{ request()->routeIs('magang.wakil_perusahaan.reports*') ? 'active' : '' }}">
        <a href="{{ route('magang.wakil_perusahaan.reports') }}" data-title="Laporan Harian">
            <i class="bi bi-journal-text"></i> <!-- Ganti ikon ke logo jurnal -->
            <span class="menu-text">Laporan Harian</span>
        </a>
    </li>

    <li class="{{ request()->routeIs('magang.admin.pengajuan_judul*') ? 'active' : '' }}">
        <a href="{{ route('magang.admin.pengajuan_judul.index') }}" data-title="Kelola Judul">
            <i class="bi bi-pencil-square"></i>
            <span class="menu-text">Kelola Judul</span>
        </a>
    </li>        
            @elseif(Auth::check() && Auth::user()->role == 'siswa')
                <!-- Check if student is approved for internship -->
                @php
                    $magangSiswa = \App\Models\MagangSiswa::where('user_id', Auth::id())
                            ->where('status', 'Disetujui')
                            ->first();
                    $hasApprovedInternship = !is_null($magangSiswa);
                @endphp

                <li class="{{ request()->routeIs('magang.magang.index') ? 'active' : '' }}">
                    <a href="{{ route('magang.magang.index') }}" data-title="Program Magang">
                        <i class="bi bi-briefcase"></i>
                        <span class="menu-text">Program Magang</span>
                    </a>
                </li>

                <!-- Only show Reports menu if student has an approved internship -->
                @if($hasApprovedInternship)
                <li class="{{ request()->routeIs('magang.siswa.laporan*') ? 'active' : '' }}">
                    <a href="{{ route('magang.siswa.laporan.index') }}" data-title="Laporan Magang">
                        <i class="bi bi-journal-text"></i>
                        <span class="menu-text">Laporan Harian</span>
                    </a>
                </li>
                @endif
                @if($hasApprovedInternship)
                <li class="{{ request()->routeIs('magang.pengajuan_judul*') ? 'active' : '' }}">
                    <a href="{{ route('magang.pengajuan_judul.indexsiswa') }}" data-title="Laporan Magang">
                        <i class="bi bi-journal-text"></i>
                        <span class="menu-text">Ajukan judul</span>
                    </a>
                </li>
                @endif
            @else
                <!-- Regular User Menu Items -->
                <li class="{{ request()->routeIs('magang.magang.create') ? 'active' : '' }}">
                    <a href="{{ route('magang.magang.create') }}" data-title="Daftar Magang">
                        <i class="bi bi-briefcase"></i>
                        <span class="menu-text">Daftar Magang</span>
                    </a>
                </li>
            @endif
            
            <li>
                <a href="javascript:void(0)" onclick="document.getElementById('logoutForm').submit();" data-title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="menu-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>