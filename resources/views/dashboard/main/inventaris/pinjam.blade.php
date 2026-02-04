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
        a,
        input,
        textarea,
        select {
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
    
        .form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    
        .form-container h2 {
            margin-bottom: 20px;
        }
    
        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
    
        .form-container input,
        .form-container select,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    
        .form-container button {
            padding: 10px 20px;
            background-color: #004080;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
    
        .form-container button:hover {
            background-color: #0056b3;
        }
    
        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }
        </style>
@endsection

@section('content')

    <section class="form-container">
        <h2>Form Peminjaman Alat</h2>

        <!-- Form Peminjaman -->
        <form action="{{ route('inv.pinjam.store') }}" method="POST">
            @csrf
            <label for="nama">Nama Peminjam</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>

            <label for="kelas">Kelas</label>
            <input type="text" id="kelas" name="kelas" placeholder="Masukkan kelas" required>

            <label for="inventaris">Pilih Inventaris</label>
            <select id="inventaris" name="inventaris" required>
                <option value="">-- Pilih Inventaris --</option>
                @foreach ($inv as $item)
                    <option value="{{ $item->nama_inventaris }}">{{ $item->nama_inventaris }}</option>
                @endforeach
            </select>

            <label for="tanggal_peminjaman">Tanggal Peminjaman</label>
            <input type="date" id="tanggal_peminjaman" name="tanggal_peminjaman" required>

            <label for="tujuan">Tujuan Penggunaan</label>
            <textarea id="tujuan" name="tujuan" placeholder="Jelaskan tujuan peminjaman alat" required></textarea>

            <button type="submit">Ajukan Peminjaman</button>
        </form>
    </section>


@endsection