@extends('sistem_akademik.layouts.main')

@section('css')
@include('sistem_akademik.layouts.css')
@endsection

@section('content')
<div class="container profile-page mt-4 mb-4">
    @php
    $user = auth()->user();
    $siswa = $user->siswa ?? null;
    $guru = $user->guru ?? null;
    $admin = $user->adminProfile ?? null;
    $image = $siswa->image ?? $guru->image ?? $admin->image ?? null;
    $imageUrl = $image ? asset('assets/profile/' . $image) : asset('assets/profile/default.png');
    $identifier = $user->nis_nip ?? ($siswa->nis ?? ($guru->nip ?? '-'));
    @endphp

    <div class="profile-card">
        <div class="card-header">
            <h3 class="mb-0">Edit Profile</h3>
        </div>

        <!-- Top row: avatar + basic info -->
        <div class="card-body top">
            <div class="profile-photo-container" id="photoContainer">
                <form id="photoForm"
                    action="{{ route('sistem_akademik.updatePhoto') }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <!-- FOTO (klik langsung) -->
                    <img id="avatarPreview" class="avatar" src="{{ $imageUrl }}" alt="Foto profil {{ $user->nama }}">

                    <!-- Overlay hanya indikator -->
                    <div class="overlay">
                        <i class="fas fa-camera"></i>
                        <span>Ganti Foto</span>
                    </div>

                    <!-- input DISMBUNYIKAN -->
                    <input id="photoInput" type="file" name="image" accept="image/*" hidden>
                </form>
            </div>

            <div class="profile-info">
                <h2>{{ $user->nama }}</h2>
                <div class="identifier">{{ $identifier }}</div>
                <div class="role">
                    @if($user->role === 'siswa')
                    {{ $siswa ? ($siswa->kelas . ' - ' . $siswa->jurusan) : 'Siswa' }}
                    @elseif($user->role === 'guru')
                    {{ $guru ? ($guru->kelas . ' - ' . $guru->jurusan) : 'Guru' }}
                    @else
                    Administrator
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body form-area">
            {{-- Profile data update --}}
            <form action="{{ route('sistem_akademik.updateProfile') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 form-equal">
                    <div class="col-md-6">
                        <label class="form-label">NIS / NIP</label>
                        <input type="text" class="form-control" value="{{ old('nis_nip', $user->nis_nip) }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input name="nama" type="text" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="Laki-laki" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? $guru->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? $guru->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input name="tanggal_lahir" type="date" class="form-control"
                            value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ?? $guru->tanggal_lahir ?? $admin->tanggal_lahir ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Agama</label>
                        <input name="agama" type="text" class="form-control" value="{{ old('agama', $siswa->agama ?? $guru->agama ?? $admin->agama ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">No HP</label>
                        <input name="no_hp" type="text" class="form-control" value="{{ old('no_hp', $siswa->no_hp ?? $guru->no_hp ?? $admin->no_hp ?? '') }}">
                    </div>

                    <div class="col-6">
                        <label class="form-label">Alamat</label>
                        <input name="alamat" type="text" class="form-control" value="{{ old('alamat', $siswa->alamat ?? $guru->alamat ?? $admin->alamat ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jurusan</label>
                        <input name="jurusan" type="text" class="form-control" value="{{ old('jurusan', $admin->jurusan ?? '') }}">
                    </div>

                </div>

                <div class="profile-actions mt-3">
                    <button type="submit" class="btn btn-primary-custom">Save Changes</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary-custom">Batal</a>
                </div>
            </form>

            <hr class="mt-4 mb-4">

            {{-- Password update --}}
            <form action="{{ route('sistem_akademik.updatePassword') }}" method="POST">
                @csrf
                @method('PATCH')

                <h5>Ubah Password</h5>

                <div class="row g-3 equals-cols">
                    <div class="col-md-4">
                        <label class="form-label">Password Saat Ini</label>
                        <div class="input-group">
                            <input name="current_password" type="password" class="form-control password-field" required>
                            <span class="input-group-text password-toggle"><i class="fa fa-eye"></i></span>
                        </div>
                        @error('current_password') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Password Baru</label>
                        <div class="input-group">
                            <input name="password" type="password" class="form-control password-field" required>
                            <span class="input-group-text password-toggle"><i class="fa fa-eye"></i></span>
                        </div>
                        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <input name="password_confirmation" type="password" class="form-control password-field" required>
                            <span class="input-group-text password-toggle"><i class="fa fa-eye"></i></span>
                        </div>
                    </div>
                </div>

                <div class="profile-actions mt-3">
                    <button type="submit" class="btn btn-warning">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const photoContainer = document.getElementById('photoContainer');
        const photoInput = document.getElementById('photoInput');
        const photoForm = document.getElementById('photoForm');
        const avatar = document.getElementById('avatarPreview');

        if (!photoContainer || !photoInput || !photoForm || !avatar) return;

        // Klik container -> buka file picker
        photoContainer.addEventListener('click', function(e) {
            e.stopPropagation();
            photoInput.click();
            photoContainer.classList.add('editing');
        });

        // Keyboard accessibility
        photoContainer.addEventListener('keydown', function(ev) {
            if (ev.key === 'Enter' || ev.key === ' ') {
                ev.preventDefault();
                photoInput.click();
                photoContainer.classList.add('editing');
            }
        });

        photoInput.addEventListener('change', function() {
            const file = this.files && this.files[0];
            if (!file) {
                photoContainer.classList.remove('editing');
                return;
            }

            // preview lokal segera
            const reader = new FileReader();
            reader.onload = function(ev) {
                avatar.src = ev.target.result;
            };
            reader.readAsDataURL(file);

            // Siapkan FormData (tambahkan _method supaya Laravel menganggap ini PATCH)
            const fd = new FormData();
            fd.append('image', file);
            fd.append('_method', 'PATCH'); // <-- penting: override method

            // ambil CSRF token dari meta (pastikan ada di layout utama)
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const token = tokenEl ? tokenEl.getAttribute('content') : '{{ csrf_token() }}';

            // Kirim sebagai POST (Laravel akan mem-override menjadi PATCH karena _method)
            fetch(photoForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: fd,
                    credentials: 'same-origin'
                })
                .then(res => res.json().catch(() => ({
                    success: false,
                    message: 'Invalid response from server'
                })))
                .then(json => {
                    if (json && json.success) {
                        if (json.url) avatar.src = json.url;
                        photoContainer.classList.remove('editing');
                    } else {
                        alert(json.message || 'Gagal mengunggah foto. Silakan refresh halaman dan coba lagi.');
                        window.location.reload();
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan saat mengunggah foto.');
                    window.location.reload();
                });
        });

        // Jika user membatalkan file dialog, buang class editing
        photoInput.addEventListener('blur', function() {
            setTimeout(() => photoContainer.classList.remove('editing'), 250);
        });
    });

    document.addEventListener('click', function(e) {
        const toggle = e.target.closest('.password-toggle');
        if (!toggle) return;
        const group = toggle.closest('.input-group');
        if (!group) return;
        const input = group.querySelector('.password-field');
        const icon = toggle.querySelector('i');

        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
@endsection