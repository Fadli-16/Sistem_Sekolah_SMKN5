@extends('layouts.admin')

@section('content')
    <h1>{{ $header }}</h1>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Inventaris</th>
                <th>Tanggal Peminjaman</th>
                <th>Tujuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjaman as $item)
                <tr>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->kelas }}</td>
                    <td>{{ $item->inventaris }}</td>
                    <td>{{ $item->tanggal_peminjaman }}</td>
                    <td>{{ $item->tujuan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
