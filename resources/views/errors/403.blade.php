@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-container p-5 bg-white rounded shadow-sm">
                <div class="mb-4">
                    <i class="bi bi-shield-lock text-danger" style="font-size: 5rem;"></i>
                </div>
                <h1 class="fw-bold text-danger mb-3">Akses Ditolak</h1>
                <p class="text-muted mb-4">
                    Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Fitur ini hanya tersedia untuk peran {{ $role ?? 'admin' }} yang berwenang.
                </p>
                <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
