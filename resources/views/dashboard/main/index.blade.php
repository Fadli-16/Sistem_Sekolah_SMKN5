@extends('dashboard.layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@endsection

@section('content')
   <!-- Banner Section -->
   <section class="banner">
        <h2>Selamat Datang di Sistem Informasi Laboratorium SMK</h2>
        <p>
            Sebuah platform yang dirancang untuk memberikan kemudahan kepada pengguna
            dalam mengakses berbagai informasi terkait penggunaan laboratorium. Melalui website ini, Anda dapat dengan
            mudah
            melihat jadwal penggunaan laboratorium, mengetahui daftar inventaris alat yang tersedia, serta melakukan
            proses
            peminjaman alat dengan praktis dan efisien. Kami berharap sistem ini dapat membantu meningkatkan efektivitas
            pengelolaan laboratorium sekaligus mendukung kegiatan pembelajaran secara optimal.
        </p>
    </section>

    <!-- Tentang Laboratorium Section -->
    <section class="tentang-laboratorium">
        <h2>Tentang Laboratorium</h2>
        <p>
            Di SMK kami, terdapat tiga laboratorium yang lengkap dan modern untuk mendukung pembelajaran di bidang
            Teknik Komputer Jaringan (TKJ), Rekayasa Perangkat Lunak (RPL), dan Multimedia.
        </p>
        <a href="{{ route('lab.index') }}" class="btn">Lihat Profil Laboratorium</a>
    </section>

    <!-- Informasi Fitur -->
    <section class="fitur">
        <div class="fitur-card">
            <img src="{{ asset('assets/images/jadwal.jpg') }}" alt="Jadwal">
            <h3>Jadwal Laboratorium</h3>
            <p>
                Melihat jadwal penggunaan laboratorium dengan mudah untuk memastikan ketersediaan ruang.
            </p>
            <a href="{{ route('lab.jadwal') }}" class="btn">Lihat Jadwal</a>
        </div>
        <div class="fitur-card">
            <img src="{{ asset('assets/images/inventaris.jpg') }}" alt="Inventaris">
            <h3>Inventaris Alat</h3>
            <p>
                Lihat daftar alat yang tersedia di setiap laboratorium, lengkap dengan statusnya.
            </p>
            <a href="{{ route('inv.index') }}" class="btn">Lihat Inventaris</a>
        </div>
        <div class="fitur-card">
            <img src="{{ asset('assets/images/kerusakan.jpg') }}" alt="Laporan Kerusakan">
            <h3>Laporan Kerusakan</h3>
            <p>
                Laporkan alat yang rusak dengan mudah agar segera diperbaiki.
            </p>
            <a href="{{ route('inv.laporan') }}" class="btn">Lapor Kerusakan</a>
        </div>
    </section>
@endsection