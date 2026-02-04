@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    /* Container utama */
    .news-show .meta {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
    }

    /* Judul berita - proporsional, tidak terlalu besar */
    .news-show .news-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
        line-height: 1.2;
    }

    /* Media area: by default stack (image above text) */
    .news-show .news-media {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
        margin-top: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    /* Side layout (portrait image) */
    .news-show .news-media.side {
        flex-direction: row;
    }

    /* Stack layout (landscape image / default mobile) */
    .news-show .news-media.stack {
        flex-direction: column;
    }

    /* Image wrapper when side-by-side */
    .news-show .img-wrap {
        flex: 0 0 35%;
        max-width: 35%;
        display: block;
    }

    /* Image wrapper when stacked (full width) */
    .news-show .news-media.stack .img-wrap {
        flex: 0 0 100%;
        max-width: 100%;
    }

    /* Image rules: keep aspect ratio, no cropping (use auto height) */
    .news-show .img-wrap img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 8px;
        box-shadow: 0 6px 16px rgba(20, 20, 30, 0.04);
        background: #f6f7f9;
        /* visible area when using contain */
    }

    /* Content column */
    .news-show .content {
        flex: 1 1 60%;
        min-width: 240px;
    }

    /* Artikel text */
    .news-show .article {
        font-size: 1rem;
        color: var(--text-dark);
        line-height: 1.7;
    }

    /* Buttons */
    .news-show .actions {
        margin-top: 1rem;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-download {
        background: #6c757d;
        color: #fff;
        border: none;
        padding: 0.45rem 0.75rem;
        border-radius: 6px;
    }

    .btn-back {
        background: #6f8ea6;
        color: #fff;
        border: none;
        padding: 0.45rem 0.75rem;
        border-radius: 6px;
    }

    /* Responsive: on small screens always stack */
    @media (max-width: 767.98px) {
        .news-show .img-wrap {
            max-width: 100%;
            flex-basis: 100%;
        }

        .news-show .content {
            flex-basis: 100%;
        }

        .news-show .news-title {
            font-size: 1.25rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container news-show mt-3 mb-4">

    {{-- Judul & meta --}}
    <h1 class="news-title">{{ $berita->judul }}</h1>
    <div class="meta">
        <small>Tanggal Posting: {{ optional($berita->created_at)->format('d M Y H:i') }}</small>
    </div>

    {{-- Media area: image + content --}}
    <div class="news-media stack" id="newsMedia">
        <div class="img-wrap">
            @if ($berita->foto)
            <img id="newsImage" src="{{ asset('assets/berita/' . $berita->foto) }}" alt="Gambar Berita">
            @else
            <div style="width:100%;padding:60px;text-align:center;background:#f4f5f7;border-radius:8px;">
                <i class="fas fa-image" style="font-size:1.4rem;color:#9aa3ad;"></i>
                <div style="font-size:0.95rem;color:#9aa3ad;margin-top:6px;">Tidak ada gambar</div>
            </div>
            @endif
        </div>

        <div class="content">
            <div class="article">
                {!! $berita->isi !!}
            </div>

            <div class="actions">
                <a href="{{ route('sistem_akademik.berita.index') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>

                @if (!empty($berita->file))
                <a href="{{ asset('file/' . $berita->file) }}" class="btn btn-download" target="_blank" download>
                    <i class="bi bi-download me-1"></i> Unduh Lampiran
                </a>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imgEl = document.getElementById('newsImage');
        const media = document.getElementById('newsMedia');

        // fallback: jika tidak ada gambar, tetap stack
        if (!imgEl) {
            media.classList.remove('side');
            media.classList.add('stack');
            return;
        }

        // create temporary image to read natural dimensions (avoid cached incomplete reads)
        const tmp = new Image();
        tmp.onload = function() {
            // if height > width -> portrait -> side-by-side
            if (tmp.naturalHeight > tmp.naturalWidth) {
                media.classList.remove('stack');
                media.classList.add('side');
            } else {
                media.classList.remove('side');
                media.classList.add('stack');
            }
        };
        tmp.src = imgEl.src;

        // Also respond to viewport resize: for small screens, force stack
        function applyResponsive() {
            if (window.innerWidth <= 767.98) {
                media.classList.remove('side');
                media.classList.add('stack');
            } else {
                // re-evaluate orientation if image loaded
                if (imgEl && imgEl.naturalWidth && imgEl.naturalHeight) {
                    if (imgEl.naturalHeight > imgEl.naturalWidth) {
                        media.classList.add('side');
                        media.classList.remove('stack');
                    } else {
                        media.classList.add('stack');
                        media.classList.remove('side');
                    }
                }
            }
        }

        window.addEventListener('resize', applyResponsive);
        // initial apply when DOM ready and image has loaded (safety)
        imgEl.addEventListener('load', applyResponsive);
    });
</script>
@endsection