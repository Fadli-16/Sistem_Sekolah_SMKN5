@extends('magang.layouts.main')

@section('content')
<div class="container">
    <section class="profile-box">
        <h1 class="profile-title">Profil Perusahaan</h1>

        <div class="profile-grid">
            <div class="profile-item">
                <label class="profile-label">Nama Wakil</label>
                <div class="profile-value">{{ $wakil->nama }}</div>
            </div>

            <div class="profile-item">
                <label class="profile-label">Email</label>
                <div class="profile-value">{{ $wakil->email }}</div>
            </div>

            <div class="profile-item">
                <label class="profile-label">Nama Perusahaan</label>
                <div class="profile-value">{{ $wakil->nama_perusahaan }}</div>
            </div>

            <div class="profile-item">
                <label class="profile-label">No. Telepon Perusahaan</label>
                <div class="profile-value">{{ $wakil->no_perusahaan }}</div>
            </div>

            <div class="profile-item full">
                <label class="profile-label">Alamat Perusahaan</label>
                <div class="profile-value">{{ $wakil->alamat }}</div>
            </div>

            <div class="profile-item full">
                <label class="profile-label">Status Akun</label>
                <span class="status-badge {{ strtolower($wakil->status) }}">
                    {{ $wakil->status }}
                </span>
            </div>

            <div class="profile-item full">
                <label class="profile-label">Bukti Lampiran</label>
                <a href="{{ asset('storage/' . $wakil->bukti_lampiran) }}" target="_blank" class="attachment-link">
                    📎 Lihat Lampiran
                </a>
            </div>
        </div>

        <div class="button-group">
            <a href="{{ route('magang.wakil_perusahaan.profile.edit') }}" class="btn btn-green">✏️ Edit Profil</a>
            <a href="{{ route('magang.wakil_perusahaan.dashboard') }}" class="btn btn-outline-green">🔙 Kembali</a>
        </div>
    </section>
</div>

<style>
    /* Layout */
    .container {
        max-width: 900px;
        margin: 3rem auto;
        padding: 0 1rem;
    }

    .profile-box {
        background: #fff;
        border: 2px solid #fcd34d;
        border-radius: 12px;
        padding: 2rem;
    }

    .profile-title {
        font-size: 2rem;
        font-weight: 700;
        color: #ea580c;
        margin-bottom: 2rem;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .profile-item {
        display: flex;
        flex-direction: column;
    }

    .profile-item.full {
        grid-column: span 2;
    }

    .profile-label {
        font-weight: 600;
        color: #9a3412;
        margin-bottom: 0.5rem;
    }

    .profile-value {
        background-color: #fff7ed;
        border: 1px solid #fdba74;
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        font-weight: 600;
        border-radius: 999px;
        font-size: 0.875rem;
    }

    .status-badge.approved {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-badge.pending {
        background-color: #fef9c3;
        color: #92400e;
    }

    /* Attachment link */
    .attachment-link {
        color: #c2410c;
        font-weight: 600;
        text-decoration: underline;
        transition: color 0.3s ease;
    }

    .attachment-link:hover {
        color: #9a3412;
    }

    /* Buttons */
    .button-group {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 2rem;
    }

    @media (min-width: 768px) {
        .button-group {
            flex-direction: row;
            justify-content: space-between;
        }
    }

    .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 999px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-green {
        background-color: #16a34a;
        color: white;
        border: none;
    }

    .btn-green:hover {
        background-color: #15803d;
    }

    .btn-outline-green {
        border: 2px solid #16a34a;
        color: #16a34a;
        background-color: transparent;
    }

    .btn-outline-green:hover {
        background-color: #f0fdf4;
    }
</style>
@endsection
