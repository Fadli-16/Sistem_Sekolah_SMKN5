@extends('siswa.layouts.main')

@section('css')
<style>
    .form-card {
        background-color: var(--bg-light);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        color: var(--text-dark);
    }

    .form-control {
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: var(--radius);
        padding: 0.75rem 1rem;
        transition: var(--transition);
        background-color: var(--bg-light);
    }

    .form-control:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 0.25rem rgba(78, 205, 196, 0.25);
    }

    .form-text {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .required:after {
        content: "*";
        color: #dc3545;
        margin-left: 3px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Buat Laporan Kerusakan</h1>
            <p class="text-muted">Laporkan kerusakan alat atau perangkat laboratorium</p>
        </div>
        <a href="{{ route('siswa.laporan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="form-card">
                <form action="{{ route('siswa.laporan.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="nama_alat" class="form-label required">Nama Alat/Perangkat</label>
                        <input type="text" class="form-control @error('nama_alat') is-invalid @enderror" id="nama_alat" name="nama_alat" value="{{ old('nama_alat') }}" required>
                        @error('nama_alat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="lokasi" class="form-label required">Lokasi Laboratorium</label>
                        <select class="form-select @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" required>
                            <option value="" disabled selected>Pilih Laboratorium</option>
                            @foreach($laborList as $lab)
                                <option value="{{ $lab->nama_labor }}" {{ old('lokasi') == $lab->nama_labor ? 'selected' : '' }}>
                                    {{ $lab->nama_labor }} @if($lab->kode) ({{ $lab->kode }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="deskripsi_kerusakan" class="form-label required">Deskripsi Kerusakan</label>
                        <textarea class="form-control @error('deskripsi_kerusakan') is-invalid @enderror" id="deskripsi_kerusakan" name="deskripsi_kerusakan" rows="5" required>{{ old('deskripsi_kerusakan') }}</textarea>
                        @error('deskripsi_kerusakan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Jelaskan dengan detail kerusakan yang terjadi agar dapat ditindaklanjuti dengan tepat</div>
                    </div>

                    <input type="hidden" name="nama_pelapor" value="{{ Auth::user()->nama }}">
                    <input type="hidden" name="tanggal_laporan" value="{{ date('Y-m-d') }}">

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-send me-1"></i> Kirim Laporan
                        </button>
                        <button type="reset" class="btn btn-light">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i> Informasi</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">Panduan Pengisian</h6>
                    <ul class="mb-4">
                        <li>Masukkan nama perangkat atau alat yang mengalami kerusakan</li>
                        <li>Pilih lokasi laboratorium tempat perangkat berada</li>
                        <li>Berikan deskripsi kerusakan secara detail dan jelas</li>
                    </ul>

                    <h6 class="fw-bold">Catatan</h6>
                    <ul class="mb-0">
                        <li>Laporan Anda akan diverifikasi oleh admin laboratorium</li>
                        <li>Status laporan dapat dipantau pada halaman daftar laporan</li>
                        <li>Mohon untuk tidak mengirim laporan duplikat untuk masalah yang sama</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
