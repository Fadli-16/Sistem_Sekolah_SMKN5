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

    .preview-container {
        width: 150px;
        height: 150px;
        border: 2px dashed #ccc;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        margin-top: 0.5rem;
    }

    .preview-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .preview-placeholder {
        color: var(--text-muted);
        text-align: center;
        padding: 1rem;
    }

    .preview-placeholder i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .current-image {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Edit Inventaris</h1>
            <p class="text-muted">Ubah informasi inventaris laboratorium</p>
        </div>
        <a href="{{ route('admin.inventaris.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="form-card">
                <form action="{{ route('admin.inventaris.update', $inventaris->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nama_inventaris" class="form-label required">Nama Inventaris</label>
                        <input type="text" class="form-control @error('nama_inventaris') is-invalid @enderror" id="nama_inventaris" name="nama_inventaris" value="{{ old('nama_inventaris', $inventaris->nama_inventaris) }}" required>
                        @error('nama_inventaris')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kategori" class="form-label required">Kategori</label>
                                <input type="text" class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori" value="{{ old('kategori', $inventaris->kategori) }}" required>
                                @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Contoh: Elektronik, Perabotan, Alat Praktikum, dll.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah" class="form-label required">Jumlah</label>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah', $inventaris->jumlah) }}" min="1" required>
                                @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kondisi" class="form-label required">Kondisi</label>
                                <select class="form-select @error('kondisi') is-invalid @enderror" id="kondisi" name="kondisi" required>
                                    <option value="" disabled>Pilih Kondisi</option>
                                    <option value="Baik" {{ old('kondisi', $inventaris->kondisi) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ old('kondisi', $inventaris->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Berat" {{ old('kondisi', $inventaris->kondisi) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                                @error('kondisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lokasi" class="form-label required">Lokasi</label>
                                <select class="form-select @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" required>
                                    <option value="" disabled>Pilih Laboratorium</option>
                                    @foreach($laborList as $lab)
                                        <option value="{{ $lab->nama_labor }}" {{ old('lokasi', $inventaris->lokasi) == $lab->nama_labor ? 'selected' : '' }}>
                                            {{ $lab->nama_labor }} @if($lab->kode) ({{ $lab->kode }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Pilih laboratorium tempat inventaris ini berada</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_pengadaan" class="form-label required">Tanggal Pengadaan</label>
                        <input type="date" class="form-control @error('tanggal_pengadaan') is-invalid @enderror" id="tanggal_pengadaan" name="tanggal_pengadaan" value="{{ old('tanggal_pengadaan', $inventaris->tanggal_pengadaan) }}" required>
                        @error('tanggal_pengadaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $inventaris->deskripsi) }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label required">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="" disabled>Pilih Status</option>
                            <option value="Tersedia" {{ old('status', $inventaris->status) == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Tidak Tersedia" {{ old('status', $inventaris->status) == 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="gambar" class="form-label">Gambar</label>
                        <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept="image/*">
                        @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format: JPG, JPEG, PNG. Ukuran maks: 2MB.</div>

                        <div class="preview-container" id="preview">
                            @if($inventaris->gambar)
                                <img src="{{ asset('storage/' . $inventaris->gambar) }}" alt="{{ $inventaris->nama_inventaris }}">
                            @else
                                <div class="preview-placeholder">
                                    <i class="bi bi-image"></i>
                                    <span>Tidak ada gambar</span>
                                </div>
                            @endif
                        </div>
                        @if($inventaris->gambar)
                            <div class="current-image">
                                <i class="bi bi-check-circle-fill text-success"></i> Gambar sudah ada. Unggah yang baru untuk mengganti.
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.inventaris.index') }}" class="btn btn-light">Batal</a>
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
                        <li>Isi semua field yang bertanda <span class="text-danger">*</span> (wajib).</li>
                        <li>Nama inventaris harus jelas dan deskriptif.</li>
                        <li>Kategori menunjukkan jenis inventaris.</li>
                        <li>Jumlah harus berupa angka positif.</li>
                        <li>Pilih kondisi yang sesuai dengan keadaan inventaris.</li>
                        <li>Lokasi menunjukkan di mana inventaris ditempatkan.</li>
                        <li>Status menunjukkan ketersediaan inventaris untuk digunakan.</li>
                        <li>Gambar opsional, unggah yang baru untuk mengganti gambar yang sudah ada.</li>
                    </ul>

                    <h6 class="fw-bold">Catatan</h6>
                    <p class="mb-0">Perubahan yang dilakukan akan langsung tersimpan dalam database setelah menekan tombol Simpan Perubahan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Preview image before upload
    document.getElementById('gambar').addEventListener('change', function(e) {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';

        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        } else {
            // If no new file is selected, show the current image if it exists
            @if($inventaris->gambar)
                preview.innerHTML = `<img src="{{ asset('storage/' . $inventaris->gambar) }}" alt="{{ $inventaris->nama_inventaris }}">`;
            @else
                preview.innerHTML = `
                    <div class="preview-placeholder">
                        <i class="bi bi-image"></i>
                        <span>Tidak ada gambar</span>
                    </div>
                `;
            @endif
        }
    });
</script>
@endsection
