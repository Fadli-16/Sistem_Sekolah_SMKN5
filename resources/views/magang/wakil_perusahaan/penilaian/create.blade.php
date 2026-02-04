@extends('magang.layouts.main')

@section('content')
<div class="form-container">
    <h2 class="form-title">Input Nilai PKL Siswa</h2>

    <form action="{{ route('penilaian.store') }}" method="POST">
        @csrf

        {{-- Pilih Siswa --}}
        <div class="form-group">
            <label for="siswa_id" class="form-label">Pilih Siswa</label>
            <select name="siswa_id" class="input-field" required>
                @forelse($siswas as $siswa)
                    @php
                        $sudahDinilai = \App\Models\Penilaian::where('siswa_id', $siswa->id)->exists();
                    @endphp
                    <option value="{{ $siswa->id }}" {{ $sudahDinilai ? 'disabled' : '' }}>
                        {{ $siswa->name }} {{ $sudahDinilai ? '(Sudah Dinilai)' : '' }}
                    </option>
                @empty
                    <option disabled>Tidak ada siswa magang aktif</option>
                @endforelse
            </select>
        </div>

        {{-- Tabel Penilaian --}}
        <div class="table-wrapper">
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Indikator</th>
                        <th>Nilai (0-100)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Hard Skills --}}
                    <tr>
                        <td rowspan="3" class="kategori">Hard Skills (60%)</td>
                        <td>Kompetensi Teknis 1</td>
                        <td><input type="number" name="hard_skill_1" class="input-field" required></td>
                    </tr>
                    <tr>
                        <td>Kompetensi Teknis 2</td>
                        <td><input type="number" name="hard_skill_2" class="input-field" required></td>
                    </tr>
                    <tr>
                        <td>Kompetensi Teknis 3</td>
                        <td><input type="number" name="hard_skill_3" class="input-field" required></td>
                    </tr>

                    {{-- Kewirausahaan --}}
                    <tr>
                        <td class="kategori">Penyiapan Kewirausahaan (20%)</td>
                        <td>Nilai Kewirausahaan</td>
                        <td><input type="number" name="kewirausahaan" class="input-field" required></td>
                    </tr>

                    {{-- Soft Skills --}}
                    @php
                        $softSkills = [
                            'Etika berkomunikasi (lisan dan tulisan)',
                            'Integritas (jujur, disiplin, komitmen dan tanggung jawab)',
                            'Etos kerja',
                            'Kemampuan kerja mandiri dan/atau tim',
                            'Kepedulian sosial dan lingkungan',
                            'Ketaatan terhadap norma, K3LH dan SOP industri'
                        ];
                    @endphp
                    @foreach($softSkills as $index => $skill)
                    <tr>
                        @if($loop->first)
                            <td rowspan="{{ count($softSkills) }}" class="kategori">Soft Skills (20%)</td>
                        @endif
                        <td>{{ $loop->iteration }}. {{ $skill }}</td>
                        <td><input type="number" name="soft_skill_{{ $loop->iteration }}" class="input-field" required></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tombol Simpan --}}
        <div class="form-footer">
            <a href="{{ route('magang.wakil_perusahaan.penilaian.index') }}" class="btn-back">⬅️ Kembali</a>
            <button type="submit" class="btn-submit">💾 Simpan Nilai</button>
        </div>
    </form>
</div>

{{-- CSS Style --}}
<style>
/* (Tetap sama dengan versi sebelumnya, tidak perlu diubah ulang untuk kejelasan) */
.form-container {
    max-width: 960px;
    margin: 3rem auto;
    background-color: #ffffff;
    padding: 2.5rem;
    border-radius: 1rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
}

.form-title {
    font-size: 1.8rem;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.input-field {
    width: 100%;
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.5rem;
    background-color: #f9fafb;
    transition: 0.3s;
}

.input-field:focus {
    border-color: #3b82f6;
    outline: none;
    background-color: #fff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.table-wrapper {
    overflow-x: auto;
    margin-top: 1rem;
}

.form-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    font-size: 0.95rem;
}

.form-table thead {
    background: linear-gradient(to right, #0d9488, #22c55e);
    color: white;
}

.form-table th, .form-table td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.kategori {
    background-color: #f9fafb;
    font-weight: 600;
    vertical-align: top;
    color: #374151;
}

.form-footer {
    margin-top: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.btn-back {
    background-color: #e5e7eb;
    color: #374151;
    padding: 0.6rem 1.25rem;
    border-radius: 9999px;
    font-weight: 600;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.btn-back:hover {
    background-color: #d1d5db;
    transform: translateX(-2px);
}

.btn-submit {
    background: linear-gradient(to right, #3b82f6, #2563eb);
    color: white;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border: none;
    border-radius: 9999px;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    transition: all 0.3s ease-in-out;
}

.btn-submit:hover {
    background: linear-gradient(to right, #2563eb, #1e40af);
    transform: scale(1.05);
    box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
}
</style>

{{-- SweetAlert untuk Error --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if ($errors->has('siswa_id'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Siswa Sudah Dinilai',
            text: '{{ $errors->first('siswa_id') }}',
            confirmButtonColor: '#2563eb'
        });
    </script>
@endif

@endsection
