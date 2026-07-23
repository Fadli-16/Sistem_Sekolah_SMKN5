@extends('sistem_akademik.layouts.main')

@section('css')
    <link href="{{ asset('css/course.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid" id="course-index"
    data-timetable-url="{{ route('sistem_akademik.course.timetable') }}"
    data-download-url="{{ route('sistem_akademik.course.download-timetable') }}">

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle"><i class="bi bi-calendar3 me-1"></i>Kelola jadwal pembelajaran</p>
        </div>
        @if(in_array(Auth::user()->role, ['admin','super_admin','admin_sa']))
        <div class="d-flex gap-2">
            <a href="{{ route('sistem_akademik.auto-schedule.index') }}" class="btn-primary-app" style="background-color: #10b981; border-color: #10b981;">
                <i class="bi bi-magic"></i> Auto-Schedule AI
            </a>
            <a href="{{ route('sistem_akademik.course.create') }}" class="btn-primary-app">
                <i class="bi bi-plus-lg"></i> Tambah Jadwal
            </a>
        </div>
        @endif
    </div>

    <div class="table-container">
        @php
            $user = Auth::user();
            $isSiswa = $user->role === 'siswa';
        @endphp

        <div class="table-container-header" style="flex-wrap:wrap; gap:1rem; padding: 1.25rem 1.5rem;">
            @if(!$isSiswa)
                <div class="d-flex align-items-center gap-3">
                    <span style="font-weight:600;font-size:0.875rem;color:#374151;">Filter Jadwal</span>
                    @if(in_array(Auth::user()->role, ['admin','super_admin','admin_sa']))
                    <button type="button" id="btn-bulk-delete" class="btn btn-sm btn-danger-app d-none" onclick="bulkDelete()">
                        <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
                    </button>
                    @endif
                </div>
                
                <form action="{{ route('sistem_akademik.course.index') }}" method="GET" 
                      class="d-flex align-items-center justify-content-between flex-wrap w-100" style="gap: 1rem;">
                    
                    {{-- Area Kiri: Filter Tampilan --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="d-flex align-items-center gap-1 me-2">
                            <i class="bi bi-funnel text-primary"></i>
                            <span class="small fw-bold text-muted">Filter:</span>
                        </div>

                        {{-- Filter Mapel --}}
                        <select name="nama_mata_pelajaran" class="form-select form-select-sm" style="width:160px;" onchange="this.form.submit()">
                            <option value="">-- Semua Mapel --</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->nama_mata_pelajaran }}" {{ $selectedMapelName == $m->nama_mata_pelajaran ? 'selected' : '' }}>
                                    {{ $m->nama_mata_pelajaran }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Filter Guru --}}
                        @if(Auth::user()->role !== 'guru')
                        <select name="guru_id" class="form-select form-select-sm" style="width:160px;" onchange="this.form.submit()">
                            <option value="">-- Semua Guru --</option>
                            @foreach($guruList as $g)
                                <option value="{{ $g->id }}" {{ $selectedGuruId == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama ?? $g->name }}
                                </option>
                            @endforeach
                        </select>
                        @endif

                        {{-- Filter Hari --}}
                        <select name="hari" class="form-select form-select-sm" style="width:110px;" onchange="this.form.submit()">
                            <option value="">-- Hari --</option>
                            @foreach($hariList as $h)
                                <option value="{{ $h }}" {{ $selectedHari == $h ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </select>

                        {{-- Filter Ruangan --}}
                        <select name="ruangan" class="form-select form-select-sm" style="width:110px;" onchange="this.form.submit()">
                            <option value="">-- Ruangan --</option>
                            @foreach($ruanganList as $r)
                                <option value="{{ $r }}" {{ $selectedRuangan == $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>

                        {{-- Filter Tahun Ajaran --}}
                        <select name="tahun_ajaran" class="form-select form-select-sm" style="width:130px;" onchange="this.form.submit()">
                            <option value="">-- Th. Ajaran --</option>
                            @if(isset($tahunAjaranList))
                                @foreach($tahunAjaranList as $ta)
                                    <option value="{{ $ta }}" {{ (isset($selectedTahunAjaran) && $selectedTahunAjaran == $ta) ? 'selected' : '' }}>
                                        {{ $ta }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Area Kanan: Filter Kelas & Aksi Cetak --}}
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center gap-1 me-1">
                            <i class="bi bi-printer text-success"></i>
                            <span class="small fw-bold text-muted">Cetak per Kelas:</span>
                        </div>
                        
                        {{-- Filter Jurusan --}}
                        <select name="jurusan" class="form-select form-select-sm border-success" style="width:140px;" onchange="this.form.submit()">
                            <option value="">-- Jurusan --</option>
                            @if(isset($jurusanList))
                                @foreach($jurusanList as $j)
                                    <option value="{{ $j->jurusan }}" {{ (isset($selectedJurusan) && $selectedJurusan == $j->jurusan) ? 'selected' : '' }}>
                                        {{ $j->jurusan }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        
                        <select name="kelas_id" class="form-select form-select-sm border-success" style="width:180px;" onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>

                        @php
                            $titleText = !$selectedKelasId ? 'title="Cetak Seluruh Jadwal"' : 'title="Cetak Jadwal Kelas"';
                        @endphp
                        <button type="button" id="download-timetable" class="btn btn-sm btn-primary-app px-3" {!! $titleText !!}>
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                        </button>
                        
                        <a href="{{ route('sistem_akademik.course.index') }}" class="btn btn-sm btn-secondary-app" title="Reset Semua Filter">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </form>
            @else
                @php
                    $siswa = $user->siswa;
                    // Gunakan fungsi relasi kelas() secara eksplisit untuk menghindari konflik atribut
                    $kelasRelasi = optional($siswa->kelasData);
                    
                    $namaKelas = $kelasRelasi->nama_kelas ?? '—';
                    $jurusan = $kelasRelasi->jurusan ?? '—';
                    $ta = $kelasRelasi->tahun_ajaran ?? '—';
                @endphp
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon info" style="width: 45px; height: 45px; font-size: 1.25rem; background-color: #e0f2fe; color: #0284c7; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: var(--text-dark); font-size: 1.1rem;">
                                Jadwal Kelas {{ $namaKelas }}
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-mortarboard me-1"></i> {{ $jurusan }} 
                                <span class="mx-2 text-gray-300">|</span>
                                <i class="bi bi-clock-history me-1"></i> Tahun Ajaran {{ $ta }}
                            </div>
                        </div>
                    </div>
                    <button type="button" id="download-timetable" class="btn btn-sm btn-primary-app shadow-sm px-3">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak Jadwal PDF
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover" id="data-table" style="width:100%;">
            <thead>
                <tr>
                    @if(in_array(Auth::user()->role, ['admin','super_admin','admin_sa']))
                    <th width="3%">
                        <input type="checkbox" id="select-all" class="form-check-input">
                    </th>
                    @endif
                    <th width="5%">No</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Ruangan</th>
                    <th width="12%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $index => $course)
                <tr>
                    @if(in_array(Auth::user()->role, ['admin','super_admin','admin_sa']))
                    <td>
                        <input type="checkbox" class="form-check-input select-item" value="{{ $course->id }}">
                    </td>
                    @endif
                    <td>{{ $index + 1 }}</td>

                    {{-- Kelas (null-safe) --}}
                    <td>
                        @if($course->kelas)
                        <span class="badge-modern badge-info">{{ $course->kelas->nama_kelas ?? '-' }}</span>
                        <div style="font-size:0.75rem;color:#64748b;margin-top:2px;">{{ $course->kelas->jurusan ?? '-' }}</div>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ optional($course->mataPelajaran)->nama_mata_pelajaran ?? '-' }}</td>
                    <td>
                        @php
                            $teacherUser = optional($course->mataPelajaran)->guru;
                            $teacherName = $teacherUser->nama ?? $teacherUser->name ?? '-';
                            $teacherAvatar = asset('assets/profile/default.png');
                            if ($teacherUser && $teacherUser->guru && $teacherUser->guru->image) {
                                $teacherAvatar = asset('assets/profile/' . ltrim($teacherUser->guru->image, '/'));
                            }
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $teacherAvatar }}" alt="avatar" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/profile/default.png') }}'">
                            <span>{{ $teacherName }}</span>
                        </div>
                    </td>
                    <td>{{ $course->hari ?? '-' }}</td>
                    <td>
                        @if(!empty($course->jam_mulai))
                        {{ date('H:i', strtotime($course->jam_mulai)) }}
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if(!empty($course->jam_selesai))
                        {{ date('H:i', strtotime($course->jam_selesai)) }}
                        @else
                        -
                        @endif
                    </td>
                    <td>{{ $course->ruangan ?? '-' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('sistem_akademik.course.show', $course->id) }}"
                               class="btn-icon btn-icon-info" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            @if(in_array(Auth::user()->role, ['admin','super_admin','admin_sa']))
                            <a href="{{ route('sistem_akademik.course.edit', $course->id) }}"
                               class="btn-icon btn-icon-warning" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form action="{{ route('sistem_akademik.course.destroy', $course->id) }}"
                                  method="post" id="deleteForm{{ $course->id }}" class="d-inline">
                                @csrf @method('delete')
                                <button type="button" onclick="confirmDelete('{{ $course->id }}')"
                                        class="btn-icon btn-icon-danger" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($courses->count() == 0)
    <div class="empty-state">
        <i class="bi bi-journal-x"></i>
        <p>Belum ada data course</p>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#data-table')) {
            $('#data-table').DataTable({
                stateSave: true,
                responsive: true,
                columnDefs: [{ orderable: false, targets: [0, -1] }],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    paginate: { first: "«", last: "»", next: "›", previous: "‹" }
                }
            });
        }

        // Select All - Use DataTable API
        $('#select-all').on('click', function() {
            const table = $('#data-table').DataTable();
            const rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
            updateBulkDeleteButton();
        });

        // Use event delegation for checkboxes to handle DataTable redraws
        $(document).on('change', '.select-item', function() {
            updateBulkDeleteButton();
        });
    });

    function updateBulkDeleteButton() {
        const table = $('#data-table').DataTable();
        const selectedCount = table.$('.select-item:checked').length;
        
        $('#selected-count').text(selectedCount);
        if (selectedCount > 0) {
            $('#btn-bulk-delete').removeClass('d-none');
        } else {
            $('#btn-bulk-delete').addClass('d-none');
            $('#select-all').prop('checked', false);
        }
    }

    function bulkDelete() {
        const table = $('#data-table').DataTable();
        const selectedIds = [];
        table.$('.select-item:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Silakan pilih data yang akan dihapus.', 'info');
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Hapus Jadwal Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} jadwal secara permanen!`,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('sistem_akademik.course.bulkDestroy') }}",
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                    }
                });
            }
        });
    }

    // Handle Download PDF
    $('#download-timetable').on('click', function() {
        var container = document.getElementById('course-index');
        var baseUrl = container ? container.dataset.downloadUrl : null;
        if (!baseUrl) return;

        // Ambil semua parameter filter yang aktif
        var params = new URLSearchParams(window.location.search);
        var url = baseUrl + (baseUrl.indexOf('?') === -1 ? '?' : '&') + params.toString();
        
        window.open(url, '_blank');
    });

    function confirmDelete(id) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Data Course?',
            text: 'Data jadwal ini akan dihapus secara permanen!',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }
</script>
@endsection