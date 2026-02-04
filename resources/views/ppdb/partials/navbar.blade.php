<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <div class="brand-logo-container">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK">
            </div>
            <span class="ms-2 text-white fw-bold d-none d-sm-inline">PPDB SMKN 5 Padang</span>
            <span class="ms-2 text-white fw-bold d-sm-none">PPDB</span>
        </a>
        
        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Home - Tersedia untuk semua role -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door me-1"></i><span>Beranda</span>
                    </a>
                </li>
                
                <!-- Informasi PPDB - Tersedia untuk semua role -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ppdb.index') ? 'active' : '' }}" href="{{ route('ppdb.index') }}">
                        <i class="bi bi-file-earmark-text me-1"></i><span>Informasi PPDB</span>
                    </a>
                </li>
                
                @auth
                    @if(Auth::user()->role == 'admin_ppdb')
                        <!-- Kelola Daftar Ulang - Hanya untuk Admin PPDB -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.ppdb.daftar-ulang.*') ? 'active' : '' }}" href="{{ route('admin.ppdb.daftar-ulang.index') }}">
                                <i class="bi bi-people me-1"></i><span>Kelola Daftar Ulang</span>
                            </a>
                        </li>
                    @else
                        <!-- Daftar Ulang Form - Untuk Siswa/Calon Siswa -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('daftar-ulang.create') ? 'active' : '' }}" href="{{ route('daftar-ulang.create') }}">
                                <i class="bi bi-pencil-square me-1"></i><span>Daftar Ulang</span>
                            </a>
                        </li>
                    @endif
                    
                    <!-- User Menu - Untuk Semua User yang Login -->
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle user-dropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i><span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('logoutForm').submit();">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Login Button - Untuk Guest/Non-Login -->
                    <li class="nav-item ms-lg-2">
                        <a class="login-btn nav-link d-inline-block" href="{{ route('login') }}">
                            <i class="bi bi-person-circle me-1"></i> Login
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
    /* Navbar styling */
    .navbar {
        background: var(--primary);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }

    .navbar-brand {
        font-size: var(--font-size-lg);
        display: flex;
        align-items: center;
    }

    .brand-logo-container {
        width: 45px;
        height: 45px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover .brand-logo-container {
        transform: scale(1.05) rotate(5deg);
    }

    .navbar-brand img {
        height: 35px;
        width: auto;
    }

    .custom-toggler {
        border: none;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        padding: 6px 8px;
        transition: background-color 0.3s ease;
    }

    .custom-toggler:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.85)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    .navbar-toggler:focus {
        box-shadow: none;
    }

    .nav-link {
        position: relative;
        font-weight: 500;
        padding: 0.6rem 1rem;
        transition: all 0.3s ease;
        color: rgba(255, 255, 255, 0.85) !important;
        border-radius: 4px;
        margin: 0 2px;
    }

    .nav-link::before {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 0;
        background-color: rgba(78, 205, 196, 0.2);
        z-index: -1;
        transition: all 0.3s ease;
        border-radius: 4px;
    }

    .nav-link:hover {
        color: var(--secondary) !important;
        transform: translateY(-2px);
    }

    .nav-link:hover::before {
        height: 100%;
    }
    
    .nav-link.active {
        color: var(--secondary) !important;
        background-color: rgba(78, 205, 196, 0.1);
    }

    .nav-link.active::before {
        height: 100%;
    }

    .login-btn {
        background-color: var(--secondary);
        color: var(--primary) !important;
        border-radius: 50px;
        padding: 0.5rem 1.2rem !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .login-btn:hover {
        background-color: var(--text-light);
        color: var(--primary) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .user-dropdown {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 30px;
        padding: 0.4rem 1rem !important;
    }

    .user-dropdown:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* Dropdown styling */
    .dropdown-menu {
        margin-top: 10px !important;
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 0.5rem 0;
        border-top: 3px solid var(--secondary);
    }

    .dropdown-item {
        padding: 0.6rem 1.2rem;
        color: var(--primary);
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
    }

    .dropdown-item:hover {
        background-color: var(--secondary-light);
        color: var(--secondary-dark);
        padding-left: 1.5rem;
    }
    
    /* Mobile responsiveness */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background-color: var(--primary);
            padding: 1rem;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .nav-item {
            margin: 0.3rem 0;
        }
        
        .nav-link {
            padding: 0.7rem 1rem;
            border-radius: 6px;
        }
        
        .dropdown-menu {
            box-shadow: none;
            background-color: rgba(255, 255, 255, 0.05);
            border-top: none;
            margin-top: 0 !important;
            padding: 0;
        }
        
        .dropdown-item {
            color: rgba(255, 255, 255, 0.85);
        }
        
        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--secondary);
        }
        
        .ms-lg-2 {
            margin-left: 0 !important;
        }
        
        .login-btn {
            display: block !important;
            text-align: center;
            margin-top: 0.5rem;
        }
    }
</style>