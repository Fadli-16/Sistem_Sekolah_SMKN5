@extends('magang.layouts.main')

@section('content')
<div class="max-w-6xl mx-auto mt-10 bg-white shadow-xl rounded-xl p-6 relative">
{{-- Judul --}}
    <h2 class="text-2xl font-bold text-gray-800 mb-10 flex items-center gap-2">
        📊 Daftar Nilai PKL Siswa
    </h2>
    {{-- Tombol Input Penilaian diletakkan di pojok kanan atas --}}
    <div class="btn-wrapper right">
    <a href="{{ route('magang.wakil_perusahaan.penilaian.create') }}" class="btn-create">
        ➕ Input Penilaian
    </a>
</div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel Penilaian --}}
    @if($penilaians->isEmpty())
        <div class="text-center text-gray-500 py-10">
            Belum ada data penilaian.
        </div>
    @else
        <div class="table-container mb-4">
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Nilai PKL</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penilaians as $index => $penilaian)
                        @php
                            $totalA = $penilaian->hard_skill_1 + $penilaian->hard_skill_2 + $penilaian->hard_skill_3;
                            $rataA = $totalA / 3;
                            $totalB = $penilaian->kewirausahaan;
                            $totalC = $penilaian->soft_skill_1 + $penilaian->soft_skill_2 + $penilaian->soft_skill_3 + $penilaian->soft_skill_4 + $penilaian->soft_skill_5 + $penilaian->soft_skill_6;
                            $rataC = $totalC / 6;
                            $totalNilai = $rataA + $totalB + $rataC;
                            $nilaiPKL = round(0.7 * $totalNilai, 2);

                            if ($nilaiPKL >= 91) {
                                $keterangan = 'Sangat Baik';
                            } elseif ($nilaiPKL >= 81) {
                                $keterangan = 'Baik';
                            } elseif ($nilaiPKL >= 71) {
                                $keterangan = 'Cukup';
                            } else {
                                $keterangan = 'Kurang';
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $penilaian->siswa->name }}</td>
                            <td>{{ $nilaiPKL }}</td>
                            <td>{{ $keterangan }}</td>
                            <td>
                                <a href="{{ route('magang.wakil_perusahaan.penilaian.show', $penilaian->id) }}" class="text-blue-600 hover:underline">Detail</a>
                                <span class="mx-2">|</span>
                                <a href="{{ route('magang.wakil_perusahaan.penilaian.edit', $penilaian->id) }}" class="text-yellow-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Custom Style --}}
<style>
    .btn-create {
        background-color: #2563eb;
        color: white;
        padding: 0.5rem 1.25rem;
        font-weight: 600;
        font-size: 0.9rem;
        border-radius: 9999px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-create:hover {
        background-color: #1e40af;
        transform: translateY(-2px) scale(1.03);
    }

    .table-container {
        border-radius: 1rem;
        overflow-x: auto;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        padding: 1rem;
        background-color: #fff;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        min-width: 600px;
        border-radius: 1rem;
        overflow: hidden;
    }

    thead {
        background: linear-gradient(to right, #f59e0b, #3b82f6);
        color: white;
    }

    th, td {
        padding: 1rem;
        text-align: left;
        white-space: nowrap;
    }

    tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }

    tbody tr:hover {
        background-color: #e0f2fe;
        transition: background-color 0.3s ease;
    }

    td a {
        transition: color 0.2s ease;
    }

    td a:hover {
        color: #1d4ed8;
        font-weight: 500;
    }
    .btn-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }
    .btn-wrapper.left {
        justify-content: flex-start;
    }
    .btn-wrapper.center {
        justify-content: center;
    }
</style>
@endsection
