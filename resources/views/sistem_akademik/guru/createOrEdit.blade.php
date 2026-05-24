@extends('sistem_akademik.layouts.main')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle">{{ isset($guru) ? 'Edit data guru yang sudah ada' : 'Tambahkan data guru baru ke sistem' }}</p>
        </div>
        <a href="{{ route('sistem_akademik.guru.index') }}" class="btn-secondary-app">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <div class="form-card-header">
            <h5><i class="bi bi-person-workspace me-2"></i>{{ isset($guru) ? 'Form Edit Guru' : 'Form Tambah Guru' }}</h5>
            <p>Lengkapi semua field yang diperlukan</p>
        </div>
        <div class="form-card-body">

            @if ($errors->any())
            <div class="alert alert-danger rounded-3 mb-4">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                            <li style="font-size:0.875rem">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ isset($guru) ? route('sistem_akademik.guru.update', $guru->id) : route('sistem_akademik.guru.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($guru)) @method('PUT') @endif

                {{-- Avatar Upload --}}
                <div class="avatar-upload-wrap mb-4">
                    @php
                        $currentAvatar = isset($guru) && $guru->image
                            ? asset('assets/profile/' . ltrim($guru->image,'/'))
                            : asset('assets/profile/default.png');
                    @endphp
                    <img id="avatarPreview" src="{{ $currentAvatar }}" alt="avatar" class="avatar-preview-lg">
                    <div>
                        <label class="form-label mb-1">Foto Profil</label>
                        <input type="file" id="image" name="image" accept="image/*" class="form-control form-control-sm">
                        <div class="text-muted mt-1" style="font-size:0.75rem"><i class="bi bi-info-circle me-1"></i>Foto akan dikompres otomatis ke ≤500KB · Format: JPG, PNG, GIF</div>
                    </div>
                </div>

                <p class="form-section-title">Informasi Akun</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                               id="nama" name="nama" value="{{ old('nama', $guru->user->nama ?? '') }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $guru->user->email ?? '') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nip') is-invalid @enderror"
                               id="nip" name="nip" value="{{ old('nip', $guru->nip ?? '') }}" required>
                        @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">
                            Password
                            <small class="text-muted fw-normal">({{ isset($guru) ? 'kosongkan jika tidak diubah' : 'wajib diisi' }})</small>
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" {{ isset($guru) ? '' : 'required' }}>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <p class="form-section-title mt-4">Informasi Akademik</p>
                <div class="row g-3">
                    @if(isset($guru) && $guru->waliKelasDi)
                    <div class="col-md-6">
                        <label for="kelas" class="form-label">Kelas (Wali Kelas)</label>
                        <input type="text" class="form-control" id="kelas" name="kelas"
                               value="{{ old('kelas', $guru->waliKelasDi->nama_kelas ?? '') }}" readonly style="background-color: #f8fafc;">
                        <small class="text-muted">Status: Wali Kelas aktif</small>
                    </div>
                    @endif
                    <div class="{{ (isset($guru) && $guru->waliKelasDi) ? 'col-md-6' : 'col-md-12' }}">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <input type="text" class="form-control" id="jurusan" name="jurusan"
                               value="{{ old('jurusan', $guru->jurusan ?? '') }}">
                    </div>
                </div>

                <p class="form-section-title mt-4">Informasi Personal</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select">
                            <option value="">Pilih...</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan"  {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'Perempuan'  ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control"
                               value="{{ old('tanggal_lahir', $guru->tanggal_lahir ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="agama" class="form-label">Agama</label>
                        <input type="text" id="agama" name="agama" class="form-control"
                               value="{{ old('agama', $guru->agama ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="no_hp" class="form-label">Nomor HP</label>
                        <input type="text" id="no_hp" name="no_hp" class="form-control"
                               value="{{ old('no_hp', $guru->no_hp ?? '') }}">
                    </div>
                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="3">{{ old('alamat', $guru->alamat ?? '') }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-primary-app">
                        <i class="bi bi-{{ isset($guru) ? 'save' : 'plus-lg' }}"></i>
                        {{ isset($guru) ? 'Simpan Perubahan' : 'Tambah Guru' }}
                    </button>
                    <a href="{{ route('sistem_akademik.guru.index') }}" class="btn-secondary-app">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageInput    = document.getElementById('image');
        const avatarPreview = document.getElementById('avatarPreview');
        const defaultAvatar = "{{ asset('assets/profile/default.png') }}";

        if (imageInput && avatarPreview) {
            imageInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;
                // No client-side size limit — server will compress automatically
                const reader = new FileReader();
                reader.onload = e => avatarPreview.src = e.target.result;
                reader.readAsDataURL(file);
            });
            avatarPreview.addEventListener('error', function () { this.onerror = null; this.src = defaultAvatar; });
        }
    });
</script>
@endsection