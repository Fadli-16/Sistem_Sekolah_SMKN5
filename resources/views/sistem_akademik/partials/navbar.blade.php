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
                @php
                $user = Auth::user();
                $avatar = null;
                $path = null;

                if (in_array($user->role, ['super_admin', 'admin_sa'])) {
                $avatar = optional($user->adminProfile)->image;
                $path = 'assets/profile/';
                } elseif ($user->role === 'guru') {
                $avatar = optional($user->guru)->image;
                $path = 'assets/profile/';
                } elseif ($user->role === 'siswa') {
                $avatar = optional($user->siswa)->image;
                $path = 'assets/profile/';
                }

                $avatarUrl = ($avatar && $path && file_exists(public_path($path . $avatar)))
                ? asset($path . $avatar)
                : asset('assets/img/default.png');
                @endphp

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-dropdown d-flex align-items-center gap-2"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                        <!-- FOTO PROFIL NAVBAR -->
                        <img src="{{ $avatarUrl }}"
                            alt="Foto {{ $user->nama }}"
                            class="navbar-avatar">

                        <span class="navbar-username">{{ $user->nama }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('sistem_akademik.profile') }}">
                                <i class="bi bi-person me-1"></i> Profil
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item"
                                href="javascript:void(0)"
                                onclick="document.getElementById('logoutForm').submit();">
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

<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>