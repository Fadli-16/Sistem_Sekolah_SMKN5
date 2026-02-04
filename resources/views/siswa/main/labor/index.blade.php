@extends('siswa.layouts.main')

@section('css')
<style>
    .lab-card {
        background-color: #fff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        transition: transform 0.3s ease;
        height: 100%;
        overflow: hidden;
    }

    .lab-card:hover {
        transform: translateY(-5px);
    }

    .lab-image {
        height: 180px;
        background-size: cover;
        background-position: center;
    }

    .lab-content {
        padding: 1.5rem;
    }

    .lab-title {
        color: var(--primary);
        font-size: 1.35rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .lab-detail {
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .lab-detail i {
        color: var(--secondary);
        margin-right: 0.5rem;
        font-size: 1.1rem;
    }

    .badge-status {
        display: inline-block;
        padding: 0.35em 0.65em;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: var(--radius-sm);
    }

    .status-available {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .status-busy {
        background-color: #f8d7da;
        color: #842029;
    }

    .lab-footer {
        padding: 1rem 1.5rem;
        background-color: #f8fafc;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .btn-view {
        color: var(--primary);
        font-weight: 500;
    }

    .btn-view:hover {
        color: var(--primary-dark);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Laboratorium</h1>
            <p class="text-muted">Daftar laboratorium yang tersedia di sekolah</p>
        </div>
        <form method="GET" action="{{ route('siswa.labor.index') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="labor" class="form-label">Jenis-Jenis Laboratorium</label>
                    <select class="form-select" id="labor" name="labor">
                        <option value="">Semua Laboratorium</option>
                        @foreach($laborList as $lab)
                            <option value="{{ $lab->kode }}" {{ $selectedLabor == $lab->kode ? 'selected' : '' }}>
                                {{ $lab->nama_labor }} @if($lab->kode) ({{ $lab->kode }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('siswa.labor.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-4">
        @forelse($labor ?? [] as $lab)
        <div class="col-lg-4 col-md-6">
            <div class="lab-card">
<div class="lab-image" style="background-image: url('{{
    $lab->foto
    ? asset('storage/labor_foto/' . $lab->foto)
    : asset('assets/images/' . strtolower($lab->kode ?? 'lab') . '.jpg')
}}')"></div>


                <div class="lab-content">
                    <h3 class="lab-title">{{ $lab->nama_labor }}</h3>
                    <p>{{ $lab->deskripsi ?? 'Laboratorium untuk kegiatan praktikum siswa.' }}</p>

                    <div class="lab-detail">
    <i class="bi bi-person-badge"></i>
    <span>Penanggung Jawab: {{ $lab->penanggung_jawab ?: 'Belum ditentukan' }}</span>
</div>

<div class="lab-detail">
    <i class="bi bi-tools"></i>
    <span>Teknisi: {{ $lab->teknisi ?: 'Belum ada teknisi' }}</span>
</div>

                    <div class="lab-detail">
                        <span>
                            @php
                                $status = \App\Models\Laboratorium::where('labor', $lab->kode)
                                    ->whereDate('start', now())
                                    ->where('start', '<=', now())
                                    ->where('end', '>=', now())
                            @endphp
                        </span>
                    </div>
                </div>

                <div class="lab-footer">
                    <a href="{{ route('siswa.jadwal.index') }}" class="btn-view">
                        <i class="bi bi-calendar-week me-1"></i> Lihat Jadwal
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Belum ada laboratorium yang terdaftar.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
