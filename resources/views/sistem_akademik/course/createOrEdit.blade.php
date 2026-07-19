@extends('sistem_akademik.layouts.main')

@section('css')
    <link href="{{ asset('css/course.css') }}?v={{ filemtime(public_path('css/course.css')) }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@php
// fallback variable names supported
$mpList = $mataPelajaran ?? $mapels ?? collect();
$selectedMataPelajaran = old('mata_pelajaran_id', $course->mata_pelajaran_id ?? '');
$selectedKelasId = old('kelas_id', $course->kelas_id ?? '');
$selectedSlotStart = old('slot_start', $selected['slot_start'] ?? null);
$selectedSlotEnd = old('slot_end', $selected['slot_end'] ?? null);
$slots = $slots ?? []; // controller should send $slots = $this->selectableSlots()

// safe URLs: recommendations fallback to URL if route missing
// if your named route for recommendations exists, use it; otherwise fallback path
$recommendationsUrl = (Route::has('sistem_akademik.get-recommendations'))
? route('sistem_akademik.get-recommendations')
: url('/sistem-akademik/course/get-recommendations');

// conflict details sent back from controller on redirect
$conflictDetails = session('conflict_details', null);
@endphp

<div id="course-form"
    data-recommendations-url="{{ $recommendationsUrl }}"
    data-conflict-url="{{ route('sistem_akademik.course.check-conflicts') }}"
    data-current-course-id="{{ isset($course) ? $course->id : '' }}"
    data-initial-kelas="{{ $selectedKelasId }}"
    data-initial-hari="{{ old('hari', $course->hari ?? '') }}"
    data-slot-ids='@json(array_keys($slots))'
    data-slot-details='@json($slots)'
    data-kelas-ruangan-map='@json($kelasRuanganMap ?? [])'
    class="container mt-4 mb-4">

    <h1 class="page-title">{{ $header }}</h1>

    <div class="card p-4">
        @if (session('status') === 'error' && session('message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('status') === 'success' && session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Gagal menyimpan data!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($course) ? route('sistem_akademik.course.update', $course->id) : route('sistem_akademik.course.store') }}" method="POST">
            @csrf
            @if(isset($course)) @method('PUT') @endif

            {{-- KELAS --}}
            <div class="mb-3">
                <label for="kelas_id" class="form-label">Kelas</label>
                <select class="form-control" id="kelas_id" name="kelas_id" required>
                    <option value="" disabled {{ $selectedKelasId == '' ? 'selected' : '' }}>-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k->id }}" data-jurusan="{{ $k->jurusan }}" {{ (string)$selectedKelasId === (string)$k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} - {{ $k->jurusan }} ({{ $k->tahun_ajaran }})
                    </option>
                    @endforeach
                </select>
                <small class="form-text text-muted"><i class="bi bi-info-circle"></i> Pilih kelas — siswa akan dimuat berdasarkan kelas.</small>

                {{-- RUANGAN CONFLICT WARNING (tampil bila controller mengirim conflict_details.ruangan) --}}
                @if($conflictDetails && isset($conflictDetails['ruangan']) && count($conflictDetails['ruangan']))
                <div class="alert alert-warning mt-2" role="alert">
                    <strong>Perhatian — Bentrok Ruangan:</strong>
                    <div class="small mt-1">
                        Ruangan yang Anda pilih bentrok dengan jadwal lain pada hari dan slot yang sama. Silakan pilih ruangan lain atau ubah slot/jadwal.
                    </div>
                    <ul class="mb-0 mt-2 small">
                        @foreach($conflictDetails['ruangan'] as $c)
                        <li>
                            <strong>{{ $c['kelas'] ?? '-' }}</strong>
                            @if(!empty($c['mata_pelajaran'])) — {{ $c['mata_pelajaran'] }} @endif
                            ({{ \Illuminate\Support\Str::limit(substr($c['jam_mulai'] ?? '',0,5),5,'') ?: '-' }} - {{ \Illuminate\Support\Str::limit(substr($c['jam_selesai'] ?? '',0,5),5,'') ?: '-' }})
                            @if(!empty($c['ruangan'])) — Ruangan: <code>{{ $c['ruangan'] }}</code>@endif
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            {{-- MATA PELAJARAN --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="mata_pelajaran_umum" class="form-label">Mata Pelajaran (Umum)</label>
                    <select class="form-control select2-mp" id="mata_pelajaran_umum">
                        <option value="" {{ $selectedMataPelajaran == '' ? 'selected' : '' }}>-- Pilih Mapel Umum --</option>
                        @foreach($mpList as $mp)
                            @if(strtolower(trim($mp->jurusan)) == 'umum' || empty(trim($mp->jurusan)))
                                @php $mpGuruName = data_get($mp, 'guru.nama', data_get($mp, 'guru.name', '')); @endphp
                                <option value="{{ $mp->id }}" data-jurusan="{{ $mp->jurusan }}" data-jp="{{ $mp->jp ?? 1 }}" {{ (string)$selectedMataPelajaran === (string)$mp->id ? 'selected' : '' }}>
                                    {{ $mp->nama_mata_pelajaran }} @if($mpGuruName) - {{ $mpGuruName }} @endif
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="mata_pelajaran_jurusan" class="form-label">Mata Pelajaran (Jurusan)</label>
                    <select class="form-control select2-mp" id="mata_pelajaran_jurusan">
                        <option value="" {{ $selectedMataPelajaran == '' ? 'selected' : '' }}>-- Pilih Mapel Jurusan --</option>
                        @foreach($mpList as $mp)
                            @if(strtolower(trim($mp->jurusan)) != 'umum' && !empty(trim($mp->jurusan)))
                                @php $mpGuruName = data_get($mp, 'guru.nama', data_get($mp, 'guru.name', '')); @endphp
                                <option value="{{ $mp->id }}" data-jurusan="{{ $mp->jurusan }}" data-jp="{{ $mp->jp ?? 1 }}" {{ (string)$selectedMataPelajaran === (string)$mp->id ? 'selected' : '' }}>
                                    {{ $mp->nama_mata_pelajaran }} @if($mpGuruName) - {{ $mpGuruName }} @endif
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="mata_pelajaran_id" id="mata_pelajaran_id" value="{{ $selectedMataPelajaran }}" required>
            </div>


            {{-- HARI --}}
            <div class="mb-3">
                <label for="hari" class="form-label">Hari</label>
                <select class="form-control" id="hari" name="hari" required>
                    <option value="" disabled {{ old('hari', $course->hari ?? '') == '' ? 'selected' : '' }}>-- Pilih Hari --</option>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                    <option value="{{ $h }}" {{ old('hari', $course->hari ?? '') == $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Pilih hari untuk melihat rekomendasi slot kosong.</small>
            </div>

            {{-- SLOTS --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="slot_start" class="form-label">Slot Awal</label>
                    <select class="form-control" id="slot_start" name="slot_start" required>
                        <option value="" disabled {{ $selectedSlotStart ? '' : 'selected' }}>-- Pilih Slot Awal --</option>
                        @foreach($slots as $id => $s)
                        <option value="{{ $id }}" {{ (string)$selectedSlotStart === (string)$id ? 'selected' : '' }}>
                            {{ $s['label'] }} ({{ $s['start'] }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="slot_end" class="form-label">Slot Akhir (Otomatis)</label>
                    <select class="form-control" id="slot_end" name="slot_end" required style="pointer-events: none; background-color: #e9ecef;" tabindex="-1">
                        <option value="" disabled {{ $selectedSlotEnd ? '' : 'selected' }}>-- Pilih Slot Akhir --</option>
                        @foreach($slots as $id => $s)
                        <option value="{{ $id }}" {{ (string)$selectedSlotEnd === (string)$id ? 'selected' : '' }}>
                            {{ $s['label'] }} ({{ $s['end'] }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <div id="recommendations-area" class="mt-2">
                        <small class="text-muted">Rekomendasi slot kosong (terkait hari & kelas):</small>
                        <div id="recommendations" class="recommendations"></div>
                    </div>
                </div>
            </div>

            {{-- RUANGAN (text input + autocomplete dari riwayat, fallback dari kelas) --}}
            <div class="mb-3 position-relative">
                <label for="ruangan" class="form-label">Ruangan</label>

                {{-- Input text yang bisa diketik --}}
                <input type="text"
                       id="ruangan"
                       name="ruangan"
                       class="form-control"
                       value="{{ old('ruangan', $course->ruangan ?? '') }}"
                       placeholder="Ketik kode ruangan, mis. R-103…"
                       autocomplete="off">

                {{-- Dropdown autocomplete --}}
                <ul id="ruangan-ac-list" style="
                    display:none;
                    position:absolute;
                    top:calc(100% + 2px);
                    left:0; right:0;
                    background:#fff;
                    border:1px solid #d1d5db;
                    border-radius:8px;
                    box-shadow:0 8px 20px rgba(0,0,0,.12);
                    z-index:1060;
                    max-height:200px;
                    overflow-y:auto;
                    padding:0;
                    margin:0;
                    list-style:none;
                "></ul>

                <small class="form-text text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Kosongkan untuk otomatis menggunakan ruangan kelas yang dipilih.
                </small>
                <small id="ruangan-fallback-hint" class="text-success d-none mt-1"
                       style="display:none; font-size:0.8rem;"></small>

                {{-- conflict warning tetap di sini --}}
                @if($conflictDetails && isset($conflictDetails['ruangan']) && count($conflictDetails['ruangan']))
                <small class="text-danger d-block mt-1">
                    Ruangan ini bentrok dengan jadwal lain. Lihat detail di atas dan pilih ruangan berbeda.
                </small>
                @endif
                <div id="live-conflict-warning" class="mt-2"></div>
            </div>

            <div class="d-flex mt-4">
                <a href="{{ route('sistem_akademik.course.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
                <button type="submit" class="btn btn-primary ms-auto">
                    <i class="bi bi-{{ isset($course) ? 'save' : 'plus-circle' }}"></i>
                    {{ isset($course) ? 'Simpan Perubahan' : 'Simpan Jadwal' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
    (function() {
        var el = document.getElementById('course-form');
        window.CourseFormConfig = {
            recommendationsUrl: el.dataset ? el.dataset.recommendationsUrl : '{{ url("/sistem-akademik/course/get-recommendations") }}',
            conflictUrl: el.dataset ? el.dataset.conflictUrl : '{{ route("sistem_akademik.course.check-conflicts") }}',
            currentCourseId: el.dataset && el.dataset.currentCourseId ? el.dataset.currentCourseId : null,
            initialKelas: el.dataset ? el.dataset.initialKelas : null,
            initialHari: el.dataset ? el.dataset.initialHari : '',
            slotIds: el.dataset ? JSON.parse(el.dataset.slotIds || '[]') : @json(array_keys($slots)),
            slotDetails: el.dataset ? JSON.parse(el.dataset.slotDetails || '{}') : @json($slots),
            kelasRuanganMap: el.dataset ? JSON.parse(el.dataset.kelasRuanganMap || '{}') : @json($kelasRuanganMap ?? []),
            ruanganFromJadwal: @json($ruanganList ?? [])
        };
    })();

    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2-mp').select2({
                width: '100%',
                allowClear: true
            });
        }
    });
</script>

<script src="{{ asset('assets/js/course.js') }}?v={{ filemtime(public_path('assets/js/course.js')) }}"></script>
@endsection