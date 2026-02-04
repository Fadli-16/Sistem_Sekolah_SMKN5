@extends('sistem_akademik.layouts.main')

@section('css')
<style>
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
</style>
@endsection

@section('content')
<div class="container mt-4 mb-4">
    <h2>{{ isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa' }}</h2>
    <div class="card p-4">
        <form action="{{ isset($siswa) ? route('sistem_akademik.siswa.update', $siswa->id) : route('sistem_akademik.siswa.store') }}" method="POST">
            @csrf
            @if(isset($siswa))
            @method('PUT')
            @endif

            <!-- Nama -->
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input
                    type="text"
                    class="form-control"
                    id="nama"
                    name="nama"
                    value="{{ old('nama', $siswa->user->nama ?? '') }}"
                    required>
            </div>

            <!-- NIS -->
            <div class="mb-3">
                <label for="nis" class="form-label">NIS</label>
                <input
                    type="text"
                    class="form-control"
                    id="nis"
                    name="nis"
                    value="{{ old('nis', $siswa->nis ?? '') }}"
                    required>
            </div>

            <div class="mb-3">
                <label>Kelas:</label>
                <select
                    name="kelas_id"
                    id="kelas_id"
                    class="form-control"
                    required>
                    <option value="" disabled selected>-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ (string) old('kelas_id', $siswa->kelas_id ?? '') == (string) $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} {{ $k->jurusan }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Lahir -->
            <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                <input
                    type="date"
                    class="form-control"
                    id="tanggal_lahir"
                    name="tanggal_lahir"
                    value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ?? '') }}"
                    required>
            </div>

            <!-- Alamat -->
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea
                    class="form-control"
                    id="alamat"
                    name="alamat"
                    rows="3"
                    required>{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
            </div>

            <!-- No HP -->
            <div class="mb-3">
                <label for="no_hp" class="form-label">Nomor HP</label>
                <input
                    type="text"
                    class="form-control"
                    id="no_hp"
                    name="no_hp"
                    value="{{ old('no_hp', $siswa->no_hp ?? '') }}"
                    required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    value="{{ old('email', $siswa->user->email ?? '') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="{{ isset($siswa) ? 'Isi Untuk Mengubah' : '' }}"
                    value="{{ old('password') }}">
            </div>

            <!-- Submit Button -->
            <button class="px-2 py-1 rounded-3" type="submit">Simpan</button>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#data-table').DataTable();
    });
</script>
@endsection