@extends('magang.layouts.main')

@section('content')
<style>
    .form-container {
        max-width: 600px;
        margin: 3rem auto;
        background: #fff;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border: 1px solid #fbbf24;
        font-family: Arial, sans-serif;
    }

    .form-container h2 {
        font-size: 1.8rem;
        font-weight: bold;
        color: #d97706;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #b45309;
    }

    select, input[type="text"], input[type="number"] {
        width: 100%;
        padding: 0.75rem;
        border-radius: 10px;
        border: 2px solid #fcd34d;
        background-color: #fffaf0;
        font-size: 1rem;
    }

    input[readonly] {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #6b7280;
    }

    button {
        background-color: #10b981;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
        display: block;
        margin: 0 auto;
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    button:hover {
        background-color: #059669;
    }
</style>

<div class="form-container">
    <h2>📝 Input Nilai Laporan PKL</h2>

    <form action="{{ route('nilai_akhir.store') }}" method="POST">
        @csrf
        <div class="form-group">
        <label for="siswa_id">Nama Siswa</label>
        <select name="siswa_id" id="siswaSelect" required>
            <option disabled selected>Pilih siswa</option>
            @foreach($penilaians as $penilaian)
                <option value="{{ $penilaian->siswa->id }}"
                    data-nilai="{{ $penilaian->getAverage() }}">
                    {{ $penilaian->siswa->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Nilai PKL (otomatis)</label>
        <input type="text" id="nilaiPKL" readonly>
    </div>

        <div class="form-group">
            <label for="nilai_laporan">Nilai Laporan</label>
            <input type="number" name="nilai_laporan" min="0" max="100" step="0.1" required>
        </div>

        <button type="submit">💾 Simpan Nilai</button>
    </form>
    <script>
    const siswaSelect = document.getElementById('siswaSelect');
    const nilaiPKL = document.getElementById('nilaiPKL');

    siswaSelect.addEventListener('change', function () {
        const selected = siswaSelect.options[siswaSelect.selectedIndex];
        const nilai = selected.getAttribute('data-nilai');
        nilaiPKL.value = nilai || '-';
    });
</script>
</div>
@endsection
