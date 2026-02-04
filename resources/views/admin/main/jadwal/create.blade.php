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
            <h1 class="page-title">Tambah Jadwal</h1>
            <p class="text-muted">Tambahkan jadwal baru untuk penggunaan laboratorium</p>
        </div>
        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="form-card">
                <form action="{{ route('admin.jadwal.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="laboratorium" class="form-label required">Laboratorium</label>
                        <select class="form-select @error('laboratorium') is-invalid @enderror" id="laboratorium" name="laboratorium" required>
                            <option value="" disabled selected>Pilih Laboratorium</option>
                            @foreach($laborList as $lab)
                                <option value="{{ $lab->kode }}" {{ old('laboratorium') == $lab->kode ? 'selected' : '' }}>
                                    {{ $lab->nama_labor }} @if($lab->kode) ({{ $lab->kode }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('laboratorium')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
    <label for="hari" class="form-label required">Hari</label>
    <select class="form-select @error('hari') is-invalid @enderror" id="hari" name="hari" required>
        <option value="" disabled selected>Pilih Hari</option>
        <option value="Senin" {{ old('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
        <option value="Selasa" {{ old('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
        <option value="Rabu" {{ old('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
        <option value="Kamis" {{ old('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
        <option value="Jumat" {{ old('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
        <option value="Sabtu" {{ old('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
        <option value="Minggu" {{ old('hari') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
    </select>
    @error('hari')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jam_mulai" class="form-label required">Jam Mulai</label>
                                <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                                @error('jam_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jam_selesai" class="form-label required">Jam Selesai</label>
                                <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                                @error('jam_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Jam selesai harus setelah jam mulai</div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Opsional. Contoh: "Kelas XI RPL 2 - Pemrograman Web"</div>
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
                        <li>Pilih laboratorium yang akan dijadwalkan.</li>
                        <li>Masukkan tanggal dan rentang waktu penggunaan.</li>
                        <li>Status "Kosong" berarti laboratorium tersedia untuk digunakan.</li>
                        <li>Status "Terpakai" berarti laboratorium sudah dijadwalkan untuk digunakan.</li>
                        <li>Keterangan dapat diisi dengan informasi tambahan seperti nama kelas dan mata pelajaran.</li>
                    </ul>

                    <h6 class="fw-bold">Catatan</h6>
                    <ul class="mb-0">
                        <li>Jadwal tidak boleh bentrok dengan jadwal yang sudah ada.</li>
                        <li>Jam selesai harus lebih besar dari jam mulai.</li>
                        <li>Prioritaskan jadwal kelas reguler sebelum kegiatan ekstrakurikuler.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
