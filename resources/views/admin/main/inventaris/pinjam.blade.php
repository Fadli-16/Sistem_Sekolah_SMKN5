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
        /* Menambahkan auto margin */
        padding: 0;
        display: flex;
        justify-content: center;
        /* Mengatur elemen agar sejajar di tengah */
        gap: 20px;
        text-align: center;
    }

    nav ul li {
        display: inline-block;
        /* Menjadikan item daftar tampil sebagai blok inline */
    }


    nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    nav ul li a:hover {
        text-decoration: underline;
    }

    .dashboard {
        padding: 20px;
    }

    .dashboard h2 {
        color: #004080;
    }

    .dashboard p {
        margin: 10px 0 20px;
    }

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .card {
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .card h3 {
        color: #004080;
        margin-bottom: 10px;
    }

    .card p {
        margin-bottom: 20px;
    }

    .card .btn {
        background-color: #004080;
        color: white;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: 5px;
        display: inline-block;
    }

    .card .btn:hover {
        background-color: #002b5c;
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

    .table-container {
        max-width: 1000px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ccc;
        text-align: left;
        padding: 10px;
    }

    th {
        background-color: #004080;
        color: white;
    }

    td {
        background-color: #f9f9f9;
    }

    button {
        padding: 10px 20px;
        background-color: #004080;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0056b3;
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
    <section class="table-container">
        <h2>Daftar Peminjaman Alat</h2>

        @if ($peminjaman->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peminjam</th>
                        <th>Kelas</th>
                        <th>Inventaris</th>
                        <th>Tanggal Peminjaman</th>
                        <th>Tujuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peminjaman as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->kelas }}</td>
                        <td>{{ $row->inventaris }}</td>
                        <td>{{ $row->tanggal_peminjaman }}</td>
                        <td>{{ $row->tujuan }}</td>
                        <td>{{ $row->status ?? 'Menunggu' }}</td>
                        <td>
                            <a href="{{ route('admin.kelola.inv.status', $row->id) }}">
                                <button>Ubah Status</button>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Belum ada peminjaman yang diajukan.</p>
        @endif
    </section>
@endsection