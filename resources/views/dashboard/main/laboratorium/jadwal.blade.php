@extends('dashboard.layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* Reset dan Styling Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
        }
    
        /* Header */
        header {
            background-color: #003366;
            color: white;
            padding: 20px;
            text-align: center;
        }
    
        header .logo img {
            width: 100px;
        }
    
        header h1 {
            font-size: 2rem;
            margin-top: 10px;
        }
    
        /* Navigation */
        nav {
            background-color: #004080;
            padding: 10px 0;
        }
    
        nav ul {
            list-style: none;
            text-align: center;
        }
    
        nav ul li {
            display: inline-block;
            margin: 0 15px;
        }
    
        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
        }
    
        nav ul li a:hover {
            text-decoration: underline;
        }
    
        /* Kalender dan Form Peminjaman */
        .jadwal-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
    
        .jadwal-table th,
        .jadwal-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
    
        .kosong {
            background-color: #FF5733;
            color: white;
            cursor: pointer;
        }
    
        .terpakai {
            background-color: #4CAF50;
            color: white;
        }
    
        /* Form Peminjaman */
        #peminjaman-form {
            display: none;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 0 auto;
        }
    
        #peminjaman-form input,
        #peminjaman-form textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    
        #peminjaman-form button {
            background-color: #004080;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    
        #peminjaman-form button:hover {
            background-color: #003366;
        }
    
        /* Footer */
        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 20px 0;
        }
    </style>
@endsection

@section('content')
    <section>
        <h2>Jadwal Laboratorium</h2>
        <table class="jadwal-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    @foreach ($uniqueDates as $date)
                        <th>Labor {{ $date }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($jam as $time)
                    <tr>
                        <td>{{ $time }}</td>
                        @foreach ($uniqueDates as $date)
                            @php
                                // Pisahkan nama labor dan tanggal dari format "TKJ (16)"
                                preg_match('/^(.*?) \((\d+)\)$/', $date, $matches);
                                $labor = $matches[1] ?? ''; // Mengambil nama labor
                                $day = $matches[2] ?? ''; // Mengambil tanggal (misalnya 16)
                        
                                // Menyaring event berdasarkan labor dan tanggal
                                $event = $events->first(function ($e) use ($labor, $day, $time) {
                                    return \Carbon\Carbon::parse($e->start)->format('d') == $day 
                                        && $e->labor == $labor 
                                        && \Carbon\Carbon::parse($e->start)->format('H:i') == $time;
                                });
                            @endphp
                            <td class="{{ $event && $event->status ? $event->status : 'tidak ada data' }}" 
                                data-id="{{ $event ? \Carbon\Carbon::parse($event->start)->format('Y-m-d H:i') : '' }}"
                                data-lab-id={{ $event ? $event->id : '' }}>
                                {{ $event ? $event->status : 'Tidak ada data' }} ({{ $event ? $event->labor : '' }})
                            </td>
                        @endforeach                    
                    </tr>
                @endforeach
            </tbody>         
        </table>
    </section>

    <!-- Form Peminjaman -->
    <div id="peminjaman-form">
        <h3>Peminjaman Laboratorium</h3>
        <form action="{{ route('lab.peminjaman') }}" method="post">
            @csrf
            <input type="hidden" name="lab_id" id="lab_id">

            <label for="jadwal">Jadwal:</label>
            <input type="text" id="jadwal" name="jadwal" readonly>

            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="Keperluan">Keperluan:</label>
            <textarea id="keperluan" name="keperluan" required></textarea>

            <button type="submit">Ajukan Peminjaman</button>
        </form>
    </div>
@endsection

@section('script')
    <script>
        // Menangani klik pada jadwal kosong untuk membuka form peminjaman
        document.querySelectorAll('.kosong').forEach(function(cell) {
            cell.addEventListener('click', function() {
                var jadwal = this.getAttribute('data-id');
                var labId = this.getAttribute('data-lab-id');
                // Tampilkan form peminjaman dan set jadwal yang dipilih
                document.getElementById('peminjaman-form').style.display = 'block';
                document.getElementById('jadwal').value = jadwal.replace('|', ' - ');
                document.getElementById('lab_id').value = labId;
            });
        });
    </script>
@endsection