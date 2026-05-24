@extends('magang.layouts.main')

@section('css')
<style>
    .page-title {
        margin-bottom: 20px;
        color: var(--dark-color);
    }
    
    .action-btn {
        background-color: var(primary-color);
        color: rgb(9, 97, 6);
        padding: 8px 16px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
        border: none;
    }
    
    .action-btn:hover {
        background-color: var(--secondary-color);
        color: rgb(17, 168, 233);
    }
    
    .btn-warning {
        background-color: #f39c12;
        border-color: #f39c12;
    }
    
    .btn-danger {
        background-color: #e74c3c;
        border-color: #e74c3c;
    }
    
    .btn-warning:hover {
        background-color: #d35400;
        border-color: #d35400;
    }
    
    .btn-danger:hover {
        background-color: #c0392b;
        border-color: #c0392b;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Data Perusahaan Magang</h4>
        <a href="{{ route('magang.perusahaan.create') }}" class="action-btn">
            <i class="bi bi-plus-circle me-1"></i> Tambah Data
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Perusahaan</th>
                        <th>Alamat</th>
                        <th>No Perusahaan</th>
                        <th>Nama Pembimbing</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
    @foreach ($wakils as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->nama_perusahaan }}</td>
            <td>{{ $item->alamat }}</td>
            <td>{{ $item->no_perusahaan }}</td>
            <td>{{ $item->nama }}</td>
            <td>
                <div class="d-flex gap-1">
                    <a href="{{ route('magang.perusahaan.edit', $item->id) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('magang.perusahaan.destroy', $item->id) }}" method="post" id="deleteForm{{ $item->id }}">
                        @csrf
                        @method('delete')
                        <button type="button" onclick="confirmDelete('{{ $item->id }}')" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>

            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        // Check if DataTable is already initialized before initializing it
        if (!$.fn.DataTable.isDataTable('#data-table')) {
            $('#data-table').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari _MAX_ total records)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }
    });
    
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            document.getElementById('deleteForm' + id).submit();
        }
    }
</script>
@endsection