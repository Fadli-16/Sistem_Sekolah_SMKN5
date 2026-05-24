@extends('ppdb.layouts.main')

@section('css')
    <style>
        a {
            background-color: #FF5733;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        a:hover {
            background-color: #de7519;
        }
    </style>
@endsection

@section('content')
    <!-- Banner Section -->
   <section class="banner">
        <h2>Selamat Datang di Sistem Informasi PPDB SMK</h2>
        <p>
            Sebuah platform yang dirancang untuk memberikan kemudahan kepada pengguna
            dalam mengakses berbagai informasi terkait Penerimaan Peserta Didik Baru (PPDB). Melalui website ini, Anda dapat dengan
            mudah
            melihat informasi terkait PPDB, mengetahui daftar persyaratan pendaftaran, serta melakukan
            proses
            pendaftaran dengan praktis dan efisien. Kami berharap sistem ini dapat membantu meningkatkan efektivitas
            pengelolaan PPDB sekaligus mendukung kegiatan penerimaan peserta didik baru secara optimal.
        </p>
    </section>

    <!-- Informasi Fitur -->
    <section class="m-4">
        <div class="container">
            <div class="card p-3">
                <a href="{{ route('ppdb.create') }}" class="px-2 py-1 rounded-3 text-decoration-none" style="width:12%;">Tambah Daftar</a>
                <table class="table table-bordered table-striped" id="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Tgl Lahir</th>
                            <th>Sekolah Asal</th>
                            <th>No Telepon</th>
                            <th>Email</th>
                            @if (Auth::check())
                                <th>Status</th>
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftar as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>{{ $item->tanggal_lahir }}</td>
                                <td>{{ $item->sekolah_asal }}</td>
                                <td>{{ $item->no_hp }}</td>
                                <td>{{ $item->email }}</td>
                                @if (Auth::check())
                                    <td>{{ $item->status_pendaftaran }}</td>
                                    <td>
                                        @if ($item->status_pendaftaran == 'Diterima')
                                            <form action="{{ route('ppdb.emailkelulusan', $item->id) }}" method="post" id="kirimEmailKelulusanForm{{ $item->id }}">
                                                @csrf
                                                <a href="javascript:void(0)" onclick="confirmSendEmailKelulusan('{{ $item->id }}')" class="p-2 rounded-3 mb-2"><i class="bi bi-send"></i></a>
                                            </form>
                                        @endif
                                        <a href="{{ route('ppdb.edit', $item->id) }}" class="p-2 rounded-3 mb-2"><i class="bi bi-pencil-square"></i></a>
                                        <form action="{{ route('ppdb.destroy', $item->id) }}" method="post" id="deleteForm{{ $item->id }}">
                                            @csrf
                                            @method('delete')
                                            <a href="javascript:void(0)" onclick="confirmDelete('{{ $item->id }}')" class="p-2 rounded-3 mb-2"><i class="bi bi-trash"></i></a>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('#data-table').DataTable();
        });

        function confirmSendEmailKelulusan(e) {
            swal({
                title: "Apakah anda yakin mengirimkan email kelulusan ini?",
                text: "Tekan OK untuk melanjutkan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete){
                    $('#kirimEmailKelulusanForm' + e).submit();
                } else {
                    swal("Data tidak jadi dihapus!", {
                        icon: "error",
                    });
                }
            });
        }
    </script>
@endsection