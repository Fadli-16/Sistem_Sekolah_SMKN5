<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Akademik SMK Negeri 5 Padang">
    <meta name="keywords" content="Akademik, SMK, Padang, Siswa, Guru, Kelas">
    <meta name="author" content="SMK Negeri 5 Padang">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - Sistem Informasi Akademik SMK</title>
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" referrerpolicy="no-referrer" />
    <link href="{{ asset('assets/css/sistem-akademik.css') }}?v={{ time() }}" rel="stylesheet">

    @yield('css')
</head>

<body class="{{ session('sidebar_collapsed') ? 'sidebar-collapsed' : '' }}">
    @include('sistem_akademik.partials.navbar')
    @include('sistem_akademik.partials.sidebar')

    <div class="content-wrapper animate-fade-in">
        @yield('content')
    </div>

    @yield('modals')

    @include('sistem_akademik.partials.footer')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ===== FLASH MESSAGES (SweetAlert Premium) =====
        @if(session('status'))
        @php
            $status  = session('status');
            $message = session('message', '');
            $title   = session('title', $status === 'success' ? 'Berhasil!' : ($status === 'warning' ? 'Perhatian!' : 'Gagal!'));
            $icon    = in_array($status, ['success','error','warning','info']) ? $status : 'info';
        @endphp

        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: '{{ $icon }}',
                title: '{{ addslashes($title) }}',
                text: '{{ addslashes($message) }}',
                confirmButtonText: 'OK',
                confirmButtonColor: @if($icon === 'success') '#10b981' @elseif($icon === 'warning') '#f59e0b' @elseif($icon === 'error') '#ef4444' @else '#3b82f6' @endif,
                timer: 3500,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        });
        @endif

        // ===== SIDEBAR TOGGLE =====
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const body = document.body;

            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                body.classList.add('sidebar-collapsed');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    sidebar.classList.toggle('collapsed');
                    body.classList.toggle('sidebar-collapsed');

                    if (window.innerWidth <= 768) {
                        sidebar.classList.toggle('active');
                    }

                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
            }

            window.addEventListener('resize', function () {
                if (window.innerWidth <= 768) {
                    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
                } else {
                    document.body.style.overflow = '';
                }
            });

            document.addEventListener('click', function (event) {
                if (window.innerWidth <= 768 &&
                    sidebar && !sidebar.contains(event.target) &&
                    sidebarToggle && !sidebarToggle.contains(event.target) &&
                    sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        // ===== DELETE CONFIRMATION =====
        function confirmDelete(id) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Data?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }

        // ===== LOGOUT CONFIRMATION =====
        function logout(e) {
            Swal.fire({
                icon: 'question',
                title: 'Keluar Akun?',
                text: 'Anda akan logout dari sistem akademik.',
                showCancelButton: true,
                confirmButtonColor: '#1e3a5f',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // ===== STICKY NAVBAR =====
        window.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar-custom');
            if (navbar) {
                navbar.style.boxShadow = window.scrollY > 20
                    ? '0 4px 20px rgba(0,0,0,0.2)'
                    : '0 2px 20px rgba(0,0,0,0.15)';
            }
        });
    </script>

    @yield('script')
</body>

</html>
