@extends('admin.layouts.main')

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
            <h1 class="page-title">Tambah Laboratorium</h1>
            <p class="text-muted">Tambahkan laboratorium baru ke sistem</p>
        </div>
        <a href="{{ route('admin.labor.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="form-card">
                <form action="{{ route('admin.labor.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="nama_labor" class="form-label required">Nama Laboratorium</label>
                        <input type="text" class="form-control @error('nama_labor') is-invalid @enderror" id="nama_labor" name="nama_labor" value="{{ old('nama_labor') }}" required>
                        @error('nama_labor')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kode" class="form-label">Kode Laboratorium</label>
                        <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode" name="kode" value="{{ old('kode') }}">
                        @error('kode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Contoh: TKJ, RPL, MM. Opsional tetapi direkomendasikan untuk pengelolaan jadwal</div>
                    </div>

                    <div class="mb-3">
    <label for="penanggung_jawab" class="form-label">Penanggung Jawab</label>
    <input type="text" name="penanggung_jawab" id="penanggung_jawab" class="form-control" value="{{ old('penanggung_jawab', $labor->penanggung_jawab ?? '') }}">
</div>

<div class="mb-3">
    <label for="teknisi" class="form-label">Teknisi</label>
    <input type="text" name="teknisi" id="teknisi" class="form-control" value="{{ old('teknisi', $labor->teknisi ?? '') }}">
</div>

                    <div class="form-group">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Opsional. Berisi deskripsi singkat tentang laboratorium</div>
                    </div>

                <div class="mb-3">
                    <label for="foto" class="form-label">Foto Laboratorium</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                    @error('foto')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-save me-1"></i> Simpan
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
                        <li>Nama Laboratorium harus diisi dengan nama lengkap yang deskriptif</li>
                        <li>Kode Laboratorium sebaiknya diisi dengan singkatan yang mudah dikenali</li>
                        <li>Deskripsi dapat diisi dengan informasi tambahan tentang laboratorium</li>
                    </ul>

                    <h6 class="fw-bold">Catatan</h6>
                    <ul class="mb-0">
                        <li>Laboratorium yang sudah dibuat akan muncul dalam pilihan jadwal</li>
                        <li>Pastikan kode laboratorium unik untuk menghindari konflik</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
