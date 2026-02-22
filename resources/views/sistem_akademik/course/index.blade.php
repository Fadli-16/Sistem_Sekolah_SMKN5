@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    /* header utama: judul + add button di satu bar */
    .table-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.6rem;
    }

    /* bar kedua: controls filter (full width under header) */
    .table-header-controls {
        display: flex;
        align-items: center;
        gap: .5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    /* title kecil di kiri */
    .table-title {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    /* tombol tambah di kanan */
    .add-course-wrap {
        flex: 0 0 auto;
    }

    /* style select kecil */
    .form-select-sm {
        height: calc(1.5em + .5rem + 2px);
        padding: .25rem .5rem;
    }

    /* small adjustments for DataTables filter area when download button moved */
    .dataTables_filter .btn {
        vertical-align: middle;
    }

    /* responsive */
    @media (max-width:767.98px) {
        .table-header-controls {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-3 mb-3" id="course-index"
    data-timetable-url="{{ route('sistem_akademik.course.timetable') }}"
    data-download-url="{{ route('sistem_akademik.course.download-timetable') }}">

    <h1 class="page-title">{{ $header }}</h1>
    <p class="text-muted mb-4">Kelola data course, termasuk siswa yang tergabung di dalamnya</p>

    <div class="table-container">
        <!-- bar 1: judul kiri + add-button kanan -->
        <div class="table-header-top">
            <div class="table-title">
                <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Daftar Course</h5>
            </div>

            <div class="add-course-wrap">
                @if(in_array(Auth::user()->role, ['admin','super_admin','admin_sa']))
                <a href="{{ route('sistem_akademik.course.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah Course
                </a>
                @endif
            </div>
        </div>

        <!-- bar 2: controls / filter -->
        <div class="table-header-controls">
            @php
            $kelasList = $kelasList ?? (\App\Models\Kelas::orderBy('nama_kelas')->get());
            $user = Auth::user();
            $isAdmin = in_array($user->role, ['admin','super_admin','admin_sa']);
            $isGuru = ($user->role === 'guru');
            $isSiswa = ($user->role === 'siswa');
            $siswaKelasId = optional(optional($user)->siswa)->kelas_id ?? null;
            @endphp

            @if($isAdmin || $isGuru)
            <label class="mb-0 small text-muted d-inline-block me-1">Pilih Kelas</label>
            <select id="filter-kelas" class="form-select form-select-sm me-1"
                data-selected-kelas="{{ $selectedKelasId ?? '' }}"
                style="min-width:220px;">
                <option value="">— Semua Kelas —</option>
                @foreach($kelasList as $k)
                <option value="{{ $k->id }}" @if(($selectedKelasId ?? '' )==$k->id) selected @endif>
                    {{ $k->nama_kelas }} — {{ $k->jurusan }} ({{ $k->tahun_ajaran }})
                </option>
                @endforeach
            </select>

            <button id="download-timetable" class="btn btn-primary btn-sm">
                <i class="bi bi-download"></i> Download PDF
            </button>
            @elseif($isSiswa && $siswaKelasId)
            <div class="me-2">
                <small class="text-muted">Kelas Anda:</small>
                <span class="badge bg-info text-dark">{{ optional(\App\Models\Kelas::find($siswaKelasId))->nama_kelas ?? '—' }}</span><small class="text-muted"> {{ optional(\App\Models\Kelas::find($siswaKelasId))->jurusan ?? '—' }} ({{ optional(\App\Models\Kelas::find($siswaKelasId))->tahun_ajaran ?? '—' }})</small>
            </div>

            <button id="download-timetable-siswa" class="btn btn-primary btn-sm">
                <i class="bi bi-download"></i> Download PDF
            </button>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover" id="data-table" style="width:100%;">
            <thead>
                <tr>
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
                    <td>{{ $index + 1 }}</td>

                    {{-- Kelas (null-safe) --}}
                    <td>
                        @if($course->kelas)
                        <span class="badge bg-info text-dark">{{ $course->kelas->nama_kelas ?? '-' }}</span><br>
                        <small>{{ $course->kelas->jurusan ?? '-' }}</small>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ optional($course->mataPelajaran)->nama_mata_pelajaran ?? '-' }}</td>
                    <td>{{ optional(optional($course->mataPelajaran)->guru)->nama ?? optional(optional($course->mataPelajaran)->guru)->name ?? '-' }}</td>
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
                        <a href="{{ route('sistem_akademik.course.show', $course->id) }}" class="btn btn-secondary btn-sm" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>

                        @if(in_array(Auth::user()->role, ['admin','super_admin','admin_sa']))
                        <a href="{{ route('sistem_akademik.course.edit', $course->id) }}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('sistem_akademik.course.destroy', $course->id) }}" method="post" id="deleteForm{{ $course->id }}" class="d-inline">
                            @csrf
                            @method('delete')
                            <button type="button" onclick="confirmDelete('{{ $course->id }}')" class="btn btn-danger btn-sm" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
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
        @if(in_array(Auth::user()->role, ['admin','super_admin','admin_sa']))
        <a href="{{ route('sistem_akademik.course.create') }}" class="btn-primary-app">
            <i class="bi bi-plus-circle"></i> Tambah Course
        </a>
        @endif
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        var dt;
        if (!$.fn.DataTable.isDataTable('#data-table')) {
            dt = $('#data-table').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data yang ditampilkan",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                    "rt" +
                    "<'row'<'col-sm-6'i><'col-sm-6'p>>"
            });
        } else {
            dt = $('#data-table').DataTable();
        }

        function moveDownloadButtonToFilter() {
            try {
                var $filter = $('.dataTables_filter');
                var $download = $('#download-timetable');

                if ($download.length && $filter.length && $filter.find('#download-timetable').length === 0) {
                    // move and style a bit
                    $download.detach().addClass('ms-2').appendTo($filter);
                }
            } catch (e) {
                console.warn('moveDownloadButtonToFilter error', e);
            }
        }

        // run after short delay to ensure DataTables rendered filter
        setTimeout(moveDownloadButtonToFilter, 50);
        // also try again in case of slow rendering
        setTimeout(moveDownloadButtonToFilter, 300);

        // ensure selected kelas value restored from data-attribute
        var sel = document.getElementById('filter-kelas');
        if (sel && sel.dataset && sel.dataset.selectedKelas) {
            try {
                sel.value = sel.dataset.selectedKelas;
            } catch (e) {
                /* ignore */
            }
        }

        // ---------- URLs ----------
        var container = document.getElementById('course-index');
        var timetableBase = container ? container.dataset.timetableUrl : null;
        var downloadBase = container ? container.dataset.downloadUrl : null;

        function openUrlWithKelas(baseUrl, kelasId) {
            if (!baseUrl) return;
            var url = baseUrl;
            if (kelasId) {
                url += (url.indexOf('?') === -1 ? '?' : '&') + 'kelas_id=' + encodeURIComponent(kelasId);
            }
            window.open(url, '_blank');
        }

        // ---------- bind download/preview buttons ----------
        // admin/guru download (moved into filter if DataTable exists)
        if (document.getElementById('download-timetable')) {
            $('#download-timetable').off('click').on('click', function() {
                var kelasId = $('#filter-kelas').val() || '';
                openUrlWithKelas(downloadBase, kelasId);
            });
        }

        // student download
        var siswaKelasId = @json($siswaKelasId ?? '');
        if (document.getElementById('download-timetable-siswa')) {
            $('#download-timetable-siswa').off('click').on('click', function() {
                openUrlWithKelas(downloadBase, siswaKelasId);
            });
        }

        // Optional: preview buttons if present (keberadaan tidak diwajibkan)
        if (document.getElementById('preview-timetable')) {
            $('#preview-timetable').off('click').on('click', function() {
                var kelasId = $('#filter-kelas').val() || '';
                openUrlWithKelas(timetableBase, kelasId);
            });
        }
        if (document.getElementById('preview-timetable-siswa')) {
            $('#preview-timetable-siswa').off('click').on('click', function() {
                openUrlWithKelas(timetableBase, siswaKelasId);
            });
        }
    });

    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Data kelas akan dihapus secara permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }
</script>
@endsection