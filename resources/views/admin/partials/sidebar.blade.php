<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK" height="32">
            <span>Sistem Laboratorium</span>
        </a>
    </div>
    
    <div class="menu-items">
        <ul>
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" data-title="Dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            
            @if(Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_lab' || Auth::user()->role == 'siswa')
                <li class="{{ request()->routeIs('admin.labor.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.labor.index') }}" data-title="Kelola Laboratorium">
                        <i class="bi bi-building"></i>
                        <span class="menu-text">Laboratorium</span>
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.jadwal.index') }}" data-title="Jadwal Laboratorium">
                        <i class="bi bi-calendar-week"></i>
                        <span class="menu-text">Jadwal Laboratorium</span>
                    </a>
                </li>
                
                @if(Auth::user()->role != 'siswa')
                <li class="{{ request()->routeIs('admin.kelola.inv') ? 'active' : '' }}">
                    <a href="{{ route('admin.kelola.inv') }}" data-title="Kelola Inventaris">
                        <i class="bi bi-tools"></i>
                        <span class="menu-text">Kelola Inventaris</span>
                    </a>
                </li>
                @else
                <li class="{{ request()->routeIs('admin.inventaris.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.inventaris.index') }}" data-title="Inventaris">
                        <i class="bi bi-tools"></i>
                        <span class="menu-text">Inventaris</span>
                    </a>
                </li>
                @endif
                
                <li class="{{ request()->routeIs('admin.kelola.laporan') ? 'active' : '' }}">
                    <a href="{{ route('admin.kelola.laporan') }}" data-title="Laporan Kerusakan">
                        <i class="bi bi-journal-text"></i>
                        <span class="menu-text">Laporan Kerusakan</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>