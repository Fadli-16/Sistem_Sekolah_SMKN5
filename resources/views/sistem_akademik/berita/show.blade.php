@extends('sistem_akademik.layouts.main')

@section('css')
    <link href="{{ asset('assets/css/berita.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container news-show mt-3 mb-4">

    {{-- Judul & meta --}}
    <h1 class="news-title">{{ $berita->judul }}</h1>
    <div class="meta">
        <small>Tanggal Posting: {{ optional($berita->created_at)->format('d M Y H:i') }}</small>
        <br>
        <small>Diposting oleh:
            {{ optional($berita->user)->nama ?? optional($berita->user)->name ?? '—' }}
            @if(optional($berita->user)->id)
            {{-- jika mau link ke profil penulis, aktifkan baris di bawah (pastikan rute ada) --}}
            {{-- — <a href="{{ route('sistem_akademik.user.profile', $berita->user->id) }}">{{ $berita->user->nama ?? $berita->user->name }}</a> --}}
            @endif
        </small>
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
                @php
                    $role = Auth::user()->role;
                    $backRoute = in_array($role, ['admin', 'super_admin', 'admin_sa']) 
                        ? route('sistem_akademik.berita.index') 
                        : route('sistem_akademik.dashboard');
                @endphp
                <a href="{{ $backRoute }}" class="btn btn-back">
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
