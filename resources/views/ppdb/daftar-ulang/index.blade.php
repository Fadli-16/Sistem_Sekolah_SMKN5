@extends('ppdb.layouts.main')

@section('css')
<style>
    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #FF5733;
        margin-bottom: 0.75rem;
        position: relative;
        display: inline-block;
    }

    .page-title::after {
        content: '';
        display: block;
        width: 70px;
        height: 3px;
        background: linear-gradient(to right, #3bafa6, #4ecdc4);
        margin-top: 0.5rem;
        border-radius: 2px;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(26, 42, 58, 0.1);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #FF5733, #CC3A1D);
        color: white;
        font-weight: 600;
        border-bottom: 4px solid #4ecdc4;
        padding: 1rem 1.5rem;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        border: none;
        background: #f8fafc;
        color: #1a2a3a;
        cursor: pointer;
        margin: 0 2px;
    }

    .btn-success {
        background-color: #de7519;
        border-color: #de7519;
    }

    .btn-success:hover {
        background-color: #ff9022;
        border-color: #ff9022;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(59, 175, 166, 0.15);
    }

    .btn-danger {
        background-color: #e74c3c;
        border-color: #e74c3c;
    }

    .btn-danger:hover {
        background-color: #c0392b;
        border-color: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(231, 76, 60, 0.15);
    }

    .btn-info {
        background-color: #ff9022;
        border-color: #ff9022;
    }

    .btn-info:hover {
        background-color: #d35400;
        border-color: #d35400;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(52, 152, 219, 0.15);
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        border-radius: 30px;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .bg-warning {
        background-color: rgba(255, 193, 7, 0.2) !important;
        color: #ff9800 !important;
    }

    .bg-success {
        background-color: rgba(25, 135, 84, 0.2) !important;
        color: #198754 !important;
    }

    .bg-danger {
        background-color: rgba(220, 53, 69, 0.2) !important;
        color: #dc3545 !important;
    }

    .bg-primary {
    background-color: #fd710d !important; /* Warna oranye terang */
    color: white !important;              /* Warna teks agar kontras dengan oranye */
}


    .table {
        width: 100%;
        margin-bottom: 0;
    }

    .table th {
        background-color: rgba(248, 250, 252, 0.5);
        color: #1a2a3a;
        font-weight: 600;
        border-bottom: 2px solid rgba(0, 0, 0, 0.03);
        white-space: nowrap;
        padding: 1rem 1.25rem;
        font-size: 0.9rem;
        letter-spacing: 0.3px;
    }

    .table td {
        padding: 0.875rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        font-size: 0.95rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(248, 250, 252, 0.5);
    }

    /* Modal styling */
    .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, #d35400, #ff9022);
        color: white;
        border-bottom: 3px solid #4ecdc4;
        padding: 1rem 1.5rem;
    }

    .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: #4ecdc4;
        box-shadow: 0 0 0 0.2rem rgba(78, 205, 196, 0.25);
    }

    /* Alert styling */
    .alert {
        border: none;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
    }

    .alert-info {
        background-color: rgba(52, 152, 219, 0.15);
        color: #d35400;
    }

    .alert-warning {
        background-color: rgba(243, 156, 18, 0.15);
        color: #d35400;
    }

    .alert i {
        font-size: 1.25rem;
        margin-right: 0.75rem;
        margin-top: 0.1rem;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-state i {
        font-size: 3.5rem;
        color: #e0e6ed;
        margin-bottom: 1.25rem;
    }

    .empty-state h5 {
        font-weight: 600;
        color: #8795a1;
        margin-bottom: 0.75rem;
    }

    .empty-state p {
        color: #a8b5c5;
        max-width: 400px;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        .table-responsive-card thead {
            display: none;
        }

        .table-responsive-card tbody tr {
            display: block;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            background-color: #fff;
            position: relative;
            overflow: hidden;
        }

        .table-responsive-card tbody tr:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: #4ecdc4;
        }

        .table-responsive-card tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table-responsive-card tbody td:last-child {
            border-bottom: none;
        }

        .table-responsive-card tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            color: #1a2a3a;
            text-align: left;
            padding-right: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">{{ $header }}</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-white">Daftar Pendaftar Ulang</h6>
            <span class="badge bg-primary">Total: {{ $pendaftaran->count() }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive table-responsive-card">
                @if($pendaftaran->count() > 0)
                <table class="table table-bordered" id="data-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaran as $index => $item)
                        <tr>
                            <td data-label="No">{{ $index + 1 }}</td>
                            <td data-label="Nama">{{ $item->name }}</td>
                            <td data-label="Email">{{ $item->email }}</td>
                            <td data-label="Jurusan">{{ $item->major }}</td>
                            <td data-label="Status">
                                @if($item->status == 'pending')
                                <span class="badge bg-warning"><i class="bi bi-clock"></i> Menunggu</span>
                                @elseif($item->status == 'approved')
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Disetujui</span>
                                @elseif($item->status == 'rejected')
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                @endif
                            </td>
                            <td data-label="Tanggal Daftar">{{ $item->created_at->format('d-m-Y H:i') }}</td>
                            <td data-label="Aksi">
                                @if($item->status == 'pending')
                                <button type="button" class="btn btn-sm btn-success"
                                        data-bs-toggle="modal" data-bs-target="#approveModal{{ $item->id }}">
                                    <i class="bi bi-check-circle"></i> Setujui
                                </button>
                                <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                    <i class="bi bi-x-circle"></i> Tolak
                                </button>
                                @else
                                <button type="button" class="btn btn-sm btn-info"
                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                    <i class="bi bi-info-circle"></i> Detail
                                </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                    <div class="alert alert-info mb-3">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Informasi:</strong> Menyetujui pendaftaran akan membuat akun siswa secara otomatis dan mengirimkan email pemberitahuan ke pendaftar.
                                    </div>

                                        <p>Apakah Anda yakin ingin menyetujui pendaftaran <strong>{{ $item->name }}</strong>?</p>

                                        <div class="card border-0 bg-light rounded-3 p-3 mt-3">
                                            <h6 class="mb-3 fw-semibold">Detail Pendaftaran:</h6>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2 d-flex">
                                                    <span class="text-muted me-2 w-25">Nama:</span>
                                                    <span class="fw-medium">{{ $item->name }}</span>
                                                </li>
                                                <li class="mb-2 d-flex">
                                                    <span class="text-muted me-2 w-25">Email:</span>
                                                    <span class="fw-medium">{{ $item->email }}</span>
                                                </li>
                                                <li class="mb-0 d-flex">
                                                    <span class="text-muted me-2 w-25">Jurusan:</span>
                                                    <span class="fw-medium">{{ $item->major }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="bi bi-x"></i> Batal
                                        </button>
                                        <form action="{{ route('admin.ppdb.daftar-ulang.approve', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-circle"></i> Setujui
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Penolakan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.ppdb.daftar-ulang.reject', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                        <div class="alert alert-info mb-3">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Informasi:</strong> Email pemberitahuan akan dikirimkan ke pendaftar beserta catatan penolakan yang Anda berikan.
                                        </div>

                                            <p>Apakah Anda yakin ingin menolak pendaftaran <strong>{{ $item->name }}</strong>?</p>

                                            <div class="mb-3">
                                                <label for="notes" class="form-label">Catatan Penolakan (opsional)</label>
                                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                                    placeholder="Berikan alasan penolakan untuk siswa..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="bi bi-x"></i> Batal
                                            </button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="bi bi-x-circle"></i> Tolak
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Pendaftaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>Nama:</strong> {{ $item->name }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Email:</strong> {{ $item->email }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Jurusan:</strong> {{ $item->major }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Jenis Kelamin:</strong> {{ $item->jenis_kelamin }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d-m-Y') }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Alamat:</strong> {{ $item->alamat }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Nomor HP:</strong> {{ $item->no_hp }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Status:</strong>
                                            @if($item->status == 'pending')
                                            <span class="badge bg-warning"><i class="bi bi-clock"></i> Menunggu</span>
                                            @elseif($item->status == 'approved')
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Disetujui</span>
                                            @elseif($item->status == 'rejected')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <strong>Tanggal Daftar:</strong> {{ $item->created_at->format('d-m-Y H:i') }}
                                        </div>
                                        @if($item->status == 'rejected' && $item->notes)
                                        <div class="alert alert-warning">
                                            <strong>Catatan Penolakan:</strong>
                                            <p class="mb-0 mt-1">{{ $item->notes }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>Belum ada data pendaftaran</h5>
                    <p>Belum ada siswa yang melakukan pendaftaran ulang saat ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#data-table').DataTable({
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: "Cari pendaftar...",
                lengthMenu: "<i class='bi bi-list-ul'></i> _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ total entri)",
                paginate: {
                    first: '<i class="bi bi-chevron-double-left"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>',
                    last: '<i class="bi bi-chevron-double-right"></i>'
                },
                zeroRecords: "Tidak ada data yang cocok",
                emptyTable: "Tidak ada data tersedia dalam tabel"
            },
            dom: '<"dt-controls dt-controls-row"<"dt-search"f><"dt-length"l>>rt<"dt-bottom"<"dt-info"i><"dt-pagination"p>>',
            drawCallback: function() {
                $('.dt-controls, .dt-bottom').addClass('dt-fade-in');
            }
        });
    });
</script>
@endsection
