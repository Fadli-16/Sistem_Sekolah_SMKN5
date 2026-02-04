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

    .filter-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .filter-title i {
        margin-right: 0.5rem;
        color: var(--secondary);
    }

    .badge-kosong {
        background-color: #4ecdc4;
    }

    .badge-terpakai {
        background-color: #ff6b6b;
    }

    .lab-badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        border-radius: var(--radius-sm);
        display: inline-block;
        color: black;
    }

    [class^="lab-badge-"] {
        background-color: #6c757d; /* Default color */
        color: white;
    }

    .lab-badge-TKJ {
        background-color: #4e73df;
    }

    .lab-badge-RPL {
        background-color: #2ecc71;
    }

    .lab-badge-MM {
        background-color: #f39c12;
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
            <h1 class="page-title">Jadwal Laboratorium</h1>
            <p class="text-muted">Kelola jadwal penggunaan laboratorium sekolah</p>
        </div>
        <a href="{{ route('admin.jadwal.create') }}" class="btn btn-secondary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped w-100" id="jadwalTable">
                    <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Laboratorium</th>
                        <th width="20%">Hari</th>
                        <th width="20%">Waktu</th>
                        <th width="20%">Keterangan</th>
                        <th width="15%">Aksi</th>
                    </tr>

                    </thead>
                   <tbody>
    @php
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
    @endphp
    @forelse($jadwal as $index => $item)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>
            @php
                $laborInfo = App\Models\Labor::where('kode', $item->labor)->first();
                $laborName = $laborInfo ? $laborInfo->nama_labor : $item->labor;
                $laborCode = $item->labor;
            @endphp
            <span class="lab-badge lab-badge-{{ $laborCode }}">
                {{ $laborName }}
            </span>
        </td>
        <td>{{ $item->hari }}</td>
        <td>{{ \Carbon\Carbon::parse($item->start)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->end)->format('H:i') }}</td>
        <td>{{ $item->keterangan ?? '-' }}</td>
        <td>
            <div class="d-flex gap-1">
                <a href="{{ route('admin.jadwal.edit', $item->id) }}" class="btn btn-sm btn-warning btn-action" data-bs-toggle="tooltip" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <button class="btn btn-sm btn-danger btn-action" onclick="confirmDelete('{{ $item->id }}')" data-bs-toggle="tooltip" title="Hapus">
                    <i class="bi bi-trash"></i>
                </button>

                <form id="deleteForm{{ $item->id }}" action="{{ route('admin.jadwal.destroy', $item->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="text-center py-4">
            <div class="empty-state">
                <i class="bi bi-calendar-x empty-icon"></i>
                <p>Belum ada jadwal laboratorium.</p>
                <a href="{{ route('admin.jadwal.create') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal
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
        // Inisialisasi DataTable hanya sekali
        let table = null;

        if (!$.fn.DataTable.isDataTable('#jadwalTable')) {
            table = $('#jadwalTable').DataTable({
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
                order: [[2, 'desc'], [3, 'asc']] // Urut berdasarkan hari dan waktu
            });
        } else {
            table = $('#jadwalTable').DataTable();
            table.draw();
        }

        // Fungsi untuk memberikan warna badge labor yang tidak predefined
        function applyLabBadgeColors() {
            $('.lab-badge').each(function() {
                // Lewati badge yang sudah memiliki warna khusus
                if (!$(this).hasClass('lab-badge-TKJ') &&
                    !$(this).hasClass('lab-badge-RPL') &&
                    !$(this).hasClass('lab-badge-MM')) {

                    const classes = $(this).attr('class').split(/\s+/);
                    let labCode = '';
                    for (let c of classes) {
                        if (c.startsWith('lab-badge-') && c !== 'lab-badge') {
                            labCode = c.replace('lab-badge-', '');
                            break;
                        }
                    }

                    if (labCode) {
                        let hash = 0;
                        for (let i = 0; i < labCode.length; i++) {
                            hash = labCode.charCodeAt(i) + ((hash << 5) - hash);
                        }
                        const hue = hash % 360;
                        $(this).css({
                            'background-color': `hsl(${hue}, 70%, 40%)`,
                            'color': 'white'
                        });
                    }
                }
            });
        }

        // Jalankan apply warna saat pertama load
        applyLabBadgeColors();

        // Jalankan ulang saat tabel digambar ulang (pagination, search, sort)
        table.on('draw', function() {
            applyLabBadgeColors();
        });

        // Tooltip Bootstrap
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    // Fungsi konfirmasi hapus dengan Swal
    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Jadwal yang dihapus tidak dapat dikembalikan!",
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

