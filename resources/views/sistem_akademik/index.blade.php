@extends('sistem_akademik.layouts.main')

@section('content')
<div class="container mt-3 mb-3">
    <h1>Data Siswa</h1>
    <div class="card p-3 ">
        <a href="{{ route('sistem_akademik.siswa.create') }}" class="px-2 py-1 rounded-3 text-decoration-none mb-3" style="width:12%;">Tambah Siswa</a>
        <table class="table table-bordered" id="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->user->nama }}</td>
                    <td>{{ $student->nis ?? '-' }}</td>
                    <td>{{ $student->kelas}}</td>
                    <td>{{ $student->jurusan}}</td>
                    <td>{{ $student->alamat ?? '-' }}</td>
                    <td>{{ $student->no_hp ?? '-' }}</td>
                    <td>
                        <a href="{{ route('sistem_akademik.siswa.edit', $student->id) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i></a>
                        <form action="{{ route('sistem_akademik.siswa.destroy', $student->id) }}" method="post" id="deleteForm{{ $student->id }}">
                            @csrf
                            @method('delete')
                            <a href="javascript:void(0)" onclick="confirmDelete('{{ $student->id }}')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Check if DataTable is already initialized before initializing it
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