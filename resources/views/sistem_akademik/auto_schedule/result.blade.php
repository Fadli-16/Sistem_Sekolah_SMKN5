@extends('sistem_akademik.layouts.main')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Hasil Generate Jadwal</h5>
            <span class="badge bg-light text-dark">Skor Optimalitas: {{ $generation->skor_kualitas ?? 0 }}/100</span>
        </div>
        <div class="card-body">
            
            <div class="row">
                <div class="col-md-8">
                    <h6>Ringkasan Draf Jadwal</h6>
                    <p class="text-muted">Jadwal ini masih berupa draf dan belum diterapkan ke sistem utama. Silakan tinjau terlebih dahulu.</p>
                    
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Kelas</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5];
                                    $sortedDrafts = $generation->drafts->sortBy(function($d) use ($hariOrder) {
                                        return sprintf('%02d-%s-%s', 
                                            $hariOrder[$d->hari] ?? 9,
                                            $d->kelas->nama_kelas ?? '',
                                            $d->jam_mulai
                                        );
                                    });
                                @endphp
                                @forelse($sortedDrafts as $draft)
                                    <tr>
                                        <td>{{ $draft->kelas->nama_kelas ?? '-' }}</td>
                                        <td>{{ $draft->hari }}</td>
                                        <td>{{ substr($draft->jam_mulai, 0, 5) }} - {{ substr($draft->jam_selesai, 0, 5) }}</td>
                                        <td>{{ $draft->mataPelajaran->nama_mata_pelajaran ?? '-' }}</td>
                                        <td>{{ $draft->mataPelajaran->guru->name ?? '-' }}</td>
                                        <td>{{ $draft->ruangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada jadwal yang berhasil di-generate. Coba periksa pengaturan data master.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-header">
                            <i class="fas fa-robot text-primary"></i> AI Insight & Rekomendasi
                        </div>
                        <div class="card-body">
                            @if($generation->catatan_ai)
                                <p class="card-text">{!! nl2br(e($generation->catatan_ai)) !!}</p>
                            @else
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                                    <p class="mb-0 small text-muted" id="ai-loading-text">AI sedang menganalisis draf jadwal ini...</p>
                                </div>
                                <div id="ai-result-container" class="d-none"></div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 d-grid gap-2">
                        <form action="{{ route('sistem_akademik.auto-schedule.apply', $generation->id) }}" method="POST" id="form-apply-jadwal">
                            @csrf
                            <button type="button" class="btn btn-primary w-100 mb-2" onclick="confirmApply()">
                                <i class="fas fa-check-circle"></i> Terapkan Jadwal ke Sistem
                            </button>
                        </form>
                        <a href="{{ route('sistem_akademik.auto-schedule.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-undo"></i> Batal & Generate Ulang
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Jika catatan_ai masih kosong, kita bisa men-trigger ajax call untuk meminta AI Service (Fase 4)
    // saat ini kita mock terlebih dahulu jika kosong
    @if(!$generation->catatan_ai)
        setTimeout(function() {
            $('#ai-loading-text').parent().addClass('d-none');
            $('#ai-result-container')
                .html('<p class="card-text text-success"><strong>AI Insight (Simulasi):</strong><br>Jadwal ini sudah cukup optimal. Pengelompokan mata pelajaran kejuruan di akhir minggu telah dilakukan.</p>')
                .removeClass('d-none');
        }, 3000);
    @endif

    // SweetAlert Konfirmasi Terapkan
    window.confirmApply = function() {
        Swal.fire({
            icon: 'question',
            title: 'Terapkan Jadwal?',
            text: 'Jadwal lama pada kelas-kelas terkait akan dihapus dan diganti dengan jadwal baru ini. Apakah Anda yakin?',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-check"></i> Ya, Terapkan!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $('#form-apply-jadwal').submit();
            }
        });
    }
});
</script>
@endsection
