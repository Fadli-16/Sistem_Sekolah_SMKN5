@extends('sistem_akademik.layouts.main')

@section('css')
    <link href="{{ asset('css/berita.css') }}" rel="stylesheet">
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

            <div class="share-section mt-4 mb-3 p-3 bg-light rounded border border-light-subtle d-flex align-items-center">
                <span class="text-muted fw-bold me-3"><i class="fas fa-share-alt me-1"></i> Bagikan:</span>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($berita->judul . ' - ' . route('dashboard') . '?berita_id=' . $berita->id) }}" target="_blank" class="btn btn-sm btn-success rounded-circle me-2" title="Bagikan ke WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('dashboard') . '?berita_id=' . $berita->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-circle me-2" title="Bagikan ke Facebook"><i class="fab fa-facebook-f"></i></a>
                <button type="button" class="btn btn-sm rounded-circle me-2 text-white" style="background: #E1306C;" title="Bagikan ke Instagram" onclick="navigator.clipboard.writeText('{{ route('dashboard') }}?berita_id={{ $berita->id }}'); alert('Tautan disalin! Silakan buka aplikasi Instagram untuk membagikan tautan ini.');"><i class="fab fa-instagram"></i></button>
                <button type="button" class="btn btn-sm btn-secondary rounded-circle" title="Salin Tautan" onclick="navigator.clipboard.writeText('{{ route('dashboard') }}?berita_id={{ $berita->id }}'); const icon = this.innerHTML; this.innerHTML = '<i class=\'fas fa-check text-white\'></i>'; setTimeout(() => this.innerHTML = icon, 2000);"><i class="fas fa-link"></i></button>
            </div>

            <div class="actions">
                @php
                    $role = Auth::user()->role;
                    $isKepsekWakil = $role == 'guru' && Auth::user()->guru && in_array(Auth::user()->guru->status, ['kepala sekolah', 'wakil kepala']);
                    $backRoute = (in_array($role, ['admin', 'super_admin', 'admin_sa']) || $isKepsekWakil)
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