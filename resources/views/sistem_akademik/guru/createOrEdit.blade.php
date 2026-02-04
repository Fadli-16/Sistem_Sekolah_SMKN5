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
    <h2>{{ isset($guru) ? 'Edit Guru' : 'Tambah Guru' }}</h2>
    <div class="card p-4">
        <form
            action="{{ isset($guru) 
        ? route('sistem_akademik.guru.update', $guru->id) 
        : route('sistem_akademik.guru.store') }}"
            method="POST">

            @csrf
            @if(isset($guru))
            @method('PUT')
            @endif

            <!-- Nama -->
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text"
                    class="form-control"
                    id="nama"
                    name="nama"
                    value="{{ old('nama', $guru->user->nama ?? '') }}"
                    required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    value="{{ old('email', $guru->user->email ?? '') }}"
                    required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="{{ isset($guru) ? 'Isi untuk mengubah password' : '' }}">
            </div>

            <!-- NIP -->
            <div class="mb-3">
                <label for="nip" class="form-label">NIP</label>
                <input type="text"
                    class="form-control"
                    id="nip"
                    name="nip"
                    value="{{ old('nip', $guru->nip ?? '') }}"
                    required>
            </div>

            <!-- Kelas -->
            <div class="mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <input type="text"
                    class="form-control"
                    id="kelas"
                    name="kelas"
                    value="{{ old('kelas', $guru->kelas ?? '') }}"
                    required>
            </div>

            <!-- Jurusan -->
            <div class="mb-3">
                <label for="jurusan" class="form-label">Jurusan</label>
                <input type="text"
                    class="form-control"
                    id="jurusan"
                    name="jurusan"
                    value="{{ old('jurusan', $guru->jurusan ?? '') }}"
                    required>
            </div>

            <!-- Tanggal Lahir -->
            <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                <input type="date"
                    class="form-control"
                    id="tanggal_lahir"
                    name="tanggal_lahir"
                    value="{{ old('tanggal_lahir', $guru->tanggal_lahir ?? '') }}"
                    required>
            </div>

            <!-- Alamat -->
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control"
                    id="alamat"
                    name="alamat"
                    rows="3"
                    required>{{ old('alamat', $guru->alamat ?? '') }}</textarea>
            </div>

            <!-- No HP -->
            <div class="mb-3">
                <label for="no_hp" class="form-label">Nomor HP</label>
                <input type="text"
                    class="form-control"
                    id="no_hp"
                    name="no_hp"
                    value="{{ old('no_hp', $guru->no_hp ?? '') }}"
                    required>
            </div>

            <button type="submit" class="px-2 py-1 rounded-3">Simpan</button>
        </form>
    </div>
</div>
@endsection