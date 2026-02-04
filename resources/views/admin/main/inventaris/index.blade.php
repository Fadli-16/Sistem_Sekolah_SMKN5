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

    .filter-container {
        background-color: var(--bg-light);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow);
    }

    .status-badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        border-radius: var(--radius-sm);
    }

    .status-Tersedia {
        background-color: #4ecdc4;
        color: #fff;
    }

    .status-Tidak {
        background-color: #ff6b6b;
        color: #fff;
    }

    .kondisi-badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        border-radius: var(--radius-sm);
    }

    .kondisi-Baik {
        background-color: #2ecc71;
        color: #fff;
    }

    .kondisi-Rusak-Ringan {
        background-color: #f39c12;
        color: #fff;
    }

    .kondisi-Rusak-Berat {
        background-color: #e74c3c;
        color: #fff;
    }

    .inventory-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: var(--radius);
        border: 2px solid #eee;
    }

    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--bg-gray);
        margin-bottom: 1rem;
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
            <h1 class="page-title">Inventaris Laboratorium</h1>
            <p class="text-muted">Kelola semua inventaris yang ada di laboratorium</p>
        </div>
        <a href="{{ route('admin.inventaris.create') }}" class="btn btn-secondary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Inventaris
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped w-100" id="inventarisTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Gambar</th>
                            <th width="20%">Nama Inventaris</th>
                            <th width="10%">Kategori</th>
                            <th width="8%">Jumlah</th>
                            <th width="12%">Kondisi</th>
                            <th width="10%">Lokasi</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventaris as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($item->gambar)
                                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama_inventaris }}" class="inventory-img">
                                @else
                                    <div class="inventory-img d-flex align-items-center justify-content-center bg-light">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $item->nama_inventaris }}</td>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>
                                <span class="kondisi-badge kondisi-{{ str_replace(' ', '-', $item->kondisi) }}">
                                    {{ $item->kondisi }}
                                </span>
                            </td>
                            <td>{{ $item->lokasi }}</td>
                            <td>
                                <span class="status-badge status-{{ explode(' ', $item->status)[0] }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.inventaris.show', $item->id) }}" class="btn btn-sm btn-secondary btn-action" data-bs-toggle="tooltip" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.inventaris.edit', $item->id) }}" class="btn btn-sm btn-warning btn-action" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger btn-action" onclick="confirmDelete('{{ $item->id }}')" data-bs-toggle="tooltip" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="deleteForm{{ $item->id }}" action="{{ route('admin.inventaris.destroy', $item->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="bi bi-box-seam empty-icon"></i>
                                    <p>Belum ada data inventaris.</p>
                                    <a href="{{ route('admin.inventaris.create') }}" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah Inventaris
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
        if (!$.fn.DataTable.isDataTable('#inventarisTable')) {
            $('#inventarisTable').DataTable({
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
                    },
                },
                order: [[0, 'asc']]
            });
        }

        // Tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    // Function to handle delete confirmation
    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Inventaris yang dihapus tidak dapat dikembalikan!",
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
