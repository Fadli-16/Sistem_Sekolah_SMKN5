@extends('admin.layouts.main')

@section('css')
<style>
    /* Global Style */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        background-color: #f9f9f9;
        color: #333;
    }

    header {
        background-color: #004080;
        color: white;
        padding: 20px;
        text-align: center;
    }

    header .logo img {
        max-height: 150px;
        margin-bottom: 10px;
    }

    nav {
        background-color: #002b5c;
        padding: 10px 20px;
        text-align: center;
    }

    nav ul {
        list-style-type: none;
        margin: 0 auto;
        padding: 0;
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    nav ul li {
        display: inline-block;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    nav ul li a:hover {
        text-decoration: underline;
    }

    .form-inventaris {
        padding: 20px;
        max-width: 600px;
        margin: 20px auto;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form-inventaris h2 {
        text-align: center;
        color: #004080;
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    input,
    select,
    button {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
    }

    input:focus,
    select:focus {
        outline: none;
        border-color: #004080;
    }

    button {
        background-color: #004080;
        color: white;
        font-weight: bold;
        cursor: pointer;
        border: none;
    }

    button:hover {
        background-color: #002b5c;
    }

    footer {
        background-color: #004080;
        color: white;
        text-align: center;
        padding: 10px;
        margin-top: 20px;
    }

    footer p {
        margin: 5px 0;
    }
    </style>
@endsection

@section('content')
    <!-- Form untuk Menambah Inventaris -->
    <section class="form-inventaris">
        <h2>Tambah Inventaris Baru</h2>
        <form action="{{ route('admin.kelola.inv.post') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label for="nama_inventaris">Nama Inventaris:</label>
            <input type="text" id="nama_inventaris" name="nama_inventaris" required>

            <label for="kategori">Kategori:</label>
            <input type="text" id="kategori" name="kategori" required>

            <label for="jumlah">Jumlah:</label>
            <input type="number" id="jumlah" name="jumlah" required>

            <label for="deskripsi">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" required></textarea>

            <select id="status" name="status" required>
                <option value="Tersedia">Tersedia</option>
                <option value="Tidak Tersedia">Tidak Tersedia</option>
            </select>

            <label for="gambar">Gambar:</label>
            <input type="file" id="gambar" name="gambar">

            <button type="submit" class="btn">Tambah Inventaris</button>
        </form>
    </section>
@endsection