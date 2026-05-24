@extends('dashboard.layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@endsection

@section('content')

    <section class="detail-laboratorium">
        <div class="laboratorium-detail-card">
            <img src="{{ asset('assets/images/tkj.jpg') }}" alt="Laboratorium TKJ" class="detail-img">
            <div class="detail-info">
                <h2>Laboratorium Teknik Komputer dan Jaringan (TKJ)</h2>
                <p>Laboratorium TKJ dilengkapi dengan berbagai perangkat keras dan perangkat lunak untuk mendukung
                    pembelajaran di bidang jaringan komputer, termasuk:</p>
                <ul>
                    <li>Router dan Switch untuk jaringan</li>
                    <li>Komputer dengan spesifikasi tinggi untuk konfigurasi jaringan</li>
                    <li>Peralatan kabel UTP, patch panel, dan peralatan jaringan lainnya</li>
                </ul>
                <p class="guru">Guru Pengelola: Bapak Fikri</p>
                <p>Laboratorium ini mendukung pembelajaran tentang instalasi jaringan, konfigurasi perangkat jaringan,
                    dan troubleshooting.</p>
            </div>
        </div>
    </section>

@endsection