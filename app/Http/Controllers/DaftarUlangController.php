<?php

namespace App\Http\Controllers;

use App\Models\DaftarUlangSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DaftarUlangController extends Controller
{
    public function create()
    {
        $title = 'Daftar Ulang Siswa';
        $header = 'Form Daftar Ulang Siswa';
        
        $majors = [
            'Teknik Otomotif Sepeda Motor',
            'Teknik Otomotif Kendaraan Ringan',
            'Teknik Pemesinan',
            'Teknik Audio Video',
            'Teknik Gambar Bangunan',
            'Teknik Konstruksi Batu dan Beton',
            'Teknik Komputer Jaringan',
            'Teknik Instalasi Tenaga Listrik'
        ];
        
        return view('daftar-ulang.create', compact('title', 'header', 'majors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:daftar_ulang_siswa,email|unique:users,email',
            'major' => 'required|string',
            'jenis_kelamin' => 'required|in:Pria,Wanita',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|min:10',
            'no_hp' => 'required|numeric|digits_between:10,15',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Alamat email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'major.required' => 'Jurusan wajib dipilih',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin harus Pria atau Wanita',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.min' => 'Alamat minimal 10 karakter',
            'no_hp.required' => 'Nomor HP wajib diisi',
            'no_hp.numeric' => 'Nomor HP harus berupa angka',
            'no_hp.digits_between' => 'Nomor HP harus antara 10-15 digit',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DaftarUlangSiswa::create([
            'name' => $request->name,
            'email' => $request->email,
            'major' => $request->major,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'status' => 'pending'
        ]);

        return redirect()->route('daftar-ulang.success')
            ->with('status', 'success')
            ->with('title', 'Pendaftaran Berhasil')
            ->with('message', 'Formulir daftar ulang berhasil dikirim. Mohon tunggu konfirmasi dari admin.');
    }

    public function success()
    {
        $title = 'Pendaftaran Berhasil';
        $header = 'Pendaftaran Berhasil';
        
        return view('daftar-ulang.success', compact('title', 'header'));
    }
}