@extends('sistem_akademik.layouts.main')

@section('css')
    <link href="{{ asset('css/peminatan.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle">{{ isset($peminatan) ? 'Perbarui data peminatan' : 'Isi form peminatan sekolah' }}</p>
        </div>
        <a href="{{ route('sistem_akademik.peminatan.index') }}" class="btn-secondary-app">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <div class="form-card-header">
            <h5><i class="bi bi-diagram-3-fill me-2"></i>{{ isset($peminatan) ? 'Form Edit Peminatan' : 'Form Peminatan' }}</h5>
            <p>Isi minat dan rencana setelah lulus SMK</p>
        </div>
        <div class="form-card-body">
            <form action="{{ isset($peminatan) ? route('sistem_akademik.peminatan.update', $peminatan->id) : route('sistem_akademik.peminatan.store') }}"
                  method="POST">
                @csrf
                @if(isset($peminatan)) @method('PUT') @endif

                <p class="form-section-title">Identitas Siswa</p>
                <div class="mb-3">
                    <label class="form-label">Nama Siswa</label>
                    @if(in_array(Auth::user()->role, ['admin_sa', 'super_admin']))
                    <select name="user_id" class="form-select select2" required>
                        <option value="">-- Pilih Nama --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            {{ old('user_id', $peminatan->user_id ?? '') == $user->id ? 'selected' : '' }}>
                            {{ $user->nama }}
                        </option>
                        @endforeach
                    </select>
                    @else
                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                    <input type="text" class="form-control" readonly value="{{ Auth::user()->nama }}"
                           style="background:#f8fafc;font-weight:600;">
                    @endif
                </div>

                <p class="form-section-title mt-4">Pilihan Minat</p>
                <div class="mb-3">
                    <label for="minat" class="form-label">Minat Setelah Lulus <span class="text-danger">*</span></label>
                    @php
                        $options = ['bekerja'=>'Bekerja','wirausaha'=>'Wirausaha','kuliah'=>'Kuliah','lainnya'=>'Lainnya'];
                        $selectedMinat = old('minat', $peminatan->minat ?? '');
                    @endphp
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($options as $key => $label)
                        @php
                            $iconMap = ['bekerja'=>'briefcase-fill','wirausaha'=>'shop','kuliah'=>'mortarboard-fill','lainnya'=>'three-dots'];
                            $colorMap = ['bekerja'=>'badge-info','wirausaha'=>'badge-success','kuliah'=>'badge-purple','lainnya'=>'badge-gray'];
                        @endphp
                        <label style="cursor:pointer;flex:1;min-width:110px;">
                            <input type="radio" name="minat" value="{{ $key }}" class="d-none minat-radio"
                                   {{ $selectedMinat === $key ? 'checked' : '' }}>
                            <div class="minat-card p-3 text-center rounded-3 border-2" style="border:2px solid #e2e8f0;transition:all 0.2s;border-radius:10px!important;">
                                <i class="bi bi-{{ $iconMap[$key] }}" style="font-size:1.5rem;display:block;margin-bottom:4px;"></i>
                                <span style="font-size:0.8rem;font-weight:600;">{{ $label }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Conditional fields --}}
                <div id="group-pemilihan-jurusan" class="conditional-field mb-3" data-for="kuliah" style="display:none;">
                    <label class="form-label">Jurusan yang Dipilih</label>
                    <select name="pemilihan_jurusan" class="form-select">
                        <option value="">-- Pilih Jurusan --</option>
                        @php
                            $jurusanKuliah = ['Teknik Informatika / Ilmu Komputer', 'Teknik Mesin / Otomotif', 'Teknik Elektro', 'Teknik Sipil / Arsitektur', 'Sistem Informasi', 'Pendidikan Teknik', 'Manajemen Bisnis', 'Lainnya'];
                        @endphp
                        @foreach($jurusanKuliah as $jk)
                        <option value="{{ $jk }}" {{ old('pemilihan_jurusan', $peminatan->pemilihan_jurusan ?? '') == $jk ? 'selected' : '' }}>{{ $jk }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="group-jenis-pekerjaan" class="conditional-field mb-3" data-for="bekerja" style="display:none;">
                    <label class="form-label">Jenis Pekerjaan</label>
                    <select name="jenis_pekerjaan" class="form-select">
                        <option value="">-- Pilih Jenis Pekerjaan --</option>
                        @php
                            $pekerjaanList = ['Teknisi Jaringan / IT Support', 'Mekanik / Montir Otomotif', 'Teknisi Elektronika / Listrik', 'Operator Mesin Industri', 'Drafter / Estimator Bangunan', 'Administrasi / Pemasaran', 'Lainnya'];
                        @endphp
                        @foreach($pekerjaanList as $pk)
                        <option value="{{ $pk }}" {{ old('jenis_pekerjaan', $peminatan->jenis_pekerjaan ?? '') == $pk ? 'selected' : '' }}>{{ $pk }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="group-ide-bisnis" class="conditional-field mb-3" data-for="wirausaha" style="display:none;">
                    <label class="form-label">Ide Bisnis</label>
                    <select name="ide_bisnis" class="form-select">
                        <option value="">-- Pilih Ide Bisnis --</option>
                        @php
                            $bisnisList = ['Bengkel Motor / Mobil', 'Jasa Servis Elektronik', 'Jasa Instalasi Jaringan / Listrik', 'Biro Bangunan / Desain', 'Toko Komputer / Elektronik', 'Lainnya'];
                        @endphp
                        @foreach($bisnisList as $bl)
                        <option value="{{ $bl }}" {{ old('ide_bisnis', $peminatan->ide_bisnis ?? '') == $bl ? 'selected' : '' }}>{{ $bl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alasan</label>
                    <textarea name="alasan" class="form-control" rows="3"
                              placeholder="Ceritakan alasan Anda memilih minat tersebut...">{{ old('alasan', $peminatan->alasan ?? '') }}</textarea>
                </div>

                <p class="form-section-title mt-4">Data Ekonomi Keluarga</p>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Penghasilan Orang Tua (Rp)</label>
                        <input type="number" name="penghasilan_ortu" class="form-control"
                               value="{{ old('penghasilan_ortu', $peminatan->penghasilan_ortu ?? '') }}"
                               placeholder="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggungan Keluarga (Orang)</label>
                        <input type="number" name="tanggungan_keluarga" class="form-control"
                               value="{{ old('tanggungan_keluarga', $peminatan->tanggungan_keluarga ?? '') }}"
                               placeholder="0" min="0">
                    </div>
                </div>

                <p class="form-section-title mt-4">Dokumen Pendukung</p>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Link Google Drive Raport</label>
                        <input type="url" name="file_raport" class="form-control @error('file_raport') is-invalid @enderror"
                               placeholder="https://drive.google.com/..."
                               value="{{ old('file_raport', $peminatan->file_raport ?? '') }}">
                        @if(isset($peminatan) && $peminatan->file_raport)
                        <a href="{{ $peminatan->file_raport }}" target="_blank"
                           class="badge-modern badge-info mt-2 d-inline-flex" style="text-decoration:none;">
                            <i class="bi bi-box-arrow-up-right"></i> Lihat Raport
                        </a>
                        @endif
                        @error('file_raport')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Link Google Drive Angket</label>
                        <input type="url" name="file_angket" class="form-control @error('file_angket') is-invalid @enderror"
                               placeholder="https://drive.google.com/..."
                               value="{{ old('file_angket', $peminatan->file_angket ?? '') }}">
                        @if(isset($peminatan) && $peminatan->file_angket)
                        <a href="{{ $peminatan->file_angket }}" target="_blank"
                           class="badge-modern badge-info mt-2 d-inline-flex" style="text-decoration:none;">
                            <i class="bi bi-box-arrow-up-right"></i> Lihat Angket
                        </a>
                        @endif
                        @error('file_angket')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-primary-app">
                        <i class="bi bi-{{ isset($peminatan) ? 'save' : 'plus-lg' }}"></i>
                        {{ isset($peminatan) ? 'Simpan Perubahan' : 'Simpan Peminatan' }}
                    </button>
                    <a href="{{ route('sistem_akademik.peminatan.index') }}" class="btn-secondary-app">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "-- Pilih Nama --",
            allowClear: true,
            width: '100%'
        });

        // Minat Radio Toggle Logic
        const radios = $('.minat-radio');
        const cards = $('.minat-card');
        const fields = $('.conditional-field');

        function updateUI() {
            const selected = $('.minat-radio:checked').val();
            
            // Update card styling
            radios.each(function(index) {
                const card = $(cards[index]);
                if ($(this).is(':checked')) {
                    card.css({
                        'border-color': 'var(--primary-app)',
                        'background-color': '#f0f7ff',
                        'color': 'var(--primary-app)',
                        'transform': 'translateY(-2px)',
                        'box-shadow': '0 4px 12px rgba(30, 58, 95, 0.1)'
                    });
                } else {
                    card.css({
                        'border-color': '#e2e8f0',
                        'background-color': 'white',
                        'color': '#64748b',
                        'transform': 'none',
                        'box-shadow': 'none'
                    });
                }
            });

            // Show/hide fields
            fields.hide();
            if(selected) {
                $(`.conditional-field[data-for="${selected}"]`).fadeIn();
            }
        }

        radios.on('change', updateUI);
        updateUI(); // Initial run
    });
</script>
@endsection