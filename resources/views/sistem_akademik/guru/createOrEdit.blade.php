@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    .avatar-preview {
        width: 96px;
        height: 96px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
        display: inline-block;
    }

    .photo-input-wrap {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-bottom: 1rem;
    }

    .btn-save {
        background-color: #004080;
        color: #fff;
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
    }

    .btn-save:hover {
        background-color: #00305a;
    }

    .field-row {
        margin-bottom: 0.85rem;
    }
</style>
@endsection

@section('content')
<div class="container mt-4 mb-4">
    <h1 class="page-title">{{ $header }}</h1>
    <div class="card p-4">
        <form
            action="{{ isset($guru) ? route('sistem_akademik.guru.update', $guru->id) : route('sistem_akademik.guru.store') }}"
            method="POST"
            enctype="multipart/form-data">
            @csrf
            @if(isset($guru)) @method('PUT') @endif

            {{-- Photo preview + upload --}}
            <div class="photo-input-wrap">
                @php
                $currentAvatar = isset($guru) && $guru->image ? asset('assets/profile/' . ltrim($guru->image,'/')) : asset('assets/profile/default.png');
                @endphp
                <img id="avatarPreview" src="{{ $currentAvatar }}" alt="avatar" class="avatar-preview">
                <div>
                    <label class="form-label">Foto Profil</label><br>
                    <input type="file" id="image" name="image" accept="image/*">
                    <div class="small text-muted mt-1">Maks 500kb. Format: jpg, png.</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 field-row">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $guru->user->nama ?? '') }}" required>
                </div>

                <div class="col-md-6 field-row">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $guru->user->email ?? '') }}" required>
                </div>

                <div class="col-md-6 field-row">
                    <label for="password" class="form-label">Password <small class="text-muted">({{ isset($guru) ? 'isi untuk mengubah' : 'wajib' }})</small></label>
                    <input type="password" class="form-control" id="password" name="password" {{ isset($guru) ? '' : 'required' }}>
                </div>

                <div class="col-md-6 field-row">
                    <label for="nip" class="form-label">NIP</label>
                    <input type="text" class="form-control" id="nip" name="nip" value="{{ old('nip', $guru->nip ?? '') }}" required>
                </div>

                <div class="col-md-6 field-row">
                    <label for="kelas" class="form-label">Kelas</label>
                    <input type="text" class="form-control" id="kelas" name="kelas" value="{{ old('kelas', $guru->kelas ?? '') }}" required>
                </div>

                <div class="col-md-6 field-row">
                    <label for="jurusan" class="form-label">Jurusan</label>
                    <input type="text" class="form-control" id="jurusan" name="jurusan" value="{{ old('jurusan', $guru->jurusan ?? '') }}" required>
                </div>

                <div class="col-md-6 field-row">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                        <option value="">Pilih...</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="col-md-6 field-row">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $guru->tanggal_lahir ?? '') }}" required>
                </div>

                <div class="col-md-6 field-row">
                    <label for="agama" class="form-label">Agama</label>
                    <input type="text" id="agama" name="agama" class="form-control" value="{{ old('agama', $guru->agama ?? '') }}" required>
                </div>

                <div class="col-md-12 field-row">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3" required>{{ old('alamat', $guru->alamat ?? '') }}</textarea>
                </div>

                <div class="col-md-6 field-row">
                    <label for="no_hp" class="form-label">Nomor HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control" value="{{ old('no_hp', $guru->no_hp ?? '') }}" required>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn-save">Simpan</button>
                <a href="{{ route('sistem_akademik.guru.index') }}" class="btn btn-secondary ms-2">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const avatarPreview = document.getElementById('avatarPreview');
        const defaultAvatar = "{{ asset('assets/profile/default.png') }}";

        if (imageInput && avatarPreview) {
            imageInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });

            avatarPreview.addEventListener('error', function() {
                this.onerror = null;
                this.src = defaultAvatar;
            });

            if (!avatarPreview.getAttribute('src')) avatarPreview.src = defaultAvatar;
        }
    });
</script>
@endsection