@extends('admin.layouts.main')

@section('css')
<style>
    .laporan-container {
        background-color: var(--bg-light);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .laporan-table {
        width: 100%;
        margin: 0 auto;
        border-collapse: collapse;
    }

    .laporan-table th,
    .laporan-table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #eee;
    }

    .laporan-table th {
        background-color: var(--primary);
        color: white;
        font-weight: 600;
    }

    .laporan-table tr:nth-child(even) {
        background-color: #f9f9f9;
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

    .btn-action {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-sm);
        transition: var(--transition);
        margin-right: 5px;
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
            <p class="text-muted">Kelola dan proses laporan kerusakan dari siswa</p>
        </div>
    </div>

    <div class="laporan-container">
        <div class="table-responsive">
            <table class="laporan-table" id="laporanTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pelapor</th>
                        <th>Nama Alat</th>
                        <th>Deskripsi Kerusakan</th>
                        <th>Tanggal Laporan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($laporan->isNotEmpty())
                        @foreach($laporan as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->nama_pelapor }}</td>
                                <td>{{ $data->nama_alat }}</td>
                                <td>{{ substr($data->deskripsi_kerusakan, 0, 50) }}{{ strlen($data->deskripsi_kerusakan) > 50 ? '...' : '' }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->tanggal_laporan)->format('d M Y') }}</td>
                                <td>
                                    <span class="status-badge status-{{ $data->status ?? 'pending' }}">
                                        {{ ucfirst($data->status ?? 'Pending') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-secondary btn-action" data-bs-toggle="modal" data-bs-target="#viewModal{{ $data->id }}" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-warning btn-action" data-bs-toggle="modal" data-bs-target="#processModal{{ $data->id }}" title="Proses">
                                            <i class="bi bi-gear"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- View Modal -->
                            <div class="modal fade" id="viewModal{{ $data->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Laporan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h6>Informasi Pelapor</h6>
                                                    <p><strong>Nama:</strong> {{ $data->nama_pelapor }}</p>
                                                    <p><strong>Tanggal Laporan:</strong> {{ \Carbon\Carbon::parse($data->tanggal_laporan)->format('d M Y') }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Status Laporan</h6>
                                                    <span class="status-badge status-{{ $data->status ?? 'pending' }}">
                                                        {{ ucfirst($data->status ?? 'Pending') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <h6>Informasi Kerusakan</h6>
                                                <p><strong>Nama Alat:</strong> {{ $data->nama_alat }}</p>
                                                <p><strong>Deskripsi Kerusakan:</strong></p>
                                                <div class="p-3 bg-light rounded">
                                                    {{ $data->deskripsi_kerusakan }}
                                                </div>
                                            </div>
                                            @if($data->tanggapan)
                                            <div class="mb-3">
                                                <h6>Tanggapan Admin</h6>
                                                <div class="p-3 bg-light rounded">
                                                    {{ $data->tanggapan }}
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Process Modal -->
                            <div class="modal fade" id="processModal{{ $data->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Proses Laporan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.kelola.laporan.update', $data->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="status{{ $data->id }}" class="form-label">Status Laporan</label>
                                                    <select class="form-select" id="status{{ $data->id }}" name="status" required>
                                                        <option value="pending" {{ $data->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="process" {{ $data->status == 'process' ? 'selected' : '' }}>Dalam Proses</option>
                                                        <option value="completed" {{ $data->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                                        <option value="rejected" {{ $data->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="tanggapan{{ $data->id }}" class="form-label">Tanggapan</label>
                                                    <textarea class="form-control" id="tanggapan{{ $data->id }}" name="tanggapan" rows="4">{{ $data->tanggapan }}</textarea>
                                                    <div class="form-text">Berikan penjelasan atau tanggapan terhadap laporan ini.</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-secondary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-4">Tidak ada laporan kerusakan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
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
                order: [[0, 'desc']]
            });
        }
    });
</script>
@endsection
