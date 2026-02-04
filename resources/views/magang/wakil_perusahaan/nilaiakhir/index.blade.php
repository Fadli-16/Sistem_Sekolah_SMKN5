@extends('magang.layouts.main')

@section('content')
<style>
    .rekap-container {
        max-width: 80rem;
        margin: 3rem auto;
        background-color: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .rekap-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .rekap-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-tambah, .btn-pdf {
        background-color: #38a169;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-decoration: none;
        transition: background-color 0.3s ease;
        font-weight: 500;
    }

    .btn-tambah:hover {
        background-color: #2f855a;
    }

    .btn-pdf {
        background-color: #4a5568;
    }

    .btn-pdf:hover {
        background-color: #2d3748;
    }

    .rekap-table {
        width: 100%;
        border-collapse: collapse;
    }

    .rekap-table th,
    .rekap-table td {
        border: 1px solid #e2e8f0;
        padding: 0.75rem;
        text-align: left;
    }

    .rekap-table thead {
        background-color: #f97316;
        color: white;
    }

    .nilai-keterangan {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        color: white;
        font-weight: 500;
    }

    .sangat-baik {
        background-color: #16a34a;
    }

    .baik {
        background-color: #3b82f6;
    }

    .cukup {
        background-color: #facc15;
        color: black;
    }

    .kurang {
        background-color: #ef4444;
    }

    .text-center-gray {
        text-align: center;
        color: #a0aec0;
        padding: 2.5rem 0;
    }
</style>

<div class="rekap-container">
    <div class="rekap-header">
        <h2 class="rekap-title">📊 Rekap Nilai Akhir PKL</h2>
        <div class="header-actions">
            <a href="{{ route('magang.wakil_perusahaan.nilaiakhir.create') }}" class="btn-tambah">➕ Tambah Nilai Laporan</a>
            <a href="{{ route('magang.wakil_perusahaan.nilaiakhir.export') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="btn-pdf">
                📄 Rekap PDF
                </a>
        </div>
    </div>

    @if($penilaians->isEmpty())
        <div class="text-center-gray">Belum ada data penilaian.</div>
    @else
    <table class="rekap-table">
        <thead>
            <tr>
                <th>Nama Siswa</th>
                <th>Perusahaan</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Pembimbing Lapangan</th>
                <th>Nilai Akhir</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penilaians as $item)
            <tr>
                <td>{{ $item->siswa?->name ?? '-' }}</td>
                <td>{{ $item->wakilPerusahaan?->nama_perusahaan ?? '-' }}</td>
                <td>{{ $item->siswa?->magangreports?->tanggal_mulai ?? '-' }}</td>
                <td>{{ $item->siswa?->magangreports?->tanggal_selesai ?? '-' }}</td>
                <td>{{ $item->wakilPerusahaan?->nama ?? '-' }}</td>
                <td><strong>{{ $item->nilai_akhir }}</strong></td>
                <td>
                    @php
                        $na = $item->nilai_akhir;
                        $keterangan = $na >= 91 ? 'Sangat Baik' : ($na >= 81 ? 'Baik' : ($na >= 71 ? 'Cukup' : 'Kurang'));
                        $kelasWarna = match ($keterangan) {
                            'Sangat Baik' => 'sangat-baik',
                            'Baik' => 'baik',
                            'Cukup' => 'cukup',
                            default => 'kurang',
                        };
                    @endphp
                    <span class="nilai-keterangan {{ $kelasWarna }}">
                        {{ $keterangan }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
