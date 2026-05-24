@extends('sistem_akademik.layouts.main')

@section('css')
    <link href="{{ asset('css/berita.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle">{{ isset($berita) ? 'Edit berita yang sudah ada' : 'Buat berita atau informasi baru' }}</p>
        </div>
        <a href="{{ route('sistem_akademik.berita.index') }}" class="btn-secondary-app">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <div class="form-card-header">
            <h5><i class="bi bi-newspaper me-2"></i>{{ isset($berita) ? 'Form Edit Berita' : 'Form Tambah Berita' }}</h5>
            <p>Isi konten berita dan unggah gambar pendukung</p>
        </div>
        <div class="form-card-body">

            <form id="beritaForm"
                  action="{{ isset($berita) ? route('sistem_akademik.berita.update', $berita->id) : route('sistem_akademik.berita.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($berita)) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-12">
                        <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror"
                               id="judul" name="judul" value="{{ old('judul', $berita->judul ?? '') }}" required
                               placeholder="Masukkan judul berita...">
                        @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" id="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                            <option value="">Pilih kategori...</option>
                            <option value="informasi"     {{ old('kategori', $berita->kategori ?? '') === 'informasi'     ? 'selected' : '' }}>Informasi</option>
                            <option value="prestasi"      {{ old('kategori', $berita->kategori ?? '') === 'prestasi'      ? 'selected' : '' }}>Prestasi</option>
                            <option value="pemberitahuan" {{ old('kategori', $berita->kategori ?? '') === 'pemberitahuan' ? 'selected' : '' }}>Pemberitahuan</option>
                        </select>
                        @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="isi" class="form-label">Isi Berita <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('isi') is-invalid @enderror"
                                  id="isi" name="isi" rows="7" required
                                  placeholder="Tulis isi berita di sini...">{{ old('isi', $berita->isi ?? '') }}</textarea>
                        @error('isi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="upload" class="form-label">Foto Berita</label>
                        @if(isset($berita) && $berita->foto)
                            <img src="{{ asset('assets/berita/' . $berita->foto) }}" id="output_image"
                                 class="d-block mb-2" style="max-width:200px;max-height:150px;object-fit:cover;border-radius:8px;">
                        @else
                            <img src="" id="output_image" class="d-block mb-2"
                                 style="max-width:200px;max-height:150px;object-fit:cover;border-radius:8px;display:none!important;">
                        @endif
                        <input type="file" id="upload" name="foto" class="form-control @error('foto') is-invalid @enderror"
                               onchange="preview_image(event)" accept="image/*">
                        <div class="text-muted mt-1" style="font-size:0.75rem">Maks 4MB · JPG, PNG, GIF</div>
                        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="file" class="form-label">Lampiran File <small class="text-muted fw-normal">(opsional)</small></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                        @if(isset($berita) && $berita->file)
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <a href="{{ asset('file/' . $berita->file) }}" target="_blank"
                                   class="badge-modern badge-info" style="text-decoration:none;">
                                    <i class="bi bi-file-earmark-arrow-down"></i> File saat ini
                                </a>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="remove_file" id="remove_file" value="1">
                                    <label class="form-check-label" for="remove_file" style="font-size:0.8rem;color:#ef4444;">Hapus file</label>
                                </div>
                            </div>
                        @endif
                        <div class="text-muted mt-1" style="font-size:0.75rem">Maks 8MB · PDF, DOC, XLS, ZIP</div>
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-primary-app">
                            <i class="bi bi-{{ isset($berita) ? 'save' : 'plus-lg' }}"></i>
                            {{ isset($berita) ? 'Simpan Perubahan' : 'Tambah Berita' }}
                        </button>
                        <a href="{{ route('sistem_akademik.berita.index') }}" class="btn-secondary-app">Batal</a>
                    </div>

                    @isset($berita)
                    <button type="button" id="btnDelete"
                            style="background:#fee2e2;color:#dc2626;border:none;padding:0.5rem 1.25rem;
                                   border-radius:10px;font-weight:600;font-size:0.875rem;cursor:pointer;
                                   display:inline-flex;align-items:center;gap:6px;transition:all 0.25s;"
                            onmouseover="this.style.background='#dc2626';this.style.color='white';"
                            onmouseout="this.style.background='#fee2e2';this.style.color='#dc2626';">
                        <i class="bi bi-trash-fill"></i> Hapus Berita
                    </button>
                    @endisset
                </div>
            </form>

            @isset($berita)
            <form id="deleteForm" action="{{ route('sistem_akademik.berita.destroy', $berita->id) }}" method="POST">
                @csrf @method('DELETE')
            </form>
            @endisset
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#isi').summernote({
            placeholder: 'Tulis isi berita di sini...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onInit: function() {
                    $(this).summernote('code', $(this).val());
                }
            }
        });
    });

    function preview_image(event) {
        const output = document.getElementById('output_image');
        const reader = new FileReader();
        reader.onload = function () {
            output.src = reader.result;
            output.style.display = 'block';
        };
        if (event.target.files && event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    const btnDelete = document.getElementById('btnDelete');
    if (btnDelete) {
        btnDelete.addEventListener('click', function () {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Berita?',
                text: 'Berita ini akan dihapus secara permanen!',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('deleteForm').submit();
            });
        });
    }
</script>
@endsection