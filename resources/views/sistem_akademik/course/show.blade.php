@extends('sistem_akademik.layouts.main')

@section('css')
    <link href="{{ asset('css/course.css') }}?v={{ filemtime(public_path('css/course.css')) }}" rel="stylesheet">
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
            $teacherUser = optional($course->mataPelajaran)->guru;
            $guruName = $teacherUser->nama ?? $teacherUser->name ?? '-';
            $teacherAvatar = asset('assets/profile/default.png');
            if ($teacherUser && $teacherUser->guru && $teacherUser->guru->image) {
                $teacherAvatar = asset('assets/profile/' . ltrim($teacherUser->guru->image, '/'));
            }
            @endphp
            <span class="info-value d-flex align-items-center gap-2" style="display: inline-flex !important; vertical-align: middle;">
                <img src="{{ $teacherAvatar }}" alt="avatar" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/profile/default.png') }}'">
                <span>{{ $guruName }}</span>
            </span>
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
                @php
                    $siswaList = optional($course->kelas)->siswa;
                @endphp
                @if($siswaList && $siswaList->count() > 0)
                <div class="student-list">
                    @foreach($siswaList as $s)
                    @php
                    // fallbacks: siswa->user->nama || siswa->nama || nisn
                    $sNama = data_get($s, 'user.nama') ?? data_get($s, 'user.name') ?? ($s->nama ?? ($s->nisn ?? '-'));
                    $sAvatar = asset('assets/profile/default.png');
                    if ($s->image) {
                        $sAvatar = asset('assets/profile/' . ltrim($s->image, '/'));
                    }
                    @endphp
                    <div class="student-list-item d-flex align-items-center gap-2">
                        <img src="{{ $sAvatar }}" alt="avatar" class="rounded-circle border" style="width: 30px; height: 30px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/profile/default.png') }}'">
                        <div>
                            {{ $sNama }}
                            @if(!empty($s->nisn))
                            <span class="student-badge">{{ $s->nisn }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted">Belum ada siswa yang terdaftar di kelas ini.</p>
                @endif
            </div>
        </div>

        <a href="{{ route('sistem_akademik.course.index') }}" class="back-btn">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection