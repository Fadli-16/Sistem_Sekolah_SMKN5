@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    .avatar-preview {
        width: 96px;
        height: 96px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
        display: block;
    }

    .form-actions {
        margin-top: 1rem;
        display: flex;
        gap: .5rem;
    }
</style>
@endsection

@section('content')
<div class="container mt-4 mb-4">
    <h2>{{ isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa' }}</h2>

    <div class="card p-4">
        <form action="{{ isset($siswa) ? route('sistem_akademik.siswa.update', $siswa->id) : route('sistem_akademik.siswa.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($siswa)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Foto</label><br>
                    @php
                    $avatar = isset($siswa) && $siswa->image ? asset('assets/profile/' . ltrim($siswa->image,'/')) : asset('assets/profile/default.png');
                    @endphp
                    <img id="avatarPreview" src="{{ $avatar }}" alt="avatar" class="avatar-preview mb-2">
                    <input type="file" name="image" accept="image/*" class="form-control form-control-sm" id="imageInput">
                    <small class="text-muted">Maksimal 500KB (sistem akan mengompres jika perlu)</small>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $siswa->user->nama ?? '') }}" required>

                    <label class="form-label mt-2">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $siswa->user->email ?? '') }}" required>

                    <label class="form-label mt-2">Password {{ isset($siswa) ? '(isi jika ingin mengganti)' : '' }}</label>
                    <input type="password" name="password" class="form-control" {{ isset($siswa) ? '' : 'required' }}>
                </div>

                <div class="col-md-4">
                    <label class="form-label">NIS</label>
                    <input type="text" name="nis" class="form-control" value="{{ old('nis', $siswa->nis ?? '') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kelas</label>
                    <select name="kelas_id" class="form-select" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ (string) old('kelas_id', $siswa->kelas_id ?? '') === (string) $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }} — {{ $k->jurusan }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ?? '') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
                </div>

                <div class="col-md-3">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $siswa->no_hp ?? '') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control">
                        <option value="">— Pilih —</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Agama</label>
                    <input type="text" name="agama" class="form-control" value="{{ old('agama', $siswa->agama ?? '') }}">
                </div>
            </div>

            <div class="form-actions mt-3">
                <button type="submit" class="btn btn-primary">{{ isset($siswa) ? 'Simpan Perubahan' : 'Tambah Siswa' }}</button>
                <a href="{{ route('sistem_akademik.siswa.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@section('script')
<script>
    const avatarPreview = document.getElementById('avatarPreview');
    avatarPreview?.addEventListener('error', function() {
        this.src = "{{ asset('assets/profile/default.png') }}";
    });

    document.getElementById('imageInput')?.addEventListener('change', function(e) {
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            avatarPreview.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection

@endsection