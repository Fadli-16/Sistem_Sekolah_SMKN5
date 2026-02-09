@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    .course-header {
        background: linear-gradient(135deg, #1a2a3a, #2c3e50);
        color: white;
        padding: 2rem;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
    }

    .course-content {
        background: white;
        padding: 2rem;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .info-label {
        font-weight: 600;
        color: #1a2a3a;
        width: 140px;
        display: inline-block;
    }

    .info-value {
        color: #495057;
    }

    .info-item {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .back-btn {
        background-color: #004080;
        color: white;
        font-weight: 600;
        border-radius: 4px;
        padding: 0.5rem 1rem;
        text-decoration: none;
        display: inline-block;
        margin-top: 1.5rem;
    }

    .back-btn:hover {
        background-color: #002b5c;
        color: white;
    }

    .student-list {
        margin-top: 1rem;
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 0.5rem;
    }

    .student-list-item {
        padding: 0.5rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .student-list-item:last-child {
        border-bottom: none;
    }

    .student-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background-color: #e9ecef;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-left: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container mt-4 mb-4">
    <div class="course-header">
        <h2>
            {{
            data_get($course, 'kelas.nama_kelas')
                ? data_get($course, 'kelas.nama_kelas')
                    . ' - ' . data_get($course, 'kelas.jurusan', '-')
                    . ' (' . data_get($course, 'kelas.tahun_ajaran', '-') . ')'
                : '-'
        }}
        </h2>
        <p class="mb-0">
            {{ data_get($course, 'mataPelajaran.nama_mata_pelajaran') ?? '-' }}
        </p>
    </div>

    <div class="course-content">
        <div class="info-item">
            <span class="info-label">Kelas:</span>
            <span class="info-value">
                {{
                    data_get($course, 'kelas.nama_kelas')
                    ? data_get($course, 'kelas.nama_kelas') . ' - ' . data_get($course, 'kelas.jurusan', '-') . ' (' . data_get($course, 'kelas.tahun_ajaran', '-') . ')'
                    : '-'
                }}
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">Guru:</span>
            @php
            // cari nama guru: coba (guru.nama) lalu (guru.name) lalu fallback '-'
            $guruName = data_get($course, 'mataPelajaran.guru.nama')
            ?? data_get($course, 'mataPelajaran.guru.name')
            ?? '-';
            @endphp
            <span class="info-value">{{ $guruName }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">Jadwal:</span>
            <span class="info-value">
                {{ $course->hari ?? '-' }},
                {{
                    $course->jam_mulai
                        ? \Carbon\Carbon::createFromFormat('H:i:s', $course->jam_mulai)->format('H:i')
                        : ( $course->jam_mulai ? date('H:i', strtotime($course->jam_mulai)) : '-' )
                }}
                -
                {{
                    $course->jam_selesai
                        ? \Carbon\Carbon::createFromFormat('H:i:s', $course->jam_selesai)->format('H:i')
                        : ( $course->jam_selesai ? date('H:i', strtotime($course->jam_selesai)) : '-' )
                }}
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">Ruangan:</span>
            <span class="info-value">
                {{ $course->ruangan ?? '-' }}
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">Siswa:</span>
            <div class="info-value">
                @if(method_exists($course, 'siswa') && $course->siswa->count() > 0)
                <div class="student-list">
                    @foreach($course->siswa as $s)
                    @php
                    // fallbacks: siswa->user->nama || siswa->nama || nisn
                    $sNama = data_get($s, 'user.nama') ?? data_get($s, 'user.name') ?? ($s->nama ?? ($s->nisn ?? '-'));
                    @endphp
                    <div class="student-list-item">
                        {{ $sNama }}
                        @if(!empty($s->nisn))
                        <span class="student-badge">{{ $s->nisn }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted">Belum ada siswa yang terdaftar di course ini.</p>
                @endif
            </div>
        </div>

        <a href="{{ route('sistem_akademik.course.index') }}" class="back-btn">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection