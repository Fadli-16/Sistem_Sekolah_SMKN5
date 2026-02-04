@extends('admin.layouts.main')

@section('css')
<style>
    .btn-action {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius);
        transition: var(--transition);
    }

    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        display: block;
    }

    .table-responsive {
        min-height: 300px;
    }

    .dtr-control {
        cursor: pointer;
    }

    .table > :not(caption) > * > * {
        padding: 0.75rem 1rem;
    }

    .table thead th {
        font-weight: 600;
        border-bottom-width: 1px;
    }
    /* Warna untuk teks paginasi yang tidak aktif */
    .pagination .page-link {
        color: #fd7e14; /* Warna oranye untuk teks */
    }

    /* Warna latar belakang tombol aktif (yang sudah Anda terapkan) */
    .pagination .page-item.active .page-link {
        background-color: #fd7e14; /* Warna oranye */
        border-color: #fd7e14; /* Warna oranye */
        color: white; /* Teks putih untuk kontras */
    }

    /* Efek hover untuk tombol yang tidak aktif */
    .pagination .page-link:hover {
        color: #e66a00; /* Oranye sedikit lebih gelap untuk teks saat hover */
        /* Anda bisa menambahkan background-color dan border-color di sini jika ingin */
        /* background-color: rgba(253, 126, 20, 0.1); */ /* Contoh: latar belakang oranye transparan saat hover */
        /* border-color: rgba(253, 126, 20, 0.1); */
    }

    /* Efek hover untuk tombol aktif (yang sudah Anda terapkan) */
    .pagination .page-item.active .page-link:focus,
    .pagination .page-item.active .page-link:hover {
        background-color: #e66a00; /* Oranye sedikit lebih gelap saat hover/focus */
        border-color: #e66a00;
    }

    /* Pastikan warna border default juga oranye jika Anda menginginkan konsistensi */
    .pagination .page-item .page-link {
        border-color: #dee2e6; /* Border default Bootstrap, Anda bisa mengubahnya */
        /* Misalnya, jika ingin border juga oranye untuk semua */
        /* border-color: #fd7e14; */
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Manajemen Laboratorium</h1>
            <p class="text-muted">Kelola daftar laboratorium yang tersedia di sekolah</p>
        </div>
        <a href="{{ route('admin.labor.create') }}" class="btn btn-secondary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Laboratorium
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped w-100" id="laborTable">
                    <thead class="table-light">
    <tr>
        <th width="5%">No</th>
        <th width="20%">Nama Laboratorium</th>
        <th width="10%">Kode</th>
        <th width="25%">Deskripsi</th>
        <th width="20%">Penanggung Jawab</th>
        <th width="20%">Teknisi</th>
        <th width="10%">Foto</th>
        <th width="20%" class="text-center">Aksi</th>
    </tr>
<tbody>
@forelse($labor as $index => $item)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $item->nama_labor }}</td>
    <td>{{ $item->kode ?: '-' }}</td>
    <td>{{ $item->deskripsi ?: '-' }}</td>
    <td>{{ $item->penanggung_jawab ?: '-' }}</td>
    <td>{{ $item->teknisi ?: '-' }}</td>
    <td class="text-center">
        @if($item->foto)
            <img src="{{ asset('storage/labor_foto/' . $item->foto) }}" alt="Foto Laboratorium" style="width: 60px; height: auto; border-radius: 4px;">
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td class="text-center">
        <div class="d-flex gap-1 justify-content-center">
            <a href="{{ route('admin.labor.edit', $item->id) }}" class="btn btn-sm btn-warning btn-action" data-bs-toggle="tooltip" title="Edit">
                <i class="bi bi-pencil-square"></i>
            </a>
            <button class="btn btn-sm btn-danger btn-action" onclick="confirmDelete('{{ $item->id }}')" data-bs-toggle="tooltip" title="Hapus">
                <i class="bi bi-trash"></i>
            </button>
            <form id="deleteForm{{ $item->id }}" action="{{ route('admin.labor.destroy', $item->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-4">
        <div class="empty-state">
            <i class="bi bi-building-x empty-icon"></i>
            <p>Belum ada laboratorium yang terdaftar.</p>
            <a href="{{ route('admin.labor.create') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Laboratorium
            </a>
        </div>
    </td>
</tr>
@endforelse
</tbody>


                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Check if the table is already initialized as a DataTable
        if (!$.fn.DataTable.isDataTable('#laborTable')) {
            $('#laborTable').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRow,
                        type: 'column',
                        target: 'tr'
                    }
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // No column always visible
                    { responsivePriority: 2, targets: 1 }, // Name column always visible
                    { responsivePriority: 3, targets: 4 }  // Actions column always visible
                ],
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
                    },
                    emptyTable: "Tidak ada data tersedia"
                }
            });
        }

        // Tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    // Function to handle delete confirmation
    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Laboratorium yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4ecdc4",
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
