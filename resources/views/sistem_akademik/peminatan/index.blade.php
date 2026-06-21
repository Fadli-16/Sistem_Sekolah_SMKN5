@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #dee2e6;
        height: 38px;
        border-radius: 8px;
        padding-top: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    input.minat-radio:checked + .minat-card {
        border-color: #f97316 !important;
        background: #fff7ed;
        color: #c2410c;
    }

    /* Premium Stats Cards */
    .stat-card-premium {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .stat-card-premium:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px -4px rgba(0,0,0,0.08);
    }
    .stat-icon-wrap {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        font-size: 1.1rem;
    }
    .stat-card-premium.purple .stat-icon-wrap { background: #f5f3ff; color: #8b5cf6; }
    .stat-card-premium.blue .stat-icon-wrap { background: #f0f9ff; color: #0ea5e9; }
    .stat-card-premium.green .stat-icon-wrap { background: #f0fdf4; color: #10b981; }
    .stat-card-premium.gray .stat-icon-wrap { background: #f8fafc; color: #64748b; }

    .stat-value-large {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.15rem;
    }
    .stat-label-modern {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    /* Chart Containers */
    .chart-container-modern {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 4px -1px rgba(0,0,0,0.05);
    }
    .chart-header-modern {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .chart-title-modern {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
    }

    /* Progress bar refined */
    .progress-modern {
        height: 10px;
        background-color: #f1f5f9;
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-bar-modern {
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 10px;
    }

    /* Pagination Modern */
    .pagination-modern .pagination {
        margin-bottom: 0;
        gap: 5px;
    }
    .pagination-modern .page-link {
        border-radius: 8px !important;
        border: none;
        padding: 0.5rem 0.8rem;
        color: #64748b;
        font-weight: 500;
        transition: all 0.2s;
    }
    .pagination-modern .page-item.active .page-link {
        background-color: #3b82f6;
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    .pagination-modern .page-link:hover:not(.active) {
        background-color: #f1f5f9;
        color: #3b82f6;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $header }}</h1>
            <p class="page-subtitle"><i class="bi bi-lightbulb me-1"></i>Gambaran minat dan rencana masa depan siswa</p>
        </div>

        @php
        $userRole = Auth::user()->role;
        $isAdminRole = in_array($userRole, ['superadmin', 'super_admin', 'admin_sa', 'admin']);
        $canCreate = $isAdminRole || ($userRole === 'siswa' && ! ($hasOwnPeminatan ?? false) && $isWithinTimeframe);
        @endphp

        <div class="d-flex gap-2 align-items-center">
            @if($isAdminRole)
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#settingsModal">
                <i class="bi bi-gear-fill me-1"></i> Pengaturan Waktu
            </button>
            @endif

            @if($canCreate)
                @if($isAdminRole)
                <button type="button" id="btn-bulk-delete" class="btn btn-sm btn-danger-app d-none" onclick="bulkDelete()">
                    <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
                </button>
                @endif
                <a href="{{ route('sistem_akademik.peminatan.create') }}" class="btn-primary-app">
                    <i class="bi bi-plus-lg"></i> Tambah Peminatan
                </a>
            @endif
        </div>
    </div>


    @if($userRole === 'siswa' && !$isWithinTimeframe && !($hasOwnPeminatan ?? false))
    <div class="alert alert-warning mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <div>
            <strong>Perhatian:</strong> Saat ini di luar waktu pengisian data peminatan. Silakan hubungi admin jika ada pertanyaan.
        </div>
    </div>
    @endif


    {{-- Stats Cards (Premium - Compact) --}}
    <div class="row g-3 mb-4">
        @php
            $statConfigs = [
                'kuliah' => ['icon' => 'mortarboard', 'color' => 'purple', 'label' => 'Melanjutkan Kuliah'],
                'bekerja' => ['icon' => 'briefcase', 'color' => 'blue', 'label' => 'Langsung Bekerja'],
                'wirausaha' => ['icon' => 'shop', 'color' => 'green', 'label' => 'Wirausaha'],
                'lainnya' => ['icon' => 'three-dots', 'color' => 'gray', 'label' => 'Rencana Lainnya']
            ];
        @endphp
        @foreach($statConfigs as $key => $cfg)
        <div class="col-6 col-lg-3">
            <div class="stat-card-premium {{ $cfg['color'] }} fade-in-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
                <div class="stat-icon-wrap">
                    <i class="bi bi-{{ $cfg['icon'] }}"></i>
                </div>
                <div class="stat-value-large">{{ number_format($counts[$key] ?? 0) }}</div>
                <div class="stat-label-modern">{{ $cfg['label'] }}</div>
                <div class="mt-2 text-muted small">
                    @php $pct = $totalRespondents > 0 ? round((($counts[$key] ?? 0) / $totalRespondents) * 100, 1) : 0; @endphp
                    <span class="fw-bold">{{ $pct }}%</span> dari total responden
                </div>
                {{-- Decorative background icon --}}
                <i class="bi bi-{{ $cfg['icon'] }}" style="position: absolute; right: -5px; bottom: -5px; font-size: 3.5rem; opacity: 0.03;"></i>
            </div>
        </div>
        @endforeach
    </div>

    <div class="table-container">
        {{-- FILTER BAR --}}
        <div class="table-container-header" style="padding: 1.25rem 1.5rem;">
            <form action="{{ route('sistem_akademik.peminatan.index') }}" method="GET" class="d-flex justify-content-between align-items-center flex-wrap w-100 gap-3">
                {{-- Bagian Kiri: Dropdowns --}}
                <div class="d-flex gap-2 flex-wrap">
                    <select name="kelas" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                        <option value="">-- Kelas --</option>
                        @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>

                    <select name="minat" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                        <option value="">-- Minat --</option>
                        <option value="kuliah" {{ request('minat') == 'kuliah' ? 'selected' : '' }}>Kuliah</option>
                        <option value="bekerja" {{ request('minat') == 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                        <option value="wirausaha" {{ request('minat') == 'wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                        <option value="lainnya" {{ request('minat') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>

                    @php
                        $user = Auth::user();
                        $isAdmin = in_array($user->role, ['admin', 'super_admin', 'admin_sa']);
                        $isGuru = $user->role === 'guru';
                    @endphp

                    @if($isAdmin)
                    <select name="guru_bk" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                        <option value="">-- Guru BK --</option>
                        @foreach ($guruBKList as $bk)
                        <option value="{{ $bk->id }}" {{ request('guru_bk') == $bk->id ? 'selected' : '' }}>{{ $bk->nama ?? $bk->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    <select name="tahun_ajaran" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                        <option value="">-- TA --</option>
                        @foreach ($tahunAjaranList as $ta)
                        <option value="{{ $ta }}" {{ request('tahun_ajaran') == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>

                    <a href="{{ route('sistem_akademik.peminatan.index') }}" class="btn btn-sm btn-secondary-app" title="Reset Filter">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="data-table">
                <thead>
                    <tr>
                        @if(in_array(Auth::user()->role, ['admin', 'super_admin', 'admin_sa']))
                        <th width="3%">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        @endif
                        <th width="40px">No</th>
                        <th>Siswa</th>
                        <th>Minat</th>
                        <th>Detail Tujuan</th>
                        <th>Ekonomi</th>
                        <th class="text-center">Dokumen</th>
                        <th width="80px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peminatans as $p)
                    @php
                        $isOwner = $user->role === 'siswa' && optional($user->siswa)->id === $p->siswa_id;
                        $canSeePrivate = $isAdmin || $isGuru || $isOwner;
                    @endphp
                    <tr>
                        @if(in_array(Auth::user()->role, ['admin', 'super_admin', 'admin_sa']))
                        <td>
                            <input type="checkbox" class="form-check-input select-item" value="{{ $p->id }}">
                        </td>
                        @endif
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="font-weight:600; color:var(--text-dark);">{{ optional(optional($p->siswa)->user)->nama }}</div>
                                <span class="badge-modern badge-gray" style="font-size: 0.65rem;">{{ optional($p->siswa)->nis }}</span>
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">
                                @php
                                    $siswa = $p->siswa;
                                    $namaKelas = '-';
                                    if($siswa) {
                                        $namaKelas = optional($siswa->kelasData)->nama_kelas ?? '-';
                                    }
                                @endphp
                                Kelas: {{ $namaKelas }}
                            </div>
                        </td>
                        <td>
                            @php
                                $m = strtolower($p->minat);
                                $mBadge = match($m) {
                                    'kuliah' => 'badge-purple',
                                    'bekerja' => 'badge-info',
                                    'wirausaha' => 'badge-success',
                                    default => 'badge-gray'
                                };
                                $icon = match($m) {
                                    'kuliah' => 'mortarboard',
                                    'bekerja' => 'briefcase',
                                    'wirausaha' => 'shop',
                                    default => 'three-dots'
                                };
                            @endphp
                            <span class="badge-modern {{ $mBadge }} text-capitalize">
                                <i class="bi bi-{{ $icon }} me-1"></i>
                                {{ $m }}
                            </span>
                        </td>
                        <td style="max-width: 250px;">
                            <div style="font-weight:500; font-size:0.875rem;">
                                @if($m === 'kuliah') {{ $p->pemilihan_jurusan }}
                                @elseif($m === 'bekerja') {{ $p->jenis_pekerjaan }}
                                @elseif($m === 'wirausaha') {{ $p->ide_bisnis }}
                                @else - @endif
                            </div>
                            <div class="text-muted small text-truncate" title="{{ $p->alasan }}">{{ $p->alasan }}</div>
                        </td>
                        <td>
                            @if($canSeePrivate)
                                <div style="font-size: 0.8rem; font-weight: 600;">Rp{{ number_format($p->penghasilan_ortu, 0, ',', '.') }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ $p->tanggungan_keluarga }} Tanggungan</div>
                            @else
                                <span class="text-muted small"><em>Rahasia</em></span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                @if($canSeePrivate)
                                    @if($p->file_raport)
                                    <a href="{{ $p->file_raport }}" target="_blank" class="btn-icon btn-icon-info" title="Raport"><i class="bi bi-file-earmark-text"></i></a>
                                    @endif
                                    @if($p->file_angket)
                                    <a href="{{ $p->file_angket }}" target="_blank" class="btn-icon btn-icon-warning" title="Angket"><i class="bi bi-file-earmark-check"></i></a>
                                    @endif
                                @else
                                    <i class="bi bi-lock text-muted"></i>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                @php
                                    $user = Auth::user();
                                    $isAdmin = in_array($user->role, ['admin', 'super_admin', 'admin_sa']);
                                    $isGuru = $user->role === 'guru';
                                    $isSiswa = $user->role === 'siswa';
                                    $isOwner = $user->role === 'siswa' && optional($user->siswa)->id === $p->siswa_id;
                                @endphp

                                @if($isAdmin)
                                    {{-- Admin Full Access --}}
                                    <a href="{{ route('sistem_akademik.peminatan.edit', $p->id) }}" class="btn-icon btn-icon-warning"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('sistem_akademik.peminatan.destroy', $p->id) }}" method="POST" id="deleteForm{{ $p->id }}" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('{{ $p->id }}')" class="btn-icon btn-icon-danger"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                @elseif($isSiswa && $isOwner)
                                    {{-- Siswa Only Edit Own Data --}}
                                    <a href="{{ route('sistem_akademik.peminatan.edit', $p->id) }}" class="btn-icon btn-icon-warning"><i class="bi bi-pencil-fill"></i></a>
                                @else
                                    {{-- Guru or Other Siswa: View Only --}}
                                    <span class="text-muted small">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        
    </div>

    {{-- RINGKASAN DI ATAS CHART --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 12px;">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                {{-- Partisipasi --}}
                <div class="col-md-5 border-end">
                    <h6 class="text-muted small text-uppercase fw-bold mb-3">Partisipasi Siswa</h6>
                    @php
                        $percentage = $totalStudents > 0 ? round(($totalRespondents / $totalStudents) * 100, 1) : 0;
                    @endphp
                    <div class="d-flex align-items-end gap-2 mb-2">
                        <span class="h2 mb-0 fw-bold">{{ $totalRespondents }}</span>
                        <span class="text-muted mb-1">/ {{ $totalStudents }} Siswa</span>
                        <span class="badge bg-soft-primary text-primary ms-auto" style="background-color: #e0e7ff; color: #4338ca; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.75rem;">{{ $percentage }}%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px; background-color: #f1f5f9;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                {{-- Rincian Minat --}}
                <div class="col-md-7">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Rincian Per Minat</h6>
                        <small class="text-muted">
                            @php $kelasAktif = $kelasList->firstWhere('id', request('kelas')); @endphp
                            Filter: <strong>{{ request('minat') ? ucfirst(request('minat')) : 'Semua' }}</strong> | 
                            Kelas: <strong>{{ $kelasAktif ? $kelasAktif->nama_kelas : 'Semua' }}</strong>
                        </small>
                    </div>
                    <div class="d-flex gap-4 flex-wrap">
                        @foreach (['kuliah' => 'purple', 'bekerja' => 'info', 'wirausaha' => 'success', 'lainnya' => 'gray'] as $key => $color)
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $key == 'kuliah' ? '#8b5cf6' : ($key == 'bekerja' ? '#0ea5e9' : ($key == 'wirausaha' ? '#10b981' : '#94a3b8')) }};"></div>
                                <span class="text-muted small text-capitalize">{{ $key }}:</span>
                                <span class="fw-bold small">{{ $statsPerOption[$key] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK MODERN (Compact) --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="chart-container-modern h-100">
                <div class="chart-header-modern mb-2">
                    <h5 class="chart-title-modern small mb-0">Tren Distribusi Minat</h5>
                    <div class="badge bg-soft-primary text-primary px-2 py-1" style="background: #eff6ff; border-radius: 6px; font-size: 0.7rem;">Perbandingan Tahunan</div>
                </div>
                <div style="height: 220px;">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-container-modern h-100">
                <div class="chart-header-modern mb-2">
                    <h5 class="chart-title-modern small mb-0">Proporsi Saat Ini</h5>
                </div>
                <div style="height: 180px; position: relative;">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-3">
                    <div class="row g-2">
                        @foreach($statsPerOption as $optKey => $count)
                        @php 
                            $colors = ['kuliah' => '#8b5cf6', 'bekerja' => '#0ea5e9', 'wirausaha' => '#10b981', 'lainnya' => '#94a3b8'];
                            $p = $totalRespondents > 0 ? round(($count / $totalRespondents) * 100, 1) : 0;
                        @endphp
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-1 mb-0">
                                <div style="width: 6px; height: 6px; border-radius: 50%; background: {{ $colors[$optKey] ?? '#ccc' }}"></div>
                                <span class="small text-muted text-capitalize" style="font-size: 0.65rem;">{{ $optKey }}</span>
                            </div>
                            <div class="fw-bold small" style="font-size: 0.75rem;">{{ $p }}%</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SUMMARY DINAMIS --}}
    <div class="card p-3 mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-bar-chart-line-fill fs-3 text-primary"></i>
            <div>
                <h6 class="mb-1">Ringkasan Hasil</h6>
                <p class="mb-0 text-muted small">
                    {!! $summaryText ?? 'Belum ada ringkasan karena tidak ada data.' !!}
                </p>
            </div>
        </div>
    </div>

    {{-- TREN & RINCIAN --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card p-3 h-100">
                <h6 class="mb-2">Tren Per Minat (tahun terakhir)</h6>
                @if(!empty($trendSummary))
                <ul class="mb-0 list-unstyled small">
                    @foreach($trendSummary as $key => $t)
                    <li class="mb-2">
                        <strong>{{ $t['label'] ?? ucfirst($key) }}:</strong>
                        <div class="text-muted">{!! $t['text'] ?? 'Tidak cukup data.' !!}</div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="text-muted small">Tidak ada data tren.</div>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 h-100">
                <h6 class="mb-2">Rincian & Alasan Umum</h6>

                <p class="mb-1 small text-muted">Jumlah per pilihan (filter aktif):</p>
                <ul class="mb-2">
                    @foreach(['bekerja','wirausaha','kuliah','lainnya'] as $opt)
                    <li>
                        <strong>{{ ucfirst($opt) }}:</strong>
                        {{ $detailedCounts[$opt] ?? 0 }} siswa
                        @php
                        $pct = isset($detailedCounts[$opt]) && $totalRespondents>0 ? round(($detailedCounts[$opt]/max(1,$detailedCounts[$opt] + array_sum($detailedCounts) - $detailedCounts[$opt]))*100,1) : null;
                        @endphp
                    </li>
                    @endforeach
                </ul>

                <p class="mb-1 small text-muted">Alasan teratas (global):</p>
                @if(!empty($topReasonsGlobal))
                <ol class="mb-0">
                    @foreach($topReasonsGlobal as $reason => $count)
                    <li class="small">{{ Str::limit($reason, 120) }} <span class="text-muted">({{ $count }}x)</span></li>
                    @endforeach
                </ol>
                @else
                <div class="small text-muted">Alasan beragam / tidak cukup contoh untuk dirangkum.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
{{-- Modal Pengaturan Waktu --}}
@if($isAdminRole)
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="settingsModalLabel">Pengaturan Waktu Peminatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-4">Atur rentang waktu untuk siswa dapat mengisi data peminatan</p>
                <form action="{{ route('sistem_akademik.peminatan.updateSettings') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="start_date" class="form-label fw-bold">Waktu Mulai</label>
                        <input type="datetime-local" name="start_date" id="start_date" class="form-control" 
                               value="{{ $peminatanSetting && $peminatanSetting->start_date ? \Carbon\Carbon::parse($peminatanSetting->start_date)->format('Y-m-d\TH:i') : '' }}">
                        <div class="form-text">Kosongkan jika tidak ada batas awal.</div>
                    </div>

                    <div class="mb-4">
                        <label for="end_date" class="form-label fw-bold">Waktu Selesai</label>
                        <input type="datetime-local" name="end_date" id="end_date" class="form-control" 
                               value="{{ $peminatanSetting && $peminatanSetting->end_date ? \Carbon\Carbon::parse($peminatanSetting->end_date)->format('Y-m-d\TH:i') : '' }}">
                        <div class="form-text">Kosongkan jika tidak ada batas akhir.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#data-table')) {
            $('#data-table').DataTable({
                responsive: true,
                @if(in_array(Auth::user()->role, ['admin', 'super_admin', 'admin_sa']))
                columnDefs: [{ orderable: false, targets: [0, -1, -2] }],
                @else
                columnDefs: [{ orderable: false, targets: [-1, -2] }],
                @endif
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: { first: "«", last: "»", next: "›", previous: "‹" },
                    zeroRecords: "Data tidak ditemukan"
                }
            });
        }

        // Select All - Use DataTable API
        $('#select-all').on('click', function() {
            const table = $('#data-table').DataTable();
            const rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
            updateBulkDeleteButton();
        });

        // Event delegation for checkboxes
        $(document).on('change', '.select-item', function() {
            updateBulkDeleteButton();
        });
    });

    function updateBulkDeleteButton() {
        const table = $('#data-table').DataTable();
        const selectedCount = table.$('.select-item:checked').length;
        
        if ($('#selected-count').length) {
            $('#selected-count').text(selectedCount);
        }
        if (selectedCount > 0) {
            $('#btn-bulk-delete').removeClass('d-none');
        } else {
            $('#btn-bulk-delete').addClass('d-none');
            $('#select-all').prop('checked', false);
        }
    }

    function bulkDelete() {
        const table = $('#data-table').DataTable();
        const selectedIds = [];
        table.$('.select-item:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Silakan pilih data yang akan dihapus.', 'info');
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Hapus Data Peminatan Terpilih?',
            text: `Anda akan menghapus ${selectedIds.length} data peminatan secara permanen!`,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('sistem_akademik.peminatan.bulkDestroy') }}",
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                    }
                });
            }
        });
    }

    function confirmDelete(id) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Data Peminatan?',
            text: 'Data peminatan ini akan dihapus secara permanen!',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }

    // Chart data dari controller
    const years = JSON.parse("{!! addslashes(json_encode($years ?? [])) !!}");
    const perOption = JSON.parse("{!! addslashes(json_encode($perOptionPerYear ?? [])) !!}");
    const chartPieLabels = JSON.parse("{!! addslashes(json_encode($chartPie['labels'] ?? [])) !!}");
    const chartPieData = JSON.parse("{!! addslashes(json_encode($chartPie['totals'] ?? [])) !!}");

    // Helper function for Chart Gradients
    function createGradient(ctx, color1, color2) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }

    // Area chart
    (function() {
        const palette = {
            'kuliah': { solid: '#8b5cf6', bg: 'rgba(139, 92, 246, 0.1)' },
            'bekerja': { solid: '#0ea5e9', bg: 'rgba(14, 165, 233, 0.1)' },
            'wirausaha': { solid: '#10b981', bg: 'rgba(16, 185, 129, 0.1)' },
            'lainnya': { solid: '#94a3b8', bg: 'rgba(148, 163, 184, 0.1)' }
        };

        const datasets = [];
        for (const [key, arr] of Object.entries(perOption)) {
            const color = palette[key] || { solid: '#ccc', bg: 'rgba(0,0,0,0.05)' };
            datasets.push({
                label: key.charAt(0).toUpperCase() + key.slice(1),
                data: arr,
                fill: true,
                tension: 0.4,
                borderColor: color.solid,
                backgroundColor: color.bg,
                pointBackgroundColor: color.solid,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 3
            });
        }

        if (document.getElementById('myAreaChart')) {
            const ctx = document.getElementById('myAreaChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: { labels: years, datasets },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: true, position: 'top', labels: { usePointStyle: true, boxWidth: 6, padding: 20, font: { weight: '600' } } },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 10,
                            displayColors: true
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { stepSize: 1, color: '#64748b', font: { weight: '500' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { weight: '500' } }
                        }
                    }
                }
            });
        }
    })();

    // Pie chart
    (function() {
        if (document.getElementById('myPieChart')) {
            const ctx = document.getElementById('myPieChart').getContext('2d');
            const paletteMap = {
                'kuliah': '#8b5cf6',
                'bekerja': '#0ea5e9',
                'wirausaha': '#10b981',
                'lainnya': '#94a3b8'
            };
            const colors = chartPieLabels.map(label => paletteMap[label.toLowerCase()] || '#ccc');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartPieLabels,
                    datasets: [{
                        data: chartPieData,
                        backgroundColor: colors,
                        hoverOffset: 15,
                        borderRadius: 8,
                        spacing: 5
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = Math.round((val / total) * 100);
                                    return ` ${context.label}: ${val} Siswa (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    })();
</script>
@endsection