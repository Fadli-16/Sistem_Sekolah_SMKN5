@extends('sistem_akademik.layouts.main')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Menyesuaikan Select2 dengan .form-select custom dari sistem-akademik.css */
    .select2-container--bootstrap-5 .select2-selection {
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius);
        padding: 0.55rem 0.875rem;
        font-size: 0.875rem;
        transition: var(--transition);
        background-color: var(--bg-card);
        min-height: auto;
        display: flex;
        align-items: center;
    }
    
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(249,115,22,0.12);
    }
    
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding: 0;
        line-height: normal;
        color: var(--text-dark);
    }
    
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 0.875rem;
    }
</style>
@endsection

@php
    $jurusans = $kelas->pluck('jurusan')->unique()->filter()->values();
    $kelasByJurusan = [];
    foreach($kelas as $k) {
        if ($k->jurusan) {
            $kelasByJurusan[$k->jurusan][] = [
                'id' => $k->id,
                'nama' => $k->nama_kelas,
                'tahun' => $k->tahun_ajaran ?? 'Tidak diset'
            ];
        }
    }
    $selectedJurusan = old('jurusan', $siswa->jurusan ?? '');
    $selectedKelas = old('kelas_id', $siswa->kelas_id ?? '');
@endphp

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa' }}</h1>
            <p class="page-subtitle">{{ isset($siswa) ? 'Perbarui data siswa' : 'Daftarkan siswa baru ke sistem' }}</p>
        </div>
        <a href="{{ route('sistem_akademik.siswa.index') }}" class="btn-secondary-app">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <div class="form-card-header">
            <h5><i class="bi bi-people-fill me-2"></i>{{ isset($siswa) ? 'Form Edit Siswa' : 'Form Tambah Siswa' }}</h5>
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

            <form action="{{ isset($siswa) ? route('sistem_akademik.siswa.update', $siswa->id) : route('sistem_akademik.siswa.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($siswa)) @method('PUT') @endif

                {{-- Avatar --}}
                <div class="avatar-upload-wrap mb-4">
                    @php
                        $currentAvatar = isset($siswa) && $siswa->image
                            ? asset('assets/profile/' . ltrim($siswa->image,'/'))
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
                        <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nis') is-invalid @enderror"
                               id="nis" name="nis" value="{{ old('nis', $siswa->nis ?? '') }}" required>
                        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <small class="text-muted fw-normal">(opsional, otomatis jika kosong)</small></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $siswa->user->email ?? '') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                               id="nama" name="nama" value="{{ old('nama', $siswa->user->nama ?? '') }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">
                            Password
                            <small class="text-muted fw-normal">(opsional, otomatis jika kosong)</small>
                        </label>
                        <div class="input-group has-validation">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password">
                            <span class="input-group-text toggle-password" style="cursor: pointer;">
                                <i class="bi bi-eye"></i>
                            </span>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <p class="form-section-title mt-4">Informasi Akademik</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                        <select id="jurusan" name="jurusan" class="form-select select2-basic" required>
                            <option value="">Pilih Jurusan...</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j }}" {{ $selectedJurusan == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="kelas_id" class="form-label">Kelas (Thn Ajaran) <span class="text-danger">*</span></label>
                        <select name="kelas_id" id="kelas_id" class="form-select select2-basic @error('kelas_id') is-invalid @enderror" required>
                            <option value="">Pilih Jurusan Terlebih Dahulu</option>
                            {{-- Diisi via JS --}}
                        </select>
                        @error('kelas_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                            <option value="">Pilih...</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan"  {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'Perempuan'  ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <p class="form-section-title mt-4">Informasi Personal</p>
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror"
                               value="{{ old('tempat_lahir', $siswa->tempat_lahir ?? '') }}">
                        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                               value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ?? '') }}">
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="agama" class="form-label">Agama</label>
                        <input type="text" id="agama" name="agama" class="form-control @error('agama') is-invalid @enderror"
                               value="{{ old('agama', $siswa->agama ?? '') }}">
                        @error('agama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="no_hp" class="form-label">Nomor HP</label>
                        <input type="text" id="no_hp" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                               value="{{ old('no_hp', $siswa->no_hp ?? '') }}">
                        @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                        <input type="number" id="tahun_masuk" name="tahun_masuk" class="form-control @error('tahun_masuk') is-invalid @enderror"
                               value="{{ old('tahun_masuk', $siswa->tahun_masuk ?? '') }}" placeholder="Contoh: 2023">
                        @error('tahun_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-primary-app">
                        <i class="bi bi-{{ isset($siswa) ? 'save' : 'plus-lg' }}"></i>
                        {{ isset($siswa) ? 'Simpan Perubahan' : 'Tambah Siswa' }}
                    </button>
                    <a href="{{ route('sistem_akademik.siswa.index') }}" class="btn-secondary-app">Batal</a>
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

        const togglePassword = document.querySelector('.toggle-password');
        const password = document.querySelector('#password');
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function (e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        }

        // Select2 & Filter Kelas
        const kelasData = @json($kelasByJurusan);
        const selectedKelas = "{{ $selectedKelas }}";
        
        $('.select2-basic').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        const jurusanSelect = $('#jurusan');
        const kelasSelect = $('#kelas_id');

        function populateKelas(jurusan, selectedId = null) {
            kelasSelect.empty();
            if (!jurusan || !kelasData[jurusan]) {
                kelasSelect.append('<option value="">Pilih Jurusan Terlebih Dahulu</option>');
                return;
            }
            
            kelasSelect.append('<option value="">Pilih Kelas...</option>');
            kelasData[jurusan].forEach(function(k) {
                const text = k.nama + ' — TA. ' + k.tahun;
                const isSelected = (String(k.id) === String(selectedId)) ? 'selected' : '';
                kelasSelect.append(`<option value="${k.id}" ${isSelected}>${text}</option>`);
            });
        }

        jurusanSelect.on('change', function() {
            populateKelas($(this).val());
        });

        // Trigger on load for edit mode
        if (jurusanSelect.val()) {
            populateKelas(jurusanSelect.val(), selectedKelas);
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection