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
                <img src="{{ $teacherAvatar }}" alt="avatar" class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/profile/default.png') }}'">
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
                @if($course->jam_mulai && $course->jam_selesai)
                    @php
                        $jp = 0;
                        if ($course->jam_mulai && $course->jam_selesai) {
                            $cStart = substr($course->jam_mulai, 0, 5);
                            $cEnd = substr($course->jam_selesai, 0, 5);
                            $slotStarts = ['07:15', '08:00', '08:45', '10:00', '10:45', '11:30', '13:15', '13:45', '14:15', '14:45', '15:45', '16:15', '16:45'];
                            
                            foreach ($slotStarts as $time) {
                                if ($time >= $cStart && $time < $cEnd) {
                                    $jp++;
                                }
                            }
                        }
                    @endphp
                    ({{ $jp }} JP)
                @endif
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
                    // fallbacks: siswa->user->nama || siswa->nama || nis
                    $sNama = data_get($s, 'user.nama') ?? data_get($s, 'user.name') ?? ($s->nama ?? ($s->nis ?? '-'));
                    $sAvatar = asset('assets/profile/default.png');
                    if ($s->image) {
                        $sAvatar = asset('assets/profile/' . ltrim($s->image, '/'));
                    }
                    @endphp
                    <div class="student-list-item d-flex align-items-center gap-2">
                        <img src="{{ $sAvatar }}" alt="avatar" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/profile/default.png') }}'">
                        <div class="text-truncate">
                            @if(!empty($s->nis))
                            <span class="student-badge text-muted">{{ $s->nis }}</span>
                            @endif
                            {{ $sNama }}
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