<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="toggle-container">
        <button id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>
    
    <div class="navbar-content ms-auto">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <i class="bi bi-three-dots-vertical text-white"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-md-inline me-1">{{ Auth::user()->nama }}</span>
                        <i class="bi bi-person-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('sistem_akademik.profile') }}">
                                <i class="bi bi-person me-2"></i> Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>