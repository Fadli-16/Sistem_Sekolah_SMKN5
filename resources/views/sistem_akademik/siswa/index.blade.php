@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    /* tombol tambah lebih kecil */
    .btn-add-siswa {
        min-width: 0;
        padding: .35rem .6rem;
    }

    /* avatar kecil + crop (lebih kecil agar tidak memaksa lebar kolom) */
    .avatar {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
        display: inline-block;
        vertical-align: middle;
        flex-shrink: 0;
    }

    /* wrapper foto + teks */
    .media {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .name-wrap {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.1;
    }

    /* Biarkan nama melakukan wrapping agar tidak memperlebar tabel */
    .name-wrap .name {
        font-weight: 600;
        display: block;
        white-space: normal;
        /* allow wrapping */
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    /*
     * Force table to fit container and allow cells to wrap content
     * - table-layout: fixed membuat kolom dipatok pada lebar yang ditetapkan,
     *   sehingga total lebar tidak melebihi container.
     * - menghilangkan min-width yang memaksa overflow.
     */
    .table {
        table-layout: fixed;
        width: 100% !important;
    }

    .table th,
    .table td {
        white-space: normal;
        /* allow wrapping inside cells */
        word-wrap: break-word;
        /* break long words/strings */
        vertical-align: middle;
    }

    /* atur lebar relatif (persentase sehingga tidak overflow) */
    th.col-no {
        width: 4%;
    }

    th.col-nis {
        width: 7%;
    }

    th.col-photo {
        width: 22%;
    }

    th.col-kelas {
        width: 6%;
    }

    th.col-jurusan {
        width: 12%;
    }

    th.col-jk {
        width: 9%;
    }

    th.col-agama {
        width: 8%;
    }

    th.col-alamat {
        width: 19%;
    }

    th.col-hp {
        width: 7%;
    }

    th.col-aksi {
        width: 6%;
    }

    /* aksi & tombol kecil */
    .btn-aksi {
        padding: .25rem .45rem;
        font-size: .85rem;
    }

    /* header compact */
    .page-header-left p {
        margin: 0;
    }

    /* responsive tweaks: pada layar sangat sempit kita kecilkan avatar & font sedikit */
    @media (max-width: 768px) {
        .avatar {
            width: 40px;
            height: 40px;
        }

        th.col-photo {
            width: 26%;
        }

        th.col-alamat {
            width: 18%;
        }

        th.col-jurusan {
            width: 14%;
        }

        .name-wrap .name {
            font-size: 0.95rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container mb-3">
    {{-- header bar: judul (kiri) + tombol (kanan) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="page-header-left">
            <h1 class="page-title mb-3">{{ $header }}</h1>
            <p class="text-muted mb-0"><i class="bi bi-people me-2"></i>Kelola biodata seluruh siswa</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('sistem_akademik.siswa.create') }}"
                class="btn btn-primary btn-sm btn-add-siswa mt-5">
                <i class="bi bi-plus-circle"></i> Tambah Siswa
            </a>
        </div>
    </div>

    <div class="card p-3">
        {{-- table-responsive dibiarkan, namun CSS di atas memastikan overflow horizontal tidak diperlukan --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="data-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th class="col-nis">NIS</th>
                        <th class="col-photo">Nama</th>
                        <th class="col-kelas">Kelas</th>
                        <th class="col-jurusan">Jurusan</th>
                        <th class="col-jk">Jenis Kelamin</th>
                        <th class="col-agama">Agama</th>
                        <th class="col-alamat">Alamat</th>
                        <th class="col-hp">No HP</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $index => $student)
                    @php
                    $defaultImage = asset('assets/profile/default.png');

                    // prefer full path to stored image (public/assets/profile/)
                    $avatar = $student->image
                    ? asset('assets/profile/' . ltrim($student->image, '/'))
                    : $defaultImage;

                    $name = optional($student->user)->nama ?? '-';
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $student->nis ?? '-' }}</td>

                        <td class="col-photo">
                            <div class="media">
                                <img src="{{ $avatar }}" alt="avatar" class="avatar"
                                    onerror="this.onerror=null;this.src='{{ $defaultImage }}'">
                                <div class="name-wrap">
                                    <span class="name">{{ $name }}</span>
                                    {{-- jika ingin menampilkan email lalu hilangkan komentar:
                                    <small class="text-muted">{{ optional($student->user)->email ?? '' }}</small>
                                    --}}
                                </div>
                            </div>
                        </td>

                        <td>{{ $student->kelas ?? '-' }}</td>
                        <td class="col-jurusan">{{ $student->jurusan ?? '-' }}</td>
                        <td>{{ $student->jenis_kelamin ?? '-' }}</td>
                        <td>{{ $student->agama ?? '-' }}</td>
                        <td>{{ $student->alamat ?? '-' }}</td>
                        <td>{{ $student->no_hp ?? '-' }}</td>
                        <td>
                            <a href="{{ route('sistem_akademik.siswa.edit', $student->id) }}"
                                class="btn btn-warning btn-sm btn-aksi" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form action="{{ route('sistem_akademik.siswa.destroy', $student->id) }}"
                                method="post" id="deleteForm{{ $student->id }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('{{ $student->id }}')"
                                    class="btn btn-danger btn-sm btn-aksi" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
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
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#data-table')) {
            $('#data-table').DataTable({
                responsive: false, // non-aktifkan responsive agar kolom tetap terlihat (tidak di-collapse)
                autoWidth: false, // biarkan CSS mengatur lebar kolom
                columnDefs: [{
                    orderable: false,
                    targets: [-1] // aksi (kolom terakhir)
                }],
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
                    }
                }
            });
        }
    });

    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Data siswa akan dihapus secara permanen!",
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