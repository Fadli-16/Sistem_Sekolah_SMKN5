@extends('sistem_akademik.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle"><i class="bi bi-newspaper me-1"></i>Kelola berita dan informasi sekolah</p>
        </div>
        <a href="{{ route('sistem_akademik.berita.create') }}" class="btn-primary-app">
            <i class="bi bi-plus-lg"></i> Tambah Berita
        </a>
    </div>

    <div class="table-container">
        {{-- Filter Bar --}}
        <div class="table-container-header" style="flex-wrap:wrap;gap:0.75rem; padding: 1rem 1.5rem; display:flex; align-items:center;">
            <div class="d-flex align-items-center gap-3">
                <span style="font-weight:600;font-size:0.875rem;color:#374151;white-space:nowrap;">Daftar Berita</span>
                <button type="button" id="btn-bulk-delete" class="btn btn-sm btn-danger-app d-none" onclick="bulkDelete()">
                    <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
                </button>
            </div>
            <form action="{{ route('sistem_akademik.berita.index') }}" method="GET"
                  class="d-flex align-items-center gap-2 flex-wrap" style="flex:1;justify-content:flex-end;">
                {{-- Kolom Search Custom --}}
                <div class="search-box-custom">
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Cari berita..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <select name="filter" class="form-select form-select-sm" style="width:160px;min-width:130px;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <option value="informasi"     {{ request('filter') === 'informasi'     ? 'selected' : '' }}>Informasi</option>
                    <option value="prestasi"      {{ request('filter') === 'prestasi'      ? 'selected' : '' }}>Prestasi</option>
                    <option value="pemberitahuan" {{ request('filter') === 'pemberitahuan' ? 'selected' : '' }}>Pemberitahuan</option>
                    <option value="terbaru"       {{ request('filter') === 'terbaru'       ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama"       {{ request('filter') === 'terlama'       ? 'selected' : '' }}>Terlama</option>
                </select>
                <div class="d-flex align-items-center gap-1" style="white-space:nowrap;">
                    <input type="date" name="from" class="form-control form-control-sm" style="width:145px;"
                           value="{{ request('from') }}" onchange="this.form.submit()">
                    <span class="text-muted" style="font-size:0.8rem;flex-shrink:0;">s/d</span>
                    <input type="date" name="to" class="form-control form-control-sm" style="width:145px;"
                           value="{{ request('to') }}" onchange="this.form.submit()">
                </div>
                <button type="submit" class="btn btn-sm btn-primary-app" style="box-shadow: none; padding: 0.4rem 0.8rem;">
                    <i class="bi bi-filter"></i>
                </button>
                <a href="{{ route('sistem_akademik.berita.index') }}" class="btn btn-sm btn-secondary-app" style="padding: 0.4rem 0.8rem;">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="data-table">
                <thead>
                    <tr>
                        <th width="3%">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th width="5%">No</th>
                        <th width="10%">Foto</th>
                        <th>Judul</th>
                        <th width="12%">Kategori</th>
                        <th width="14%">Tanggal</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($berita as $b)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input select-item" value="{{ $b->id }}">
                        </td>
                        <td>
                            @if($berita->firstItem())
                                {{ $berita->firstItem() + $loop->index }}
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </td>
                        <td>
                            @if($b->foto)
                                <img src="{{ asset('assets/berita/' . $b->foto) }}" alt=""
                                     style="width:70px;height:50px;object-fit:cover;border-radius:6px;">
                            @else
                                <div style="width:70px;height:50px;background:#f1f5f9;border-radius:6px;
                                            display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:0.7rem;">
                                    No Img
                                </div>
                            @endif
                        </td>
                        <td style="max-width:300px;">
                            <div style="font-weight:500;font-size:0.875rem;">{{ Str::limit($b->judul, 80) }}</div>
                            <div style="font-size:0.75rem;color:#64748b;">{{ Str::limit(strip_tags($b->isi), 80) }}</div>
                        </td>
                        <td>
                            @php
                                $cat = $b->kategori ?? '';
                                $badgeClass = match($cat) {
                                    'informasi'     => 'badge-info',
                                    'prestasi'      => 'badge-success',
                                    'pemberitahuan' => 'badge-warning',
                                    default         => 'badge-gray'
                                };
                            @endphp
                            <span class="badge-modern {{ $badgeClass }}">{{ ucfirst($cat ?: '-') }}</span>
                        </td>
                        <td style="font-size:0.8rem;color:#64748b;">
                            {{ optional($b->created_at)->format('d M Y, H:i') }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('sistem_akademik.berita.show', $b->id) }}"
                                   class="btn-icon btn-icon-info" title="Lihat">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('sistem_akademik.berita.edit', $b->id) }}"
                                   class="btn-icon btn-icon-warning" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('sistem_akademik.berita.destroy', $b->id) }}"
                                      method="post" id="deleteForm{{ $b->id }}" class="d-inline">
                                    @csrf @method('delete')
                                    <button type="button" onclick="confirmDelete('{{ $b->id }}')"
                                            class="btn-icon btn-icon-danger" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                                @if(!empty($b->file))
                                <a href="{{ asset('file/' . $b->file) }}" class="btn-icon btn-icon-info" title="Unduh" target="_blank" download>
                                    <i class="bi bi-download"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color:#94a3b8;">
                            <i class="bi bi-newspaper" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
                            Belum ada berita untuk kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center py-3">
            {!! $berita->appends(request()->query())->links() !!}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Suppress DataTables alert warnings and log to console instead
        $.fn.dataTable.ext.errMode = 'none';

        if ($.fn.DataTable.isDataTable('#data-table')) {
            $('#data-table').DataTable().destroy();
        }
        $('#data-table').DataTable({
            responsive: true,
            autoWidth: false,
            searching: false,
            columnDefs: [{ orderable: false, targets: [0, 2, 6] }],
            language: {
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Data tidak ditemukan",
                paginate: { first: "«", last: "»", next: "›", previous: "‹" }
            }
        });

        // Select All Checkbox - Use DataTable API to select all rows across pages
        $('#select-all').on('click', function() {
            const table = $('#data-table').DataTable();
            const rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
            updateBulkDeleteButton();
        });

        // Use event delegation for individual checkboxes
        $(document).on('change', '.select-item', function() {
            updateBulkDeleteButton();
        });
    });

    function updateBulkDeleteButton() {
        const selectedCount = $('.select-item:checked').length;
        $('#selected-count').text(selectedCount);
        if (selectedCount > 0) {
            $('#btn-bulk-delete').removeClass('d-none');
        } else {
            $('#btn-bulk-delete').addClass('d-none');
            $('#select-all').prop('checked', false);
        }
    }

    function bulkDelete() {
        // Use DataTable API to find checked boxes even on other pages
        const table = $('#data-table').DataTable();
        const selectedIds = [];
        table.$('.select-item:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Silakan pilih berita yang akan dihapus.', 'info');
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Hapus Berita Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} berita secara permanen!`,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('sistem_akademik.berita.bulkDestroy') }}",
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
            title: 'Hapus Berita?',
            text: 'Berita ini akan dihapus secara permanen!',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('deleteForm' + id).submit();
        });
    }
</script>
@endsection