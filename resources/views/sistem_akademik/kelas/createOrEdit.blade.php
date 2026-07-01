@extends('sistem_akademik.layouts.main')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle">{{ isset($kelas) ? 'Edit data kelas yang sudah ada' : 'Tambahkan data kelas baru' }}</p>
        </div>
        <a href="{{ route('sistem_akademik.kelas.index') }}" class="btn-secondary-app">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <div class="form-card-header">
            <h5><i class="bi bi-building me-2"></i>{{ isset($kelas) ? 'Form Edit Kelas' : 'Form Tambah Kelas' }}</h5>
            <p>Lengkapi informasi kelas dengan benar</p>
        </div>
        <div class="form-card-body">

            @if ($errors->any())
            <div class="alert alert-danger rounded-3 mb-4">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                            <li style="font-size:0.875rem">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ isset($kelas) ? route('sistem_akademik.kelas.update', $kelas) : route('sistem_akademik.kelas.store') }}"
                  method="POST">
                @csrf
                @if(isset($kelas)) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama_kelas" class="form-label">Nama / Kode Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror"
                               id="nama_kelas" name="nama_kelas"
                               value="{{ old('nama_kelas', $kelas->nama_kelas ?? '') }}"
                               placeholder="Contoh: X, XI TKJ, XII TKR" required>
                        @error('nama_kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tahun_ajaran') is-invalid @enderror"
                               id="tahun_ajaran" name="tahun_ajaran"
                               placeholder="Contoh: 2026/2027"
                               value="{{ old('tahun_ajaran', $kelas->tahun_ajaran ?? date('Y').'/'.((int)date('Y')+1)) }}" required>
                        @error('tahun_ajaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                        <select class="form-select @error('jurusan') is-invalid @enderror" id="jurusan" name="jurusan" required>
                            <option value="" disabled {{ old('jurusan', $kelas->jurusan ?? '') == '' ? 'selected' : '' }}>-- Pilih Jurusan --</option>
                            @php
                                $jurusans = [
                                    'Bisnis Konstruksi dan Properti',
                                    'Desain Pemodelan dan Informasi Bangunan',
                                    'Teknik Audio Video',
                                    'Teknik Elektronika Industri',
                                    'Teknik Instalasi Tenaga Listrik',
                                    'Teknik Pemesinan',
                                    'Teknik Kendaraan Ringan',
                                    'Teknik Bodi Kendaraan Ringan',
                                    'Teknik Bisnis Sepeda Motor',
                                    'Teknik Pendingin dan Tata Udara',
                                    'Teknik Komputer Jaringan'
                                ];
                            @endphp
                            @foreach($jurusans as $j)
                            <option value="{{ $j }}" {{ (old('jurusan', $kelas->jurusan ?? '') == $j) ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                        @error('jurusan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="wali_kelas_search" class="form-label">Wali Kelas</label>
                        @php
                            $currentWali = isset($kelas) && $kelas->wali_kelas_id ? optional($kelas->waliKelas)->nama : '';
                            $currentWaliId = old('wali_kelas_id', isset($kelas) ? $kelas->wali_kelas_id : '');
                        @endphp
                        <input type="text"
                               id="wali_kelas_search"
                               class="form-control @error('wali_kelas_id') is-invalid @enderror"
                               placeholder="Ketik nama guru..."
                               value="{{ old('_wali_kelas_name', $currentWali) }}"
                               list="wali_kelas_options"
                               autocomplete="off"
                               oninput="syncWaliId(this.value)">
                        <datalist id="wali_kelas_options">
                            <option value="">-- Tidak ada --</option>
                            @if(isset($availableWali) && $availableWali->count())
                                @foreach($availableWali as $w)
                                    <option value="{{ $w->nama }}" data-id="{{ $w->id }}">{{ $w->nama }}</option>
                                @endforeach
                            @endif
                            @if(isset($kelas) && $kelas->wali_kelas_id && isset($availableWali) && !$availableWali->pluck('id')->contains($kelas->wali_kelas_id))
                                <option value="{{ optional($kelas->waliKelas)->nama }}" data-id="{{ $kelas->wali_kelas_id }}">{{ optional($kelas->waliKelas)->nama }}</option>
                            @endif
                        </datalist>
                        <input type="hidden" name="wali_kelas_id" id="wali_kelas_id" value="{{ $currentWaliId }}">
                        <div class="form-text">Guru yang sudah menjadi wali kelas lain tidak ditampilkan.</div>
                        @error('wali_kelas_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="guru_bk_search" class="form-label">Guru BK <small class="text-muted fw-normal">(maks 6 penugasan)</small></label>
                        @php
                            $currentBk = isset($kelas) && $kelas->guru_bk_id ? optional($kelas->guruBK)->nama : '';
                            $currentBkId = old('guru_bk_id', isset($kelas) ? $kelas->guru_bk_id : '');
                        @endphp
                        <input type="text"
                               id="guru_bk_search"
                               class="form-control @error('guru_bk_id') is-invalid @enderror"
                               placeholder="Ketik nama guru BK..."
                               value="{{ old('_guru_bk_name', $currentBk) }}"
                               list="guru_bk_options"
                               autocomplete="off"
                               oninput="syncGuruBkId(this.value)">
                        <datalist id="guru_bk_options">
                            <option value="">-- Tidak ada --</option>
                            @if(isset($availableGuruBk) && $availableGuruBk->count())
                                @foreach($availableGuruBk as $g)
                                    @php $count = isset($g->kelas_count) ? (int)$g->kelas_count : 0; @endphp
                                    <option value="{{ $g->nama }}" data-id="{{ $g->id }}">{{ $g->nama }} (terisi: {{ $count }})</option>
                                @endforeach
                            @endif
                            @if(isset($kelas) && $kelas->guru_bk_id && isset($availableGuruBk) && !$availableGuruBk->pluck('id')->contains($kelas->guru_bk_id))
                                <option value="{{ optional($kelas->guruBK)->nama }}" data-id="{{ $kelas->guru_bk_id }}">{{ optional($kelas->guruBK)->nama }}</option>
                            @endif
                        </datalist>
                        <input type="hidden" name="guru_bk_id" id="guru_bk_id" value="{{ $currentBkId }}">
                        <div class="form-text">Guru BK dapat menangani maksimal 6 kelas.</div>
                        @error('guru_bk_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Data Siswa</label>
                            <input type="text" id="searchSiswa" class="form-control form-control-sm" style="width: 250px;" placeholder="Cari nama atau NIS...">
                        </div>
                        <div class="row g-2 border rounded p-2 bg-light" id="siswaContainer" style="max-height: 350px; overflow-y: auto;">
                            @if(isset($siswaList) && $siswaList->isNotEmpty())
                                @foreach($siswaList as $s)
                                    @php
                                        $nama = optional($s->user)->nama ?? ($s->nama ?? '-');
                                        $nis = $s->nis ?? $s->nisn ?? '-';
                                        $avatar = $s->image ? asset('assets/profile/' . ltrim($s->image, '/')) : asset('assets/profile/default.png');
                                        $isChecked = in_array($s->id, (array)$selectedSiswaIds) ? 'checked' : '';
                                    @endphp
                                    <div class="col-md-4 siswa-item">
                                        <div class="card h-100 siswa-card {{ $isChecked ? 'border-primary' : '' }}">
                                            <div class="card-body p-2 d-flex align-items-center">
                                                <div class="form-check m-0">
                                                    <input class="form-check-input siswa-checkbox" type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" id="siswa_{{ $s->id }}" {{ $isChecked }}>
                                                </div>
                                                <label class="form-check-label d-flex align-items-center w-100 ms-2" for="siswa_{{ $s->id }}" style="cursor: pointer;">
                                                    <img src="{{ $avatar }}" alt="avatar" class="rounded-circle me-2 border" style="width: 38px; height: 38px; object-fit: cover;">
                                                    <div style="line-height: 1.2; overflow: hidden;">
                                                        <span class="fw-semibold text-truncate d-block siswa-nama" style="font-size: 0.85rem;" title="{{ $nama }}">{{ $nama }}</span>
                                                        <small class="text-muted siswa-nis" style="font-size: 0.75rem;">{{ $nis }}</small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 text-center text-muted p-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada data siswa yang tersedia.
                                </div>
                            @endif
                        </div>
                        <div class="form-text mt-1">Pilih siswa yang akan dimasukkan ke dalam kelas ini. Hanya menampilkan siswa yang belum memiliki kelas.</div>
                        @error('siswa_ids')<div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="ruangan" class="form-label">Ruangan</label>
                        <input type="text" class="form-control @error('ruangan') is-invalid @enderror"
                               id="ruangan" name="ruangan"
                               placeholder="Contoh: R-101 / Lab Komputer"
                               value="{{ old('ruangan', $kelas->ruangan ?? '') }}">
                        <div class="form-text">Nama ruangan harus unik antar kelas.</div>
                        @error('ruangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-primary-app">
                        <i class="bi bi-{{ isset($kelas) ? 'save' : 'plus-lg' }}"></i>
                        {{ isset($kelas) ? 'Simpan Perubahan' : 'Tambah Kelas' }}
                    </button>
                    <a href="{{ route('sistem_akademik.kelas.index') }}" class="btn-secondary-app">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Build lookup maps from datalist options on page load
    const waliMap   = {};
    const guruBkMap = {};

    document.addEventListener('DOMContentLoaded', function () {
        // Populate wali map from datalist
        document.querySelectorAll('#wali_kelas_options option').forEach(opt => {
            if (opt.dataset.id) {
                waliMap[opt.value.trim()] = opt.dataset.id;
            }
        });

        // Populate guru BK map from datalist
        // The value contains "(terisi: X)" so we store both full value and just name
        document.querySelectorAll('#guru_bk_options option').forEach(opt => {
            if (opt.dataset.id) {
                const name = opt.value.split(' (terisi:')[0].trim();
                guruBkMap[opt.value.trim()] = opt.dataset.id;
                guruBkMap[name] = opt.dataset.id; // also map plain name
            }
        });
    });

    function syncWaliId(val) {
        const trimmed = val.trim();
        const id = waliMap[trimmed] || '';
        document.getElementById('wali_kelas_id').value = id;
        // If cleared, reset
        if (!trimmed) {
            document.getElementById('wali_kelas_id').value = '';
        }
    }

    function syncGuruBkId(val) {
        const trimmed = val.trim();
        // Try exact match first, then name-only match
        const id = guruBkMap[trimmed] || guruBkMap[trimmed.split(' (terisi:')[0].trim()] || '';
        document.getElementById('guru_bk_id').value = id;
        if (!trimmed) {
            document.getElementById('guru_bk_id').value = '';
        }
    }

    $(document).ready(function() {
        // Search functionality for Siswa grid
        $('#searchSiswa').on('input', function() {
            const val = $(this).val().toLowerCase();
            $('.siswa-item').each(function() {
                const name = $(this).find('.siswa-nama').text().toLowerCase();
                const nis = $(this).find('.siswa-nis').text().toLowerCase();
                if (name.includes(val) || nis.includes(val)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Toggle card style on checkbox change
        $('.siswa-checkbox').on('change', function() {
            const card = $(this).closest('.siswa-card');
            if ($(this).is(':checked')) {
                card.addClass('border-primary');
            } else {
                card.removeClass('border-primary');
            }
        });
    });
</script>
@endsection