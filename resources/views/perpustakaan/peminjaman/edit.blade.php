@extends('perpustakaan.layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <h1 class="page-title">Update Status Peminjaman</h1>
            <p class="text-muted mb-4">Ubah status peminjaman buku perpustakaan SMK Negeri 5 Padang</p>

            <div class="form-container" data-aos="fade-up">
                <form action="{{ route('perpustakaan.peminjaman.update', $peminjaman->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="nama" class="form-label">Nama Peminjam</label>
                        <input
                            type="text"
                            name="nama"
                            id="nama"
                            class="form-control"
                            value="{{ $peminjaman->nama }}"
                            readonly>
                    </div>

                    <div class="mb-4">
                        <label for="buku_judul" class="form-label">Buku yang Dipinjam</label>
                        <input
                            type="text"
                            name="buku_judul"
                            id="buku_judul"
                            class="form-control"
                            value="{{ $peminjaman->buku->judul }}"
                            readonly>
                        <div class="form-text">
                            Stok saat ini: <span data-book-stock="{{ $peminjaman->buku->stok }}">{{ $peminjaman->buku->stok }}</span> buku
                            @if ($peminjaman->status == 'Disetujui' && $peminjaman->buku->stok == 0)
                                <span class="text-danger"> • Buku ini sedang habis stok</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="tanggal_pinjam" class="form-label">Tanggal Peminjaman</label>
                        <input
                            type="text"
                            name="tanggal_pinjam"
                            id="tanggal_pinjam"
                            class="form-control"
                            value="{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d F Y') }}"
                            readonly>
                    </div>

                    <div class="mb-4">
                        <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                        <input
                            type="date"
                            name="tanggal_kembali"
                            id="tanggal_kembali"
                            class="form-control @error('tanggal_kembali') is-invalid @enderror"
                            value="{{ old('tanggal_kembali', $peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('Y-m-d') : '') }}"
                            {{ $peminjaman->status == 'Dikembalikan' ? '' : 'readonly' }}>
                        @error('tanggal_kembali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Isi tanggal kembali jika status <b>Dikembalikan</b>.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">
                            Status Peminjaman
                            @if ($peminjaman->status == 'Menunggu')
                                <span class="status-badge status-pending">{{ $peminjaman->status }}</span>
                            @elseif ($peminjaman->status == 'Ditolak')
                                <span class="status-badge status-rejected">{{ $peminjaman->status }}</span>
                            @elseif ($peminjaman->status == 'Disetujui')
                                <span class="status-badge status-approved">{{ $peminjaman->status }}</span>
                            @elseif ($peminjaman->status == 'Dikembalikan')
                                <span class="status-badge status-returned">{{ $peminjaman->status }}</span>
                            @endif
                        </label>
                        <select name="status" id="status" class="form-select status-field" data-current-status="{{ $peminjaman->status }}">
                            <option value="Menunggu" {{ $peminjaman->status == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="Disetujui" {{ $peminjaman->status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="Ditolak" {{ $peminjaman->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="Dikembalikan" {{ $peminjaman->status == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        </select>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Perubahan status ke "Disetujui" akan mengurangi stok buku.
                            Status "Dikembalikan" akan menambah stok kembali.
                        </div>
                    </div>

                    <div class="d-flex mt-5 flex-wrap">
                        <a href="{{ route('perpustakaan.peminjaman.index') }}" class="btn-secondary-app mb-2 me-2">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn-secondary-app mb-2">
                            <i class="bi bi-check-circle"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
