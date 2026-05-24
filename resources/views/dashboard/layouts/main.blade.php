<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Sistem Informasi Laboratorium SMK</title>
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">
    @yield('css')
</head>

<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK">
        </div>
        <h1>{{ isset($header) ? $header : '' }}</h1>
    </header>

    @include('dashboard.partials.navbar')

    @yield('content')

    @include('dashboard.partials.footer')

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        @if(session('status'))
            swal('{{ session('title') }}', '{{ session('message') }}', '{{ session('status') }}')
        @endif
    </script>

    @yield('script')
</body>

</html>