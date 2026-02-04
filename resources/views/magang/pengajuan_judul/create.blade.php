@extends('magang.layouts.main')

@section('content')
<div class="container">
    <h3>Form Pengajuan Judul Laporan Akhir Magang</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('pengajuan-judul.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nama">Nama Lengkap</label>
            <input type="text" class="form-control" value="{{ Auth::user()->nama }}" disabled>
        </div>

        <div class="mb-3">
            <label for="nis">NIS/NISN</label>
            <input type="text" class="form-control" value="{{ Auth::user()->nis_nip ?? 'Belum diatur' }}" disabled>
        </div>

        <div class="mb-3">
    <label for="jurusan">Jurusan</label>
    <select name="jurusan" class="form-control" required>
        <option value="">-- Pilih Jurusan --</option>
        <option value="Bisnis Konstruksi dan Properti">Bisnis Konstruksi dan Properti</option>
        <option value="Desain Pemodelan dan Informasi Bangunan">Desain Pemodelan dan Informasi Bangunan</option>
        <option value="Teknik Audio Video A">Teknik Audio Video A</option>
        <option value="Teknik Elektronika Industri">Teknik Elektronika Industri</option>
        <option value="Teknik Instalasi Tenaga Listrik A">Teknik Instalasi Tenaga Listrik A</option>
        <option value="Teknik Pemesinan A">Teknik Pemesinan A</option>
        <option value="Teknik Kendaraan Ringan A">Teknik Kendaraan Ringan A</option>
        <option value="Teknik Bodi Kendaraan Ringan">Teknik Bodi Kendaraan Ringan</option>
        <option value="Teknik Bisnis Sepeda Motor A">Teknik Bisnis Sepeda Motor A</option>
        <option value="Teknik Pendingin dan Tata Udara">Teknik Pendingin dan Tata Udara</option>
        <option value="Teknik Komputer Jaringan A">Teknik Komputer Jaringan A</option>
    </select>
</div>


       {{-- Nama Perusahaan (otomatis) --}}
<div class="mb-3">
    <label for="nama_perusahaan">Nama Perusahaan</label>
    <input type="text" class="form-control" value="{{ $namaPerusahaan ?? 'Belum terhubung' }}" disabled>
    <input type="hidden" name="wakil_perusahaan_id" value="{{ $wakilPerusahaanId }}">
</div>



        <div class="mb-3">
            <label for="judul_laporan">Judul Laporan</label>
            <input type="text" name="judul_laporan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="alasan">Alasan Memilih Judul</label>
            <textarea name="alasan" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Kirim</button>
    </form>
</div>
@endsection
