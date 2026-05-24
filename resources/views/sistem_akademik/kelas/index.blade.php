@extends('sistem_akademik.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle"><i class="bi bi-building me-1"></i>Kelola data kelas SMK Negeri 5 Padang</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button type="button" id="btn-bulk-delete" class="btn btn-sm btn-danger-app d-none" onclick="bulkDelete()">
                <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
            </button>
            <a href="{{ route('sistem_akademik.kelas.create') }}" class="btn-primary-app">
                <i class="bi bi-plus-lg"></i> Tambah Kelas
            </a>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="table-container mb-3" style="padding: 1rem 1.25rem;">
        <form method="GET" action="{{ route('sistem_akademik.kelas.index') }}" id="filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-auto">
                    <label class="form-label fw-semibold mb-1" style="font-size:0.85rem;"><i class="bi bi-mortarboard me-1"></i>Jurusan</label>
                    <select name="filter_jurusan" id="filter_jurusan" class="form-select form-select-sm" style="min-width:200px;">
                        <option value="">-- Semua Jurusan --</option>
                        @foreach($jurusanList as $j)
                            <option value="{{ $j }}" {{ $selectedJurusan == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <label class="form-label fw-semibold mb-1" style="font-size:0.85rem;"><i class="bi bi-calendar-range me-1"></i>Tahun Ajaran</label>
                    <select name="filter_tahun_ajaran" id="filter_tahun_ajaran" class="form-select form-select-sm" style="min-width:160px;">
                        <option value="">-- Semua Tahun --</option>
                        @foreach($tahunAjaranList as $ta)
                            <option value="{{ $ta }}" {{ $selectedTahunAjaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary-app">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    @if($selectedJurusan || $selectedTahunAjaran)
                    <a href="{{ route('sistem_akademik.kelas.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                    @endif
                </div>
                @if($selectedJurusan || $selectedTahunAjaran)
                <div class="col-12 col-md-auto d-flex align-items-end">
                    <span class="badge bg-info text-dark" style="font-size:0.78rem; padding:0.4rem 0.7rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Menampilkan {{ $kelas->count() }} kelas
                        @if($selectedJurusan) &bull; {{ $selectedJurusan }} @endif
                        @if($selectedTahunAjaran) &bull; {{ $selectedTahunAjaran }} @endif
                    </span>
                </div>
                @endif
            </div>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover" id="data-table">
                <thead>
                    <tr>
                        <th width="3%">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th width="5%">No</th>
                        <th width="6%">ID</th>
                        <th width="8%">Kode</th>
                        <th>Jurusan</th>
                        <th>Tahun Ajaran</th>
                        <th>Wali Kelas</th>
                        <th>Guru BK</th>
                        <th>Ruangan</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kelas as $index => $item)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input select-item" value="{{ $item->id }}">
                        </td>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-secondary" style="font-size:0.75rem; font-family:monospace;">{{ $item->id }}</span></td>
                        <td><span class="badge-modern badge-orange">{{ $item->nama_kelas }}</span></td>
                        <td>
                            <span class="badge-modern badge-info">{{ $item->jurusan }}</span>
                        </td>
                        <td>{{ $item->tahun_ajaran }}</td>
                        <td>
                            @if(optional($item->waliKelas)->nama)
                                <div style="font-size:0.875rem;font-weight:500;">{{ $item->waliKelas->nama }}</div>
                            @else
                                <span class="text-muted" style="font-size:0.8rem;">Belum ditentukan</span>
                            @endif
                        </td>
                        <td>
                            @if(optional($item->guruBK)->nama)
                                <div style="font-size:0.875rem;font-weight:500;">{{ $item->guruBK->nama }}</div>
                            @else
                                <span class="text-muted" style="font-size:0.8rem;">Belum ditentukan</span>
                            @endif
                        </td>
                        <td>{{ $item->ruangan ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sistem_akademik.kelas.edit', $item->id) }}"
                                   class="btn-icon btn-icon-warning" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('sistem_akademik.kelas.destroy', $item->id) }}"
                                      method="post" id="deleteForm{{ $item->id }}" class="d-inline">
                                    @csrf @method('delete')
                                    <button type="button" onclick="confirmDelete('{{ $item->id }}')"
                                            class="btn-icon btn-icon-danger" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($kelas->count() == 0)
        <div class="empty-state">
            <i class="bi bi-building-x"></i>
            <p>Belum ada data kelas</p>
            <a href="{{ route('sistem_akademik.kelas.create') }}" class="btn-primary-app">
                <i class="bi bi-plus-lg"></i> Tambah Kelas
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#data-table')) {
            $('#data-table').DataTable({
                responsive: true,
                columnDefs: [{ orderable: false, targets: [0, -1] }],
                language: {
                    search: "Cari:", lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data", zeroRecords: "Data tidak ditemukan",
                    paginate: { first: "«", last: "»", next: "›", previous: "‹" }
                }
            });
        }

        // Auto-submit filter saat dropdown berubah
        $('#filter_jurusan, #filter_tahun_ajaran').on('change', function () {
            $('#filter-form').submit();
        });

        // Select All - Use DataTable API
        $('#select-all').on('click', function() {
            const table = $('#data-table').DataTable();
            const rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
            updateBulkDeleteButton();
        });

        // Event delegation for checkboxes
        $(document).on('change', '.select-item', function() {
            updateBulkDeleteButton();
        });
    });

    function updateBulkDeleteButton() {
        const table = $('#data-table').DataTable();
        const selectedCount = table.$('.select-item:checked').length;
        
        if ($('#selected-count').length) {
            $('#selected-count').text(selectedCount);
        }
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
            title: 'Hapus Data Kelas Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} data kelas secara permanen!`,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('sistem_akademik.kelas.bulkDestroy') }}",
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

    function confirmDelete(id) {
        Swal.fire({
            icon: 'warning', title: 'Hapus Data Kelas?',
            text: 'Data kelas ini akan dihapus secara permanen!',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal', reverseButtons: true, focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('deleteForm' + id).submit();
        });
    }
</script>
@endsection
