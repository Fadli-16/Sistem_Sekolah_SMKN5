@extends('siswa.layouts.main')

@section('css')
<style>
    .laporan-container {
        background-color: var(--bg-light);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 2rem;
    }

    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3.5rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }

    .laporan-title {
        color: var(--primary);
        margin-bottom: 1.5rem;
        font-weight: 600;
        position: relative;
        padding-bottom: 0.75rem;
    }

    .laporan-title::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        height: 3px;
        width: 60px;
        background-color: var(--secondary);
    }

    .status-badge {
        font-size: 0.8rem;
        padding: 0.35rem 0.65rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
    }

    .status-pending {
        background-color: #ffeeba;
        color: #856404;
    }

    .status-process {
        background-color: #b8daff;
        color: #004085;
    }

    .status-completed {
        background-color: #c3e6cb;
        color: #155724;
    }

    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-sm);
        transition: var(--transition);
    }

    .table-responsive {
        border-radius: var(--radius);
        overflow: hidden;
    }

    .laporan-table {
        margin-bottom: 0;
    }

    .laporan-table th {
        background-color: var(--primary);
        color: white;
        font-weight: 600;
        border: none;
        padding: 1rem;
    }

    .laporan-table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .laporan-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        background-color: #fff;
        transition: var(--transition);
    }

    .laporan-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .tanggapan-preview {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .badge-response {
        background-color: #e0e0e0;
        color: #333;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
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
            <h1 class="page-title">Laporan Kerusakan</h1>
            <p class="text-muted">Laporkan kerusakan alat atau perangkat laboratorium</p>
        </div>
        <a href="{{ route('siswa.laporan.create') }}" class="btn btn-secondary">
            <i class="bi bi-plus-lg me-1"></i> Buat Laporan
        </a>
    </div>

    <div class="laporan-container">
        <h2 class="laporan-title">Daftar Laporan Kerusakan Saya</h2>

        <div class="table-responsive">
            <table class="table laporan-table" id="laporanTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Alat</th>
                        <th>Deskripsi Kerusakan</th>
                        <th>Tanggal Laporan</th>
                        <th>Status</th>
                        <th>Tanggapan Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($laporan) && $laporan->isNotEmpty())
                        @foreach($laporan as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->nama_alat }}</td>
                                <td>
                                    @if(strlen($data->deskripsi_kerusakan) > 50)
                                        {{ substr($data->deskripsi_kerusakan, 0, 50) }}...
                                        <button class="btn btn-link p-0 ms-1" onclick="showFullDescription('{{ addslashes($data->deskripsi_kerusakan) }}')">Lihat</button>
                                    @else
                                        {{ $data->deskripsi_kerusakan }}
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($data->tanggal_laporan)->format('d M Y') }}</td>
                                <td>
                                    <span class="status-badge status-{{ $data->status ?? 'pending' }}">
                                        {{ ucfirst($data->status ?? 'Pending') }}
                                    </span>
                                </td>
                                <td>
                                    @if($data->tanggapan)
                                        <div class="tanggapan-preview">
                                            {{ $data->tanggapan }}
                                            @if(strlen($data->tanggapan) > 30)
                                                <button class="btn btn-link p-0 ms-1" onclick="showFullResponse('{{ addslashes($data->tanggapan) }}')">Lihat</button>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge-response">Belum ada tanggapan</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('siswa.laporan.show', $data->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-clipboard-x empty-icon"></i>
                                    <h5>Belum ada laporan kerusakan</h5>
                                    <p class="text-muted">Anda belum pernah membuat laporan kerusakan</p>
                                    <a href="{{ route('siswa.laporan.create') }}" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Buat Laporan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Deskripsi -->
<div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="descriptionModalLabel">Deskripsi Lengkap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="fullDescription"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tanggapan -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Tanggapan Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="fullResponse"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        if (!$.fn.DataTable.isDataTable('#laporanTable')) {
            $('#laporanTable').DataTable({
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
                dom: '<"top"lf>rt<"bottom"ip>',
            });
        }
    });

    function showFullDescription(description) {
        document.getElementById('fullDescription').innerText = description;
        var descModal = new bootstrap.Modal(document.getElementById('descriptionModal'));
        descModal.show();
    }

    function showFullResponse(response) {
        document.getElementById('fullResponse').innerText = response;
        var respModal = new bootstrap.Modal(document.getElementById('responseModal'));
        respModal.show();
    }
</script>
@endsection
