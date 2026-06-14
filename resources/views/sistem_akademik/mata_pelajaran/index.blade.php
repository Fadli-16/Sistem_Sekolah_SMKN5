@extends('sistem_akademik.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle"><i class="bi bi-book-fill me-1"></i>Kelola data mata pelajaran di SMK Negeri 5 Padang</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @if(Auth::check() && (Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa'))
            <button type="button" id="btn-bulk-delete" class="btn btn-sm btn-danger-app d-none" onclick="bulkDelete()">
                <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
            </button>
            <a href="{{ route('sistem_akademik.mata_pelajaran.create') }}" class="btn-primary-app">
                <i class="bi bi-plus-lg"></i> Tambah Mata Pelajaran
            </a>
            @endif
        </div>
    </div>

    <div class="table-container mb-3 p-3">
        <form action="{{ route('sistem_akademik.mata_pelajaran.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Filter Jurusan</label>
                <select name="jurusan" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('sistem_akademik.mata_pelajaran.index') }}" class="btn btn-sm btn-secondary-app w-100">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover" id="data-table">
                <thead>
                    <tr>
                        @if(Auth::check() && (Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa'))
                        <th width="3%">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        @endif
                        <th width="5%">No</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Jurusan</th>
                        <th>Guru Pengampu</th>
                        @if(Auth::check() && (Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa'))
                        <th width="12%">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mapels as $index => $mapel)
                    <tr>
                        @if(Auth::check() && (Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa'))
                        <td>
                            <input type="checkbox" class="form-check-input select-item" value="{{ $mapel->id }}">
                        </td>
                        @endif
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:32px;height:32px;background:#eff6ff;border-radius:8px;
                                            display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0;">
                                    <i class="bi bi-book" style="font-size:0.85rem;"></i>
                                </div>
                                <span style="font-weight:500;font-size:0.875rem;">{{ $mapel->nama_mata_pelajaran }}</span>
                            </div>
                        </td>
                        <td>
                            @if($mapel->jurusan)
                                <span class="badge-modern badge-blue">{{ $mapel->jurusan }}</span>
                            @else
                                <span class="badge-modern badge-blue">Umum</span>
                            @endif
                        </td>
                        <td>
                            @php $guruNama = optional($mapel->guru)->nama ?? optional($mapel->guru)->name ?? null; @endphp
                            @if($guruNama)
                                <span class="badge-modern badge-teal">
                                    <i class="bi bi-person-workspace"></i> {{ $guruNama }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:0.8rem;">Belum ada guru</span>
                            @endif
                        </td>
                        @if(Auth::check() && (Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa'))
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sistem_akademik.mata_pelajaran.edit', $mapel->id) }}"
                                   class="btn-icon btn-icon-warning" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('sistem_akademik.mata_pelajaran.destroy', $mapel->id) }}"
                                      method="post" id="deleteForm{{ $mapel->id }}" class="d-inline">
                                    @csrf @method('delete')
                                    <button type="button" onclick="confirmDelete('{{ $mapel->id }}')"
                                            class="btn-icon btn-icon-danger" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($mapels->count() == 0)
        <div class="empty-state">
            <i class="bi bi-book-x"></i>
            <p>Belum ada data mata pelajaran</p>
            @if(Auth::check() && (Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa'))
            <a href="{{ route('sistem_akademik.mata_pelajaran.create') }}" class="btn-primary-app">
                <i class="bi bi-plus-lg"></i> Tambah Mata Pelajaran
            </a>
            @endif
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
                stateSave: true,
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

        // Select All - Use DataTable API to select all rows across pages
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
        // Use table.$ to find checkboxes across all pages
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
            title: 'Hapus Mata Pelajaran Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} mata pelajaran secara permanen!`,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('sistem_akademik.mata_pelajaran.bulkDestroy') }}",
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
            icon: 'warning', title: 'Hapus Mata Pelajaran?',
            text: 'Data mata pelajaran ini akan dihapus secara permanen!',
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