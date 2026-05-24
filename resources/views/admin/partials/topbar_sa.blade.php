<header class="sa-topbar">
    <div class="sa-topbar-left">
        <button class="sa-menu-toggle">
            <i class="bi bi-list"></i>
        </button>
        <h4 class="sa-page-title">@yield('page-title', 'Super Admin Dashboard')</h4>
    </div>

    <div class="sa-topbar-right">
        <div class="dropdown">
            <div class="sa-user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="sa-user-avatar">
                    {{ substr(auth()->user()->nama, 0, 1) }}
                </div>
                <div class="sa-user-info">
                    <div class="sa-user-name">{{ auth()->user()->nama }}</div>
                    <div class="sa-user-role">Super Administrator</div>
                </div>
                <i class="bi bi-chevron-down ms-2 small"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item text-start" href="{{ route('dashboard') }}">
                        <i class="bi bi-house me-1"></i> Beranda
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="SuperAdmin.logout()">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>