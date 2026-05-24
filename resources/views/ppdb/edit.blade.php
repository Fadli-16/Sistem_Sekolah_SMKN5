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
    <h2>Edit Data Pendafataran</h2>
    <div class="card p-4">
        <form action="{{ route('ppdb.update', $calonSiswa->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Tambahkan ini jika menggunakan PUT -->
        
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
                <label>Status Pendaftaran:</label>
                <select class="form-control" name="status_pendaftaran">
                    <option value="Menunggu" {{ $calonSiswa->status_pendaftaran == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="Diterima" {{ $calonSiswa->status_pendaftaran == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="Ditolak" {{ $calonSiswa->status_pendaftaran == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Nilai Rapor:</label>
                <input class="form-control" type="file" name="file_nilai_rapor" accept=".pdf">
                <span class="text-danger">*Isi jika ingin mengubah file</span>
            </div>
            @if($calonSiswa->nilai_rapor && file_exists(public_path('file/nilai_rapor/' . $calonSiswa->nilai_rapor)))
                <div class="mb-3">
                    <label>Preview Nilai Rapor:</label>
                    <embed src="{{ asset('file/nilai_rapor/' . $calonSiswa->nilai_rapor) }}" type="application/pdf" width="100%" height="500px" />
                </div>
            @endif
            <button class="px-2 py-1 rounded-3" type="submit">Simpan</button>
        </form>        
    </div>

</section>
@endsection