@extends('dashboard.layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@endsection

@section('content')

    <section class="detail-laboratorium">
        <div class="laboratorium-detail-card">
            <img src="{{ asset('assets/images/multimedia.jpeg') }}" alt="Laboratorium Multimedia" class="detail-img">
            <div class="detail-info">
                <h2>Laboratorium Multimedia</h2>
                <p>Laboratorium Multimedia dilengkapi dengan berbagai perangkat untuk desain grafis, pengeditan video,
                    dan pembuatan animasi, antara lain:</p>
                <ul>
                    <li>Komputer dengan spesifikasi tinggi dan software desain grafis (Adobe Photoshop, Illustrator,
                        After Effects)</li>
                    <li>Peralatan video editing dan animasi (kamera, tripod, lighting)</li>
                    <li>Software 3D modeling dan animasi</li>
                </ul>
                <p class="guru">Guru Pengelola: Ibu Rizky</p>
                <p>Laboratorium ini mendukung pembelajaran tentang desain grafis, pengeditan video, serta pembuatan
                    animasi dan konten multimedia.</p>
            </div>
        </div>
    </section>

@endsection