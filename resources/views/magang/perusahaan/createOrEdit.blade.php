@extends('magang.layouts.main')

@section('css')
<style>
    body {
        background-color: #f5f7fa;
    }

    .page-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 30px 15px;
    }

    .form-container {
        max-width: 700px;
        width: 100%;
        background-color: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #333;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #ccc;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.2);
    }

    .submit-btn {
        background-color: #3498db;
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .submit-btn:hover {
        background-color: #2c80b4;
    }

    .btn-secondary {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
    }

    h4.mb-0 {
        font-weight: 700;
        color: #2c3e50;
    }
</style>
@endsection

@section('content')
<div class="page-wrapper">
    <div class="form-container">
        <h4 class="mb-4">{{ isset($wakil) ? 'Edit Perusahaan' : 'Tambah Perusahaan' }}</h4>

        <form action="{{ isset($wakil) ? route('magang.perusahaan.update', $wakil->id) : route('magang.perusahaan.store') }}" method="POST">
            @csrf
            @if(isset($wakil))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $wakil->nama ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $wakil->email ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" {{ isset($wakil) ? '' : 'required' }}>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" {{ isset($wakil) ? '' : 'required' }}>
            </div>

            <div class="mb-3">
                <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan', $wakil->nama_perusahaan ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" required>{{ old('alamat', $wakil->alamat ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="no_perusahaan" class="form-label">No. Perusahaan</label>
                <input type="text" name="no_perusahaan" class="form-control" value="{{ old('no_perusahaan', $wakil->no_perusahaan ?? '') }}" required>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('magang.perusahaan.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="submit-btn">{{ isset($wakil) ? 'Update' : 'Simpan' }}</button>
            </div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        </form>
    </div>
</div>
@endsection
