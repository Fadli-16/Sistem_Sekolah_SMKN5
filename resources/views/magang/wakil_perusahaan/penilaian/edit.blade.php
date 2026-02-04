@extends('magang.layouts.main')

@section('content')
<div class="max-w-5xl mx-auto mt-10 bg-white shadow-xl rounded-xl p-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center gap-2">
        Edit Nilai PKL Siswa
    </h2>

    <form action="{{ route('magang.wakil_perusahaan.penilaian.update', $penilaian->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Pilih Siswa (Readonly) --}}
        <div>
            <label class="block text-base font-medium text-gray-700 mb-2">👩‍🎓 Nama Siswa</label>
            <input type="text" class="select-field w-full bg-gray-100 cursor-not-allowed" value="{{ $penilaian->siswa->name ?? 'N/A' }}" disabled>
            <input type="hidden" name="siswa_id" value="{{ $penilaian->siswa_id }}">
        </div>

        {{-- Tabel Penilaian --}}
     <div class="overflow-x-auto rounded-xl shadow-md border border-gray-300">
    <table class="min-w-full divide-y divide-gray-300 text-sm">
       <thead class="table-header">
        <tr>
            <th class="px-6 py-3 font-semibold tracking-wide" style="width: 20%;">Kategori</th>
            <th class="px-6 py-3 font-semibold tracking-wide" style="width: 55%;">Indikator</th>
            <th class="px-6 py-3 font-semibold tracking-wide" style="width: 25%;">Nilai (0-100)</th>
        </tr>
    </thead>

        <tbody class="bg-white divide-y divide-gray-200">

            {{-- Hard Skills --}}
            @php $hard = ['Kompetensi Teknis 1', 'Kompetensi Teknis 2', 'Kompetensi Teknis 3']; @endphp
            @foreach($hard as $i => $label)
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                @if($loop->first)
                <td rowspan="3" class="kategori align-middle bg-gray-100 font-semibold text-gray-700 text-center px-4 py-6 rounded-l-xl" style="vertical-align: middle;">
                    Hard Skills <br><span class="text-xs text-gray-500">(60%)</span>
                </td>
                @endif
                <td class="px-6 py-4 text-gray-800">{{ $label }}</td>
                <td class="px-6 py-4">
                    <input type="number"
                        name="hard_skill_{{ $i + 1 }}"
                        value="{{ $penilaian->{'hard_skill_'.($i+1)} }}"
                        class="input-field"
                        min="0" max="100"
                        placeholder="0 - 100"
                        required
                        />
                </td>
            </tr>
            @endforeach

            {{-- Kewirausahaan --}}
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="kategori align-middle bg-gray-100 font-semibold text-gray-700 text-center px-4 py-6 rounded-l-xl" style="vertical-align: middle;">
                    Penyiapan Kewirausahaan <br><span class="text-xs text-gray-500">(20%)</span>
                </td>
                <td class="px-6 py-4 text-gray-800">Nilai Kewirausahaan</td>
                <td class="px-6 py-4">
                    <input type="number"
                        name="kewirausahaan"
                        value="{{ $penilaian->kewirausahaan }}"
                        class="input-field"
                        min="0" max="100"
                        placeholder="0 - 100"
                        required
                        />
                </td>
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
            <tr class="hover:bg-gray-50 transition-colors duration-150">
                @if($i == 0)
                <td rowspan="6" class="kategori align-middle bg-gray-100 font-semibold text-gray-700 text-center px-4 py-6 rounded-l-xl" style="vertical-align: middle;">
                    Soft Skills <br><span class="text-xs text-gray-500">(20%)</span>
                </td>
                @endif
                <td class="px-6 py-4 text-gray-800">{{ ($i+1) . '. ' . $label }}</td>
                <td class="px-6 py-4">
                    <input type="number"
                        name="soft_skill_{{ $i+1 }}"
                        value="{{ $penilaian->{'soft_skill_'.($i+1)} }}"
                        class="input-field"
                        min="0" max="100"
                        placeholder="0 - 100"
                        required
                        />
                </td>
            </tr>
            @endforeach

                </tbody>
            </table>
        </div>

        {{-- Tombol Simpan & Kembali --}}
        <div class="form-footer">
            <a href="{{ route('magang.wakil_perusahaan.penilaian.index') }}" class="btn-back">
                ⬅️ Kembali
            </a>
            <button type="submit" class="btn-submit">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- Style --}}
{{-- Tambahkan ini ke dalam style tag di akhir file --}}

<style>
    /* HEADER TABEL DENGAN WARNA OREN GRADASI */
    .table-header {
        background: linear-gradient(to right, #f97316, #fb923c); /* gradasi oranye */
        color: white;
        text-align: left;
        font-weight: 700;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        letter-spacing: 0.04em;
    }

    .select-field {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        background-color: #f9fafb;
        color: #374151;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .input-field {
        width: 100%;
        max-width: 100px;
        padding: 0.5rem 0.75rem;
        border: 1.5px solid #d1d5db;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        text-align: center;
        transition: 0.3s;
        color: #1f2937;
    }

    .input-field::placeholder {
        color: #9ca3af;
        font-style: italic;
    }

    .input-field:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.4);
    }

    .kategori {
        font-weight: 700;
        background-color: #fef3c7;
        color: #78350f;
        border-right: 1px solid #fcd34d;
        text-align: center;
        vertical-align: middle;
        font-size: 0.95rem;
    }

    .form-footer {
        margin-top: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .btn-back {
        background-color: #f3f4f6;
        color: #1f2937;
        padding: 0.65rem 1.5rem;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease-in-out;
    }

    .btn-back:hover {
        background-color: #e5e7eb;
        transform: translateX(-3px);
    }

    .btn-submit {
        background: linear-gradient(to right, #22c55e, #16a34a); /* warna hijau */
        color: white;
        padding: 0.75rem 1.75rem;
        font-weight: 600;
        border: none;
        border-radius: 9999px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        transition: all 0.3s ease-in-out;
    }

    .btn-submit:hover {
        background: linear-gradient(to right, #16a34a, #15803d);
        transform: scale(1.05);
        box-shadow: 0 6px 18px rgba(34, 197, 94, 0.5);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-back, .btn-submit {
            width: 100%;
            text-align: center;
        }
    }
</style>

@endsection
