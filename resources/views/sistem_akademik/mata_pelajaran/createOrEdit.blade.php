@extends('sistem_akademik.layouts.main')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* ── Autocomplete dropdown ── */
    .autocomplete-wrapper {
        position: relative;
    }
    .autocomplete-list {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        z-index: 1055;
        max-height: 220px;
        overflow-y: auto;
        display: none;
    }
    .autocomplete-list li {
        list-style: none;
        padding: 0.5rem 0.85rem;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background .15s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .autocomplete-list li:hover,
    .autocomplete-list li.active {
        background: #f0f9ff;
        color: #0369a1;
    }
    .autocomplete-list li .ac-icon {
        color: #94a3b8;
        font-size: 0.8rem;
    }
    .autocomplete-list li mark {
        background: #fde68a;
        padding: 0 2px;
        border-radius: 2px;
    }

    /* ── Select2 customization ── */
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px) !important;
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        display: flex;
        align-items: center;
        padding: 0 0.75rem;
        font-size: 0.95rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        padding: 0 !important;
        color: #1e293b;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        top: 0 !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #4ecdc4 !important;
        box-shadow: 0 0 0 3px rgba(78,205,196,.15) !important;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,.12) !important;
    }
    .select2-container--default .select2-results__option--highlighted {
        background-color: #0369a1 !important;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 6px !important;
        border: 1px solid #d1d5db !important;
        padding: 0.4rem 0.7rem !important;
    }
    .is-invalid + .select2-container .select2-selection--single {
        border-color: #ef4444 !important;
    }
</style>
@endsection

@section('content')
@php $isEdit = !empty($mapel) && $mapel !== null; @endphp

<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle">{{ $isEdit ? 'Edit data mata pelajaran' : 'Tambahkan mata pelajaran baru' }}</p>
        </div>
        <a href="{{ route('sistem_akademik.mata_pelajaran.index') }}" class="btn-secondary-app">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-card" style="max-width:600px;">
        <div class="form-card-header">
            <h5><i class="bi bi-book-fill me-2"></i>{{ $isEdit ? 'Form Edit Mata Pelajaran' : 'Form Tambah Mata Pelajaran' }}</h5>
        </div>
        <div class="form-card-body">
            <form method="POST"
                  action="{{ $isEdit
                      ? route('sistem_akademik.mata_pelajaran.update', ['mata_pelajaran' => $mapel->id])
                      : route('sistem_akademik.mata_pelajaran.store') }}">
                @csrf
                @if($isEdit) @method('PUT') @endif

                {{-- ── NAMA MATA PELAJARAN (autocomplete) ── --}}
                <div class="mb-3">
                    <label for="nama_mata_pelajaran" class="form-label">
                        Nama Mata Pelajaran <span class="text-danger">*</span>
                    </label>
                    <div class="autocomplete-wrapper">
                        <input type="text"
                               id="nama_mata_pelajaran"
                               name="nama_mata_pelajaran"
                               class="form-control @error('nama_mata_pelajaran') is-invalid @enderror"
                               value="{{ old('nama_mata_pelajaran', $mapel->nama_mata_pelajaran ?? '') }}"
                               placeholder="Ketik nama mapel atau pilih dari daftar…"
                               autocomplete="off"
                               required>
                        <ul class="autocomplete-list" id="ac-mapel-list"></ul>
                    </div>
                    @error('nama_mata_pelajaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted"><i class="bi bi-lightbulb me-1"></i>Mulai ketik untuk melihat saran nama mapel yang sudah ada.</small>
                </div>

                {{-- ── GURU (Select2 searchable) ── --}}
                <div class="mb-4">
                    <label for="guru_id" class="form-label">
                        Guru Pengampu <span class="text-danger">*</span>
                    </label>
                    <select id="guru_id" name="guru_id"
                            class="form-select @error('guru_id') is-invalid @enderror"
                            required>
                        <option value="" disabled {{ !$isEdit ? 'selected' : '' }}>-- Cari / Pilih Guru --</option>
                        @foreach(($gurus ?? collect()) as $g)
                        <option value="{{ $g->id }}"
                            {{ old('guru_id', $mapel->guru_id ?? '') == $g->id ? 'selected' : '' }}>
                            {{ $g->nama ?? $g->name ?? '-' }}
                        </option>
                        @endforeach
                    </select>
                    @error('guru_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <small class="text-muted"><i class="bi bi-search me-1"></i>Ketik nama guru untuk memfilter pilihan.</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-primary-app">
                        <i class="bi bi-{{ $isEdit ? 'save' : 'plus-lg' }}"></i>
                        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Mata Pelajaran' }}
                    </button>
                    <a href="{{ route('sistem_akademik.mata_pelajaran.index') }}" class="btn-secondary-app">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {

    /* ════════════════════════════════════════════════
       1. Select2 untuk kolom Guru
    ════════════════════════════════════════════════ */
    $('#guru_id').select2({
        placeholder: '-- Cari / Pilih Guru --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () { return 'Guru tidak ditemukan'; },
            searching: function () { return 'Mencari…'; }
        }
    });

    /* ════════════════════════════════════════════════
       2. Autocomplete untuk Nama Mata Pelajaran
    ════════════════════════════════════════════════ */
    const suggestions = @json($namaMapelList ?? []);
    const input  = document.getElementById('nama_mata_pelajaran');
    const acList = document.getElementById('ac-mapel-list');
    let activeIdx = -1;

    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }

    function highlight(str, query) {
        if (!query) return escapeHtml(str);
        const re = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');
        return escapeHtml(str).replace(re, '<mark>$1</mark>');
    }

    function renderList(query) {
        const q = query.trim().toLowerCase();
        const filtered = q
            ? suggestions.filter(s => s.toLowerCase().includes(q))
            : suggestions;

        if (!filtered.length) {
            acList.style.display = 'none';
            return;
        }

        acList.innerHTML = filtered.slice(0, 30).map((s, i) =>
            `<li data-value="${escapeHtml(s)}" data-idx="${i}">
                <i class="bi bi-bookmark ac-icon"></i>
                <span>${highlight(s, q)}</span>
            </li>`
        ).join('');
        acList.style.display = 'block';
        activeIdx = -1;
    }

    input.addEventListener('input', function () {
        renderList(this.value);
    });

    input.addEventListener('focus', function () {
        if (this.value === '') renderList('');
    });

    acList.addEventListener('mousedown', function (e) {
        const li = e.target.closest('li');
        if (!li) return;
        input.value = li.dataset.value;
        acList.style.display = 'none';
        input.focus();
    });

    // Keyboard navigation
    input.addEventListener('keydown', function (e) {
        const items = acList.querySelectorAll('li');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIdx = Math.min(activeIdx + 1, items.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIdx = Math.max(activeIdx - 1, 0);
        } else if (e.key === 'Enter' && activeIdx >= 0) {
            e.preventDefault();
            input.value = items[activeIdx].dataset.value;
            acList.style.display = 'none';
            activeIdx = -1;
            return;
        } else if (e.key === 'Escape') {
            acList.style.display = 'none';
            activeIdx = -1;
            return;
        }

        items.forEach((li, i) => li.classList.toggle('active', i === activeIdx));
        if (activeIdx >= 0) items[activeIdx].scrollIntoView({ block: 'nearest' });
    });

    // Tutup list saat klik di luar
    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !acList.contains(e.target)) {
            acList.style.display = 'none';
        }
    });
});
</script>
@endsection