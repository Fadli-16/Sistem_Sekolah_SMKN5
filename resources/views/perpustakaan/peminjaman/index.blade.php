@extends('perpustakaan.layouts.main')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="page-title">Data Peminjaman Buku</h1>
            <p class="text-muted mb-4">Kelola dan pantau status peminjaman buku dari perpustakaan SMK Negeri 5 Padang</p>

            <!-- Tombol Tambah Data -->
            <div class="actions-row">
                @if (Auth::check() && Auth::user()->role == 'admin_perpus')
                <a href="{{ route('perpustakaan.buku.create') }}" class="btn-add">
                    <i class="bi bi-plus-circle"></i> Tambah Data Peminjaman
                </a>
                @endif

                <div class="toggle-view">
                    <button type="button" class="view-btn" id="gridViewBtn">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Grid
                    </button>
                    <button type="button" class="view-btn active" id="tableViewBtn">
                        <i class="bi bi-table"></i> Tabel
                    </button>
                </div>
            </div>

            <!-- Grid View for Peminjaman -->
            <div class="book-grid d-none" id="gridView">
                @foreach($peminjaman as $p)
                <div class="book-card">
                    <div class="book-card-header">
                        <h3 class="book-title">{{ $p->nama }}</h3>
                        <p class="book-author">Buku: {{ $p->buku->judul }}</p>
                    </div>
                    <div class="book-card-body">
                        <div class="book-details">
                            <span class="book-detail-item">
                                <i class="bi bi-calendar"></i> Tanggal Pinjam: {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}
                            </span>
                            <span class="book-detail-item">
                                <i class="bi bi-calendar-check"></i>
                                Tanggal Kembali:
                                @if($p->tanggal_kembali)
                                    {{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </span>
                            <span class="book-detail-item">
                                <i class="bi bi-check-circle"></i> Status:
                                @if ($p->status == 'Menunggu')
                                    <span class="status-badge status-pending">Menunggu</span>
                                @elseif ($p->status == 'Ditolak')
                                    <span class="status-badge status-rejected">Ditolak</span>
                                @elseif ($p->status == 'Disetujui')
                                    <span class="status-badge status-approved">Disetujui</span>
                                @elseif ($p->status == 'Dikembalikan')
                                    <span class="status-badge status-returned">Dikembalikan</span>
                                @endif
                            </span>
                        </div>
                        <div class="book-actions">
                            <a href="{{ route('perpustakaan.peminjaman.edit', $p->id) }}" class="btn-secondary-app">
                                <i class="bi bi-pencil-square"></i> Update
                            </a>
                            <form action="{{ route('perpustakaan.peminjaman.destroy', $p->id) }}" method="post" id="deleteForm{{ $p->id }}" class="d-inline">
                                @csrf
                                @method('delete')
                                <button type="button" onclick="Perpustakaan.confirmDelete('{{ $p->id }}')" class="btn-action btn-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Table View for Peminjaman -->
            <div class="table-container" id="tableView">
                <div class="table-responsive">
                    <table class="table" id="data-table">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell">No</th>
                                <th>Nama</th>
                                <th>Buku</th>
                                <th class="d-none d-md-table-cell">Tanggal Pinjam</th>
                                <th class="d-none d-md-table-cell">Tanggal Kembali</th> <!-- Tambahkan ini -->
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peminjaman as $index => $p)
                            <tr>
                                <td class="d-none d-md-table-cell" data-label="No">{{ $index + 1 }}</td>
                                <td data-label="Nama">{{ $p->nama }}</td>
                                <td data-label="Buku">{{ $p->buku->judul }}</td>
                                <td class="d-none d-md-table-cell" data-label="Tanggal Pinjam">{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}</td>
                                <td class="d-none d-md-table-cell" data-label="Tanggal Kembali">
                                    @if($p->tanggal_kembali)
                                        {{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="Status">
                                    @if ($p->status == 'Menunggu')
                                    <span class="status-badge status-pending">Menunggu</span>
                                    @elseif ($p->status == 'Ditolak')
                                    <span class="status-badge status-rejected">Ditolak</span>
                                    @elseif ($p->status == 'Disetujui')
                                    <span class="status-badge status-approved">Disetujui</span>
                                    @elseif ($p->status == 'Dikembalikan')
                                    <span class="status-badge status-returned">Dikembalikan</span>
                                    @endif
                                </td>
                                <td data-label="Aksi">
                                    <div class="action-buttons">
                                        @if ($p->status != 'Dikembalikan')
                                        <a href="{{ route('perpustakaan.peminjaman.edit', $p->id) }}" class="btn-action btn-edit" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @endif
                                        <form action="{{ route('perpustakaan.peminjaman.destroy', $p->id) }}" method="post" id="deleteForm{{ $p->id }}">
                                            @csrf
                                            @method('delete')
                                            <button type="button" onclick="Perpustakaan.confirmDelete('{{ $p->id }}')" class="btn-action btn-delete" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
