@extends('dashboard.layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@endsection

@section('content')
    <section class="banner">
        <h2>Kenali Laboratorium Kami</h2>
        <p>Kami memiliki laboratorium modern untuk mendukung pembelajaran dan praktik di SMK.</p>
    </section>

    <section class="profil-laboratorium">
        <!-- Laboratorium TKJ -->
        <div class="laboratorium-card">
            <img src="{{ asset('assets/images/tkj.jpg') }}" alt="Laboratorium TKJ">
            <h3>Laboratorium TKJ</h3>
            <p>Laboratorium Teknik Komputer Jaringan (TKJ) dilengkapi dengan berbagai perangkat keras jaringan komputer
                seperti router, switch, dan kabel UTP.</p>
            <p class="guru">Guru Pengelola: Bapak Fikri</p>
            <a href="{{ route('lab.detail.tkj') }}" class="btn">Lihat Detail</a>
        </div>

        <!-- Laboratorium RPL -->
        <div class="laboratorium-card">
            <img src="{{ asset('assets/images/rpl.jpg') }}" alt="Laboratorium RPL">
            <h3>Laboratorium RPL</h3>
            <p>Laboratorium Rekayasa Perangkat Lunak (RPL) menyediakan berbagai perangkat untuk pengembangan software,
                termasuk komputer dengan software pengembangan aplikasi terkini.</p>
            <p class="guru">Guru Pengelola: Ibu Amira</p>
            <a href="{{ route('lab.detail.rpl') }}" class="btn">Lihat Detail</a>
        </div>

        <!-- Laboratorium Multimedia -->
        <div class="laboratorium-card">
            <img src="{{ asset('assets/images/multimedia.jpeg') }}" alt="Laboratorium Multimedia">
            <h3>Laboratorium Multimedia</h3>
            <p>Laboratorium Multimedia dilengkapi dengan peralatan modern untuk desain grafis, editing video, dan
                animasi komputer.</p>
            <p class="guru">Guru Pengelola: Ibu Rizky</p>
            <a href="{{ route('lab.detail.mm') }}" class="btn">Lihat Detail</a>
        </div>
    </section>
@endsection