@extends('dashboard.layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@endsection

@section('content')

    <section class="detail-laboratorium">
        <div class="laboratorium-detail-card">
            <img src="{{ asset('assets/images/rpl.jpg') }}" alt="Laboratorium RPL" class="detail-img">
            <div class="detail-info">
                <h2>Laboratorium Rekayasa Perangkat Lunak (RPL)</h2>
                <p>Laboratorium RPL menyediakan berbagai perangkat keras dan perangkat lunak untuk pengembangan aplikasi
                    perangkat lunak dan sistem operasi, seperti:</p>
                <ul>
                    <li>Komputer dengan spesifikasi tinggi untuk coding dan pengembangan perangkat lunak</li>
                    <li>Software pengembangan aplikasi seperti Visual Studio, NetBeans, Eclipse, dan lain-lain</li>
                    <li>Perangkat untuk pembuatan aplikasi web dan mobile</li>
                </ul>
                <p class="guru">Guru Pengelola: Ibu Amirah</p>
                <p>Laboratorium ini mendukung pembelajaran tentang pengembangan aplikasi berbasis desktop, web, dan
                    mobile, serta pemrograman tingkat lanjut.</p>
            </div>
        </div>
    </section>

@endsection