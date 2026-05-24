@extends('admin.layouts.superadmin')

@section('title', 'Import Users - Super Admin')
@section('page-title', 'Import Users')

@section('styles')
<style>
    .template-card {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 1.2rem;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }
    .template-card:hover {
        border-color: #3498db;
        background: #edf4fd;
    }
    .template-card .template-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    .column-table th, .column-table td {
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem;
    }
    .required-badge {
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        padding: 1px 6px;
        border-radius: 10px;
    }
    .optional-badge {
        background: #6b7280;
        color: white;
        font-size: 0.7rem;
        padding: 1px 6px;
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="sa-page-header">
    <h1 class="sa-page-header-title">Import Users</h1>
    <p class="sa-page-header-subtitle">Upload file CSV untuk mengimpor data pengguna secara massal</p>
</div>

<div class="row g-4">

    {{-- Kolom kiri: Form upload --}}
    <div class="col-lg-6">
        <div class="sa-card h-100">
            <div class="sa-card-header">
                <h5 class="sa-card-header-title">
                    <i class="bi bi-upload me-2"></i> Upload File CSV
                </h5>
            </div>
            <div class="sa-card-body">

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.manage.users.import.post') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Tipe Import</label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="guru">Guru</option>
                            <option value="siswa">Siswa</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-semibold">Pilih File CSV</label>
                        <input type="file" name="csv_file" id="csv_file"
                            class="form-control @error('csv_file') is-invalid @enderror"
                            accept=".csv,.txt" required>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted mt-1 d-block">Format yang diterima: .csv</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="sa-btn sa-btn-primary">
                            <i class="bi bi-upload me-1"></i> Upload & Import
                        </button>
                        <a href="{{ route('admin.manage.users') }}" class="sa-btn sa-btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom kanan: Download template --}}
    <div class="col-lg-6">
        <div class="sa-card h-100">
            <div class="sa-card-header">
                <h5 class="sa-card-header-title">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i> Download Template CSV
                </h5>
            </div>
            <div class="sa-card-body">
                <p class="text-muted small mb-4">Download template sesuai tipe data yang ingin diimport. Template sudah berisi contoh baris data agar format CSV benar.</p>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="template-card text-center">
                            <div class="template-icon text-success">
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <div class="fw-semibold mb-2">Template Guru</div>
                            <a href="{{ route('admin.manage.users.import.template', 'guru') }}"
                               class="sa-btn sa-btn-success w-100">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="template-card text-center">
                            <div class="template-icon text-primary">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="fw-semibold mb-2">Template Siswa</div>
                            <a href="{{ route('admin.manage.users.import.template', 'siswa') }}"
                               class="sa-btn sa-btn-primary w-100">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Panduan kolom --}}
                <div class="accordion" id="columnGuide">
                    <div class="accordion-item border-0 mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2 px-3 rounded" type="button"
                                data-bs-toggle="collapse" data-bs-target="#guruColumns"
                                style="background:#f0fdf4; font-size:0.9rem; font-weight:600;">
                                <i class="bi bi-mortarboard text-success me-2"></i> Kolom Template Guru
                            </button>
                        </h2>
                        <div id="guruColumns" class="accordion-collapse collapse" data-bs-parent="#columnGuide">
                            <div class="accordion-body p-2">
                                <table class="table column-table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr><th>Kolom</th><th>Keterangan</th><th>Status</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>NIP</td><td>NIP Guru</td><td><span class="required-badge">Wajib</span></td></tr>
                                        <tr><td>Nama</td><td>Nama lengkap</td><td><span class="required-badge">Wajib</span></td></tr>
                                        <tr><td>Email</td><td>Alamat email</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>Jurusan</td><td>Nama jurusan</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>Jenis kelamin</td><td>Laki-laki / Perempuan</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>agama</td><td>Agama</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>Tanggal lahir</td><td>Format: YYYY-MM-DD</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>Alamat</td><td>Alamat lengkap</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>No.hp</td><td>Nomor HP</td><td><span class="optional-badge">Opsional</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2 px-3 rounded" type="button"
                                data-bs-toggle="collapse" data-bs-target="#siswaColumns"
                                style="background:#eff6ff; font-size:0.9rem; font-weight:600;">
                                <i class="bi bi-people text-primary me-2"></i> Kolom Template Siswa
                            </button>
                        </h2>
                        <div id="siswaColumns" class="accordion-collapse collapse" data-bs-parent="#columnGuide">
                            <div class="accordion-body p-2">
                                <table class="table column-table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr><th>Kolom</th><th>Keterangan</th><th>Status</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>NIS</td><td>NIS Siswa</td><td><span class="required-badge">Wajib</span></td></tr>
                                        <tr><td>Nama</td><td>Nama lengkap</td><td><span class="required-badge">Wajib</span></td></tr>
                                        <tr><td>Email</td><td>Alamat email</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>Jurusan</td><td>Nama jurusan</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>kelas</td><td>Nama kelas (teks)</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>Jenis kelamin</td><td>Laki-laki / Perempuan</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>agama</td><td>Agama</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>Tanggal lahir</td><td>Format: YYYY-MM-DD</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>Alamat</td><td>Alamat lengkap</td><td><span class="optional-badge">Opsional</span></td></tr>
                                        <tr><td>No.hp</td><td>Nomor HP</td><td><span class="optional-badge">Opsional</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection