<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a class="navbar-brand" href="{{ route('siswa.dashboard') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK" height="32">
            <span>SMK Negeri 5 Padang</span>
        </a>
    </div>
    
    <div class="menu-items">
        <ul>
            <li class="{{ request()->routeIs('siswa.labor.*') ? 'active' : '' }}">
                <a href="{{ route('siswa.labor.index') }}" data-title="Laboratorium">
                    <i class="bi bi-building"></i>
                    <span class="menu-text">Laboratorium</span>
                </a>
            </li>
            
            <li class="{{ request()->routeIs('siswa.jadwal.*') ? 'active' : '' }}">
                <a href="{{ route('siswa.jadwal.index') }}" data-title="Jadwal">
                    <i class="bi bi-calendar-week"></i>
                    <span class="menu-text">Jadwal</span>
                </a>
            </li>
            
            <li class="{{ request()->routeIs('siswa.inventaris.*') ? 'active' : '' }}">
                <a href="{{ route('siswa.inventaris.index') }}" data-title="Inventaris">
                    <i class="bi bi-box-seam"></i>
                    <span class="menu-text">Inventaris</span>
                </a>
            </li>
            
            <li class="{{ request()->routeIs('siswa.laporan.*') ? 'active' : '' }}">
                <a href="{{ route('siswa.laporan.index') }}" data-title="Laporan">
                    <i class="bi bi-journal-text"></i>
                    <span class="menu-text">Laporan Kerusakan</span>
                </a>
            </li>
            
            <!-- Other existing menu items -->
        </ul>
    </div>
</div>