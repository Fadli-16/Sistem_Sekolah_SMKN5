<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem PPDB SMK Negeri 5 Padang - Platform pendaftaran siswa baru dan daftar ulang.">
    <meta name="keywords" content="PPDB, SMK, Padang, Pendaftaran, Siswa Baru">
    <meta name="author" content="SMK Negeri 5 Padang">
    
    <title>{{ $title ?? 'PPDB' }} - Sistem PPDB SMK Negeri 5 Padang</title>
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        /* Base styles with perpustakaan theme */
        :root {
            /* Color System */
            --primary: #ff9022;
            --primary-dark: #ff6b35;
            --primary-light: #2c3e50;
            --secondary: #000000;
            --secondary-dark: #3bafa6;
            --secondary-light: #e8f7f6;
            --navbar-bg: #ff6b35; /* Warna navbar yang berbeda dari sidebar */
            --accent: #ff6b35;
            --accent-light: #fff1eb;
            
            /* Text Colors */
            --text-light: #ffffff;
            --text-dark: #1a2a3a;
            --text-muted: #6c757d;
            
            /* Background Colors */
            --bg-light: #ffffff;
            --bg-gray: #f8fafc;
            
            /* Shadow System */
            --shadow-sm: 0 2px 4px rgba(26, 42, 58, 0.05);
            --shadow: 0 4px 8px rgba(26, 42, 58, 0.08);
            --shadow-lg: 0 8px 16px rgba(26, 42, 58, 0.1);
            
            /* Border Radius */
            --radius-sm: 6px;
            --radius: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            
            /* Spacing */
            --spacing-xs: 0.5rem;
            --spacing-sm: 0.75rem;
            --spacing: 1rem;
            --spacing-md: 1.5rem;
            --spacing-lg: 2rem;
            --spacing-xl: 3rem;
            
            /* Typography */
            --font-family: "Poppins", sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size: 1rem;
            --font-size-md: 1.125rem;
            --font-size-lg: 1.25rem;
            --font-size-xl: 1.5rem;
            --font-size-2xl: 1.875rem;
            --font-size-3xl: 2.25rem;
            
            /* Transitions */
            --transition-fast: all 0.2s ease;
            --transition: all 0.3s ease;
            --transition-slow: all 0.4s ease;
        }
        
        body {
            font-family: var(--font-family);
            color: var(--text-dark);
            background-color: var(--bg-light);
            font-size: var(--font-size);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .content-wrapper {
            flex: 1;
            background-color: var(--bg-gray);
        }
        
        /* Global utility classes */
        .text-primary { color: var(--primary) !important; }
        .text-secondary { color: var(--secondary) !important; }
        .bg-primary { background-color: var(--primary) !important; }
        .bg-secondary { background-color: var(--secondary) !important; }
    </style>
    
    @yield('css')
</head>

<body>
    @include('ppdb.partials.navbar')
    
    <div class="content-wrapper">
        @yield('content')
    </div>
    
    @include('ppdb.partials.footer')
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Display flash messages
        @if(session('status'))
            Swal.fire({
                title: '{{ session('title') }}',
                text: '{{ session('message') }}',
                icon: '{{ session('status') }}',
                confirmButtonColor: '#4ecdc4'
            });
        @endif
        
        // Confirmation function
        function confirmDelete(e) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + e).submit();
                }
            });
        }
    </script>
    
    @yield('script')
</body>

</html>