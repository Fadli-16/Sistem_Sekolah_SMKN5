@extends('magang.layouts.main')

@section('content')
<div class="container">
    <section class="profile-box">
        <h2 class="profile-title">Edit Profil Wakil Perusahaan</h2>

        <form action="{{ route('magang.wakil_perusahaan.profile.update') }}" method="POST" enctype="multipart/form-data" class="form-grid">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="profile-label">Nama</label>
                <input type="text" name="nama" class="input-field" value="{{ old('nama', $wakil->nama) }}" required>
            </div>

            <div class="form-group">
                <label class="profile-label">Email</label>
                <input type="email" name="email" class="input-field readonly" value="{{ $wakil->email }}" readonly>
            </div>

            <div class="form-group">
                <label class="profile-label">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" class="input-field" value="{{ old('nama_perusahaan', $wakil->nama_perusahaan) }}" required>
            </div>

            <div class="form-group">
                <label class="profile-label">No. Telepon Perusahaan</label>
                <input type="text" name="no_perusahaan" class="input-field" value="{{ old('no_perusahaan', $wakil->no_perusahaan) }}" required>
            </div>

            <div class="form-group full">
                <label class="profile-label">Alamat Perusahaan</label>
                <textarea name="alamat" class="input-field" rows="3" required>{{ old('alamat', $wakil->alamat) }}</textarea>
            </div>

            <div class="form-group full">
                <label class="profile-label">Ganti Bukti Lampiran (PDF, opsional)</label>
                <input type="file" name="bukti_lampiran" accept=".pdf" class="input-field file">
                <small class="form-note">Kosongkan jika tidak ingin mengganti.</small>
            </div>

            <div class="button-group">
                <a href="{{ route('magang.wakil_perusahaan.profile') }}" class="btn btn-outline-green">🔙 Batal</a>
                <button type="submit" class="btn btn-green">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</div>

<style>
    .container {
        max-width: 900px;
        margin: 3rem auto;
        padding: 0 1rem;
    }

    .profile-box {
        background: #fff;
        border: 2px solid #fcd34d;
        border-radius: 12px;
        padding: 2rem;
    }

    .profile-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #ea580c;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full {
        grid-column: span 2;
    }

    .profile-label {
        font-weight: 600;
        color: #9a3412;
        margin-bottom: 0.5rem;
    }

    .input-field {
        width: 100%;
        padding: 0.6rem 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        background-color: #f9fafb;
        transition: 0.3s;
    }

    .input-field:focus {
        border-color: #3b82f6;
        outline: none;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .input-field.readonly {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }

    .input-field.file {
        padding: 0.4rem 0.5rem;
        background-color: transparent;
        border: none;
    }

    .form-note {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.3rem;
    }

    .button-group {
        grid-column: span 2;
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .btn {
        padding: 0.7rem 1.8rem;
        font-weight: 600;
        border-radius: 999px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
        font-size: 0.95rem;
    }

    .btn-green {
        background-color: #16a34a;
        color: white;
        border: none;
        box-shadow: 0 4px 10px rgba(22, 163, 74, 0.3);
    }

    .btn-green:hover {
        background-color: #15803d;
        transform: scale(1.05);
    }

    .btn-outline-green {
        border: 2px solid #16a34a;
        color: #16a34a;
        background-color: transparent;
    }

    .btn-outline-green:hover {
        background-color: #f0fdf4;
    }
</style>
@endsection
