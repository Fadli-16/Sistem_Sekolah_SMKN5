@extends('dashboard.layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* Reset dan Styling Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
    
        /* Header */
        header {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 20px;
        }
    
        header .logo img {
            width: 80px;
        }
    
        header h1 {
            font-size: 2rem;
            margin-top: 10px;
        }
    
        /* Navigation */
        nav {
            background-color: #004080;
            padding: 10px 0;
        }
    
        nav ul {
            list-style: none;
            text-align: center;
        }
    
        nav ul li {
            display: inline-block;
            margin: 0 15px;
        }
    
        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
        }
    
        nav ul li a:hover {
            text-decoration: underline;
        }
    
        /* Form Laporan Kerusakan */
        .laporan-form {
            background-color: #fff;
            padding: 40px;
            max-width: 800px;
            margin: 30px auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    
        .laporan-form h2 {
            font-size: 2rem;
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
        }
    
        .laporan-form label {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #555;
        }
    
        .laporan-form input,
        .laporan-form textarea,
        .laporan-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
    
        .laporan-form textarea {
            resize: vertical;
            min-height: 150px;
        }
    
        .laporan-form button {
            background-color: #004080;
            color: white;
            padding: 12px 25px;
            font-size: 1.2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
    
        .laporan-form button:hover {
            background-color: #003366;
        }
    
        /* Footer */
        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }
        </style>
@endsection

@section('content')

    <section class="laporan-form">
        <h2>Formulir Laporan Kerusakan</h2>
        <form action="{{ route('inv.laporan.store') }}" method="POST">
            @csrf
            <label for="nama-pelapor">Nama Pelapor</label>
            <input type="text" id="nama-pelapor" name="nama_pelapor" required placeholder="Masukkan nama Anda">

            <label for="nama-alat">Nama Alat</label>
            <select id="nama-alat" name="nama_alat" required>
                <option value="" disabled selected>-- Pilih Alat --</option>
                @foreach ($inv as $item)
                    <option value="{{ $item->nama_inventaris }}">{{ $item->nama_inventaris }}</option>
                @endforeach
            </select>

            <label for="deskripsi-kerusakan">Deskripsi Kerusakan</label>
            <textarea id="deskripsi-kerusakan" name="deskripsi_kerusakan" required
                placeholder="Deskripsikan kerusakan alat" rows="5"></textarea>

            <label for="tanggal-laporan">Tanggal Laporan</label>
            <input type="date" id="tanggal-laporan" name="tanggal_laporan" required>

            <button type="submit">Kirim Laporan</button>
        </form>
    </section>

@endsection