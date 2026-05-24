@extends('sistem_akademik.layouts.main')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle"><i class="bi bi-people-fill me-1"></i>Kelola biodata seluruh siswa</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button type="button" id="btn-bulk-delete" class="btn btn-sm btn-danger-app d-none" onclick="bulkDelete()">
                <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
            </button>
            <a href="{{ route('sistem_akademik.siswa.create') }}" class="btn-primary-app">
                <i class="bi bi-plus-lg"></i> Tambah Siswa
            </a>
        </div>
    </div>

    <div class="table-container mb-3 p-3">
        <form action="{{ route('sistem_akademik.siswa.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Filter Jurusan</label>
                <select name="jurusan" class="form-select form-select-sm" onchange="document.querySelector('select[name=\'kelas_id\']').value=''; this.form.submit()">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $j)
                        <option value="{{ $j->jurusan }}" {{ request('jurusan') == $j->jurusan ? 'selected' : '' }}>{{ $j->jurusan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Filter Kelas</label>
                <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('sistem_akademik.siswa.index') }}" class="btn btn-sm btn-secondary-app w-100">
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
                        <th width="3%">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th width="4%">No</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Jenis Kelamin</th>
                        <th>Agama</th>
                        <th>No HP</th>
                        <th width="8%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $index => $student)
                    @php
                        $defaultImage = asset('assets/profile/default.png');
                        $avatar = $student->image ? asset('assets/profile/' . ltrim($student->image, '/')) : $defaultImage;
                        $name   = optional($student->user)->nama ?? '-';
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input select-item" value="{{ $student->id }}">
                        </td>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="{{ $avatar }}" alt="avatar" class="avatar-circle"
                                     onerror="this.onerror=null;this.src='{{ $defaultImage }}'">
                                <div>
                                    <div style="font-weight:600;font-size:0.875rem;">{{ $name }}</div>
                                    <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">NIS: {{ $student->nis ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $student->kelas ?? '-' }}</td>
                        <td>
                            @if($student->jurusan)
                                <span class="badge-modern badge-info">{{ $student->jurusan }}</span>
                            @else -
                            @endif
                        </td>
                        <td>
                            @if($student->jenis_kelamin === 'Laki-laki')
                                <span class="badge-modern badge-info"><i class="bi bi-gender-male"></i> L</span>
                            @elseif($student->jenis_kelamin === 'Perempuan')
                                <span class="badge-modern badge-purple"><i class="bi bi-gender-female"></i> P</span>
                            @else -
                            @endif
                        </td>
                        <td>{{ $student->agama ?? '-' }}</td>
                        <td>{{ $student->no_hp ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sistem_akademik.siswa.edit', $student->id) }}"
                                   class="btn-icon btn-icon-warning" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('sistem_akademik.siswa.destroy', $student->id) }}"
                                      method="post" id="deleteForm{{ $student->id }}" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete('{{ $student->id }}')"
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

        @if($students->isEmpty())
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <p>Belum ada data siswa</p>
            <a href="{{ route('sistem_akademik.siswa.create') }}" class="btn-primary-app">
                <i class="bi bi-plus-lg"></i> Tambah Siswa Pertama
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
                responsive: false,
                autoWidth: false,
                columnDefs: [{ orderable: false, targets: [0, -1] }],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total)",
                    paginate: { first: "«", last: "»", next: "›", previous: "‹" },
                    zeroRecords: "Data tidak ditemukan"
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

        // Event delegation for checkboxes
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
            title: 'Hapus Data Siswa Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} data siswa secara permanen!`,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('sistem_akademik.siswa.bulkDestroy') }}",
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
            icon: 'warning',
            title: 'Hapus Data Siswa?',
            text: 'Data siswa ini akan dihapus secara permanen!',
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
