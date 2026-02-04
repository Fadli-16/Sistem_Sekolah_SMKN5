@extends('sistem_akademik.layouts.main')

@section('content')
<div class="container-fluid mt-3 mb-3">
    <h1 class="page-title">{{ $header }}</h1>
    <p class="text-muted mb-4">Kelola data course, termasuk siswa yang tergabung di dalamnya</p>

    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Daftar Course</h5>
            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa')
            <a href="{{ route('sistem_akademik.course.create') }}" class="btn-primary-app">
                <i class="bi bi-plus-circle"></i> Tambah Course
            </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Course</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Jadwal</th>
                        <th>Jumlah Siswa</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $index => $course)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $course->nama_course }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $course->kelas->nama_kelas }}</span>
                            <br>
                            <small>{{ $course->kelas->jurusan }}</small>
                        </td>
                        <td>{{ $course->mataPelajaran->nama_mata_pelajaran }}</td>
                        <td>{{ $course->guru->nama }}</td>
                        <td>{{ $course->hari }}, {{ date('H:i', strtotime($course->jam_mulai)) }} - {{ date('H:i', strtotime($course->jam_selesai)) }}</td>
                        <td>
                            <span class="badge badge-primary text-dark">
                                <i class="bi bi-people"></i> {{ $course->siswa->count() }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('sistem_akademik.course.show', $course->id) }}" class="btn-action btn-view" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa')
                            <a href="{{ route('sistem_akademik.course.edit', $course->id) }}" class="btn-action btn-edit" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('sistem_akademik.course.destroy', $course->id) }}" method="post" id="deleteForm{{ $course->id }}" class="d-inline">
                                @csrf
                                @method('delete')
                                <button type="button" onclick="confirmDelete('{{ $course->id }}')" class="btn-action btn-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($courses->count() == 0)
        <div class="empty-state">
            <i class="bi bi-journal-x"></i>
            <p>Belum ada data course</p>
            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin' || Auth::user()->role == 'admin_sa')
            <a href="{{ route('sistem_akademik.course.create') }}" class="btn-primary-app">
                <i class="bi bi-plus-circle"></i> Tambah Course
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#data-table')) {
            $('#data-table').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data yang ditampilkan",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                }
            });
        }
    });

    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Data kelas akan dihapus secara permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }
</script>
@endsection