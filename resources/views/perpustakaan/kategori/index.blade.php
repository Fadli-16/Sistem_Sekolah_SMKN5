@extends('perpustakaan.layouts.main')

@section('content')
<style>
    .pdf-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background-color: var(--primary);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        box-shadow: var(--shadow-sm);
        z-index: 2;
    }

    .pdf-icon {
        color: var(--primary);
        margin-left: 0.5rem;
        font-size: 0.9rem;
    }

    .book-card-header {
        padding-top: 3.5rem !important;
        position: relative;
    }

    .stock-badge {
        top: 1rem;
        right: 1rem;
        position: absolute;
        z-index: 2;
    }

    .badge-container {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3rem;
        display: flex;
        justify-content: space-between;
        padding: 1rem;
        pointer-events: none;
    }

    .badge-container > * {
        pointer-events: auto;
    }

    .book-title {
        margin-top: 0.5rem;
        width: 100%;
        max-width: 100%;
    }
</style>

<section class="book-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1 class="page-title">Data Kategori Buku Perpustakaan</h1>
                    <p class="text-muted">Koleksi kategori buku yang tersedia di perpustakaan SMK Negeri 5 Padang</p>
                </div>

                <div class="actions-row">
                    @if (Auth::check() && Auth::user()->role == 'admin_perpus')
                    <a href="{{ route('kategori.create') }}" class="btn-add">
                        <i class="bi bi-plus-circle"></i> Tambah Kategori Buku
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

                <!-- Grid View for Categories -->
                <div class="book-grid d-none" id="gridView">
                    @foreach($kategoris as $kategori)
                    <div class="book-card">
                        <div class="book-card-header">
                            <h3 class="book-title">{{ $kategori->nama_kategori }}</h3>
                            <p class="book-author">Kode Buku: {{ $kategori->kode_buku }}</p>
                        </div>
                        <div class="book-card-body">
                            <div class="book-details">
                                <span class="book-detail-item">
                                    <i class="bi bi-journal-bookmark"></i> Jumlah: {{ $kategori->jumlah }}
                                </span>
                            </div>
                            <div class="book-actions">
                                <!-- Ganti tombol Detail menjadi Edit -->
                                @if (Auth::check() && Auth::user()->role == 'admin_perpus')
                                <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn-secondary-app">
                                <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <form action="{{ route('kategori.destroy', $kategori->id) }}" method="post" id="deleteForm{{ $kategori->id }}" class="d-inline">
                                    @csrf
                                    @method('delete')
                                    <button type="button" onclick="Perpustakaan.confirmDelete('{{ $kategori->id }}')" class="btn-action btn-delete" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Table View for Categories -->
                <div class="table-container" id="tableView">
                    <div class="table-responsive">
                        <table class="table" id="kategoriTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Kode Buku</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kategoris as $index => $kategori)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $kategori->nama_kategori }}</td>
                                    <td>{{ $kategori->kode_buku }}</td>
                                    <td>{{ $kategori->jumlah }}</td>
                                    <td>
                                        <!-- Ganti tombol Detail menjadi Edit -->
                                        @if (Auth::check() && Auth::user()->role == 'admin_perpus')
                                        <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn-action" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('kategori.destroy', $kategori->id) }}" method="post" id="deleteFormTable{{ $kategori->id }}" class="d-inline">
                                            @csrf
                                            @method('delete')
                                            <button type="button" onclick="Perpustakaan.confirmDelete('{{ $kategori->id }}')" class="btn-action btn-delete" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
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
</section>

@endsection
