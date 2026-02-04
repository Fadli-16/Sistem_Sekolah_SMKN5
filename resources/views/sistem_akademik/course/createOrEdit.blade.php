@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    button {
        background-color: #004080;
        color: white;
        font-weight: bold;
        cursor: pointer;
        border: none;
    }

    button:hover {
        background-color: #002b5c;
    }

    /* Styling untuk multi-select */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: 38px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #4ecdc4;
        border: 1px solid #4ecdc4;
        color: white;
        padding: 2px 8px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }

    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: middle;
        border: 0.2em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
        margin-right: 0.5rem;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container mt-4 mb-4">
    <h2>{{ $header }}</h2>
    <div class="card p-4">
        <form action="{{ isset($course) ? route('sistem_akademik.course.update', $course->id) : route('sistem_akademik.course.store') }}" method="POST">
            @csrf
            @if(isset($course))
            @method('PUT')
            @endif

            <div class="mb-3">
                <label for="nama_course" class="form-label">Nama Course</label>
                <input
                    type="text"
                    class="form-control"
                    id="nama_course"
                    name="nama_course"
                    value="{{ old('nama_course', $course->nama_course ?? '') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="kelas_id" class="form-label">Kelas</label>
                <select
                    class="form-control"
                    id="kelas_id"
                    name="kelas_id"
                    required>
                    <option value="" disabled selected>-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k->id }}"
                        data-jurusan="{{ $k->jurusan }}"
                        {{ (old('kelas_id', $course->kelas_id ?? '') == $k->id) ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} - {{ $k->jurusan }} ({{ $k->tahun_ajaran }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="jurusan" class="form-label">Jurusan</label>
                <select
                    class="form-control"
                    id="jurusan"
                    name="jurusan"
                    required>
                    <option value="" disabled selected>-- Pilih Jurusan --</option>
                    @foreach($jurusanList as $j)
                    <option value="{{ $j }}" {{ (isset($selectedJurusan) && $selectedJurusan == $j) ? 'selected' : '' }}>
                        {{ $j }}
                    </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    <i class="bi bi-info-circle"></i> Pilih jurusan untuk menampilkan daftar siswa.
                </small>
            </div>

            <div class="mb-3">
                <label for="mata_pelajaran_id" class="form-label">Mata Pelajaran</label>
                <select
                    class="form-control"
                    id="mata_pelajaran_id"
                    name="mata_pelajaran_id"
                    required>
                    <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                    @foreach($mataPelajaran as $mp)
                    <option value="{{ $mp->id }}" {{ (old('mata_pelajaran_id', $course->mata_pelajaran_id ?? '') == $mp->id) ? 'selected' : '' }}>
                        {{ $mp->nama_mata_pelajaran }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="guru_id" class="form-label">Guru</label>
                <select
                    class="form-control"
                    id="guru_id"
                    name="guru_id"
                    required>
                    <option value="" disabled selected>-- Pilih Guru --</option>
                    @foreach($guru as $g)
                    <option value="{{ $g->id }}" {{ (old('guru_id', $course->guru_id ?? '') == $g->id) ? 'selected' : '' }}>
                        {{ $g->nama }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="siswa_ids" class="form-label">
                    Siswa
                    <span id="students-loading" class="d-none">
                        <span class="loading-spinner"></span>Memuat data siswa...
                    </span>
                </label>
                <select
                    class="form-control select2-multiple"
                    id="siswa_ids"
                    name="siswa_ids[]"
                    multiple>
                    @foreach($siswa as $s)
                    <option value="{{ $s->id }}"
                        {{ (isset($selectedSiswaIds) && in_array($s->id, $selectedSiswaIds)) ? 'selected' : '' }}>
                        {{ $s->user->nama }} ({{ $s->nisn }})
                    </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    <i class="bi bi-info-circle"></i> Anda dapat memilih beberapa siswa sekaligus.
                </small>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea
                    class="form-control"
                    id="deskripsi"
                    name="deskripsi"
                    rows="3">{{ old('deskripsi', $course->deskripsi ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="hari" class="form-label">Hari</label>
                <select
                    class="form-control"
                    id="hari"
                    name="hari"
                    required>
                    <option value="" disabled selected>-- Pilih Hari --</option>
                    <option value="Senin" {{ (old('hari', $course->hari ?? '') == 'Senin') ? 'selected' : '' }}>Senin</option>
                    <option value="Selasa" {{ (old('hari', $course->hari ?? '') == 'Selasa') ? 'selected' : '' }}>Selasa</option>
                    <option value="Rabu" {{ (old('hari', $course->hari ?? '') == 'Rabu') ? 'selected' : '' }}>Rabu</option>
                    <option value="Kamis" {{ (old('hari', $course->hari ?? '') == 'Kamis') ? 'selected' : '' }}>Kamis</option>
                    <option value="Jumat" {{ (old('hari', $course->hari ?? '') == 'Jumat') ? 'selected' : '' }}>Jumat</option>
                    <option value="Sabtu" {{ (old('hari', $course->hari ?? '') == 'Sabtu') ? 'selected' : '' }}>Sabtu</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jam_mulai" class="form-label">Jam Mulai</label>
                        <input
                            type="time"
                            class="form-control"
                            id="jam_mulai"
                            name="jam_mulai"
                            value="{{ old('jam_mulai', isset($course) ? date('H:i', strtotime($course->jam_mulai)) : '') }}"
                            required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jam_selesai" class="form-label">Jam Selesai</label>
                        <input
                            type="time"
                            class="form-control"
                            id="jam_selesai"
                            name="jam_selesai"
                            value="{{ old('jam_selesai', isset($course) ? date('H:i', strtotime($course->jam_selesai)) : '') }}"
                            required>
                    </div>
                </div>
            </div>

            <div class="d-flex mt-4">
                <a href="{{ route('sistem_akademik.course.index') }}" class="btn-secondary-app">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn-primary-app ms-auto">
                    <i class="bi bi-{{ isset($course) ? 'save' : 'plus-circle' }}"></i>
                    {{ isset($course) ? 'Update Course' : 'Simpan Course' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({
            placeholder: 'Pilih siswa...',
            width: '100%'
        });

        // Initialize kelas dropdown to set jurusan
        const setJurusanFromKelas = () => {
            const kelasSelect = $('#kelas_id');
            const selectedOption = kelasSelect.find('option:selected');

            if (selectedOption.val()) {
                const jurusan = selectedOption.data('jurusan');
                $('#jurusan').val(jurusan).trigger('change');
            }
        };

        // Sync kelas and jurusan selection
        $('#kelas_id').change(function() {
            setJurusanFromKelas();
        });

        // Load students when jurusan changes
        $('#jurusan').change(function() {
            const jurusan = $(this).val();
            const kelas_id = $('#kelas_id').val();

            if (jurusan && kelas_id) {
                // Show loading indicator
                $('#students-loading').removeClass('d-none');

                // Clear existing options
                $('#siswa_ids').empty().trigger('change');

                // Fetch students by jurusan via AJAX
                $.ajax({
                    url: '{{ route("sistem_akademik.get-students-by-jurusan") }}',
                    type: 'GET',
                    data: {
                        jurusan: jurusan,
                        kelas_id: kelas_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const students = response.students;
                            const studentIds = students.map(s => s.id);

                            // Add students to select
                            students.forEach(function(student) {
                                const option = new Option(
                                    `${student.user.nama} (${student.nisn})`,
                                    student.id,
                                    true,
                                    true
                                );
                                $('#siswa_ids').append(option);
                            });

                            $('#siswa_ids').trigger('change');

                            // Hide loading indicator
                            $('#students-loading').addClass('d-none');

                            // If no students found
                            if (students.length === 0) {
                                Swal.fire({
                                    title: "Informasi",
                                    text: "Tidak ada siswa dalam jurusan yang dipilih.",
                                    icon: "info",
                                    confirmButtonColor: "#4ecdc4"
                                });
                            }
                        }
                    },
                    error: function() {
                        // Hide loading indicator
                        $('#students-loading').addClass('d-none');

                        Swal.fire({
                            title: "Error",
                            text: "Gagal memuat data siswa. Silakan coba lagi.",
                            icon: "error",
                            confirmButtonColor: "#4ecdc4"
                        });
                    }
                });
            } else {
                // Clear students if no jurusan selected
                $('#siswa_ids').empty().trigger('change');
            }
        });

        // If editing, trigger jurusan change to load students
        @if(isset($course))
        $('#jurusan').trigger('change');
        @endif

        // Filter kelas dropdown based on jurusan
        $('#jurusan').on('change', function() {
            const selectedJurusan = $(this).val();

            if (selectedJurusan) {
                // First reset kelas dropdown if jurusan changed manually
                const currentKelasJurusan = $('#kelas_id option:selected').data('jurusan');

                if (currentKelasJurusan !== selectedJurusan) {
                    $('#kelas_id').val('');
                }

                // Show only kelas from selected jurusan
                $('#kelas_id option').each(function() {
                    const kelasJurusan = $(this).data('jurusan');

                    if (kelasJurusan === selectedJurusan || !kelasJurusan) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                // Show all kelas options
                $('#kelas_id option').show();
            }
        });
    });
</script>
@endsection