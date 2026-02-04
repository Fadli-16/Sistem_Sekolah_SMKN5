@extends('magang.layouts.main')

@section('content')
<div class="container">
    <div class="card">
        {{-- Tombol Kembali di kanan atas --}}
        <div class="top-right">
            <a href="{{ route('magang.wakil_perusahaan.penilaian.index') }}" class="btn-back">← Kembali</a>
        </div>

        <h2 class="title">📄 Detail Nilai PKL Siswa</h2>

        {{-- Info Siswa --}}
        <div class="form-group">
            <label class="label">👩‍🎓 Nama Siswa</label>
            <input type="text" class="input-field" value="{{ $penilaian->siswa->name ?? 'N/A' }}" disabled>
        </div>

        {{-- Tabel Penilaian --}}
        <div class="table-wrapper">
            <table id="penilaianTable" class="table display">
                <thead>
                    <tr>
                        <th class="th kategori-col">Kategori</th>
                        <th class="th">Indikator</th>
                        <th class="th nilai-col">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Hard Skills --}}
                    @php $hard = ['Kompetensi Teknis 1', 'Kompetensi Teknis 2', 'Kompetensi Teknis 3']; @endphp
                    @foreach($hard as $i => $label)
                    <tr>
                        <td class="td kategori">Hard Skills<br><span class="persentase">(60%)</span></td>
                        <td class="td">{{ $label }}</td>
                        <td class="td nilai">{{ $penilaian->{'hard_skill_'.($i+1)} }}</td>
                    </tr>
                    @endforeach

                    {{-- Kewirausahaan --}}
                    <tr>
                        <td class="td kategori">Kewirausahaan<br><span class="persentase">(20%)</span></td>
                        <td class="td">Nilai Kewirausahaan</td>
                        <td class="td nilai">{{ $penilaian->kewirausahaan }}</td>
                    </tr>

                    {{-- Soft Skills --}}
                    @php
                        $softLabels = [
                            'Etika berkomunikasi (lisan dan tulisan)',
                            'Integritas (jujur, disiplin, komitmen dan tanggung jawab)',
                            'Etos kerja',
                            'Kemampuan kerja mandiri dan/atau tim',
                            'Kepedulian sosial dan lingkungan',
                            'Ketaatan terhadap norma, K3LH dan SOP industri'
                        ];
                    @endphp
                    @foreach($softLabels as $i => $label)
                    <tr>
                        <td class="td kategori">Soft Skills<br><span class="persentase">(20%)</span></td>
                        <td class="td">{{ ($i+1) . '. ' . $label }}</td>
                        <td class="td nilai">{{ $penilaian->{'soft_skill_'.($i+1)} }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#penilaianTable').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false
        });
    });
</script>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f3f4f6;
    }

    .container {
        display: flex;
        justify-content: center;
        padding: 40px 20px 80px;
        box-sizing: border-box;
    }

    .card {
        width: 100%;
        max-width: 1000px;
        background-color: white;
        padding: 32px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        margin-bottom: 40px;
        position: relative;
    }

    .top-right {
        position: absolute;
        top: 24px;
        right: 32px;
    }

    .title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 24px;
        color: #1f2937;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .label {
        display: block;
        font-size: 16px;
        margin-bottom: 8px;
        color: #374151;
        font-weight: 500;
    }

    .input-field {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background-color: #f9fafb;
        color: #4b5563;
        font-size: 15px;
    }

    .input-field:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .th {
        background: linear-gradient(to right, #f97316, #fb923c);
        color: white;
        padding: 14px;
        text-align: left;
        font-weight: 700;
    }

    .kategori-col {
        width: 20%;
    }

    .nilai-col {
        width: 15%;
    }

    .td {
        padding: 14px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
        color: #1f2937;
    }

    .kategori {
        font-weight: bold;
        background-color: #fef3c7;
        color: #78350f;
        text-align: center;
    }

    .persentase {
        font-size: 12px;
        color: #9ca3af;
        display: block;
        margin-top: 4px;
    }

    .nilai {
        font-family: monospace;
        font-weight: bold;
        text-align: right;
        color: #4338ca;
    }

    .btn-back {
        display: inline-block;
        background-color: #f3f4f6;
        color: #1f2937;
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease-in-out;
    }

    .btn-back:hover {
        background-color: #e5e7eb;
        transform: translateX(-3px);
    }

    @media (max-width: 768px) {
        .title {
            font-size: 22px;
        }

        .top-right {
            position: static;
            text-align: right;
            margin-bottom: 16px;
        }

        .btn-back {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endsection
