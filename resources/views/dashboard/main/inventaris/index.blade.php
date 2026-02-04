@extends('dashboard.layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* Reset some basic styles */
        body,
        h1,
        h2,
        h3,
        p,
        ul,
        li,
        a {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
    
        body {
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
    
        header {
            background-color: #003366;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
    
        header .logo img {
            max-width: 100px;
        }
    
        h1 {
            margin-top: 10px;
        }
    
        nav {
            background-color: #004080;
            padding: 10px;
            text-align: center;
        }
    
        nav ul {
            list-style: none;
        }
    
        nav ul li {
            display: inline;
            margin: 0 10px;
        }
    
        nav ul li a {
            color: white;
            text-decoration: none;
        }
    
        .inventaris-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin: 20px;
        }
    
        .inventaris-item {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 10px;
            padding: 15px;
            width: 250px;
            text-align: center;
        }
    
        .inventaris-img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
    
        .inventaris-item h3 {
            margin: 10px 0;
            font-size: 1.2em;
        }
    
        .inventaris-item p {
            font-size: 0.9em;
            margin-bottom: 10px;
        }
    
        .status {
            font-weight: bold;
        }
    
        .status.available {
            color: green;
        }
    
        .status.unavailable {
            color: red;
        }
    
        .action-buttons {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px 0;
            background-color: #003366;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
    
        .btn:hover {
            background-color: #0056b3;
        }
    
        footer {
            background-color: #004080;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }
        </style>
@endsection

@section('content')
    <section class="inventaris">
        <h2>Daftar Inventaris Laboratorium</h2>
        <p>Berikut adalah daftar inventaris yang tersedia di laboratorium kami. Klik pada tombol di bawah untuk
            mengajukan peminjaman atau melaporkan kerusakan alat.</p>

        <div class="inventaris-list">
            @forelse ($inv as $item)
                <div class="inventaris-item">
                    <img src="{{ asset('assets/inventaris/' . $item->gambar) }}" alt="{{ $item->nama_inventaris }}" class="inventaris-img">
                    <h3>{{ $item->nama_inventaris }}</h3>
                    <p>{{ $item->deskripsi }}</p>
                    <p>Status: 
                        <span class="status {{ $item->status == 'Tersedia' ? 'available' : 'unavailable' }}">
                            {{ $item->status == 'Tersedia' ? 'Tersedia' : 'Tidak Tersedia' }}
                        </span>
                    </p>
                    <div class="action-buttons">
                        @if ($item->status == 'Tersedia')
                            <a href="{{ route('inv.pinjam') }}" class="btn">Ajukan Peminjaman</a>
                        @endif
                        <a href="{{ route('inv.laporan') }}" class="btn">Laporkan Kerusakan</a>
                    </div>
                </div>
            @empty
                <p>Belum ada inventaris yang tersedia.</p>
            @endforelse
        </div>
    </section>

@endsection