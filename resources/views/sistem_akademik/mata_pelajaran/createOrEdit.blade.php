@extends('sistem_akademik.layouts.main')

@section('content')
@php
$isEdit = ! empty($mapel) && $mapel !== null;
@endphp

<div class="container animate-fade-in">
    <h1 class="page-title">{{ $header }}</h1>

    <form method="POST"
        action="{{ $isEdit
              ? route('sistem_akademik.mata_pelajaran.update', ['mata_pelajaran' => $mapel->id])
              : route('sistem_akademik.mata_pelajaran.store')
          }}">
        @csrf
        @if($isEdit)
        @method('PUT')
        @endif

        {{-- Nama Mapel --}}
        <div class="mb-3">
            <label for="nama_mata_pelajaran" class="form-label">Nama Mata Pelajaran</label>
            <input type="text" id="nama_mata_pelajaran" name="nama_mata_pelajaran"
                class="form-control @error('nama_mata_pelajaran') is-invalid @enderror"
                value="{{ old('nama_mata_pelajaran', $mapel->nama_mata_pelajaran ?? '') }}"
                required>
            @error('nama_mata_pelajaran')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Guru --}}
        <div class="mb-3">
            <label for="guru_id" class="form-label">Guru</label>
            <select id="guru_id" name="guru_id" class="form-select @error('guru_id') is-invalid @enderror" required>
                <option value="" disabled {{ !$isEdit ? 'selected' : '' }}>-- Pilih Guru --</option>
                @foreach(($gurus ?? $users ?? collect()) as $g)
                <option value="{{ $g->id }}"
                    {{ old('guru_id', $mapel->guru_id ?? '') == $g->id ? 'selected' : '' }}>
                    {{ $g->nama ?? $g->name ?? '-' }}
                </option>
                @endforeach
            </select>
            @error('guru_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex mt-4">
            <a href="{{ route('sistem_akademik.mata_pelajaran.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                <i class="bi bi-{{ $isEdit ? 'save' : 'plus-circle' }}"></i>
                {{ $isEdit ? 'Update' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>
@endsection
@section('script')

@endsection