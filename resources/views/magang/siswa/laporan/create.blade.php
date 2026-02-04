@extends('magang.layouts.main')

@section('css')
<style>
    .form-section {
        background-color: #fff;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-section-title {
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
    }

    .guidelines {
        background-color: #f8f9fa;
        border-left: 4px solid var(--primary);
        border-radius: var(--radius);
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">{{ $header }}</h1>
        <a href="{{ route('magang.siswa.laporan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('magang.siswa.laporan.store') }}" method="POST">
                @csrf

                <div class="form-section">
                    <h4 class="form-section-title">Informasi Dasar</h4>

                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Laporan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Contoh: Laporan Harian #{{ $nextWeek }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="minggu_ke" class="form-label">Hari Ke <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('minggu_ke') is-invalid @enderror" id="minggu_ke" name="minggu_ke" value="{{ old('minggu_ke', $nextWeek) }}" min="1" required>
                                @error('minggu_ke')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Kegiatan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="form-section-title">Isi Laporan</h4>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="10" required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Tuliskan aktivitasmu secara rinci, tantangan yang dihadapi, solusi, dan pembelajaran baru.</div>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="form-section-title">Status Laporan</h4>

                    <div class="mb-3">
                        <label class="form-label">Pilih Status <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status_draft" value="draft" {{ old('status') == 'draft' ? 'checked' : '' }} checked>
                            <label class="form-check-label" for="status_draft">
                                <span class="badge bg-secondary">Draft</span> - Simpan sebagai draft
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="status" id="status_submitted" value="submitted" {{ old('status') == 'submitted' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_submitted">
                                <span class="badge bg-warning text-dark">Submit</span> - Kirim untuk di-review
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <a href="{{ route('magang.siswa.laporan.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Laporan</button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-info-circle me-1"></i> Panduan Membuat Laporan
                </h4>

                <div class="guidelines">
                    <p><strong>Isi laporan harian:</strong></p>
                    <ul class="mb-3">
                        <li>Uraian kegiatan</li>
                        <li>Masalah & Solusi</li>
                        <li>Keterampilan yang dipelajari</li>
                        <li>Refleksi pribadi</li>
                    </ul>

                    <p><strong>Tips:</strong></p>
                    <ul class="mb-0">
                        <li>Tulis ringkas & sistematis</li>
                        <li>Gunakan bahasa yang sopan dan profesional</li>
                        <li>Jangan copy-paste dari laporan sebelumnya</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <h5>Status Laporan</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><span class="badge bg-secondary">Draft</span> - Belum dikirim</li>
                        <li class="mb-2"><span class="badge bg-warning text-dark">Submitted</span> - Menunggu review</li>
                        <li class="mb-2"><span class="badge bg-success">Approved</span> - Disetujui</li>
                        <li class="mb-2"><span class="badge bg-danger">Rejected</span> - Perlu revisi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
