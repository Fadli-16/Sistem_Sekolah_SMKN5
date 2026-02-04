<div class="toggle-container">
    <button type="button" id="sidebarToggle" aria-label="Toggle Sidebar">
        <i class="bi bi-chevron-left"></i>
    </button>
</div>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="navbar-content ms-auto">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <i class="bi bi-three-dots-vertical text-white"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                @if(Auth::check())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i><span>{{ Auth::user()->name ?? Auth::user()->nama }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-person me-1"></i> Profil
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('logoutForm').submit();">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-person-circle me-1"></i> Login
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<form id="logoutForm" action="{{ route('logout') }}" method="get" style="display: none;">
    @csrf
</form>