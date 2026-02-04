<?php

namespace App\Http\Controllers;

use App\Mail\PpdbKelulusanEmail;
use App\Models\PPDBCalonSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PpdbController extends Controller
{
    public function index()
    {
        $title = 'PPDB';
        $header = 'Informasi PPDB Siswa Baru';
        $totalPendaftar = PPDBCalonSiswa::count();
        $diterima = PPDBCalonSiswa::where('status_pendaftaran', 'Diterima')->count();
        $ditolak = PPDBCalonSiswa::where('status_pendaftaran', 'Ditolak')->count();
        $menunggu = PPDBCalonSiswa::where('status_pendaftaran', 'Menunggu')->count();

        return view('ppdb.dashboard', compact('totalPendaftar', 'diterima', 'ditolak', 'menunggu', 'title', 'header'));
    }

    public function create()
    {
        $title = 'Tambah Pendaftar';
        $header = 'Tambah Pendaftar';
        return view('ppdb.create', compact('title', 'header'));
    }

    public function store(Request $request)
    {
        // return $request->all();

        $messages = [
            'nama.required' => 'Kolom Nama wajib diisi!',
            'tanggal_lahir.required' => 'Kolom Tanggal lahir wajib diisi!',
            'tanggal_lahir.date' => 'Kolom Tanggal lahir harus berupa tanggal yang valid!',
            'alamat.required' => 'Kolom Alamat wajib diisi!',
            'sekolah_asal.required' => 'Kolom Sekolah asal wajib diisi!',
            'no_hp.required' => 'Kolom Nomor HP wajib diisi!',
            'email.required' => 'Kolom Email wajib diisi!',
            'email.email' => 'Kolom Email harus berupa alamat email yang valid!',
            'file_nilai_rapor.required' => 'Kolom File nilai raport wajib diisi!',
            'file_nilai_rapor.mimes' => 'Kolom File nilai raport harus diisi dengan file dengan format PDF!',
            'file_nilai_rapor.max' => 'Ukuran file nilai raport tidak boleh lebih dari 2MB!',
        ];
    
        $request->validate([
            'nama' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'sekolah_asal' => 'required',
            'no_hp' => 'required',
            'email' => 'required|email',
            'file_nilai_rapor' => 'required|mimes:pdf|max:2048',
        ], $messages);

        if ($request->hasFile('file_nilai_rapor')) {
            $file = $request->file('file_nilai_rapor');
            $extension = $file->getClientOriginalExtension();
            $randomString = substr(md5(mt_rand()), 0, 5);
            $filename = 'NILAI-RAPOR-' . time() . '-' . $randomString . '.' . $extension;
            $file->move(public_path('file/nilai_rapor'), $filename);
            $request->merge(['nilai_rapor' => $filename]);
        }

        PPDBCalonSiswa::create(array_merge($request->all(), [
            'tanggal_pendaftaran' => now(),
        ]));

        return redirect()->route('ppdb.create')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Berhasil menambah daftar siswa');
    }

    public function edit(PPDBCalonSiswa $calonSiswa)
    {
        $title = 'Edit Pendaftar';
        $header = 'Edit Pendaftar';
        return view('ppdb.edit', compact('calonSiswa', 'title', 'header'));
    }

    public function update(Request $request, PPDBCalonSiswa $calonSiswa)
    {
        $messages = [
            'nama.required' => 'Kolom Nama wajib diisi!',
            'tanggal_lahir.required' => 'Kolom Tanggal lahir wajib diisi!',
            'tanggal_lahir.date' => 'Kolom Tanggal lahir harus berupa tanggal yang valid!',
            'alamat.required' => 'Kolom Alamat wajib diisi!',
            'sekolah_asal.required' => 'Kolom Sekolah asal wajib diisi!',
            'no_hp.required' => 'Kolom Nomor HP wajib diisi!',
            'email.required' => 'Kolom Email wajib diisi!',
            'email.email' => 'Kolom Email harus berupa alamat email yang valid!',
            'file_nilai_rapor.required' => 'Kolom File nilai raport wajib diisi!',
            'file_nilai_rapor.mimes' => 'Kolom File nilai raport harus diisi dengan file dengan format PDF!',
            'file_nilai_rapor.max' => 'Ukuran file nilai raport tidak boleh lebih dari 2MB!',
        ];

        $request->validate([
            'nama' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'sekolah_asal' => 'required',
            'no_hp' => 'required',
            'email' => 'required|email',
            'status_pendaftaran' => 'required',
            'file_nilai_rapor' => 'mimes:pdf|max:2048',
        ], $messages);

        // script ubah gambar nilai
        if ($request->hasFile('file_nilai_rapor')) {
            // Hapus file lama jika ada
            if ($calonSiswa && $calonSiswa->nilai_rapor && file_exists(public_path('file/nilai_rapor/' . $calonSiswa->nilai_rapor))) {
                unlink(public_path('file/nilai_rapor/' . $calonSiswa->nilai_rapor));
            }

            $file = $request->file('file_nilai_rapor');
            $extension = $file->getClientOriginalExtension();
            $randomString = substr(md5(mt_rand()), 0, 5);
            $filename = 'NILAI-RAPOR-' . time() . '-' . $randomString . '.' . $extension;
            $file->move(public_path('file/nilai_rapor'), $filename);
            $request->merge(['nilai_rapor' => $filename]);
        }

        $calonSiswa->update($request->all());

        if ($calonSiswa) {
            return redirect()->route('ppdb.laporan')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Berhasi mengubah data siswa');
        }
    }

    public function destroy(PPDBCalonSiswa $calonSiswa)
    {
        if ($calonSiswa && $calonSiswa->nilai_rapor && file_exists(public_path('file/nilai_rapor/' . $calonSiswa->nilai_rapor))) {
            unlink(public_path('file/nilai_rapor/' . $calonSiswa->nilai_rapor));
        }

        $calonSiswa->delete();
        return redirect()->route('ppdb.laporan')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Berhasi menghapus data siswa');
    }

    public function laporan()
    {
        $title = 'Laporan PPDB';
        $header = 'Laporan';
        $pendaftar = PPDBCalonSiswa::all();
        return view('ppdb.laporan', compact('pendaftar', 'title', 'header'));
    }

    public function sendEmailKelulusan(PPDBCalonSiswa $calonSiswa)
    {
        $alamat_email = $calonSiswa->email;
        $nama_siswa = $calonSiswa->nama;
        $status_pendaftaran = $calonSiswa->status_pendaftaran;

        $subject = 'Pemberitahuan PPDB';
        $messageLulus = "Halo, {$nama_siswa}, <br><br>Selamat! Kami dengan senang hati menginformasikan bahwa Anda telah <b>Diterima</b> sebagai peserta didik baru di <b>SMK Kota Padang</b> melalui proses PPDB. <br><br>Kami mengucapkan selamat atas pencapaian ini dan menyambut Anda menjadi bagian dari keluarga besar SMK Kota Padang. Mari bersama-sama kita wujudkan masa depan yang gemilang dengan semangat belajar dan berprestasi.<br><br>Untuk informasi lebih lanjut mengenai jadwal daftar ulang dan kegiatan awal, silakan menghubungi pihak sekolah atau kunjungi website resmi kami.<br><br>Salam hangat, <br>Panitia PPDB SMK Kota Padang";
        $messageTidakLulus = "Halo, {$nama_siswa}, <br><br>Terima kasih telah mengikuti proses Penerimaan Peserta Didik Baru (PPDB) di <b>SMK Kota Padang</b>. Dengan berat hati kami menginformasikan bahwa Anda <b>Belum Diterima</b> sebagai peserta didik baru pada tahun ini. <br><br>Kami menghargai semangat dan usaha yang telah Anda tunjukkan. Jangan menyerah, tetaplah semangat dalam mengejar pendidikan dan cita-cita Anda. Kami yakin peluang besar lainnya akan datang di masa depan.<br><br>Jika ada pertanyaan lebih lanjut, jangan ragu untuk menghubungi kami.<br><br>Salam hangat, <br>Panitia PPDB SMK Kota Padang";

        Log::info('Send Email Start');

        try {

            // Send email
            Mail::to($alamat_email)->send(new PpdbKelulusanEmail('', $subject, $status_pendaftaran == 'Diterima' ? $messageLulus : $messageTidakLulus));

            Log::info('Send Email Success');
            return redirect()->route('ppdb.laporan')->with('status', 'success')->with('title', 'Berhasil')->with('message', 'Berhasi Mengirim Email Kelulusan');
        } catch (\Exception $e) {
            Log::error('Error Send Email: ' . $e->getMessage());

            return redirect()->route('ppdb.laporan')->with('status', 'error')->with('title', 'Gagal')->with('message', 'Gagal Mengirim Email Kelulusan');
        }
    }
}