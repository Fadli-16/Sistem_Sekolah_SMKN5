@extends('ppdb.layouts.main')

@section('css')
    <style>
        button {
            background-color: #FF5733;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #de7519;
        }
    </style>
@endsection

@section('content')
<section class="container mt-4 mb-4">
    <h2>Tambah Data Pendafataran</h2>
    <div class="card p-4">
        <form action="{{ route('ppdb.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label>Nama:</label>
                <input class="form-control" type="text" name="nama" value="{{ $calonSiswa->nama ?? '' }}">
            </div>
            <div class="mb-3">
                <label>Tanggal Lahir:</label>
            <input class="form-control" type="date" name="tanggal_lahir" value="{{ $calonSiswa->tanggal_lahir ?? '' }}">
            </div>
            <div class="mb-3">
                <label for="alamat">Alamat:</label>
                <div class="form-floating">
                    <textarea class="form-control" name="alamat" id="alamat">{{ $calonSiswa->alamat ?? '' }}</textarea>
                </div>
            </div>
            <div class="mb-3">
                <label>Asal Sekolah:</label>
                <input class="form-control" type="text" name="sekolah_asal" value="{{ $calonSiswa->sekolah_asal ?? '' }}">
            </div>
            <div class="mb-3">
                <label>No. HP:</label>
                <input class="form-control" type="text" name="no_hp" value="{{ $calonSiswa->no_hp ?? '' }}">
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input class="form-control" type="email" name="email" value="{{ $calonSiswa->email ?? '' }}">
            </div>
            <div class="mb-3">
                <label>Nilai Rapor:</label>
                <input class="form-control" type="file" name="file_nilai_rapor" accept=".pdf">
            </div>
            <button class="px-2 py-1 rounded-3 " type="submit">Simpan</button>
        </form>
    </div>

</section>
@endsection

@section('js')
@endsection