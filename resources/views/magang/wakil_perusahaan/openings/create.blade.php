@extends('magang.layouts.main')

@section('css')
<style>
    .form-container {
        background-color: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
    }
    
    .form-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .form-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0;
        color: var(--primary);
    }
    
    .form-body {
        padding: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .form-subtitle {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }
    
    .form-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }
    
    .submit-btn {
        background-color: var(--primary);
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        border-radius: var(--radius);
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .submit-btn:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
    }
    
    .submit-btn i {
        margin-right: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <div class="form-header">
                    <h1 class="form-title">{{ isset($opening) ? 'Edit Program Magang' : 'Tambah Program Magang Baru' }}</h1>
                </div>

                <form action="{{ isset($opening) ? route('magang.wakil_perusahaan.openings.update', $opening->id) : route('magang.wakil_perusahaan.openings.store') }}" method="POST">
                    @csrf
                    @if(isset($opening))
                        @method('PUT')
                    @endif
                    
                    <div class="form-body">
                        <p class="form-subtitle">Isi detail program magang yang akan dibuka untuk siswa-siswi SMK.</p>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="posisi" class="form-label">Posisi/Judul Program Magang</label>
                                <input type="text" class="form-control @error('posisi') is-invalid @enderror" id="posisi" name="posisi" value="{{ old('posisi', isset($opening) ? $opening->posisi : '') }}" required>
                                @error('posisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Program</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi', isset($opening) ? $opening->deskripsi : '') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="keahlian" class="form-label">Keahlian yang Dibutuhkan</label>
                                <input type="text" class="form-control @error('keahlian') is-invalid @enderror" id="keahlian" name="keahlian" value="{{ old('keahlian', isset($opening) ? $opening->keahlian : '') }}" placeholder="Contoh: HTML, CSS, JavaScript, Desain Grafis">
                                <div class="form-text">Pisahkan keahlian dengan tanda koma (,)</div>
                                @error('keahlian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="jumlah_posisi" class="form-label">Jumlah Posisi</label>
                                <input type="number" class="form-control @error('jumlah_posisi') is-invalid @enderror" id="jumlah_posisi" name="jumlah_posisi" value="{{ old('jumlah_posisi', isset($opening) ? $opening->jumlah_posisi : '1') }}" min="1" required>
                                @error('jumlah_posisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', isset($opening) ? $opening->tanggal_mulai : '') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', isset($opening) ? $opening->tanggal_selesai : '') }}" required>
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="Aktif" {{ old('status', isset($opening) ? $opening->status : '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ old('status', isset($opening) ? $opening->status : '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-footer">
                        <a href="{{ route('magang.wakil_perusahaan.openings.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="submit-btn">
                            <i class="bi bi-save"></i> {{ isset($opening) ? 'Update Program' : 'Simpan Program' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection