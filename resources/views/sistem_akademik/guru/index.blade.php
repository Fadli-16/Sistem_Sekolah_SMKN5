@extends('sistem_akademik.layouts.main')

@section('css')
<style>
    .card-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    /* Photo + name cell layout */
    .cell-photo-name {
        min-width: 260px;
        /* lebih lebar supaya tidak sempit */
    }

    .photo-name-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
        display: inline-block;
        flex: 0 0 56px;
    }

    .name-wrap {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .name-wrap .name {
        font-weight: 600;
        font-size: 1rem;
    }

    .small-muted {
        font-size: 0.85rem;
        color: #666;
    }

    /* adjust table spacing a bit */
    table#data-table td,
    table#data-table th {
        vertical-align: middle;
    }

    /* responsive */
    @media (max-width: 768px) {
        .avatar {
            width: 44px;
            height: 44px;
        }

        .cell-photo-name {
            min-width: 180px;
        }
    }
</style>
@endsection

@section('content')
<div class="container mt-3 mb-3">

    {{-- HEADER: Judul kiri + Button kanan (sejajar) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="page-title mb-3">{{ $header }}</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-person-workspace me-3"></i>
                Kelola biodata guru dan tendik
            </p>
        </div>

        <div>
            <a href="{{ route('sistem_akademik.guru.create') }}"
                class="btn btn-primary btn-sm mt-5"
                style="min-width:140px;">
                <i class="bi bi-plus-circle"></i> Tambah Guru
            </a>
        </div>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>NIP</th>
                        <th class="cell-photo-name">Nama</th>
                        <th>Jurusan</th>
                        <th>Jenis Kelamin</th>
                        <th>Tgl Lahir</th>
                        <th>Agama</th>
                        <th>Alamat</th>
                        <th>No. HP</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gurus as $index => $guru)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $guru->nip ?? '-' }}</td>

                        {{-- Photo + Nama --}}
                        <td>
                            @php
                            $avatar = $guru->image
                            ? asset('assets/profile/' . ltrim($guru->image, '/'))
                            : asset('assets/profile/default.png');
                            $nama = optional($guru->user)->nama ?? '-';
                            @endphp

                            <div class="photo-name-wrap">
                                <img src="{{ $avatar }}" alt="avatar" class="avatar">
                                <div class="name-wrap">
                                    <span class="name">{{ $nama }}</span>
                                </div>
                            </div>
                        </td>

                        <td>{{ $guru->jurusan ?? '-' }}</td>
                        <td>{{ $guru->jenis_kelamin ?? '-' }}</td>
                        <td>
                            @if(!empty($guru->tanggal_lahir))
                            {{ \Carbon\Carbon::parse($guru->tanggal_lahir)->format('d M Y') }}
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $guru->agama ?? '-' }}</td>
                        <td style="max-width:240px; white-space:normal;">
                            {{ $guru->alamat ?? '-' }}
                        </td>
                        <td>{{ $guru->no_hp ?? '-' }}</td>

                        <td>
                            <a href="{{ route('sistem_akademik.guru.edit', $guru->id) }}"
                                class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form action="{{ route('sistem_akademik.guru.destroy', $guru->id) }}"
                                method="POST"
                                id="deleteForm{{ $guru->id }}"
                                style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button"
                                    onclick="confirmDelete('{{ $guru->id }}')"
                                    class="btn btn-sm btn-danger"
                                    title="Hapus">
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
                responsive: true,
                columnDefs: [{
                        orderable: false,
                        targets: [2, 9]
                    } // photo & aksi
                ],
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

        // fallback image handler
        const defaultAvatar = "{{ asset('assets/profile/default.png') }}";
        document.querySelectorAll('img.avatar').forEach(img => {
            img.addEventListener('error', function() {
                this.onerror = null;
                this.src = defaultAvatar;
            });
            if (!img.getAttribute('src')) img.src = defaultAvatar;
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Data guru akan dihapus secara permanen!",
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