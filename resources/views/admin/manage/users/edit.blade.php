<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Super Admin Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Super Admin CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/superadmin.css') }}">

    <style>
        .sa-form-container {
            background-color: var(--sa-bg-card);
            border-radius: var(--sa-radius-lg);
            box-shadow: var(--sa-shadow);
            padding: var(--sa-spacing-lg);
            transition: all var(--sa-transition);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .sa-form-label {
            font-weight: 600;
            color: var(--sa-text-dark);
            margin-bottom: var(--sa-spacing-xs);
            display: block;
        }

        .sa-form-control {
            border-radius: var(--sa-radius);
            border: 1px solid rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
            font-size: var(--sa-font-size);
            width: 100%;
            transition: all var(--sa-transition-fast);
        }

        .sa-form-control:focus {
            border-color: var(--sa-secondary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.25);
            outline: none;
        }

        .sa-form-control.is-invalid {
            border-color: var(--sa-danger);
        }

        .sa-form-select {
            border-radius: var(--sa-radius);
            border: 1px solid rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
            font-size: var(--sa-font-size);
            width: 100%;
            transition: all var(--sa-transition-fast);
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px 12px;
        }

        .sa-form-select:focus {
            border-color: var(--sa-secondary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.25);
            outline: none;
        }

        .sa-form-text {
            font-size: var(--sa-font-size-xs);
            color: var(--sa-text-muted);
            margin-top: var(--sa-spacing-xs);
        }

        .sa-form-error {
            color: var(--sa-danger);
            font-size: var(--sa-font-size-xs);
            margin-top: var(--sa-spacing-xs);
        }

        .sa-user-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.7rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: var(--sa-secondary-light);
            color: var(--sa-secondary);
            margin-left: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="sa-wrapper">
        <!-- Sidebar -->
        <aside class="sa-sidebar">
            <div class="sa-sidebar-header">
                <a href="{{ route('admin.manage.index') }}" class="sa-logo">
                    <div class="sa-logo-img">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" width="30">
                    </div>
                    <span class="sa-logo-text">Super Admin</span>
                </a>
            </div>

            <div class="sa-sidebar-body">
                <ul class="sa-nav">
                    <li class="sa-nav-item">
                        <a href="{{ route('admin.manage.index') }}" class="sa-nav-link">
                            <span class="sa-nav-icon"><i class="bi bi-grid-1x2"></i></span>
                            <span class="sa-nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="sa-nav-item">
                        <a href="{{ route('admin.manage.users') }}" class="sa-nav-link active">
                            <span class="sa-nav-icon"><i class="bi bi-people"></i></span>
                            <span class="sa-nav-text">User Management</span>
                        </a>
                    </li>

                    <span class="sa-nav-title">System Modules</span>

                    <li class="sa-nav-item">
                        <a href="{{ route('ppdb.index') }}" class="sa-nav-link">
                            <span class="sa-nav-icon"><i class="bi bi-journal-text"></i></span>
                            <span class="sa-nav-text">PPDB</span>
                        </a>
                    </li>

                    <li class="sa-nav-item">
                        <a href="{{ route('sistem_akademik.dashboard') }}" class="sa-nav-link">
                            <span class="sa-nav-icon"><i class="bi bi-mortarboard"></i></span>
                            <span class="sa-nav-text">Academic System</span>
                        </a>
                    </li>

                    <li class="sa-nav-item">
                        <a href="{{ route('perpustakaan.buku.index') }}" class="sa-nav-link">
                            <span class="sa-nav-icon"><i class="bi bi-book"></i></span>
                            <span class="sa-nav-text">Library</span>
                        </a>
                    </li>

                    <li class="sa-nav-item">
                        <a href="{{ route('lab.dashboard') }}" class="sa-nav-link">
                            <span class="sa-nav-icon"><i class="bi bi-cpu"></i></span>
                            <span class="sa-nav-text">Laboratory</span>
                        </a>
                    </li>

                    <li class="sa-nav-item">
                        <a href="{{ route('magang.dashboard') }}" class="sa-nav-link">
                            <span class="sa-nav-icon"><i class="bi bi-briefcase"></i></span>
                            <span class="sa-nav-text">Internship</span>
                        </a>
                    </li>

                    <span class="sa-nav-title">Settings</span>

                    <li class="sa-nav-item">
                        <a href="#" class="sa-nav-link">
                            <span class="sa-nav-icon"><i class="bi bi-gear"></i></span>
                            <span class="sa-nav-text">System Settings</span>
                        </a>
                    </li>

                    <li class="sa-nav-item">
                        <a href="javascript:void(0)" onclick="SuperAdmin.logout()" class="sa-nav-link">
                            <span class="sa-nav-icon"><i class="bi bi-box-arrow-right"></i></span>
                            <span class="sa-nav-text">Log Out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="sa-content">
            <!-- Top Navigation Bar -->
            <header class="sa-topbar">
                <div class="sa-topbar-left">
                    <button class="sa-menu-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h4 class="sa-page-title">Edit User</h4>
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
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="SuperAdmin.logout()">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="sa-main-content">
                <div class="sa-page-header">
                    <h1 class="sa-page-header-title">Edit User</h1>
                    <p class="sa-page-header-subtitle">
                        User: <strong>{{ $user->nama }}</strong>
                        @php
                        $roleDisplay = '';
                        switch($user->role) {
                        case 'super_admin':
                        $roleDisplay = 'Super Admin';
                        break;
                        case 'admin_ppdb':
                        $roleDisplay = 'Admin PPDB';
                        break;
                        case 'admin_sa':
                        $roleDisplay = 'Admin Sistem Akademik';
                        break;
                        case 'admin_perpus':
                        $roleDisplay = 'Admin Perpustakaan';
                        break;
                        case 'admin_lab':
                        $roleDisplay = 'Admin Laboratorium';
                        break;
                        case 'admin_magang':
                        $roleDisplay = 'Admin Magang';
                        break;
                        case 'guru':
                        $roleDisplay = 'Guru';
                        break;
                        case 'siswa':
                        $roleDisplay = 'Siswa';
                        break;
                        }
                        @endphp
                        <span class="sa-user-badge">{{ $roleDisplay }}</span>
                    </p>
                </div>

                <div class="sa-row">
                    <div class="sa-col sa-col-lg-8 sa-col-md-10 sa-col-sm-12">
                        <div class="sa-form-container">
                            <form action="{{ route('admin.manage.users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label for="nama" class="sa-form-label">Full Name</label>
                                    <input type="text" name="nama" id="nama" class="sa-form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" required>
                                    @error('nama')
                                    <div class="sa-form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="nis_nip" class="sa-form-label">NIS/NIP</label>
                                    <input type="text" name="nis_nip" id="nis_nip" class="sa-form-control @error('nis_nip') is-invalid @enderror" value="{{ old('nis_nip', $user->nis_nip) }}" required>
                                    @error('nis_nip')
                                    <div class="sa-form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="sa-form-label">Email Address</label>
                                    <input type="email" name="email" id="email" class="sa-form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                    <div class="sa-form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="old_password" class="sa-form-label">Old Password</label>
                                    <input type="password"
                                        name="old_password"
                                        id="old_password"
                                        class="sa-form-control @error('old_password') is-invalid @enderror">
                                    @error('old_password')
                                    <div class="sa-form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="sa-form-label">New Password (Optional)</label>
                                    <input type="password"
                                        name="password"
                                        id="password"
                                        class="sa-form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                    <div class="sa-form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="sa-form-label">Confirm New Password</label>
                                    <input type="password"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        class="sa-form-control @error('password_confirmation') is-invalid @enderror">
                                    @error('password_confirmation')
                                    <div class="sa-form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="role" class="sa-form-label">User Role</label>
                                    <select name="role" id="role" class="sa-form-select @error('role') is-invalid @enderror" required>
                                        <option value="" disabled>-- Select Role --</option>
                                        @foreach($roles as $key => $value)
                                        <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                    <div class="sa-form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between mt-5">
                                    <a href="{{ route('admin.manage.users') }}" class="sa-btn sa-btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back to Users
                                    </a>
                                    <button type="submit" class="sa-btn sa-btn-primary">
                                        <i class="bi bi-check-circle"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="sa-footer">
                <div class="container-fluid">
                    <div class="sa-row">
                        <div class="sa-col sa-col-12 sa-text-center">
                            <p class="sa-text-muted">© {{ date('Y') }} SMKN 5 Padang School Management System</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- Custom Super Admin JS -->
    <script src="{{ asset('assets/js/superadmin.js') }}"></script>
</body>

</html>