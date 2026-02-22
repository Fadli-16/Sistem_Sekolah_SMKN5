@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    button {
        background-color: #004080;
        color: white;
        font-weight: bold;
        cursor: pointer;
        border: none;
    }

    button:hover {
        background-color: #002b5c;
    }

    .btn-delete {
        background-color: #d33;
        color: white;
        border: none;
        padding: 0.45rem 0.8rem;
        border-radius: .35rem;
        font-weight: 600;
    }

    .btn-delete:hover {
        background-color: #b02222;
    }

    .img-preview {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
        display: block;
        margin-bottom: 8px;
        border-radius: 6px;
    }

    .current-file {
        display: block;
        margin-bottom: 8px;
    }

    .actions-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
    }

    .actions-left {
        display: flex;
        gap: .6rem;
    }

    .actions-right {
        display: flex;
    }

    /* responsive minor */
    @media (max-width: 576px) {
        .img-preview {
            max-width: 150px;
            max-height: 150px;
        }

        .actions-row {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
@endsection

@section('content')
<div class="container mt-4 mb-4">
    <h1 class="page-title">{{ $header }}</h1>

    <div class="card p-4">
        <form id="beritaForm"
            action="{{ isset($berita) ? route('sistem_akademik.berita.update', $berita->id) : route('sistem_akademik.berita.store') }}"
            method="POST"
            enctype="multipart/form-data">
            @csrf
            @if(isset($berita))
            @method('PUT')
            @endif

            <!-- Judul -->
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input
                    type="text"
                    class="form-control"
                    id="judul"
                    name="judul"
                    value="{{ old('judul', $berita->judul ?? '') }}"
                    required>
            </div>

            <!-- Isi -->
            <div class="mb-3">
                <label for="isi" class="form-label">Isi</label>
                <textarea
                    class="form-control"
                    id="isi"
                    name="isi"
                    rows="6"
                    required>{{ old('isi', $berita->isi ?? '') }}</textarea>
            </div>

            <!-- Preview gambar saat ini -->
            <div class="mb-3">
                <label for="foto" class="form-label">Foto</label><br>
                @if(isset($berita) && $berita->foto)
                <img src="{{ asset('assets/berita/' . $berita->foto) }}" id="output_image" class="img-preview mb-2" alt="">
                @else
                <img src="" id="output_image" class="img-preview mb-2" style="display:none;">
                @endif

                <input type="file" id="upload" name="foto" onchange="preview_image(event)" class="form-control" />
            </div>

            <!-- File attachment (dokumen/pdf/etc) -->
            <div class="mb-3">
                <label for="file" class="form-label">Lampirkan File (opsional)</label>
                <input type="file" name="file" class="form-control mb-2" />
                @if(isset($berita) && $berita->file)
                <a href="{{ asset('file/' . $berita->file) }}" target="_blank" class="current-file">File saat ini: {{ $berita->file }}</a>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="remove_file" id="remove_file" value="1">
                    <label class="form-check-label" for="remove_file">Hapus file saat ini</label>
                </div>
                @endif
            </div>

            <!-- Kategori -->
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select name="kategori" id="kategori" class="form-select" required>
                    <option value="">Pilih kategori</option>
                    <option value="informasi" {{ old('kategori', $berita->kategori ?? '') === 'informasi' ? 'selected' : '' }}>Informasi</option>
                    <option value="prestasi" {{ old('kategori', $berita->kategori ?? '') === 'prestasi' ? 'selected' : '' }}>Prestasi</option>
                    <option value="pemberitahuan" {{ old('kategori', $berita->kategori ?? '') === 'pemberitahuan' ? 'selected' : '' }}>Pemberitahuan</option>
                </select>
            </div>

            <!-- Aksi: Simpan + (Jika edit) tombol Hapus -->
            <div class="actions-row">
                <!-- KIRI -->
                <div class="actions-left">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary px-3 py-1 rounded-3">
                        Kembali
                    </a>

                    <button type="submit" class="px-3 py-1 rounded-3">
                        Simpan
                    </button>
                </div>

                <!-- KANAN -->
                @isset($berita)
                <div class="actions-right">
                    <button type="button" class="btn-delete" id="btnDelete">
                        Hapus
                    </button>
                </div>
                @endisset
            </div>
        </form>

        @isset($berita)
        <form id="deleteForm"
            action="{{ route('sistem_akademik.berita.destroy', $berita->id) }}"
            method="POST">
            @csrf
            @method('DELETE')
        </form>
        @endisset
    </div>
</div>
@endsection

@section('script')
<script type='text/javascript'>
    function preview_image(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('output_image');
            output.src = reader.result;
            output.style.display = 'block';
        }
        if (event.target.files && event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    (function() {
        // Hapus: konfirmasi menggunakan SweetAlert2 bila ada, fallback ke confirm()
        const btnDelete = document.getElementById('btnDelete');
        if (!btnDelete) return;

        btnDelete.addEventListener('click', function() {
            // jika SweetAlert2 tersedia (biasanya sudah dipakai di project)
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Anda yakin?',
                    text: 'Data berita akan dihapus dan tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteForm').submit();
                    }
                });
                return;
            }

            // fallback sederhana
            if (confirm('Hapus berita ini? Tindakan ini tidak dapat dibatalkan.')) {
                document.getElementById('deleteForm').submit();
            }
        });
    })();
</script>
@endsection